<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/TasaModel.php';

class TasaController extends Controller {
    public function index(): void {
        $this->requireLogin();

        $tasaModel = new TasaModel();
        $tasas = $tasaModel->getAll();
        $msg = $_GET['msg'] ?? '';

        $this->render('tasas/listar', [
            'tasas' => $tasas,
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
                    $error = 'Ya existe una tasa registrada para esa fecha.';
                }
            }
        }

        $this->render('tasas/crear', [
            'error' => $error
        ], 'Registrar tasa de cambio');
    }
}
