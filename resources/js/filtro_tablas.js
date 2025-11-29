// resources/js/filtro_tablas.js

document.addEventListener('DOMContentLoaded', function () {
    console.log('filtro_tablas.js cargado en reporte');

    // Función común para normalizar texto
    const normalizar = (texto) =>
        (texto || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');

    // =====================================================
    // 1) FILTRO GENÉRICO → Proyectos y Prácticas (Semestral)
    // =====================================================

    const inputsFiltroGenericos = document.querySelectorAll('[data-table-filter]');

    inputsFiltroGenericos.forEach((input) => {
        const selectorTablas = input.dataset.tableFilter; // "#tabla-proyectos" o "#tabla-practicas"

        input.addEventListener('input', function () {
            const query = normalizar(this.value.trim());
            const tablas = document.querySelectorAll(selectorTablas);

            tablas.forEach((table) => {
                const filas = table.querySelectorAll('tbody tr');

                filas.forEach((fila) => {
                    const textoFila = normalizar(fila.textContent);
                    const coincide = !query || textoFila.includes(query);
                    fila.style.display = coincide ? '' : 'none';
                });
            });
        });
    });

    // =====================================================
    // 2) FILTRO AVANZADO → Listado Histórico
    //    (Profesor / Alumno / Semestre / Rol)
    // =====================================================

    const inputHistorico   = document.getElementById('filtro-historico');
    const selectCampo      = document.getElementById('filtro-historico-campo');
    const inputSemestre    = document.getElementById('filtro-historico-semestre');
    const selectRol        = document.getElementById('filtro-historico-rol');

    if (inputHistorico) {
        const tablasHistorico = document.querySelectorAll('.tabla-historico');

        const filtrarHistorico = () => {
            const texto     = normalizar(inputHistorico.value.trim());
            const campo     = selectCampo ? selectCampo.value : 'todos'; // 'profesor' | 'alumno' | 'todos'
            const semestreQ = normalizar(inputSemestre?.value.trim() || '');
            const rolQ      = normalizar(selectRol?.value.trim() || '');

            tablasHistorico.forEach((table) => {
                const filas = table.querySelectorAll('tbody tr');

                filas.forEach((fila) => {
                    const celdas = fila.querySelectorAll('td');

                    // columnas: 0 = semestre, 1 = rol, 2 = alumno, 3 = tipo
                    const valorSemestre = normalizar(celdas[0]?.textContent || '');
                    const valorRol      = normalizar(celdas[1]?.textContent || '');
                    const valorAlumno   = normalizar(celdas[2]?.textContent || '');
                    const valorTipo     = normalizar(celdas[3]?.textContent || '');

                    // Profesor viene del header de la card
                    const card          = fila.closest('.card-historico');
                    const headerTexto   = card?.querySelector('.card-header')?.textContent || '';
                    const valorProfesor = normalizar(headerTexto);

                    // --- filtro por texto (Profesor / Alumno / Ambos) ---
                    let coincideTexto = true;
                    if (texto) {
                        if (campo === 'alumno') {
                            coincideTexto = valorAlumno.includes(texto);
                        } else if (campo === 'profesor') {
                            coincideTexto = valorProfesor.includes(texto);
                        } else {
                            // 'todos': profesor o alumno
                            coincideTexto =
                                valorAlumno.includes(texto) ||
                                valorProfesor.includes(texto);
                        }
                    }

                    // --- filtro por semestre (ej: "2025-2") ---
                    const coincideSemestre = !semestreQ || valorSemestre.includes(semestreQ);

                    // --- filtro por rol (guía, co-guía, comisión, tutor) ---
                    const coincideRol = !rolQ || valorRol.includes(rolQ);

                    const visible = coincideTexto && coincideSemestre && coincideRol;
                    fila.style.display = visible ? '' : 'none';
                });
            });

            // Ocultar cards completas sin filas visibles
            document.querySelectorAll('.card-historico').forEach((card) => {
                const visibleFila = card.querySelector(
                    'tbody tr:not([style*="display: none"])'
                );
                card.style.display = visibleFila ? '' : 'none';
            });
        };

        // Disparar filtro al escribir/cambiar
        inputHistorico.addEventListener('input', filtrarHistorico);
        selectCampo?.addEventListener('change', filtrarHistorico);
        inputSemestre?.addEventListener('input', filtrarHistorico);
        selectRol?.addEventListener('change', filtrarHistorico);
    }
});
