<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Habilitación Profesional</title>
    @vite('resources/css/stylePrincipal.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link type="text/css" rel="stylesheet" href="css/style.css"/>
</head>
<body>

    <main class="main-container">
        <h1>Gestión de Habilitación Profesional</h1>
        
        <nav class="menu-container">
            <a href="formulario.html" class="menu-button" id="btn-ingreso">
                <div class="icon-wrapper">
                    <i class="fa-solid fa-file-circle-plus"></i>
                </div>
                <span>Ingreso de datos Habilitación Profesional</span>
            </a>

            <a href="{{ route('habilitacion.create') }}" class="menu-button" id="btn-ingreso"></a>
                <div class="icon-wrapper">
                    <i class="fa-solid fa-file-pen"></i>
                </div>
                <span>Editar datos de Habilitación Profesional</span>
            </a>

            <a href="listado.html" class="menu-button" id="btn-listado">
                <div class="icon-wrapper">
                    <i class="fa-solid fa-table-list"></i>
                </div>
                <span>Crear listado de registros Habilitación Profesional</span>
            </a>
        </nav>
    </main>

    @vite('resources/js/scriptPrincipal.js')
<script type="text/javascript" src="js/script.js"></script>
</body>
</html>