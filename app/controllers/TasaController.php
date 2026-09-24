<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/TasaModel.php';

class TasaController extends Controller {
    public function index(): void {
        $this->requireLogin();

        $tasaModel = new TasaModel();
        $tasas = $tasaModel->getAll();
        $msg = $_GET['msg'] ?? '';
        $ultima = $tasaModel->getUltima();

        $this->render('tasas/listar', [
            'tasas' => $tasas,
            'ultimaTasa' => $ultima,
            'msg' => $msg
        ], 'Tasa de cambio (BCV)');
    }

    public function crear(): void {
        $this->requireRole(['Administrador', 'Operador']);

        $tasaModel = new TasaModel();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $fecha = $_POST['fecha'] ?? '';
            $valor = $_POST['valor_tasa'] ?? '';
            $fuente = trim($_POST['fuente'] ?? 'BCV');

            if (!$fecha || $valor === '' || !is_numeric($valor)) {
                $error = 'Completa la fecha y el valor de la tasa con valores válidos.';
            } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || strtotime($fecha) === false) {
                $error = 'La fecha ingresada no es válida.';
            } elseif ($fecha > date('Y-m-d')) {
                $error = 'La fecha de la tasa no puede ser una fecha futura (máximo hoy ' . date('d/m/Y') . ').';
            } elseif ($fecha < '2000-01-01') {
                $error = 'La fecha de la tasa no puede ser anterior al año 2000.';
            } elseif ((float)$valor <= 0) {
                $error = 'El valor de la tasa debe ser un monto numérico mayor a cero.';
            } elseif ((float)$valor > 1000000) {
                $error = 'El valor de la tasa supera el límite permitido.';
            } elseif (mb_strlen($fuente) > 50) {
                $error = 'El campo fuente no puede exceder los 50 caracteres.';
            } else {
                try {
                    $tasaModel->create($fecha, (float)$valor, $fuente);
                    Audit::log('TASAS', 'REGISTRO_MANUAL', "Registró manualmente la tasa de cambio de {$valor} Bs/USD para el día {$fecha}", 'tb_tasa_bcv', null, [
                        'fecha' => $fecha,
                        'valor' => (float)$valor,
                        'fuente' => $fuente
                    ]);
                    $this->redirect('index.php?r=tasas&msg=' . urlencode('Tasa registrada correctamente.'));
                } catch (PDOException $e) {
                    $error = 'Error al registrar la tasa: ' . $e->getMessage();
                }
            }
        }

        $this->render('tasas/crear', [
            'error' => $error
        ], 'Registrar tasa de cambio');
    }

    /**
     * Sincroniza la tasa del BCV con APIs públicas y caché offline.
     * Soporta peticiones AJAX (JSON) y peticiones regulares (redirección).
     */
    public function sincronizar(): void {
        $this->requireLogin();

        $tasaModel = new TasaModel();
        $resultado = $tasaModel->sincronizarTasaBcv();

        if (!empty($resultado['exito'])) {
            Audit::log('TASAS', 'SINCRONIZAR_BCV', "Sincronizó tasa oficial del BCV: " . ($resultado['tasa'] ?? 'N/A') . " Bs/USD", 'tb_tasa_bcv', null, $resultado);
        }

        $isJson = isset($_GET['json']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        if ($isJson) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($resultado);
            exit;
        }

        $destino = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php?r=tasas';
        if (strpos($destino, 'tasas/sincronizar') !== false) {
            $destino = 'index.php?r=tasas';
        }
        // Limpiar parámetros viejos de mensaje en el referrer
        $destino = preg_replace('/([?&])msg=[^&]+(&|$)/', '$1', $destino);
        $destino = rtrim($destino, '&?');
        $sep = strpos($destino, '?') !== false ? '&' : '?';
        
        $this->redirect($destino . $sep . 'msg=' . urlencode($resultado['mensaje']));
    }
}
