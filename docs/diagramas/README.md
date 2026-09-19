# Diagramas UML — Sistema de Roles y Permisos

Esta carpeta contiene los diagramas del sistema de gestión de usuarios y roles del sistema de nómina.

## Archivos

| Archivo | Descripción |
|---|---|
| `01_jerarquia_roles.mmd` | Jerarquía de los 3 roles del sistema |
| `02_casos_de_uso.mmd` | Diagrama de casos de uso por actor y módulo |
| `03_clases_bd.mmd` | Diagrama de clases (estructura de BD) |
| `04_matriz_permisos.mmd` | Matriz visual de permisos por rol |
| `05_flujo_autorizacion.mmd` | Secuencia del middleware de autorización |
| `06_flujo_crear_usuario.mmd` | Secuencia de creación de usuario |

## Cómo importar a Draw.io

1. Abre [draw.io](https://app.diagrams.net/)
2. Ve a **Extras → Mermaid** (o **Insert → Advanced → Mermaid**)
3. Pega el contenido del archivo `.mmd`
4. Click **Insert** — el diagrama se renderizará como figura editable

> **Alternativa online:** Pega el contenido en [mermaid.live](https://mermaid.live) para previsualizar y exportar como SVG/PNG.

## Roles del Sistema

- **🔴 Administrador** — Acceso total, gestión de usuarios
- **🟡 Operador** — Gestión operativa (empleados, nóminas, permisos)
- **🟢 Personal de Apoyo** — Solo lectura y exportación
