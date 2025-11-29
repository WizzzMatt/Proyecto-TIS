<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Habilitación</title>
    <link rel="icon" href="{{ asset('logo.ico') }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/editar_eliminar.css', 'resources/js/editar_eliminar.js'])
</head>
<body>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm custom-card">
                    <div class="card-body p-4 p-md-5">

                        <div id="vista-seleccion">
                            <h1 class="h3 custom-card-title text-center mb-4">Gestión de Habilitaciones</h1>
                            <p class="text-muted text-center mb-5">Seleccione el tipo de habilitación que desea modificar o eliminar.</p>

                            <div class="row justify-content-center gap-4">
                                <div class="col-md-5">
                                    <button id="btn-opcion-proyecto" class="btn btn-outline-danger w-100 p-4 h-100 shadow-sm d-flex flex-column align-items-center">
                                        <i class="fa-solid fa-book-open fa-3x mb-3"></i>
                                        <h3 class="h5">Proyectos</h3>
                                        <p class="small text-muted mb-0">Investigación e Ingeniería</p>
                                    </button>
                                </div>
                                <div class="col-md-5">
                                    <button id="btn-opcion-practica" class="btn btn-outline-success w-100 p-4 h-100 shadow-sm d-flex flex-column align-items-center">
                                        <i class="fa-solid fa-briefcase fa-3x mb-3"></i>
                                        <h3 class="h5">Práctica Tutelada</h3>
                                        <p class="small text-muted mb-0">En empresa externa</p>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="vista-buscador" class="d-none">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <button class="btn btn-sm btn-outline-secondary" id="btn-volver-seleccion">
                                    <i class="fa-solid fa-arrow-left"></i> Volver
                                </button>
                                <h1 class="h4 custom-card-title mb-0" id="titulo-buscador">Buscar Habilitación</h1>
                                <div style="width: 80px;"></div> 
                            </div>
                            
                            <p class="text-muted text-center mb-4">Seleccione una habilitación de la lista para ver las acciones disponibles.</p>

                            <div id="contenedor-select-proyectos" class="d-none">
                                <label class="form-label fw-bold">Listado de Proyectos:</label>
                                <select id="select-proyectos" class="form-select form-select-lg selector-habilitacion">
                                    <option value="">-- Seleccione un Proyecto --</option>
                                    @foreach($proyectos as $p)
                                        <option 
                                            value="{{ $p->id_habilitacion }}" 
                                            data-tipo-hab="proyecto"
                                            data-subtipo="{{ $p->tipo_proyecto }}" 
                                            data-nombre="{{ $p->alumno->nombre_alumno ?? 'Sin Alumno' }}"
                                            data-descripcion="{{ $p->descripcion }}"
                                            data-titulo="{{ $p->titulo }}"
                                            data-guia="{{ $p->profesor_guia_rut }}"
                                            data-comision="{{ $p->profesor_comision_rut }}"
                                            data-coguia="{{ $p->profesor_coguia_rut }}"
                                        >
                                            {{ $p->id_habilitacion }} - {{ $p->alumno->nombre_alumno ?? 'Sin Alumno' }} - {{ $p->tipo_proyecto }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="contenedor-select-practicas" class="d-none">
                                <label class="form-label fw-bold">Listado de Prácticas:</label>
                                <select id="select-practicas" class="form-select form-select-lg selector-habilitacion">
                                    <option value="">-- Seleccione una Práctica --</option>
                                    @foreach($practicas as $p)
                                        <option 
                                            value="{{ $p->id_habilitacion }}" 
                                            data-tipo-hab="practica"
                                            data-subtipo="PrTut"
                                            data-nombre="{{ $p->alumno->nombre_alumno ?? 'Sin Alumno' }}"
                                            data-descripcion="{{ $p->descripcion }}"
                                            data-empresa="{{ $p->nombre_empresa }}"
                                            data-supervisor="{{ $p->nombre_supervisor }}"
                                            data-tutor="{{ $p->profesor_tutor_rut }}"
                                        >
                                            {{ $p->id_habilitacion }} - {{ $p->alumno->nombre_alumno ?? 'Sin Alumno' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="detalle-seleccion" class="card bg-light border-0 d-none mt-4 shadow-sm">
                                <div class="card-body text-center p-4">
                                    <h5 class="text-secondary text-uppercase small ls-1 mb-3">Habilitación Seleccionada</h5>
                                    
                                    <h3 class="card-title fw-bold text-dark mb-2" id="detalle-nombre">Nombre Alumno</h3>
                                    
                                    <div class="mb-3">
                                        <span class="badge bg-primary fs-6" id="detalle-tipo">TIPO</span>
                                    </div>
                                    
                                    <p class="text-muted small mb-4 fw-bold" style="font-size: 1.1rem;">
                                        ID: <span id="detalle-id" class="text-dark">---</span>
                                    </p>
                                    
                                    <div class="d-flex justify-content-center gap-3">
                                        <button class="btn btn-primary px-4 py-2" id="btn-accion-actualizar">
                                            <i class="fa-solid fa-pen me-2"></i> Actualizar
                                        </button>
                                        <button class="btn btn-outline-danger px-4 py-2" id="btn-accion-eliminar">
                                            <i class="fa-solid fa-trash me-2"></i> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="vista-formulario" class="d-none">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2 class="h4 custom-card-title mb-0">Actualizar Datos</h2>
                                <span class="badge bg-secondary" id="badge-id-hab">ID: --</span>
                            </div>

                            <form id="form-actualizar" novalidate>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">ID Habilitación</label>
                                    <input type="text" class="form-control" id="input-id" readonly disabled>
                                </div>

                                <div id="campos-proyecto" class="d-none">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Título (R2.5)</label>
                                        <input type="text" class="form-control" id="input-titulo">
                                        <div class="invalid-feedback">Modificación no es válida.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Profesor Guía</label>
                                        <select class="form-select" id="select-prof-guia"></select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Profesor Comisión</label>
                                        <select class="form-select" id="select-prof-comision"></select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Profesor Co-Guía</label>
                                        <select class="form-select" id="select-prof-coguia">
                                            <option value="">-- Ninguno --</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="campos-practica" class="d-none">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nombre Empresa (R2.7)</label>
                                        <input type="text" class="form-control" id="input-empresa">
                                        <div class="invalid-feedback">Modificación no es válida.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nombre Supervisor (R2.8)</label>
                                        <input type="text" class="form-control" id="input-supervisor">
                                        <div class="invalid-feedback">Modificación no es válida.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Profesor Tutor</label>
                                        <select class="form-select" id="select-prof-tutor"></select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Descripción (R2.2)</label>
                                    <textarea class="form-control" id="input-descripcion" rows="4"></textarea>
                                    <div class="form-text">Mínimo 100 caracteres, máx 1000.</div>
                                    <div class="invalid-feedback">Modificación no es válida.</div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-secondary" id="btn-cancelar-edicion">Cancelar operación</button>
                                    <button type="button" class="btn btn-custom-red" id="btn-terminar-actualizacion">Terminar actualización</button>
                                </div>
                            </form>
                        </div>

                        <div id="vista-final" class="d-none text-center py-5">
                            <h2 class="text-success mb-3"><i class="fa-solid fa-check-circle"></i> Operación finalizada</h2>
                            <p>El sistema ha terminado de eliminar o actualizar los datos.</p>
                            <a href="/" class="btn btn-primary mt-3">Ir al Inicio</a>
                        </div>

                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="/" class="back-link">Volver al menú principal</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEliminar" tabindex="-1" data-bs-backdrop="static"><div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-danger text-white"><h5 class="modal-title">Confirmar Eliminación</h5></div><div class="modal-body"><p>¿Está seguro que desea eliminar esta Habilitación?</p></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-danger" id="btn-confirmar-eliminar">Confirmar</button></div></div></div></div>
    <div class="modal fade" id="modalGuardarCambios" tabindex="-1" data-bs-backdrop="static"><div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-custom-red text-white"><h5 class="modal-title">Guardar Cambios</h5></div><div class="modal-body"><p>¿Desea guardar los cambios realizados?</p></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-custom-red" id="btn-confirmar-guardado">Confirmar</button></div></div></div></div>
    <div class="modal fade" id="modalOtraOperacion" tabindex="-1" data-bs-backdrop="static"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Operación Exitosa</h5></div><div class="modal-body"><p id="mensaje-exito-modal" class="fw-bold text-success"></p><hr><p>¿Necesita realizar otra operación?</p></div><div class="modal-footer"><button type="button" class="btn btn-secondary" id="btn-salir">Salir</button><button type="button" class="btn btn-primary" id="btn-si-otra">Si</button></div></div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Esta línea pasa la lista de profesores de PHP a JavaScript
        // Usamos las banderas JSON para evitar errores con tildes o caracteres raros.
        window.datosProfesores = {!! json_encode($profesores ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_INVALID_UTF8_IGNORE) !!};
        
        console.log("Profesores cargados en la vista:", window.datosProfesores);
    </script>

</body>
</html>