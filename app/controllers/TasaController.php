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
        $this->requireLogin();

        $tasaModel = new TasaModel();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fecha = $_POST['fecha'] ?? '';
            $valor = $_POST['valor_tasa'] ?? '';
            $fuente = trim($_POST['fuente'] ?? 'BCV');

            if (!$fecha || $valor === '' || !is_numeric($valor)) {
                $error = 'Completa fecha y valor de la tasa correctamente.';
            } else {
                try {
                    $tasaModel->create($fecha, (float)$valor, $fuente);
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

        $isJson = isset($_GET['json']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        if ($isJson) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($resultado);
            exit;
        }

        $destino = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php?r=tasas';
        // Limpiar parámetros viejos de mensaje en el referrer
        $destino = preg_replace('/([?&])msg=[^&]+(&|$)/', '$1', $destino);
        $sep = strpos($destino, '?') !== false ? '&' : '?';
        
        $this->redirect(rtrim($destino, '&?') . $sep . 'msg=' . urlencode($resultado['mensaje']));
    }
}
