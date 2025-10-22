# 🚀 Inicio Rápido - Catálogo de Clientes

Esta guía te permite tener el sistema funcionando en **menos de 5 minutos**.

## ⚡ Instalación Express (5 Minutos)

### 1️⃣ Copiar Archivos (30 segundos)

```bash
# Copia la carpeta al servidor web
cp -r catalogo_clientes /tu/servidor/web/
```

### 2️⃣ Crear Base de Datos (1 minuto)

**Opción A - Línea de comandos:**
```bash
mysql -u root -p -e "CREATE DATABASE clientes_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p clientes_db < database/clientes.sql
```

**Opción B - phpMyAdmin:**
1. Ir a http://localhost/phpmyadmin
2. Crear base de datos: `clientes_db`
3. Importar: `database/clientes.sql`

### 3️⃣ Configurar Conexión (1 minuto)

Editar `app/config/database.php`:

```php
$db_host = "localhost";
$db_user = "root";        // ← Tu usuario
$db_pass = "";            // ← Tu contraseña
$db_name = "clientes_db";
```

### 4️⃣ Verificar (30 segundos)

Visita: `http://localhost/catalogo_clientes/test_connection.php`

✅ Si todo está verde, ¡listo!

### 5️⃣ Usar el Sistema (∞)

Visita: `http://localhost/catalogo_clientes/`

---

## 🎯 Primeros Pasos

### Ver Clientes Existentes
El sistema incluye 5 clientes de ejemplo. Explóralos primero.

### Crear un Nuevo Cliente
1. Clic en **"Nuevo Cliente"**
2. Llenar el formulario (solo Razón Social y RFC son obligatorios)
3. Guardar

### Buscar Clientes
- Usa la barra de búsqueda para filtrar por nombre o RFC
- Aplica filtros por estatus, vendedor o país

### Exportar Datos
- **CSV**: Botón verde "Exportar CSV" (todo el listado)
- **PDF**: Icono PDF en cada cliente (ficha individual)

---

## 🔧 Configuración Avanzada Rápida

### Cambiar Puerto MySQL
```php
// En app/config/database.php
$db_host = "localhost:3307";  // Puerto 3307
```

### Cambiar Registros por Página
```javascript
// En app/assets/app.js, línea 3
const limite = 50;  // De 20 a 50
```

### Personalizar Colores
```css
/* En app/assets/style.css, línea 2 */
--primary-color: #dc3545;  /* Rojo en vez de azul */
```

---

## 🐛 Solución Rápida de Problemas

| Problema | Solución Rápida |
|----------|----------------|
| "Error de conexión" | Verifica credenciales en `database.php` |
| "Código PHP visible" | Accede vía `http://` no `file://` |
| "404 en botones" | Verifica la ruta de instalación |
| "No hay clientes" | Importa `database/clientes.sql` |

---

## 📱 Atajos Útiles

- **Crear cliente**: Botón azul arriba a la derecha
- **Editar**: Icono amarillo del lápiz
- **Ver detalle**: Icono azul del ojo
- **Exportar PDF**: Icono verde del PDF
- **Bloquear**: Icono rojo de prohibido
- **Buscar**: Escribir y Enter (o botón de búsqueda)
- **Limpiar filtros**: Botón gris "Limpiar Filtros"

---

## 🎓 Tips para Nuevos Usuarios

### Campos Obligatorios
Solo estos 2 son obligatorios:
- ✅ Razón Social
- ✅ RFC

El resto son opcionales pero recomendados.

### RFC Válido
Formato correcto:
- Personas Morales: `ABC123456XYZ` (12 caracteres)
- Personas Físicas: `ABCD123456XYZ` (13 caracteres)

### Estatus del Cliente
- **Activo**: Cliente normal, puede hacer pedidos
- **Suspendido**: Temporalmente inactivo
- **Bloqueado**: No puede hacer más operaciones

### Días de Crédito
Valores permitidos: 0, 15, 30, 45, 60, 90
- **0** = Contado (pago inmediato)
- **30** = 30 días de crédito (lo más común)

---

## 📚 Recursos Adicionales

- **Documentación completa**: Ver `README.md`
- **Guía de instalación**: Ver `INSTALL.txt`
- **Historial de cambios**: Ver `CHANGELOG.md`
- **Verificar instalación**: `test_connection.php`
- **Ver PHP info**: `phpinfo.php`

---

## 🆘 ¿Necesitas Ayuda?

1. Revisa el archivo `README.md` (más detallado)
2. Ejecuta `test_connection.php` para diagnosticar
3. Revisa `phpinfo.php` para ver configuración PHP
4. Verifica logs de PHP y MySQL

---

## 🔐 Seguridad

⚠️ **Antes de producción:**

1. Eliminar archivos de prueba:
   ```bash
   rm test_connection.php
   rm phpinfo.php
   ```

2. Cambiar credenciales de base de datos
3. Implementar sistema de login real
4. Configurar HTTPS
5. Revisar permisos de archivos

---

## ✅ Checklist de Instalación

- [ ] Archivos copiados al servidor web
- [ ] Base de datos creada
- [ ] Archivo SQL importado
- [ ] Credenciales configuradas en `database.php`
- [ ] `test_connection.php` muestra todo verde
- [ ] Sistema carga correctamente
- [ ] Se pueden ver los 5 clientes de ejemplo
- [ ] Se puede crear un nuevo cliente
- [ ] Exportación CSV funciona
- [ ] Exportación PDF funciona
- [ ] (Producción) Archivos de prueba eliminados

---

**¡Sistema listo en 5 minutos!** ⚡

¿Problemas? Revisa `README.md` para documentación completa.
