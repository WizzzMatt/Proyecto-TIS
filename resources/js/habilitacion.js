// public/js/habilitacion.js
document.addEventListener('DOMContentLoaded', function () {
    
    const tipoSelect = document.getElementById('tipo_habilitacion');
    const camposProyecto = document.getElementById('campos_proyecto');
    const camposPractica = document.getElementById('campos_practica');

    function toggleHabilitacionFields() {
        const selectedValue = tipoSelect.value;

        // Ocultar y deshabilitar todo primero
        camposProyecto.style.display = 'none';
        camposProyecto.querySelectorAll('input, select').forEach(el => el.disabled = true);
        
        camposPractica.style.display = 'none';
        camposPractica.querySelectorAll('input, select').forEach(el => el.disabled = true);

        if (selectedValue === 'Pring' || selectedValue === 'Prinv') {
            // R2.13: Mostrar campos de Proyecto [cite: 126]
            camposProyecto.style.display = 'block';
            camposProyecto.querySelectorAll('input, select').forEach(el => el.disabled = false);
        } else if (selectedValue === 'PrTut') {
            // R2.14: Mostrar campos de Práctica [cite: 150]
            camposPractica.style.display = 'block';
            camposPractica.querySelectorAll('input, select').forEach(el => el.disabled = false);
        }
        // Si no se selecciona nada, todo queda oculto y deshabilitado
    }

    // Ejecutar la función cuando cambia el selector [cite: 123]
    tipoSelect.addEventListener('change', toggleHabilitacionFields);
    
    // Ejecutar al cargar (por si el formulario vuelve con error y datos 'old')
    toggleHabilitacionFields();
});