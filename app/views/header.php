<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Catálogo de Clientes'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'Manrope', 'system-ui', 'sans-serif']
                    },
                    colors: {
                        corporate: {
                            50: '#f1f7ff',
                            100: '#dbeafe',
                            200: '#b7d4fb',
                            300: '#8fbaf7',
                            400: '#5a9df1',
                            500: '#2f7de7',
                            600: '#1f5fc4',
                            700: '#1c4fa0',
                            800: '#1a427f',
                            900: '#162f57'
                        }
                    },
                    boxShadow: {
                        'elevated': '0 35px 120px -25px rgba(15, 118, 110, 0.45)',
                        'frosted': '0 20px 80px -25px rgba(30, 64, 175, 0.55)'
                    },
                    backdropBlur: {
                        xs: '3px'
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/app/assets/style.css">
    <script>
        const BASE_URL = '<?php echo BASE_PATH; ?>';
        console.log('BASE_URL configurado como:', BASE_URL);
    </script>
</head>
<body class="bg-slate-950 text-slate-100 antialiased min-h-screen">
    <div class="absolute inset-0 -z-10 overflow-hidden">
        <div class="pointer-events-none hero-mist"></div>
    </div>

    <header class="relative isolate overflow-hidden border-b border-white/10 bg-slate-950/60 backdrop-blur">
        <div class="absolute inset-0 -z-10 opacity-60">
            <div class="hero-gradient"></div>
        </div>
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-8 sm:px-6 lg:px-10 lg:flex-row lg:items-center lg:justify-between">
            <nav class="flex w-full flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <a href="<?php echo BASE_PATH; ?>/index.php" class="group inline-flex w-full max-w-xl items-center gap-4 rounded-3xl border border-white/10 bg-white/5 px-5 py-4 text-left shadow-frosted backdrop-blur-xs transition duration-300 hover:-translate-y-1 hover:border-corporate-300/80 hover:bg-white/10">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-corporate-500/30 via-corporate-400/20 to-corporate-700/40 text-3xl text-corporate-200 shadow-elevated transition duration-300 group-hover:scale-105 group-hover:from-corporate-400/60 group-hover:text-white">
                        <i class="fas fa-users-cog"></i>
                    </span>
                    <span class="flex flex-col">
                        <span class="text-lg font-semibold tracking-tight text-white sm:text-xl">Catálogo Maestro de Clientes</span>
                        <span class="text-sm font-medium text-slate-300/90">Consolida relaciones comerciales con precisión y estilo corporativo</span>
                    </span>
                </a>
                <div class="flex items-center justify-between gap-3 rounded-3xl border border-white/10 bg-white/5 px-4 py-3 shadow-lg shadow-slate-950/50 backdrop-blur-xs lg:w-auto">
                    <a href="<?php echo BASE_PATH; ?>/index.php" class="group inline-flex items-center gap-2 rounded-2xl px-4 py-2 text-sm font-semibold text-slate-100 transition hover:bg-white/10">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5 text-lg text-corporate-200 transition duration-300 group-hover:bg-corporate-500/20 group-hover:text-white">
                            <i class="fas fa-list"></i>
                        </span>
                        <span class="tracking-wide">Listado</span>
                    </a>
                    <span class="inline-flex items-center gap-2 rounded-2xl border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200/90">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5 text-lg text-corporate-300">
                            <i class="fas fa-user-tie"></i>
                        </span>
                        <span class="tracking-wide">Admin</span>
                    </span>
                </div>
            </nav>
        </div>
    </header>

    <main class="relative z-10 mx-auto w-full max-w-7xl px-4 pb-16 pt-10 sm:px-6 lg:px-10">
