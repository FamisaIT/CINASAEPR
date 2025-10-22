# Catálogo Maestro de Clientes (PHP + MySQL)

Sistema empresarial completo para la gestión de catálogo de clientes con funcionalidades CRUD, filtros avanzados, exportación a CSV y PDF.

## 📦 Características

- ✅ **CRUD Completo**: Alta, baja, modificación y consulta de clientes
- 🔍 **Filtros Avanzados**: Búsqueda por nombre, RFC, vendedor, país o estatus
- 📊 **Exportación**: CSV del listado completo y PDF individual por cliente
- 🎨 **Diseño Empresarial**: Interfaz moderna con Bootstrap 5
- 🔒 **Seguridad**: Consultas preparadas y sanitización de datos
- 📄 **Paginación**: Sistema eficiente con 20 registros por página
- 🔄 **Ordenamiento**: Columnas ordenables (ASC/DESC)
- ✔️ **Validaciones**: Frontend y backend completas

## 🚀 Instalación

### Requisitos Previos

- PHP 8.1 o superior
- MySQL 8.0 o superior
- Servidor web (Apache, Nginx) o XAMPP/WAMP/LAMP

### Pasos de Instalación

1. **Copiar archivos**
   ```bash
   # Copiar la carpeta completa a tu servidor web
   # Ejemplo para XAMPP:
   cp -r catalogo_clientes /Applications/XAMPP/htdocs/
   # O para Apache:
   cp -r catalogo_clientes /var/www/html/
   ```

2. **Crear base de datos**
   ```sql
   CREATE DATABASE clientes_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Importar estructura**
   ```bash
   mysql -u root -p clientes_db < database/clientes.sql
   ```
   
   O desde phpMyAdmin:
   - Selecciona la base de datos `clientes_db`
   - Ve a la pestaña "Importar"
   - Selecciona el archivo `database/clientes.sql`
   - Haz clic en "Continuar"

4. **Configurar conexión**
   
   Edita el archivo `/app/config/database.php` con tus credenciales:
   ```php
   $db_host = "localhost";
   $db_user = "root";           // Tu usuario de MySQL
   $db_pass = "";               // Tu contraseña de MySQL
   $db_name = "clientes_db";    // Nombre de tu base de datos
   ```

5. **Verificar instalación**
   
   Primero, verifica que todo esté configurado correctamente:
   ```
   http://localhost/catalogo_clientes/test_connection.php
   ```
   
   Este script verificará:
   - Versión de PHP
   - Extensiones requeridas
   - Conexión a la base de datos
   - Existencia de la tabla clientes

6. **Acceder al sistema**
   
   Una vez verificado, abre tu navegador y visita:
   ```
   http://localhost/catalogo_clientes/
   ```
   
   ⚠️ **Nota:** Después de verificar la instalación, puedes eliminar el archivo `test_connection.php`

## 📁 Estructura del Proyecto

```
catalogo_clientes/
│
├── app/
│   ├── config/
│   │   ├── database.php          # Configuración de conexión MySQL
│   │   └── session.php           # Manejo de sesiones PHP
│   │
│   ├── controllers/
│   │   ├── clientes_crear.php    # Crear nuevo cliente
│   │   ├── clientes_editar.php   # Editar cliente existente
│   │   ├── clientes_eliminar.php # Bloquear cliente
│   │   ├── clientes_listar.php   # Listar con filtros y paginación
│   │   ├── clientes_detalle.php  # Ver detalle de cliente
│   │   ├── export_csv.php        # Exportar a CSV
│   │   ├── export_pdf.php        # Exportar ficha a PDF
│   │   └── obtener_filtros.php   # Obtener vendedores y países
│   │
│   ├── models/
│   │   └── clientes_model.php    # Modelo de datos (consultas SQL)
│   │
│   ├── views/
│   │   ├── header.php            # Encabezado HTML
│   │   ├── footer.php            # Pie de página HTML
│   │   ├── modal_cliente.php     # Modal formulario de cliente
│   │   └── pdf_cliente.php       # Plantilla PDF
│   │
│   └── assets/
│       ├── style.css             # Estilos personalizados
│       └── app.js                # JavaScript principal
│
├── database/
│   └── clientes.sql              # Script SQL con estructura y datos
│
├── vendor/
│   └── fpdf.php                  # Librería FPDF para PDFs
│
├── index.php                     # Página principal
├── README.md                     # Este archivo
└── .env.example                  # Plantilla de configuración
```

## 🎯 Funcionalidades Principales

### 1. Gestión de Clientes

**Crear Cliente**
- Formulario completo con validaciones
- Campos fiscales (RFC, régimen, uso CFDI)
- Condiciones comerciales (crédito, límite)
- Información de contacto y bancaria

**Editar Cliente**
- Actualización de todos los campos
- Validación de RFC único
- Preservación de datos históricos

**Bloquear Cliente**
- Cambio de estatus a "bloqueado"
- No elimina el registro de la base de datos

**Ver Detalle**
- Vista completa de la información del cliente
- Organizada por secciones

### 2. Búsqueda y Filtros

- **Búsqueda General**: Por razón social, RFC o contacto
- **Filtro por Estatus**: Activo, Suspendido, Bloqueado
- **Filtro por Vendedor**: Vendedores registrados
- **Filtro por País**: Países de clientes

### 3. Exportación

**CSV**
- Exporta todos los clientes filtrados
- Incluye todos los campos
- Compatible con Excel

**PDF**
- Ficha individual de cliente
- Diseño profesional
- Información organizada por secciones

## 🔧 Configuración Avanzada

### Modificar Puerto o Host

Si tu MySQL está en otro puerto o host:

```php
// En app/config/database.php
$db_host = "192.168.1.100:3307";  // IP y puerto personalizado
```

### Cambiar Cantidad de Registros por Página

```javascript
// En app/assets/app.js, línea ~3
const limite = 50;  // Cambiar de 20 a 50 registros
```

### Personalizar Colores

```css
/* En app/assets/style.css */
:root {
    --primary-color: #0d6efd;  /* Cambiar color principal */
    --success-color: #198754;  /* Cambiar color de éxito */
}
```

## 📊 Campos del Catálogo

| Campo | Tipo | Descripción |
|-------|------|-------------|
| **Razón Social** | Texto | Nombre completo de la empresa |
| **RFC** | Texto | Registro Federal de Contribuyentes (único) |
| **Régimen Fiscal** | Texto | Régimen según SAT |
| **Dirección** | Texto | Dirección completa |
| **País** | Texto | País del cliente |
| **Contacto Principal** | Texto | Nombre del responsable |
| **Teléfono** | Texto | Número de contacto |
| **Correo** | Email | Email de contacto |
| **Días de Crédito** | Número | 0, 15, 30, 45, 60, 90 días |
| **Límite de Crédito** | Decimal | Monto máximo de crédito |
| **Condiciones de Pago** | Texto | Forma de pago acordada |
| **Moneda** | Texto | MXN, USD, EUR, etc. |
| **Uso CFDI** | Texto | G01, G03, I04, P01, etc. |
| **Método de Pago** | Texto | PUE o PPD |
| **Forma de Pago** | Texto | 01, 02, 03, 04, 28, 99 |
| **Banco** | Texto | Nombre del banco |
| **Cuenta Bancaria** | Texto | Número de cuenta |
| **Estatus** | Enum | activo, suspendido, bloqueado |
| **Vendedor Asignado** | Texto | Responsable del cliente |

## 🔒 Seguridad

- ✅ **Consultas Preparadas**: PDO para prevenir SQL Injection
- ✅ **Sanitización**: `htmlspecialchars()` en todas las salidas
- ✅ **Validaciones**: Frontend (HTML5) y Backend (PHP)
- ✅ **Sesiones Seguras**: `httponly` y configuración segura
- ✅ **Sin localStorage**: Todos los datos en base de datos

## 🛠️ Solución de Problemas

### Error de Conexión a MySQL

```
Error de conexión a la base de datos: SQLSTATE[HY000] [1045] Access denied
```

**Solución**: Verifica las credenciales en `app/config/database.php`

### La página muestra código PHP

**Problema**: PHP no está instalado o configurado

**Solución**: 
- Asegúrate de acceder vía `http://localhost/` no `file:///`
- Verifica que PHP esté instalado: `php -v`
- Reinicia el servidor web

### No se muestran los clientes

**Problema**: Base de datos no importada

**Solución**: Importa el archivo `database/clientes.sql`

### Error al exportar PDF

**Problema**: FPDF no encontrado

**Solución**: Verifica que exista el archivo `vendor/fpdf.php`

## 🧩 Próximos Módulos (Roadmap)

- [ ] Módulo de Proveedores
- [ ] Módulo de Ventas y Cotizaciones
- [ ] Módulo de Crédito y Cobranza
- [ ] Módulo de Recursos Humanos
- [ ] Dashboard con estadísticas
- [ ] Reportes avanzados
- [ ] API REST para integración

## 📝 Notas Importantes

- Este sistema está diseñado para funcionar **sin Docker ni Git**
- Es **plug-and-play**: solo copiar, configurar DB y usar
- El código está preparado para **ampliarse fácilmente**
- Compatible con PHP 8.1+ y MySQL 8.0+
- Sesiones auto-autenticadas (para producción integrar con login real)

## 👥 Datos de Prueba

El archivo SQL incluye 5 clientes de ejemplo:
- COMERCIALIZADORA DELTA SA DE CV
- GRUPO INDUSTRIAL OMEGA SAB DE CV
- DISTRIBUIDORA BETA Y CIA SC
- EXPORTACIONES GAMMA SA DE CV
- IMPORTACIONES SIGMA SAPI DE CV

## 📞 Soporte

Para reportar problemas o sugerencias, documenta:
1. Versión de PHP (`php -v`)
2. Versión de MySQL (`mysql --version`)
3. Mensaje de error completo
4. Pasos para reproducir el problema

## 📄 Licencia

Sistema empresarial para uso interno. Todos los derechos reservados.

---

**¡Sistema listo para producción!** 🚀

Desarrollado con PHP nativo, MySQL y Bootstrap 5 para máxima compatibilidad y rendimiento.
