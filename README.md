# Sistema de Nómina — Base de Datos `dbnomina1` (Arquitectura MVC)

Sistema web en PHP + MySQL desarrollado para XAMPP bajo una arquitectura **MVC (Modelo-Vista-Controlador)** ligera, basada en el patrón *Front Controller*. 

Gestiona el ciclo completo de nómina laboral: empleados, cargos, permisos con aprobación, tasa de cambio oficial BCV, periodos quincenales/semanales/mensuales, cálculo detallado de asignaciones y deducciones de ley venezolana (S.S.O, R.P.E, L.P.H), exportación de nóminas a Excel, recibos individuales imprimibles, control de acceso basado en roles (RBAC) y personalización visual dinámica de la interfaz de usuario.

---

## 📁 Estructura Detallada del Proyecto

```text
Sistema-nomina-new/
├── index.php                 # Front Controller y Enrutador único del sistema
├── README.md                 # Documentación técnica general
├── docs/                     # Documentación de diseño y arquitectura
│   └── diagramas/            # Diagramas UML en formato nativo .drawio y Mermaid .mmd
├── app/                      # Código fuente de la aplicación MVC
│   ├── controllers/          # Controladores (Lógica de flujo y peticiones HTTP)
│   ├── models/               # Modelos (Consultas SQL, cálculos y reglas de negocio)
│   └── views/                # Vistas (Plantillas de interfaz gráfica en HTML/PHP)
├── assets/                   # Recursos estáticos (CSS, JavaScript, imágenes)
│   ├── css/                  # Hojas de estilo modular (style, perfil, usuarios)
│   └── js/                   # Scripts del cliente e interactividad UI
├── config/                   # Configuración del entorno (Conexión PDO MySQL)
├── core/                     # Núcleo del framework MVC y utilidades de seguridad
└── database/                 # Scripts SQL para importación y migraciones
```

---

## 🏛️ Componentes de la Arquitectura

### 🔹 1. Front Controller (`index.php`)
Punto de entrada único a la aplicación. Inicializa sesiones seguras, carga las dependencias del núcleo, define la tabla de rutas permitidas y despacha las peticiones hacia el controlador correspondiente usando el parámetro `?r=ruta/metodo`.

### 🔹 2. Controladores (`app/controllers/`)
* **`AuthController.php`**: Autenticación, inicio y cierre de sesión seguro, control de intentos.
* **`DashboardController.php`**: Panel principal con métricas globales, totales de empleados, estado de nóminas e indicadores.
* **`EmpleadoController.php`**: CRUD de empleados (registro, edición, fichas detalladas y activación/inactivación).
* **`NominaController.php`**: Generación de nómina por periodo, cálculo de asignaciones y deducciones, ajuste de novedades, emisión de recibos de pago y exportación a Excel.
* **`PeriodoController.php`**: Apertura, consulta y cierre de periodos de pago.
* **`PermisoController.php`**: Solicitud de permisos laborales, flujo de aprobación/rechazo y control de estado.
* **`TasaController.php`**: Registro y consulta del historial de la tasa de cambio oficial del BCV.
* **`PerfilController.php`**: Gestión del perfil del usuario autenticado: cambio de avatar/foto, actualización de contraseña y preferencias de colores/tema.
* **`UsuarioAdminController.php`**: Módulo exclusivo para Administradores. CRUD de usuarios, asignación de roles, activación/desactivación y vinculación opcional a empleados activos.

### 🔹 3. Modelos (`app/models/`)
* **`UsuarioModel.php`**: Consultas y operaciones de la tabla `tb_usuario`, verificación de credenciales, hash `bcrypt`, guardado de preferencias en JSON y listado de operadores/empleados disponibles.
* **`EmpleadoModel.php`**: Operaciones sobre `tb_empleado`, `tb_cargo` y asignación de cargos vigentes.
* **`NominaModel.php`**: Motor matemático de fórmulas laborales (S.S.O, R.P.E, L.P.H, días laborados, horas extra), cálculo de totales y almacenamiento en base de datos.
* **`PeriodoModel.php`**: Gestión de registros en `tb_periodo_nomina`.
* **`PermisoModel.php`**: Registro y control de estados en `tb_permiso_empleado`.
* **`TasaModel.php`**: Gestión y consulta de tasas de cambio en `tb_tasa_cambio`.

### 🔹 4. Vistas (`app/views/`)
Organizadas por módulo funcional:
* `layout/`: Plantillas globales compartidas (`header.php`, `footer.php`) con navegación adaptativa según el rol.
* `auth/`: Pantalla de inicio de sesión con estética moderna (`login.php`).
* `dashboard/`: Vista principal con tarjetas de métricas (`inicio.php`).
* `empleados/`: Catálogo, registro y edición de empleados (`listar.php`, `crear.php`, `editar.php`).
* `nomina/`: Procesamiento, ajuste de variables, recibo imprimible y detalle (`generar.php`, `detalle.php`, `ver.php`, `recibo.php`).
* `export/`: Plantilla para exportar nóminas en formato Excel (`excel.php`).
* `permisos/`: Gestión y aprobación de permisos (`listar.php`, `solicitar.php`).
* `periodos/`: Control de periodos de pago (`listar.php`, `crear.php`).
* `tasas/`: Formulario y tabla de tasas de cambio (`listar.php`, `crear.php`).
* `perfil/`: Gestión de cuenta, avatar unificado y personalización visual en vivo (`index.php`).
* `usuarios/`: Panel administrativo de usuarios del sistema (`index.php`).

### 🔹 5. Núcleo del Framework (`core/`)
* **`Database.php`**: Singleton para gestionar la conexión a MySQL mediante PDO de forma optimizada.
* **`Controller.php`**: Controlador base con métodos para renderizado de vistas, redirección, verificación de autenticación (`requireLogin`) y middleware de autorización por rol (`requireRole`).
* **`Model.php`**: Modelo base que expone la instancia PDO `$this->db`.
* **`Router.php`**: Enrutador ligero que mapea rutas limpias a controladores y métodos.
* **`Security.php`**: Módulo integral de seguridad con tokens CSRF, saneamiento de entradas y cabeceras HTTP.

---

## 👥 Control de Acceso Basado en Roles (RBAC)

El sistema implementa una jerarquía de **3 roles de usuario**, controlada en el backend mediante el método `$this->requireRole([...])`:

| Rol | Privilegios | Acceso Operativo | Módulo de Usuarios |
|---|---|---|---|
| 🔴 **Administrador** | **Acceso Total** | Crear, editar y procesar todos los módulos operativos y de configuración. | ✅ Acceso Exclusivo (CRUD completo de usuarios, reseteo de claves y asignación de roles). |
| 🟡 **Operador** | **Gestión Operativa** | CRUD de empleados, generación de nóminas, gestión de permisos, periodos y tasas de cambio. | ❌ Oculto y bloqueado (403 Forbidden). |
| 🟢 **Personal de Apoyo** | **Solo Lectura** | Consulta de fichas de empleados, consulta de periodos/permisos, visualización de nóminas y exportación a Excel. | ❌ Oculto y bloqueado (403 Forbidden). |

> [!NOTE]
> Todos los roles tienen acceso a su módulo personal de **Mi Perfil** para modificar su foto, cambiar su contraseña y personalizar los colores/tema de su entorno de trabajo.

---

## 🎨 Módulo de Personalización Visual y UX/UI

* **Avatar Unificado:** Subida de imagen mediante dropzone o selector de archivos con previsualización inmediata.
* **Previsualización de Colores en Vivo:** Permite interactuar con los selectores de color primario y secundario observando el cambio en vivo en toda la interfaz, requiriendo pulsar **Guardar Preferencias** para persistir el cambio en base de datos.
* **Temas Claro y Oscuro:** Soporte para paletas claras y oscuras almacenadas en formato JSON en `tb_usuario.preferencias`.

---

## 📊 Diagramas de Arquitectura y UML (`docs/diagramas/`)

La carpeta [`docs/diagramas/`](docs/diagramas/) contiene la documentación visual completa del sistema en **dos formatos** (nativos `.drawio` para Diagrams.net y código fuente `.mmd` para Mermaid):

1. **`01_jerarquia_roles`**: Jerarquía de los 3 roles y herencia de privilegios.
2. **`02_casos_de_uso`**: Diagrama de casos de uso con actores a ambos lados (Admin/Operador a la izquierda, Apoyo a la derecha) e inclusiones de casos de uso.
3. **`03_clases_bd`**: Modelo entidad-relación de `tb_rol`, `tb_usuario` y `tb_empleado`.
4. **`04_matriz_permisos`**: Matriz comparativa de permisos módulo por módulo.
5. **`05_flujo_autorizacion`**: Diagrama de secuencia del middleware `requireRole` (200 / 302 / 403).
6. **`06_flujo_crear_usuario`**: Diagrama de secuencia del flujo de creación y validación de usuarios con CSRF y bcrypt.

---

## 📐 Fórmulas de Cálculo Laboral (Ley Orgánica del Trabajo / Venezuela)

* **Sueldo Diario:**
  $$\text{sueldo\_diario} = \frac{\text{sueldo\_divisa}}{30}$$
* **Monto Días Trabajados:**
  $$\text{monto\_dias\_trabajados} = \text{sueldo\_diario} \times \text{dias\_trabajados}$$
* **Total Asignaciones:**
  $$\text{total\_asignaciones} = \text{monto\_dias\_trabajados} + \text{horas\_extra} + \text{bono\_nocturno} + \text{feriados} + \text{otras\_asignaciones}$$
* **Seguro Social Obligatorio (S.S.O - 4%):**
  $$\text{S.S.O} = 0.04 \times \text{salario\_minimo} \times \left(\frac{\text{semanas\_ivss}}{4.333333}\right)$$
* **Régimen Prestacional de Empleo (R.P.E - 0.5%):**
  $$\text{R.P.E} = 0.005 \times \text{salario\_minimo} \times \left(\frac{\text{semanas\_ivss}}{4.333333}\right)$$
* **Ley de Política Habitacional (L.P.H - 1%):**
  $$\text{L.P.H} = 0.01 \times \text{monto\_dias\_trabajados}$$
* **Total Deducciones:**
  $$\text{total\_deducciones} = \text{S.S.O} + \text{R.P.E} + \text{L.P.H} + \text{prestamo} + \text{inasistencia} + \text{islr}$$
* **Neto a Cobrar:**
  $$\text{neto\_pagar} = \text{total\_asignaciones} - \text{total\_deducciones}$$

---

## 🚀 Puesta en Marcha Local (XAMPP)

1. **Clonar o copiar el proyecto** dentro del directorio de publicación:
   ```text
   C:\xampp\htdocs\Sistema-nomina-new\
   ```
2. **Iniciar servicios:** Iniciar Apache y MySQL desde el *XAMPP Control Panel*.
3. **Importar la Base de Datos:**
   * Abrir [phpMyAdmin](http://localhost/phpmyadmin/).
   * Crear la base de datos `dbnomina1`.
   * Importar el archivo [`database/dbnomina1.sql`](database/dbnomina1.sql).
4. **Acceder a la aplicación:**
   * Navegador: `http://localhost/Sistema-nomina-new/`
