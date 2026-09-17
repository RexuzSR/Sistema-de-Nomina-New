<?php

require_once __DIR__ . '/../../core/Model.php';

class TasaModel extends Model {
    public function getAll(int $limit = 30): array {
        $stmt = $this->db->prepare("SELECT * FROM tb_tasa_cambio ORDER BY fecha DESC, id_tasa DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUltima(): ?array {
        $stmt = $this->db->query("SELECT * FROM tb_tasa_cambio ORDER BY fecha DESC, id_tasa DESC LIMIT 1");
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getByFecha(string $fecha): ?array {
        $stmt = $this->db->prepare("SELECT * FROM tb_tasa_cambio WHERE fecha = ? ORDER BY id_tasa DESC LIMIT 1");
        $stmt->execute([$fecha]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(string $fecha, float $valor, string $fuente = 'BCV'): void {
        $existente = $this->getByFecha($fecha);
        if ($existente) {
            $stmt = $this->db->prepare("UPDATE tb_tasa_cambio SET valor_tasa = ?, fuente = ? WHERE id_tasa = ?");
            $stmt->execute([$valor, $fuente ?: 'BCV', $existente['id_tasa']]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO tb_tasa_cambio (fecha, valor_tasa, fuente) VALUES (?,?,?)");
            $stmt->execute([$fecha, $valor, $fuente ?: 'BCV']);
        }
    }

    /**
     * Sincroniza la tasa oficial del BCV desde APIs comunitarias en cascada.
     * Si no hay internet o falla, recurre a la última tasa almacenada (caché offline).
     */
    public function sincronizarTasaBcv(): array {
        $fuentes = [
            [
                'url' => 'https://ve.dolarapi.com/v1/dolares/oficial',
                'parse' => function($data) {
                    if (isset($data['promedio']) && is_numeric($data['promedio']) && (float)$data['promedio'] > 0) {
                        $fecha = !empty($data['fechaActualizacion']) ? substr($data['fechaActualizacion'], 0, 10) : date('Y-m-d');
                        return ['tasa' => (float)$data['promedio'], 'fecha' => $fecha, 'fuente' => 'BCV Oficial (DolarApi)'];
                    }
                    return null;
                }
            ],
            [
                'url' => 'https://bcv.today/api/v1/rate.json',
                'parse' => function($data) {
                    if (isset($data['USD']) && is_numeric($data['USD']) && (float)$data['USD'] > 0) {
                        $fecha = !empty($data['date']) ? $data['date'] : (!empty($data['effective_date']) ? $data['effective_date'] : date('Y-m-d'));
                        return ['tasa' => (float)$data['USD'], 'fecha' => $fecha, 'fuente' => 'BCV Oficial (BCV.Today)'];
                    }
                    return null;
                }
            ]
        ];

        $resultado = null;

        foreach ($fuentes as $f) {
            $json = null;
            if (function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $f['url'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 3,
                    CURLOPT_CONNECTTIMEOUT => 3,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) SistemaNomina/2.0'
                ]);
                $resp = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($httpCode === 200 && $resp) {
                    $json = $resp;
                }
            } else {
                $ctx = stream_context_create(['http' => ['timeout' => 3]]);
                $json = @file_get_contents($f['url'], false, $ctx);
            }

            if ($json) {
                $parsed = json_decode($json, true);
                if (is_array($parsed)) {
                    $res = $f['parse']($parsed);
                    if ($res && $res['tasa'] > 0) {
                        $resultado = $res;
                        break;
                    }
                }
            }
        }

        if ($resultado) {
            $fecha = $resultado['fecha'];
            $tasa = $resultado['tasa'];
            $fuente = $resultado['fuente'];

            // Verificar si ya existe para esa fecha
            $existente = $this->getByFecha($fecha);
            if ($existente) {
                $stmt = $this->db->prepare("UPDATE tb_tasa_cambio SET valor_tasa = ?, fuente = ? WHERE id_tasa = ?");
                $stmt->execute([$tasa, $fuente, $existente['id_tasa']]);
                $accion = 'actualizada';
            } else {
                $stmt = $this->db->prepare("INSERT INTO tb_tasa_cambio (fecha, valor_tasa, fuente) VALUES (?,?,?)");
                $stmt->execute([$fecha, $tasa, $fuente]);
                $accion = 'insertada';
            }

            return [
                'exito' => true,
                'origen' => 'online',
                'accion' => $accion,
                'tasa' => $tasa,
                'fecha' => $fecha,
                'fuente' => $fuente,
                'mensaje' => "Tasa BCV sincronizada con éxito: 1 USD = " . number_format($tasa, 4, ',', '.') . " Bs. (Vigencia: {$fecha})"
            ];
        }

        // Si falló la conexión (offline)
        $ultima = $this->getUltima();
        if ($ultima) {
            return [
                'exito' => false,
                'origen' => 'cache',
                'tasa' => (float)$ultima['valor_tasa'],
                'fecha' => $ultima['fecha'],
                'fuente' => $ultima['fuente'],
                'mensaje' => 'Sin conexión a internet: usando última tasa guardada en base de datos (' . number_format((float)$ultima['valor_tasa'], 2, ',', '.') . ' Bs.)'
            ];
        }

        return [
            'exito' => false,
            'origen' => 'ninguno',
            'tasa' => 0.0,
            'fecha' => null,
            'fuente' => null,
            'mensaje' => 'Sin conexión y no hay tasas previas registradas en la base de datos.'
        ];
    }
}
