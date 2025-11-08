document.addEventListener('DOMContentLoaded', () => {
    // --- Seleccionar elementos del DOM ---
    const form = document.querySelector('form');
    const rutAlumnoInput = document.getElementById('rut-alumno');
    const profesorGuiaInput = document.getElementById('profesor-guia');
    const semestreAnoInput = document.getElementById('semestre-ano');
    const semestrePeriodoInput = document.getElementById('semestre-periodo');
    const tipoHabilitacionSelect = document.getElementById('tipo-habilitacion');
    
    // Campos condicionales
    const tituloContainer = document.getElementById('titulo-container');
    const tituloProyectoInput = document.getElementById('titulo-proyecto');
    const practicaContainer = document.getElementById('practica-container');
    const nombreEmpresaInput = document.getElementById('nombre-empresa');
    const nombreSupervisorInput = document.getElementById('nombre-supervisor');

    form.addEventListener('submit', (event) => {


        event.preventDefault();
        const ano= document.getElementsByNameId('semestre-ano').value;
        const periodo= document.getElementsByNameId('semestre-periodo').value;
        document.getElementById('semestre_inicio').value= `${ano}-${periodo}`;

        const esValido = validarFormulario();

        // Decidir si se "envía" el formulario
        if (esValido) {
            alert('¡Formulario completado correctamente!');
            console.log('Formulario válido. Listo para enviar.');
            // En un caso real, aquí se enviaría la información al servidor.
            // form.submit(); 
        } else {
            alert('Por favor, complete todos los campos obligatorios resaltados en rojo.');
            console.log('Formulario inválido. Se encontraron campos vacíos.');
        }
    });

    //Función de Validación 
    function validarFormulario() {
        let esValido = true;
        
        // Lista de todos los campos que podrían necesitar validación
        const campos = [
            rutAlumnoInput, profesorGuiaInput, tipoHabilitacionSelect, 
            semestreAnoInput, semestrePeriodoInput, tituloProyectoInput, 
            nombreEmpresaInput, nombreSupervisorInput
        ];

        // Limpiar errores previos en todos los campos
        campos.forEach(campo => campo.classList.remove('error'));

        // Campos que siempre son obligatorios
        const camposObligatorios = [
            rutAlumnoInput, profesorGuiaInput, tipoHabilitacionSelect,
            semestreAnoInput, semestrePeriodoInput
        ];

        camposObligatorios.forEach(campo => {
            if (campo.value.trim() === '') {
                esValido = false;
                campo.classList.add('error');
            }
        });

        // Validar campos CONDICIONALES (solo si son visibles)
        // Si el contenedor del título NO está oculto...
        if (!tituloContainer.classList.contains('hidden')) {
            if (tituloProyectoInput.value.trim() === '') {
                esValido = false;
                tituloProyectoInput.classList.add('error');
            }
        }

        // Si el contenedor de la práctica NO está oculto...
        if (!practicaContainer.classList.contains('hidden')) {
            if (nombreEmpresaInput.value.trim() === '') {
                esValido = false;
                nombreEmpresaInput.classList.add('error');
            }
            if (nombreSupervisorInput.value.trim() === '') {
                esValido = false;
                nombreSupervisorInput.classList.add('error');
            }
        }

        return esValido;
    }

    // Función para mostrar/ocultar campos condicionales ---
    function manejarCamposCondicionales() {
        const seleccion = tipoHabilitacionSelect.value;
        tituloContainer.classList.add('hidden');
        practicaContainer.classList.add('hidden');

        if (seleccion === 'Prinv' || seleccion === 'Pring') {
            tituloContainer.classList.remove('hidden');
        } else if (seleccion === 'Prtut') {
            practicaContainer.classList.remove('hidden');
        }
    }

    tipoHabilitacionSelect.addEventListener('change', manejarCamposCondicionales);

    semestreAnoInput.addEventListener('blur', function() {
        if (this.value.length > 4) {
            this.value = this.value.slice(0, 4);
        }
        const ano = parseInt(this.value, 10);
        if (ano > 2045) {
            this.value = 2045;
        } else if (ano < 2025) {
            this.value = 2025;
        }
    });

    semestrePeriodoInput.addEventListener('input', function() {
        if (this.value.length > 1) {
            this.value = this.value.slice(0, 1);
        }
        const semestre = parseInt(this.value, 10);
        if (semestre > 2) {
            this.value = 2;
        } else if (semestre < 1) {
            this.value = '';
        }
    });
    form.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });

// Redirigir al presionar "Atrás" en el navegador
    history.pushState(null, '', location.href);
    window.addEventListener('popstate', function(event) {
        // Redirige al usuario a la página principal.
        // location.replace para que no pueda volver al formulario con "Adelante".
        window.location.replace('indexPrincipal.html');
    });

    // resources/js/scriptFormulario.js

// (El resto de tu JS, como 'manejarCamposCondicionales', va aquí arriba)

form.addEventListener('submit', () => {
    // --- ESTA ES LA LÓGICA CLAVE ---
    // Combinamos los campos de semestre en el formato "AAAA-Y"
    // justo antes de que el formulario se envíe a Laravel.
    try {
        const ano = document.getElementById('semestre-ano').value;
        const periodo = document.getElementById('semestre-periodo').value;
        
        // Lo ponemos en el campo oculto que SÍ se envía a Laravel
        document.getElementById('semestre_inicio').value = `${ano}-${periodo}`;
    } catch (e) {
        console.error('Error al combinar el semestre:', e);
    }
    
    // NO usamos event.preventDefault()
    // Dejamos que el formulario se envíe normalmente.
});
});