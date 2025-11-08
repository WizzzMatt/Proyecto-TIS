<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Habilitación Profesional</title>
    @vite('resources/css/styleFormulario.css')
</head>
<body>
    <div class="form-container">
        <h1>Formulario de Habilitación Profesional</h1>
        <p>Complete los siguientes campos para registrar la habilitación.</p>
        @if (session('success'))
            <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 20px;">
                <strong>¡Error! Hubo problemas con los datos ingresados:</strong>
                <ul style="margin-top: 10px; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

<form action="{{ route('habilitacion.store') }}" method="POST"></form>

        <form action="{{ route('habilitacion.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="rut_alumno">Alumno</label>
                <select id="rut_alumno" name="rut_alumno" class="form-control" required>
                <option value="">Seleccione un alumno...</option>
                    @foreach ($alumnos as $alumno)
                        <option value="{{ $alumno->rut_alumno }}">{{ $alumno->nombre_alumno }} ({{ $alumno->rut_alumno }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="rut_profesor_g">Profesor Guía</label>
                <select id="rut_profesor_g" name="profesor_guia_rut" class="form-control">
                <option value="">Seleccione un profesor...</option>
                    @foreach ($profesores as $profesor)
                        <option value="{{ $profesor->rut_profesor }}">{{ $profesor->nombre_profesor }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="tipo-habilitacion">Tipo de habilitación</label>
                <select id="tipo-habilitacion" name="tipo_habilitacion">
                    <option value="" disabled selected>Seleccione una opción</option>
                    <option value="Prtut">Práctica tutelada (PrTut)</option>
                    <option value="Prinv">Proyecto de investigación (PrInv)</option>
                    <option value="Pring">Proyecto de ingeniería (PrIng)</option>
                </select>
            </div>

            <div id="titulo-container" class="form-group hidden">
                <label for="titulo-proyecto">Título</label>
                <input type="text" id="titulo-proyecto" name="titulo" maxlength="100">
            </div>

            <div id="practica-container" class="hidden">
                <div class="form-group">
                    <label for="nombre-empresa">Nombre empresa</label>
                    <input type="text" id="nombre-empresa" name="nombre_empresa" maxlength="50">
                </div>
                <div class="form-group">
                    <label for="nombre-supervisor">Nombre Supervisor</label>
                    <input type="text" id="nombre-supervisor" name="nombre_supervisor" maxlength="100">
                </div>
            </div>

            <div class="form-group">
                <label>Semestre inicio</label>
                <div class="semestre-group">
                    <div class="semestre-field">
                        <input type="number" id="semestre-ano" placeholder="AAAA" min="0" max="2050">
                        <small class="helper-text">Ingrese año</small>
                    </div>
                    <div class="semestre-field">
                        <input type="number" id="semestre-periodo" min="1" max="2" placeholder="N">
                        <small class="helper-text">Ingrese semestre (1 - 2)</small>
                        <input type="hidden" name="semestre_inicio" id="semestre_inicio">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="nota-final">Nota Final</label>
                <input type="text" id="nota-final" name="nota_final" value="5.5" readonly>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción (min 1, max 1000)</label>
                <textarea id="descripcion" name="descripcion" class="form-control" rows="4" required>{{ old('descripcion') }}</textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="submit-btn">Registrar Habilitación</button>
            </div>
        </form>
    </div>

    @vite('resources/js/scriptFormulario.js')

</body>
</html>