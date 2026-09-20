<?php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Audit.php';
require_once __DIR__ . '/../models/AuditoriaModel.php';

class AuditoriaController extends Controller {
    private AuditoriaModel $auditoriaModel;

    public function __construct() {
        $this->auditoriaModel = new AuditoriaModel();
    }

    public function index(): void {
        $this->requireLogin();

        $userRole = $_SESSION['user_rol'] ?? 'Empleado';
        $isAdmin = in_array($userRole, ['Administrador', 'Supervisor'], true);
        $currentUserId = (int)($_SESSION['user_id'] ?? 0);

        // Parámetros de filtrado
        $filtros = [
            'q' => $_GET['q'] ?? '',
            'modulo' => $_GET['modulo'] ?? '',
            'accion' => $_GET['accion'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
        ];

        // Si es admin, puede filtrar por usuario; si no, queda forzado a su propio historial
        if ($isAdmin) {
            $filtros['id_usuario'] = !empty($_GET['id_usuario']) ? (int)$_GET['id_usuario'] : null;
        } else {
            $filtros['id_usuario'] = $currentUserId;
        }

        // Paginación y control de tamaño por página
        $allowedLimits = [10, 25, 50, 100];
        $limit = isset($_GET['limit']) && in_array((int)$_GET['limit'], $allowedLimits) ? (int)$_GET['limit'] : 25;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $totalRegistros = $this->auditoriaModel->contarTotal($filtros);
        $totalPaginas = max(1, (int)ceil($totalRegistros / $limit));
        $registros = $this->auditoriaModel->listar($filtros, $limit, $offset);

        $kpis = $this->auditoriaModel->obtenerEstadisticas($isAdmin ? null : $currentUserId);
        $usuariosFiltro = $isAdmin ? $this->auditoriaModel->obtenerUsuariosConActividad() : [];
        $modulos = $this->auditoriaModel->obtenerModulos();

        // Si la lista de módulos está vacía en BD nueva, proveer catálogo por defecto
        if (empty($modulos)) {
            $modulos = ['EMPLEADOS', 'NOMINA', 'PERMISOS', 'USUARIOS', 'TASAS', 'ACCESO', 'PERFIL'];
        }

        $this->render('auditoria/index', [
            'registros' => $registros,
            'filtros' => $filtros,
            'kpis' => $kpis,
            'usuariosFiltro' => $usuariosFiltro,
            'modulos' => $modulos,
            'page' => $page,
            'limit' => $limit,
            'allowedLimits' => $allowedLimits,
            'totalPaginas' => $totalPaginas,
            'totalRegistros' => $totalRegistros,
            'isAdmin' => $isAdmin
        ], 'Historial de Actividades');
    }

    public function detalle(): void {
        $this->requireLogin();

        header('Content-Type: application/json');
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID de auditoría no válido']);
            exit;
        }

        $registro = $this->auditoriaModel->obtenerPorId($id);
        if (!$registro) {
            echo json_encode(['success' => false, 'error' => 'Registro de auditoría no encontrado']);
            exit;
        }

        $userRole = $_SESSION['user_rol'] ?? 'Empleado';
        $isAdmin = in_array($userRole, ['Administrador', 'Supervisor'], true);
        $currentUserId = (int)($_SESSION['user_id'] ?? 0);

        // Validación de privacidad institucional: no admins solo ven su actividad
        if (!$isAdmin && (int)$registro['id_usuario'] !== $currentUserId) {
            echo json_encode(['success' => false, 'error' => 'No tienes autorización para consultar este registro']);
            exit;
        }

        // Parsear detalles_json si existe
        $detallesParsed = null;
        if (!empty($registro['detalles_json'])) {
            $detallesParsed = json_decode($registro['detalles_json'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $detallesParsed = $registro['detalles_json'];
            }
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'id_auditoria' => $registro['id_auditoria'],
                'usuario' => $registro['nombre_usuario'] ?? 'Sistema / Desconocido',
                'correo' => $registro['usuario_correo'] ?? '',
                'rol' => $registro['nombre_rol'] ?? 'N/A',
                'avatar' => $registro['usuario_avatar'] ?? '',
                'modulo' => $registro['modulo'] ?? 'GENERAL',
                'accion' => $registro['accion'],
                'tabla' => $registro['tabla_afectada'] ?? '',
                'id_registro' => $registro['id_registro_afectado'] ?? null,
                'ip' => $registro['ip'] ?? 'Local',
                'fecha_hora' => $registro['fecha_hora'],
                'detalle' => $registro['detalle'] ?? '',
                'detalles_json' => $detallesParsed
            ]
        ]);
        exit;
    }

    public function exportar(): void {
        $this->requireLogin();

        $userRole = $_SESSION['user_rol'] ?? 'Empleado';
        $isAdmin = in_array($userRole, ['Administrador', 'Supervisor'], true);
        $currentUserId = (int)($_SESSION['user_id'] ?? 0);

        $filtros = [
            'q' => $_GET['q'] ?? '',
            'modulo' => $_GET['modulo'] ?? '',
            'accion' => $_GET['accion'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
        ];

        if ($isAdmin) {
            $filtros['id_usuario'] = !empty($_GET['id_usuario']) ? (int)$_GET['id_usuario'] : null;
        } else {
            $filtros['id_usuario'] = $currentUserId;
        }

        // Límite de exportación de hasta 2500 registros
        $registros = $this->auditoriaModel->listar($filtros, 2500, 0);

        Audit::log('HISTORIAL', 'EXPORTAR', 'Exportó el historial de actividades del sistema a CSV');

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="Historial_Actividades_' . date('Y-m-d_His') . '.csv"');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM para apertura correcta en Microsoft Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, ['ID', 'Fecha y Hora', 'Usuario', 'Rol', 'Módulo', 'Acción', 'Tabla', 'ID Registro', 'IP', 'Descripción de la Acción']);

        foreach ($registros as $r) {
            fputcsv($output, [
                $r['id_auditoria'],
                $r['fecha_hora'],
                $r['nombre_usuario'] ?? 'N/A',
                $r['nombre_rol'] ?? 'N/A',
                $r['modulo'] ?? 'GENERAL',
                $r['accion'],
                $r['tabla_afectada'] ?? '',
                $r['id_registro_afectado'] ?? 'N/A',
                $r['ip'] ?? 'Local',
                $r['detalle']
            ]);
        }

        fclose($output);
        exit;
    }
}
