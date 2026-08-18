# Sistema de Nómina — Base de Datos `dbnomina1` (Arquitectura MVC)

Sistema web en PHP + MySQL desarrollado para XAMPP bajo una arquitectura **MVC (Modelo-Vista-Controlador)** ligera, basada en el patrón *Front Controller*. Gestiona usuarios/roles, empleados, cargos, permisos con aprobación, tasa de cambio BCV, periodos y nómina detallada por conceptos.

---

## 📁 Explicación Detallada de la Estructura de Carpetas

```text
nomina_dbnomina1/
├── index.php                 # Front Controller y Enrutador único del sistema
├── README.md                 # Documentación general y técnica
├── app/                      # Código fuente de la aplicación MVC
│   ├── controllers/          # Controladores (Lógica de flujo y manejo de peticiones HTTP)
│   ├── models/               # Modelos (Consultas SQL, cálculos y reglas de negocio)
│   └── views/                # Vistas (Plantillas de interfaz gráfica en HTML/PHP)
├── assets/                   # Archivos estáticos y recursos visuales (CSS, JS, imágenes)
├── config/                   # Archivos de configuración (Conexión a MySQL)
├── core/                     # Núcleo del framework MVC (Enrutador, Singleton DB, Clases base)
└── database/                 # Scripts SQL para importación y parches de la base de datos
```

### 🔹 1. `index.php` (Raíz)
Es el **Front Controller** y punto de entrada único a la aplicación. Recibe todas las peticiones HTTP (ej: `index.php?r=empleados/crear`), inicializa la sesión, carga las clases del núcleo y despacha la petición al controlador correspondiente.

### 🔹 2. `app/` (Aplicación Principal)
Contiene la lógica de la aplicación organizada estrictamente bajo el patrón MVC:

* **`app/controllers/`**: Maneja la interacción del usuario y la respuesta del servidor:
  * `AuthController.php`: Gestión de inicio de sesión (`login`) y cierre de sesión (`logout`).
  * `DashboardController.php`: Métricas e indicadores del panel de bienvenida.
  * `EmpleadoController.php`: Gestión CRUD de empleados (listado, creación, edición y activación/inactivación).
  * `PermisoController.php`: Gestión de solicitudes de permisos laborales y aprobación/rechazo.
  * `TasaController.php`: Registro y consulta de la tasa de cambio BCV del día.
  * `PeriodoController.php`: Creación y consulta de periodos de nómina (semanal, quincenal, mensual).
  * `NominaController.php`: Generación de nómina, edición de detalle individual por empleado, recálculo automático, resumen de totales, generación de recibo de pago e impresión/exportación.

* **`app/models/`**: Encapsula las consultas a la base de datos y la lógica de cálculo laboral:
  * `UsuarioModel.php`: Autenticación contra la tabla `tb_usuario`.
  * `EmpleadoModel.php`: Consultas y operaciones de `tb_empleado`, `tb_cargo` y asignación de cargos vigentes.
  * `PermisoModel.php`: Registro y actualización de estados en `tb_permiso_empleado`.
  * `TasaModel.php`: Historial de tasas en `tb_tasa_cambio`.
  * `PeriodoModel.php`: Registro de periodos en `tb_periodo_nomina`.
  * `NominaModel.php`: Fórmulas matemáticas de deducciones (S.S.O, R.P.E, L.P.H), asignaciones, inserción/actualización de detalles y recibos.

* **`app/views/`**: Plantillas HTML de presentación visual, divididas por módulo:
  * `layout/`: Componentes globales compartidos (`header.php` y `footer.php`).
  * `auth/`: Pantalla de inicio de sesión estilizada estilo macOS (`login.php`).
  * `dashboard/`: Vista de resumen principal (`inicio.php`).
  * `empleados/`: Formularios y tablas de empleados (`listar.php`, `crear.php`, `editar.php`).
  * `permisos/`: Formulario y listado de solicitudes (`listar.php`, `solicitar.php`).
  * `tasas/`: Formulario y tabla de tasas de cambio (`listar.php`, `crear.php`).
  * `periodos/`: Creación y listado de periodos (`listar.php`, `crear.php`).
  * `nomina/`: Pantallas de cálculo, ajuste de días trabajados, totales y recibo printable (`generar.php`, `detalle.php`, `ver.php`, `recibo.php`).
  * `export/`: Plantilla para exportar nóminas en formato Excel (`excel.php`).

### 🔹 3. `assets/` (Recursos Estáticos)
* Contiene `css/style.css` con los estilos globales de la aplicación, layouts de tarjetas, tablas, formularios en cuadrícula, efectos de animación 3D y diseño adaptable.

### 🔹 4. `config/` (Configuración)
* Contiene `db.php` con los parámetros de conexión MySQL (host, puerto, base de datos `dbnomina1`, usuario `root` y contraseña).

### 🔹 5. `core/` (Núcleo del Framework MVC)
* **`Database.php`**: Implementa el patrón Singleton para garantizar una única conexión PDO a la base de datos de manera eficiente.
* **`Controller.php`**: Clase base para todos los controladores. Proporciona métodos para renderizar vistas (`render`), hacer redirecciones (`redirect`) y validar sesión (`requireLogin`).
* **`Model.php`**: Clase base para los modelos, inicializando la propiedad `$db`.
* **`Router.php`**: Enrutador ligero que mapea la variable de ruta `r` hacia la clase controlador y método objetivo.

### 🔹 6. `database/` (Base de Datos)
* `dbnomina1.sql`: Estructura completa y volcado de datos iniciales.
* `patch_tasas.sql`: Parche para actualizar porcentajes por defecto de deducciones (S.S.O 4%, R.P.E 0.5%, L.P.H 1%).

---

## 📐 Fórmulas de Cálculo Laboral

- `sueldo_diario = sueldo_divisa / 30`
- `monto_dias_trabajados = sueldo_diario * dias_trabajados`
- `total_asignaciones = monto_dias_trabajados + horas_extra + bono_nocturno + feriados + otras`
- `S.S.O = 4% * salario_minimo_vigente * (semanas_descontar_ivss / 4.333333)`
- `R.P.E = 0.5% * salario_minimo_vigente * (semanas_descontar_ivss / 4.333333)`
- `L.P.H = 1% * monto_dias_trabajados`
- `total_deducciones = S.S.O + R.P.E + L.P.H + prestamo + inasistencia + islr`
- `neto_pagar = total_asignaciones - total_deducciones`
