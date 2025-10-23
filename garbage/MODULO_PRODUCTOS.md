# Módulo de Productos/Piezas

## Descripción
Módulo completo para la gestión de productos y piezas de fabricación en CINASA. Permite registrar, editar, visualizar y eliminar productos con toda la información técnica necesaria.

## Instalación

1. Ejecutar el script de instalación desde el navegador:
   ```
   http://localhost/CINASA-main/install_productos.php
   ```

2. Una vez instalado, acceder al módulo:
   ```
   http://localhost/CINASA-main/productos.php
   ```

3. **IMPORTANTE**: Eliminar el archivo `install_productos.php` por seguridad después de la instalación.

## Características

### Campos del Producto (Todos Opcionales)

#### Información Básica
- **Número de Item**: Número de item/línea del producto
- **Código de Material**: Código único del material
- **Descripción**: Descripción detallada del material
- **Cantidad de Orden**: Cantidad ordenada
- **Unidad de Medida**: EA, KG, LB, etc.

#### Precios y Cantidades
- **Precio Neto**: Precio unitario neto
- **Precio Por**: Unidad de precio (Per 1, Per 10, etc.)
- **Monto Neto**: Monto total neto
- **Fecha de Entrega**: Fecha programada de entrega

#### Origen y Clasificación
- **País de Origen**: País de fabricación (MX, US, etc.)
- **Código HTS**: Código de clasificación arancelaria
- **Descripción HTS**: Descripción del código HTS

#### Sistema de Calidad
- **Sistema de Calidad**: Sistema de calidad objetivo (J02, ISO9001, etc.)
- **Categoría**: Categoría del producto (Standard Part, Custom, etc.)

#### Información Técnica del Dibujo
- **Número de Dibujo**: Número del plano técnico
- **Versión del Dibujo**: Versión del plano
- **Hoja del Dibujo**: Número de hoja
- **Número ECM**: Número de gestión de cambios de ingeniería
- **Revisión del Material**: Revisión actual del material
- **Número de Cambio**: Número de orden de cambio

#### Componentes
- **Nivel del Componente**: Nivel en la estructura del producto
- **Componente Línea**: Línea de componente
- **Documento de Referencia**: Referencia a documentos técnicos

#### Otros
- **Notas**: Notas adicionales y observaciones
- **Estatus**: Activo, Inactivo o Descontinuado

## Funcionalidades

### Listado de Productos
- Visualización en tabla con paginación
- Ordenamiento por columnas
- Búsqueda por código, descripción o número de dibujo
- Filtros por:
  - Estatus
  - País de origen
  - Categoría

### Gestión de Productos
- **Crear**: Agregar nuevos productos con todos los campos opcionales
- **Editar**: Modificar información de productos existentes
- **Ver Detalle**: Visualizar información completa del producto
- **Eliminar**: Marcar producto como descontinuado (eliminación lógica)

### Exportación
- Datos preparados para futuras exportaciones a PDF y Excel

## Estructura de Archivos

```
CINASA-main/
├── productos.php                           # Página principal del módulo
├── install_productos.php                   # Script de instalación
├── app/
│   ├── models/
│   │   └── productos_model.php            # Modelo de datos
│   ├── controllers/
│   │   ├── productos_listar.php           # Listado con filtros
│   │   ├── productos_crear.php            # Crear producto
│   │   ├── productos_editar.php           # Editar producto
│   │   ├── productos_eliminar.php         # Eliminar producto
│   │   └── productos_detalle.php          # Detalle del producto
│   ├── views/
│   │   └── modal_producto.php             # Modal de formulario
│   └── assets/
│       └── productos.js                    # JavaScript del módulo
└── database/
    └── productos.sql                       # Script SQL de la tabla
```

## Uso

### Crear Producto
1. Click en botón "Nuevo Producto"
2. Llenar los campos deseados (todos son opcionales)
3. Click en "Guardar Producto"

### Editar Producto
1. Click en el ícono de editar (lápiz) en la fila del producto
2. Modificar los campos necesarios
3. Click en "Actualizar Producto"

### Filtrar Productos
1. Usar los campos de filtro en la parte superior
2. Click en "Buscar"
3. Para limpiar filtros, click en "Limpiar"

### Eliminar Producto
1. Click en el ícono de eliminar (papelera) en la fila del producto
2. Confirmar la acción
3. El producto será marcado como "Descontinuado"

## Validaciones

- Validación de código de material duplicado (si se proporciona)
- Todos los campos son opcionales para máxima flexibilidad
- Formato de números decimales para precios y cantidades
- Formato de fecha para fecha de entrega

## Base de Datos

La tabla `productos` contiene:
- 26 campos de datos
- Índices en campos clave para optimizar búsquedas
- Soporte completo para UTF-8
- Timestamps automáticos para auditoría

## Mejoras Futuras Sugeridas

- Exportación a PDF con formato técnico
- Exportación a Excel con todos los campos
- Importación masiva desde Excel
- Gestión de imágenes de productos
- Historial de cambios
- Vinculación con clientes/órdenes

## Soporte

Para soporte o dudas sobre el módulo, consultar la documentación del sistema CINASA.
