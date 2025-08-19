<!DOCTYPE html>
<html lang="es" data-theme="auto">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Carga de Débitos</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="./debitos.css" rel="stylesheet" />
</head>

<body>
    <header class="header" role="banner">
        <div class="header-inner">
            <div class="header-left">
                <div class="logo" aria-label="CMG">CMG</div>
                <nav class="main-nav" aria-label="Principal">
                    <ul>
                        <li><a href="#">Liquidación</a></li>
                        <li><a href="#">Liq. Proc. Mens.</a></li>
                        <li><a href="#">Socios</a></li>
                        <li><a href="#">Servicios</a></li>
                        <li class="active"><a href="#" aria-current="page">Débitos</a></li>
                        <li><a href="#">Refacturación</a></li>
                        <li><a href="#">Reintegro No Ded.</a></li>
                        <li><a href="#">Varios</a></li>
                    </ul>
                </nav>
            </div>

            <div class="header-right">
                <button class="theme-toggle" id="themeToggle" type="button" aria-label="Cambiar tema" aria-pressed="false" title="Cambiar tema (T)">
                    <!-- Sun / Moon combined icon (stroke) -->
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 4V2M12 22v-2M4 12H2M22 12h-2M5 5 3.6 3.6M20.4 20.4 19 19M5 19 3.6 20.4M20.4 3.6 19 5"></path>
                        <path d="M12 18a6 6 0 1 1 0-12 6 6 0 0 0 0 12Z"></path>
                    </svg>
                </button>
                <span class="user-name" aria-label="Usuario">Username</span>
            </div>
        </div>
    </header>

    <main class="container" role="main">
        <div class="card">
            <h1 class="card-title">Carga de Débitos</h1>
            <p class="card-subtitle">Completá el formulario y agregá los débitos. Podés buscar por número de orden o por paciente.</p>

            <!-- FORMULARIO -->
            <form class="debitos-form modern" novalidate>
                <!-- ORDEN -->
                <section class="section">
                    <div class="field">
                        <label for="orden" class="label">Orden</label>
                        <div class="input-wrap">
                            <span class="adornment left" aria-hidden="true">#</span>
                            <input id="orden" name="orden" class="input with-left" type="text" inputmode="numeric" pattern="[0-9]*" placeholder="N° de orden" aria-describedby="ordenHelp" autocomplete="off" />
                            <button type="button" class="btn icon" aria-label="Buscar orden" id="buscarOrdenBtn">
                                <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                                    <path d="M10.5 3a7.5 7.5 0 1 1 0 15 7.5 7.5 0 0 1 0-15Zm0 2a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11Zm9.78 14.22-3.33-3.33a1 1 0 0 1 1.41-1.41l3.33 3.33a1 1 0 0 1-1.41 1.41Z" />
                                </svg>
                            </button>
                        </div>
                        <p id="ordenHelp" class="help">Ingresá el número o buscá por paciente.</p>
                    </div>
                </section>

                <!-- IMPORTE / ANTIGÜEDAD -->
                <section class="section grid">
                    <div class="field">
                        <label for="honorarios" class="label">Honorarios</label>
                        <div class="input-wrap">
                            <span class="adornment left" aria-hidden="true">ARS</span>
                            <input id="honorarios" name="honorarios" class="input with-left" type="text" inputmode="decimal" placeholder="0,00" autocomplete="off" />
                        </div>
                    </div>

                    <div class="field">
                        <label for="gastos" class="label">Gastos</label>
                        <div class="input-wrap">
                            <span class="adornment left" aria-hidden="true">ARS</span>
                            <input id="gastos" name="gastos" class="input with-left" type="text" inputmode="decimal" placeholder="0,00" autocomplete="off" />
                        </div>
                    </div>

                    <div class="field">
                        <label for="antiguedad" class="label">Antigüedad</label>
                        <input id="antiguedad" name="antiguedad" class="input" type="text" placeholder="Años / detalle" autocomplete="off" />
                    </div>
                </section>

                <!-- PACIENTE / TIPO / % LIQ -->
                <section class="section grid">
                    <div class="field">
                        <label for="paciente" class="label">Paciente / Descripción</label>
                        <div class="input-wrap">
                            <button type="button" class="btn neutral" title="Corrección rápida (Corr)" id="corrBtn">Corr</button>
                            <input id="paciente" name="paciente" class="input with-prev" type="text" placeholder="Paciente o descripción" autocomplete="off" />
                        </div>
                    </div>

                    <fieldset class="field">
                        <legend class="label">Tipo de movimiento</legend>
                        <div class="radio-row">
                            <label class="radio">
                                <input type="radio" name="tipo" value="debito" checked />
                                <span>Débito</span>
                            </label>
                            <label class="radio">
                                <input type="radio" name="tipo" value="credito" />
                                <span>Crédito</span>
                            </label>
                        </div>
                    </fieldset>

                    <div class="field">
                        <label for="liq" class="label">% Liq.</label>
                        <div class="input-wrap">
                            <input id="liq" name="liq" class="input with-right" type="number" min="0" max="100" step="0.01" value="100" aria-describedby="liqHelp" />
                            <span class="adornment right" aria-hidden="true">%</span>
                        </div>
                        <p id="liqHelp" class="help">Porcentaje de liquidación (0–100).</p>
                    </div>
                </section>

                <!-- ACCIONES -->
                <div class="actions">
                    <button type="reset" class="btn ghost">Limpiar</button>
                    <button type="submit" class="btn primary">Guardar</button>
                </div>
            </form>

            <!-- TABLA -->
            <div class="table-section" role="region" aria-label="Listado de débitos">
                <table>
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Ob. Soc.</th>
                            <th>Socio</th>
                            <th>Paciente</th>
                            <th>Mes/Año</th>
                            <th>Código</th>
                            <th>Cant.</th>
                            <th>Hon.</th>
                            <th>Gast.</th>
                            <th>Antig.</th>
                            <th>%</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaDebitos">
                        <tr>
                            <td>D</td>
                            <td>2082286</td>
                            <td>285 - O.S.S.I.M.R.A.</td>
                            <td>2322 - PRUYAS JUAN MANUEL ...</td>
                            <td>1/2025</td>
                            <td>1715</td>
                            <td>1</td>
                            <td>69.500,00</td>
                            <td>0,00</td>
                            <td>0,00</td>
                            <td>100,00</td>
                            <td><span class="badge">Abierto</span></td>
                            <td><button class="delete-button" type="button">Borrar</button></td>
                        </tr>
                        <tr>
                            <td>D</td>
                            <td>2083910</td>
                            <td>285 - O.S.S.I.M.R.A.</td>
                            <td>2322 - PRUYAS JUAN MANUEL ...</td>
                            <td>1/2025</td>
                            <td>420351</td>
                            <td>3</td>
                            <td>1.860,00</td>
                            <td>0,00</td>
                            <td>0,00</td>
                            <td>100,00</td>
                            <td><span class="badge">Abierto</span></td>
                            <td><button class="delete-button" type="button">Borrar</button></td>
                        </tr>
                        <tr>
                            <td>D</td>
                            <td>2084703</td>
                            <td>285 - O.S.S.I.M.R.A.</td>
                            <td>2029 - PAVON MARIO OSCAR ...</td>
                            <td>1/2025</td>
                            <td>420101</td>
                            <td>16</td>
                            <td>3.100,00</td>
                            <td>0,00</td>
                            <td>0,00</td>
                            <td>100,00</td>
                            <td><span class="badge">Abierto</span></td>
                            <td><button class="delete-button" type="button">Borrar</button></td>
                        </tr>
                        <tr>
                            <td>D</td>
                            <td>2105350</td>
                            <td>285 - O.S.S.I.M.R.A.</td>
                            <td>2439 - MONTENEGRO ENZO JAVI...</td>
                            <td>1/2025</td>
                            <td>220101</td>
                            <td>1</td>
                            <td>820,00</td>
                            <td>0,00</td>
                            <td>0,00</td>
                            <td>100,00</td>
                            <td><span class="badge">Abierto</span></td>
                            <td><button class="delete-button" type="button">Borrar</button></td>
                        </tr>
                        <tr>
                            <td>D</td>
                            <td>2108117</td>
                            <td>285 - O.S.S.I.M.R.A.</td>
                            <td>2439 - MONTENEGRO ENZO JAVI...</td>
                            <td>1/2025</td>
                            <td>420351</td>
                            <td>8</td>
                            <td>3.720,00</td>
                            <td>0,00</td>
                            <td>0,00</td>
                            <td>100,00</td>
                            <td><span class="badge">Abierto</span></td>
                            <td><button class="delete-button" type="button">Borrar</button></td>
                        </tr>
                        <tr>
                            <td>D</td>
                            <td>2082746</td>
                            <td>285 - O.S.S.I.M.R.A.</td>
                            <td>2279 - MERMET GUSTAVO MARCE</td>
                            <td>1/2025</td>
                            <td>110403</td>
                            <td>1</td>
                            <td>48.167,50</td>
                            <td>0,00</td>
                            <td>0,00</td>
                            <td>100,00</td>
                            <td><span class="badge">Abierto</span></td>
                            <td><button class="delete-button" type="button">Borrar</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        // -------------------------------
        // THEME TOGGLE (light/dark/auto)
        // -------------------------------
        (function() {
            const EL = document.documentElement;
            const BTN = document.getElementById('themeToggle');
            const KEY = 'cmg-theme'; // values: 'light' | 'dark' | 'auto'

            function setTheme(value) {
                EL.setAttribute('data-theme', value);
                BTN.setAttribute('aria-pressed', value === 'dark');
                localStorage.setItem(KEY, value);
            }

            // Load saved theme or default to 'auto'
            const saved = localStorage.getItem(KEY) || 'auto';
            setTheme(saved);

            function toggle() {
                // Cycle: auto -> dark -> light -> auto
                const current = EL.getAttribute('data-theme');
                const next = current === 'auto' ? 'dark' : current === 'dark' ? 'light' : 'auto';
                setTheme(next);
            }

            BTN.addEventListener('click', toggle);
            // Keyboard shortcut T
            window.addEventListener('keydown', (e) => {
                if (e.key.toLowerCase() === 't') toggle();
            });

            // Update when OS theme changes and in 'auto'
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                if ((localStorage.getItem(KEY) || 'auto') === 'auto') setTheme('auto');
            });
        })();

        // -------------------------------
        // MONEY FORMAT & % LIMIT
        // -------------------------------
        const moneyInputs = [document.getElementById('honorarios'), document.getElementById('gastos')];
        const pct = document.getElementById('liq');

        function formatMoney(v) {
            const n = Number(String(v).replace(/[^\d,.-]/g, '').replace(',', '.')) || 0;
            return n.toLocaleString('es-AR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
        moneyInputs.forEach(input => input.addEventListener('blur', () => input.value = formatMoney(input.value)));
        pct.addEventListener('change', () => {
            let n = Number(pct.value);
            if (isNaN(n)) n = 0;
            if (n < 0) n = 0;
            if (n > 100) n = 100;
            pct.value = n.toFixed(2);
        });

        // Optional small helpers
        document.getElementById('corrBtn')?.addEventListener('click', () => {
            const p = document.getElementById('paciente');
            if (!p) return;
            p.value = p.value.trim().replace(/\s+/g, ' ');
            p.focus();
        });

        document.querySelectorAll('.delete-button').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const row = e.target.closest('tr');
                if (!row) return;
                // Replace with your confirm/toast logic
                if (confirm('¿Borrar este registro?')) row.remove();
            });
        });

        document.getElementById('buscarOrdenBtn')?.addEventListener('click', () => {
            // Hook real search here
            const val = (document.getElementById('orden')?.value || '').trim();
            if (!val) {
                alert('Ingresá un número de orden para buscar.');
                return;
            }
            // perform search...
        });
    </script>
</body>

</html>