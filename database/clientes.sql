-- Base de datos para el Catálogo Maestro de Clientes
-- MySQL 8.0+

-- Crear tabla de clientes
CREATE TABLE IF NOT EXISTS clientes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  razon_social VARCHAR(250) NOT NULL,
  rfc VARCHAR(14) NOT NULL UNIQUE,
  regimen_fiscal VARCHAR(100),
  direccion TEXT,
  pais VARCHAR(100) DEFAULT 'México',
  contacto_principal VARCHAR(150),
  telefono VARCHAR(30),
  correo VARCHAR(150),
  dias_credito TINYINT UNSIGNED DEFAULT 0,
  limite_credito DECIMAL(15,2) DEFAULT 0.00,
  condiciones_pago VARCHAR(100),
  moneda VARCHAR(10) DEFAULT 'MXN',
  uso_cfdi VARCHAR(10),
  metodo_pago VARCHAR(10),
  forma_pago VARCHAR(10),
  banco VARCHAR(150),
  cuenta_bancaria VARCHAR(50),
  estatus ENUM('activo','suspendido','bloqueado') DEFAULT 'activo',
  fecha_alta DATETIME DEFAULT CURRENT_TIMESTAMP,
  fecha_ultima_venta DATETIME DEFAULT NULL,
  vendedor_asignado VARCHAR(100),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_rfc (rfc),
  INDEX idx_estatus (estatus),
  INDEX idx_razon_social (razon_social(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de ejemplo
INSERT INTO clientes (razon_social, rfc, regimen_fiscal, direccion, pais, contacto_principal, telefono, correo, dias_credito, limite_credito, condiciones_pago, moneda, uso_cfdi, metodo_pago, forma_pago, banco, cuenta_bancaria, estatus, vendedor_asignado) VALUES
('COMERCIALIZADORA DELTA SA DE CV', 'CDL980101XYZ', '601 - General de Ley Personas Morales', 'Av. Reforma 123, Col. Centro, Ciudad de México, CP 06000', 'México', 'Juan Pérez López', '5551234567', 'compras@delta.com.mx', 30, 500000.00, 'Transferencia bancaria', 'MXN', 'G03', 'PPD', '03', 'BBVA Bancomer', '012180001234567890', 'activo', 'Carlos Martínez'),
('GRUPO INDUSTRIAL OMEGA SAB DE CV', 'GIO950215ABC', '601 - General de Ley Personas Morales', 'Blvd. Manuel Ávila Camacho 2000, Naucalpan, Estado de México, CP 53000', 'México', 'María González García', '5559876543', 'facturacion@omega.com.mx', 45, 1000000.00, 'Cheque', 'MXN', 'I04', 'PPD', '02', 'Santander', '014020001234567890', 'activo', 'Ana Rodríguez'),
('DISTRIBUIDORA BETA Y CIA SC', 'DBC850320DEF', '605 - Sueldos y Salarios e Ingresos Asimilados a Salarios', 'Calle Morelos 456, Col. Juárez, Guadalajara, Jalisco, CP 44100', 'México', 'Pedro Sánchez Ruiz', '3331234567', 'contacto@beta.com', 15, 250000.00, 'Contado', 'MXN', 'G03', 'PUE', '01', 'Banorte', '072580001234567890', 'activo', 'Carlos Martínez'),
('EXPORTACIONES GAMMA SA DE CV', 'EGM020610GHI', '601 - General de Ley Personas Morales', 'Av. Universidad 789, Col. Del Valle, Monterrey, Nuevo León, CP 64000', 'México', 'Laura Ramírez Torres', '8187654321', 'ventas@gamma.mx', 60, 750000.00, 'Transferencia internacional', 'USD', 'G03', 'PPD', '03', 'HSBC', '021180001234567890', 'activo', 'Ana Rodríguez'),
('IMPORTACIONES SIGMA SAPI DE CV', 'IMS100912JKL', '601 - General de Ley Personas Morales', 'Calle Juárez 321, Col. Centro, Puebla, Puebla, CP 72000', 'México', 'Roberto Flores Méndez', '2221234567', 'importaciones@sigma.com.mx', 30, 350000.00, 'Tarjeta de crédito empresarial', 'MXN', 'I04', 'PPD', '28', 'Scotiabank', '044180001234567890', 'suspendido', 'Carlos Martínez');
