document.addEventListener('DOMContentLoaded', () => {

    const ingresoBtn = document.getElementById('btn-ingreso');
    const editarBtn = document.getElementById('btn-editar');
    const listadoBtn = document.getElementById('btn-listado');

    ingresoBtn.addEventListener('click', (event) => {
        // Previene la navegación para este ejemplo
        
        console.log('Botón "Ingreso de datos" presionado.');
        // En un futuro, aquí iría la lógica para redirigir:
        // window.location.href = event.currentTarget.href;
    });

    editarBtn.addEventListener('click', (event) => {
        event.preventDefault();
        console.log('Botón "Editar datos" presionado.');
    });

    listadoBtn.addEventListener('click', (event) => {
        event.preventDefault();
        console.log('Botón "Crear listado" presionado.');
    });

});