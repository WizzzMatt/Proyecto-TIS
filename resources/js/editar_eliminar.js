document.addEventListener('DOMContentLoaded', function () {
    console.log("Sistema Iniciado - Depuración Activa");

    // 1. Obtener profesores (protegido contra fallos)
    const profesores = window.datosProfesores || [];
    
    // Referencias DOM
    const vistaSeleccion = document.getElementById('vista-seleccion');
    const vistaBuscador = document.getElementById('vista-buscador');
    const vistaFormulario = document.getElementById('vista-formulario');
    const vistaFinal = document.getElementById('vista-final');

    const contenedorProyectos = document.getElementById('contenedor-select-proyectos');
    const contenedorPracticas = document.getElementById('contenedor-select-practicas');
    const selectProyectos = document.getElementById('select-proyectos');
    const selectPracticas = document.getElementById('select-practicas');

    const tarjetaDetalle = document.getElementById('detalle-seleccion');
    const detalleNombre = document.getElementById('detalle-nombre');
    const detalleId = document.getElementById('detalle-id');
    const detalleTipo = document.getElementById('detalle-tipo');

    let seleccionActual = null;

    // --- NAVEGACIÓN ---
    const btnProyecto = document.getElementById('btn-opcion-proyecto');
    if (btnProyecto) btnProyecto.addEventListener('click', () => mostrarBuscador('proyecto'));

    const btnPractica = document.getElementById('btn-opcion-practica');
    if (btnPractica) btnPractica.addEventListener('click', () => mostrarBuscador('practica'));

    const btnVolver = document.getElementById('btn-volver-seleccion');
    if (btnVolver) {
        btnVolver.addEventListener('click', () => {
            vistaBuscador.classList.add('d-none');
            vistaSeleccion.classList.remove('d-none');
            if(selectProyectos) selectProyectos.selectedIndex = 0;
            if(selectPracticas) selectPracticas.selectedIndex = 0;
            tarjetaDetalle.classList.add('d-none');
            seleccionActual = null;
        });
    }

    function mostrarBuscador(tipo) {
        vistaSeleccion.classList.add('d-none');
        vistaBuscador.classList.remove('d-none');
        tarjetaDetalle.classList.add('d-none'); 

        if (tipo === 'proyecto') {
            contenedorProyectos.classList.remove('d-none');
            contenedorPracticas.classList.add('d-none');
            document.getElementById('titulo-buscador').innerText = "Buscar en Proyectos";
        } else {
            contenedorProyectos.classList.add('d-none');
            contenedorPracticas.classList.remove('d-none');
            document.getElementById('titulo-buscador').innerText = "Buscar en Prácticas";
        }
    }

    // --- SELECCIÓN Y CARGA DE DATOS ---
    const selects = document.querySelectorAll('.selector-habilitacion');

    selects.forEach(select => {
        select.addEventListener('change', function() {
            const opcion = this.options[this.selectedIndex];
            const valorId = this.value;

            if (!valorId) {
                tarjetaDetalle.classList.add('d-none');
                seleccionActual = null;
                return;
            }

            // LEER DATOS DEL HTML (DATASET)
            seleccionActual = {
                id: valorId,
                tipoHab: opcion.dataset.tipoHab,
                subtipo: opcion.dataset.subtipo,
                nombre: opcion.dataset.nombre,
                descripcion: opcion.dataset.descripcion,
                
                // Datos específicos
                titulo: opcion.dataset.titulo || '',
                empresa: opcion.dataset.empresa || '',
                supervisor: opcion.dataset.supervisor || '',
                
                // RUTs de Profesores (Limpiamos espacios y convertimos a String)
                guia: opcion.dataset.guia ? String(opcion.dataset.guia).trim() : '',
                comision: opcion.dataset.comision ? String(opcion.dataset.comision).trim() : '',
                coguia: opcion.dataset.coguia ? String(opcion.dataset.coguia).trim() : '',
                tutor: opcion.dataset.tutor ? String(opcion.dataset.tutor).trim() : ''
            };

            console.log("Seleccionado:", seleccionActual); // Debug para ver si lee los datos

            // Mostrar tarjeta
            detalleNombre.innerText = seleccionActual.nombre;
            detalleId.innerText = seleccionActual.id;
            detalleTipo.innerText = seleccionActual.subtipo;
            detalleTipo.className = 'badge fs-6 ' + (seleccionActual.tipoHab === 'practica' ? 'bg-success' : 'bg-primary');
            tarjetaDetalle.classList.remove('d-none');
        });
    });

    // --- INICIAR EDICIÓN (AQUÍ SE RELLENAN LOS DATOS) ---
    const btnActualizar = document.getElementById('btn-accion-actualizar');
    if (btnActualizar) {
        btnActualizar.addEventListener('click', () => {
            if (seleccionActual) iniciarActualizacion();
        });
    }

    function iniciarActualizacion() {
        vistaBuscador.classList.add('d-none');
        vistaFormulario.classList.remove('d-none');

        // 1. LLENAR SELECTS DE PROFESORES PRIMERO
        llenarSelectsProfesores();

        // 2. LLENAR CAMPOS COMUNES
        document.getElementById('input-id').value = seleccionActual.id;
        document.getElementById('badge-id-hab').innerText = `ID: ${seleccionActual.id}`;
        document.getElementById('input-descripcion').value = seleccionActual.descripcion;

        // 3. LLENAR CAMPOS ESPECÍFICOS Y SELECCIONAR PROFESORES
        if (seleccionActual.tipoHab === 'practica') {
            document.getElementById('campos-proyecto').classList.add('d-none');
            document.getElementById('campos-practica').classList.remove('d-none');
            
            document.getElementById('input-empresa').value = seleccionActual.empresa;
            document.getElementById('input-supervisor').value = seleccionActual.supervisor;
            
            // Seleccionar Tutor
            if(seleccionActual.tutor) {
                const sel = document.getElementById('select-prof-tutor');
                sel.value = seleccionActual.tutor;
                // Si el valor no cambia, es porque el profe no está en la lista (inactivo?)
                if(sel.value !== seleccionActual.tutor) console.warn("Tutor no encontrado en la lista de profesores");
            }

        } else {
            document.getElementById('campos-practica').classList.add('d-none');
            document.getElementById('campos-proyecto').classList.remove('d-none');

            document.getElementById('input-titulo').value = seleccionActual.titulo;
            
            // Seleccionar Profesores de Proyecto
            if(seleccionActual.guia) document.getElementById('select-prof-guia').value = seleccionActual.guia;
            if(seleccionActual.comision) document.getElementById('select-prof-comision').value = seleccionActual.comision;
            if(seleccionActual.coguia && seleccionActual.coguia !== 'null') {
                document.getElementById('select-prof-coguia').value = seleccionActual.coguia;
            } else {
                document.getElementById('select-prof-coguia').value = "";
            }
        }
    }

    function llenarSelectsProfesores() {
        const selectsIds = ['select-prof-guia', 'select-prof-comision', 'select-prof-coguia', 'select-prof-tutor'];
        
        selectsIds.forEach(id => {
            const select = document.getElementById(id);
            if (!select) return;

            // Guardar opción por defecto
            const defaultOpt = select.querySelector('option[value=""]');
            select.innerHTML = ''; 
            if (defaultOpt) select.appendChild(defaultOpt);

            // Rellenar opciones
            profesores.forEach(prof => {
                const opt = document.createElement('option');
                opt.value = String(prof.rut_profesor); // Convertir RUT a string
                opt.text = prof.nombre_profesor || prof.nombre; 
                select.appendChild(opt);
            });
        });
    }

    // --- RESTO DE LOGICA DE BOTONES
    const btnCancelar = document.getElementById('btn-cancelar-edicion');
    if (btnCancelar) btnCancelar.addEventListener('click', () => {
        vistaFormulario.classList.add('d-none');
        vistaBuscador.classList.remove('d-none');
    });

    const btnTerminar = document.getElementById('btn-terminar-actualizacion');
    if (btnTerminar) btnTerminar.addEventListener('click', () => {
        new bootstrap.Modal(document.getElementById('modalGuardarCambios')).show();
    });

    // Guardado REAL 
    const btnConfGuardar = document.getElementById('btn-confirmar-guardado');
    if (btnConfGuardar) {
        btnConfGuardar.addEventListener('click', () => {
            bootstrap.Modal.getInstance(document.getElementById('modalGuardarCambios')).hide();

            let datosEnviar = {};
            const tipo = seleccionActual.tipoHab;

            if (tipo === 'proyecto') {
                const valCoguia = document.getElementById('select-prof-coguia').value;
                datosEnviar = {
                    titulo: document.getElementById('input-titulo').value,
                    descripcion: document.getElementById('input-descripcion').value,
                    profesor_guia_rut: document.getElementById('select-prof-guia').value,
                    profesor_comision_rut: document.getElementById('select-prof-comision').value,
                    profesor_coguia_rut: valCoguia || null,
                    toggle_coguia: valCoguia ? 'si' : 'no'
                };
            } else {
                datosEnviar = {
                    nombre_empresa: document.getElementById('input-empresa').value,
                    nombre_supervisor: document.getElementById('input-supervisor').value,
                    descripcion: document.getElementById('input-descripcion').value,
                    descripcion_practica: document.getElementById('input-descripcion').value,
                    profesor_tutor_rut: document.getElementById('select-prof-tutor').value
                };
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            fetch(`/actualizar-habilitacion/${tipo}/${seleccionActual.id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(datosEnviar)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    mostrarMensajeExito("Habilitación actualizada correctamente.");
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(err => console.error(err));
        });
    }

    // Eliminación (DELETE)
    const btnEliminar = document.getElementById('btn-accion-eliminar');
    if (btnEliminar) btnEliminar.addEventListener('click', () => {
        if(seleccionActual) new bootstrap.Modal(document.getElementById('modalEliminar')).show();
    });

    const btnConfEliminar = document.getElementById('btn-confirmar-eliminar');
    if (btnConfEliminar) btnConfEliminar.addEventListener('click', () => {
        bootstrap.Modal.getInstance(document.getElementById('modalEliminar')).hide();
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        fetch(`/eliminar-habilitacion/${seleccionActual.tipoHab}/${seleccionActual.id}`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                mostrarMensajeExito("Eliminado correctamente.");
            } else {
                alert("Error: " + data.message);
            }
        });
    });

    function mostrarMensajeExito(msg) {
        document.getElementById('mensaje-exito-modal').innerText = msg;
        vistaBuscador.classList.add('d-none');
        vistaFormulario.classList.add('d-none');
        new bootstrap.Modal(document.getElementById('modalOtraOperacion')).show();
        
        // Recargar al salir
        document.getElementById('btn-si-otra').onclick = () => location.reload();
        document.getElementById('btn-salir').onclick = () => location.reload();
    }
});