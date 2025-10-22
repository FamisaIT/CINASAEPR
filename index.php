<?php
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/session.php';

$pageTitle = 'Catálogo Maestro de Clientes';
require_once __DIR__ . '/app/views/header.php';
?>

<section class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
    <article class="group rounded-3xl border border-corporate-500/20 bg-gradient-to-br from-corporate-800/70 via-corporate-600/60 to-corporate-900/80 p-7 shadow-frosted transition-all duration-500 hover:-translate-y-1 hover:shadow-[0_35px_120px_-25px_rgba(45,212,191,0.55)] xl:col-span-1">
        <div class="flex items-start justify-between gap-6">
            <div class="space-y-4">
                <p class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.35em] text-white/70">
                    <span class="h-1 w-1 rounded-full bg-white/70"></span>
                    Total de Clientes
                </p>
                <h3 id="totalClientes" class="text-5xl font-semibold leading-none tracking-tight text-white drop-shadow-md">-</h3>
                <p class="max-w-xs text-sm text-white/70">
                    Visualiza en tiempo real la cartera activa, suspensa o bloqueada para decisiones estratégicas inmediatas.
                </p>
            </div>
            <div class="relative flex h-20 w-20 items-center justify-center rounded-3xl bg-white/10 shadow-lg shadow-corporate-900/40 transition duration-500 group-hover:scale-105">
                <span class="absolute inset-0 animate-pulse rounded-3xl bg-corporate-400/25 blur-xl"></span>
                <i class="fas fa-chart-line text-3xl text-white drop-shadow-lg"></i>
            </div>
        </div>
        <dl class="mt-6 grid grid-cols-2 gap-4 text-sm text-white/80 md:gap-6">
            <div class="rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-center backdrop-blur-xs">
                <dt class="font-semibold uppercase tracking-[0.28em] text-xs text-white/60">Insights</dt>
                <dd class="mt-2 text-base font-semibold">Instantáneos</dd>
            </div>
            <div class="rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-center backdrop-blur-xs">
                <dt class="font-semibold uppercase tracking-[0.28em] text-xs text-white/60">Cobertura</dt>
                <dd class="mt-2 text-base font-semibold">Global</dd>
            </div>
        </dl>
    </article>
</section>

<section class="mt-10 rounded-3xl border border-white/10 bg-white/5 px-6 py-8 shadow-frosted backdrop-blur">
    <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div class="flex items-center gap-3 text-lg font-semibold text-white">
            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-corporate-400/40 to-corporate-600/60 text-2xl text-white shadow-lg">
                <i class="fas fa-filter"></i>
            </span>
            <div>
                <h2 class="text-lg font-semibold tracking-tight">Filtros de Búsqueda</h2>
                <p class="text-sm text-slate-300/80">Refina tus resultados por estatus, vendedor o región en segundos.</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-3 text-xs uppercase tracking-[0.35em] text-slate-400">
            <span class="rounded-full border border-white/10 px-3 py-1">Operaciones precisas</span>
            <span class="rounded-full border border-white/10 px-3 py-1">Estrategia comercial</span>
        </div>
    </div>

    <div class="mt-6 grid gap-5 lg:grid-cols-12">
        <div class="lg:col-span-5">
            <label for="buscar" class="block text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Buscar</label>
            <input type="text" id="buscar" placeholder="Razón social, RFC o contacto..." class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-slate-100 shadow-inner shadow-black/40 transition focus:border-corporate-400/80 focus:ring-4 focus:ring-corporate-500/30" />
        </div>
        <div class="lg:col-span-2">
            <label for="filtro_estatus" class="block text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Estatus</label>
            <select id="filtro_estatus" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-slate-100 shadow-inner shadow-black/40 transition focus:border-corporate-400/80 focus:ring-4 focus:ring-corporate-500/30">
                <option value="">Todos</option>
                <option value="activo">Activo</option>
                <option value="suspendido">Suspendido</option>
                <option value="bloqueado">Bloqueado</option>
            </select>
        </div>
        <div class="lg:col-span-3">
            <label for="filtro_vendedor" class="block text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Vendedor</label>
            <select id="filtro_vendedor" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-slate-100 shadow-inner shadow-black/40 transition focus:border-corporate-400/80 focus:ring-4 focus:ring-corporate-500/30">
                <option value="">Todos</option>
            </select>
        </div>
        <div class="lg:col-span-2">
            <label for="filtro_pais" class="block text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">País</label>
            <select id="filtro_pais" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-slate-100 shadow-inner shadow-black/40 transition focus:border-corporate-400/80 focus:ring-4 focus:ring-corporate-500/30">
                <option value="">Todos</option>
            </select>
        </div>
        <div class="flex items-end gap-3 lg:col-span-12 xl:col-span-12">
            <button id="btnBuscar" title="Buscar" class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-corporate-500 via-sky-500 to-violet-600 px-5 py-3 text-sm font-semibold uppercase tracking-[0.28em] text-white shadow-lg shadow-sky-900/30 transition duration-300 hover:scale-[1.02] hover:from-corporate-400 hover:via-sky-400 hover:to-violet-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-corporate-300 sm:w-auto">
                <i class="fas fa-search text-base"></i>
                Buscar
            </button>
            <button id="btnLimpiarFiltros" class="flex w-full items-center justify-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-5 py-3 text-sm font-semibold uppercase tracking-[0.28em] text-slate-200 transition duration-300 hover:-translate-y-0.5 hover:border-white/30 hover:bg-white/10 sm:w-auto">
                <i class="fas fa-eraser text-base"></i>
                Limpiar filtros
            </button>
        </div>
    </div>
</section>

<section class="mt-10 space-y-6">
    <article class="rounded-3xl border border-white/10 bg-slate-950/60 shadow-2xl shadow-black/50 backdrop-blur">
        <header class="flex flex-col gap-4 border-b border-white/10 px-6 py-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-corporate-400/30 via-corporate-500/20 to-corporate-700/40 text-xl text-white shadow-lg">
                    <i class="fas fa-list-ul"></i>
                </span>
                <div>
                    <h2 class="text-lg font-semibold tracking-tight text-white">Listado de Clientes</h2>
                    <p class="text-sm text-slate-400">Gestiona, edita y exporta clientes desde un panel sofisticado.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button id="btnExportarCSV" class="group inline-flex items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-5 py-3 text-sm font-semibold uppercase tracking-[0.28em] text-slate-200 transition duration-300 hover:-translate-y-0.5 hover:border-corporate-300 hover:bg-white/10">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5 text-lg text-corporate-200 transition duration-300 group-hover:bg-corporate-400/30 group-hover:text-white">
                        <i class="fas fa-file-csv"></i>
                    </span>
                    Exportar CSV
                </button>
                <button id="btnNuevoCliente" class="group inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-corporate-500 via-cyan-500 to-emerald-500 px-5 py-3 text-sm font-semibold uppercase tracking-[0.28em] text-white shadow-lg shadow-emerald-500/25 transition duration-300 hover:scale-[1.02] hover:from-corporate-400 hover:via-cyan-400 hover:to-emerald-400">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 text-lg text-white transition duration-300 group-hover:bg-white/20">
                        <i class="fas fa-plus"></i>
                    </span>
                    Nuevo Cliente
                </button>
            </div>
        </header>

        <div class="overflow-hidden px-4 pb-2">
            <div class="table-container overflow-hidden rounded-2xl border border-white/5">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300">
                        <thead class="bg-white/5 text-xs font-semibold uppercase tracking-[0.28em] text-slate-300">
                            <tr>
                                <th scope="col" class="sortable whitespace-nowrap px-5 py-4" data-column="id">ID</th>
                                <th scope="col" class="sortable whitespace-nowrap px-5 py-4" data-column="razon_social">Razón Social</th>
                                <th scope="col" class="sortable whitespace-nowrap px-5 py-4" data-column="rfc">RFC</th>
                                <th scope="col" class="whitespace-nowrap px-5 py-4">Contacto</th>
                                <th scope="col" class="whitespace-nowrap px-5 py-4">Teléfono</th>
                                <th scope="col" class="whitespace-nowrap px-5 py-4">Correo</th>
                                <th scope="col" class="sortable whitespace-nowrap px-5 py-4" data-column="estatus">Estatus</th>
                                <th scope="col" class="sortable whitespace-nowrap px-5 py-4" data-column="vendedor_asignado">Vendedor</th>
                                <th scope="col" class="whitespace-nowrap px-5 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaClientes" class="divide-y divide-white/5">
                            <tr>
                                <td colspan="9" class="px-6 py-10">
                                    <div class="flex flex-col items-center justify-center gap-4 text-slate-400">
                                        <span class="loading-spinner h-12 w-12"></span>
                                        <span class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Cargando clientes...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <footer class="border-t border-white/10 px-6 py-5">
            <div class="flex justify-center">
                <div id="paginacion" class="flex items-center justify-center"></div>
            </div>
        </footer>
    </article>
</section>

<?php require_once __DIR__ . '/app/views/modal_cliente.php'; ?>
<?php require_once __DIR__ . '/app/views/footer.php'; ?>
