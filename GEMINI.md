# Normas del Proyecto - Sistema de Nómina

## Cuentas Maestras Protegidas
- Las cuentas `samu` (ID 1) y `mobile` (ID 2) son los administradores raíz del sistema.
- **Inviolabilidad**: Queda estrictamente prohibido eliminarlas, desactivarlas o degradar sus privilegios.
- **Triggers de Base de Datos**: MySQL tiene activos los disparadores `trg_proteger_usuarios_sistema_delete` y `trg_proteger_usuarios_sistema_update` que impiden consultas SQL directas de eliminación o desactivación.
- **Logo Multitel**: Solo estas dos cuentas pueden usar el preset preinstalado `assets/images/logo_multitel.png`. El resto de usuarios deben subir sus imágenes desde su propio dispositivo.
