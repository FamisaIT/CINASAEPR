<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Catálogo de Clientes'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/app/assets/style.css">
    <script>
        // Base URL para las peticiones AJAX - DEBUG
        const BASE_URL = '<?php echo BASE_PATH; ?>';
        console.log('BASE_URL configurado como:', BASE_URL);
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASE_PATH; ?>/index.php">
                <i class="fas fa-users-cog"></i> Catálogo Maestro de Clientes
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo BASE_PATH; ?>/index.php">
                            <i class="fas fa-list"></i> Listado
                        </a>
                    </li>
                    <li class="nav-item">
                        <span class="nav-link">
                            <i class="fas fa-user"></i> Admin
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container-fluid py-4">
