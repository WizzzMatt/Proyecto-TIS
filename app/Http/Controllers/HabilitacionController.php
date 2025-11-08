<?php

namespace App\Http\Controllers;

// --- PASO 1.1: IMPORTAMOS LOS MODELOS QUE SÍ EXISTEN ---
// Estos modelos apuntan a tus tablas 'alumnos' y 'profesores'
use App\Models\Alumno;     // [cite: 25]
use App\Models\Profesor;   // [cite: 29]

// Estos modelos apuntan a las dos tablas de habilitación
use App\Models\Proyecto;           // [cite: 31] (para Pring/Prinv)
use App\Models\PracticaTutelada;   // [cite: 28] (para PrTut)

// Importamos el Request que crearemos en el Paso 3
use App\Http\Requests\StoreHabilitacionRequest; 

// Clases de Laravel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception; // Para manejar errores

class HabilitacionController extends Controller
{
    /**
     * Muestra el formulario para crear una nueva habilitación.
     * R2.12: Desplegar listado de alumnos.
     * R2.13.1 / R2.14.1: Desplegar registro de profesores.
     */
    public function create()
    {
        // Usamos tus modelos Alumno.php y Profesor.php [cite: 25, 29]
        $alumnos = Alumno::all(); 
        $profesores = Profesor::all(); 
        // Retornamos la vista (que crearemos en Paso 4)
        return view('habilitacion.create', compact('alumnos', 'profesores'));
    }

    /**
     * Almacena la nueva habilitación en la base de datos.
     */
    public function store(StoreHabilitacionRequest $request)
    {
        // La validación principal ya fue hecha por StoreHabilitacionRequest (Paso 3)

        try {
            // --- PASO 1.2: PREPARAMOS LOS DATOS COMUNES ---
            // (Estos campos existen en AMBAS tablas: 'proyectos' y 'practica_tutelada') 
            
            $semestre = $request->semestre_inicio; // Formato "AAAA-Y" [cite: 34]
            
            // R1.12: ID (BigInteger) = RUT (8) + Semestre (AAAAY) [cite: 61]
            $rut_padded = str_pad($request->rut_alumno, 8, '0', STR_PAD_LEFT);
            $semestre_formatted = str_replace('-', '', $semestre);
            $id_habilitacion = (int)($rut_padded . $semestre_formatted);
            
            // R1.12.1: Registrar día exacto (Formato R1.7: DD/MM/AAAA) [cite: 63, 39]
            $fecha_inicio = Carbon::now()->format('d/m/Y'); 

            // R2.15: Nota_Final es nula al inicio
            $nota_final = null;
            $fecha_nota = null;

            // Array con datos comunes
            $common_data = [
                'ID_Habilitacion' => $id_habilitacion,
                'RUT_Alumno' => $request->rut_alumno,
                'Semestre_Inicio' => $semestre,
                'Fecha_Inicio' => $fecha_inicio,
                'Descripcion' => $request->descripcion,
                'Nota_Final' => $nota_final,
                'Fecha_Nota' => $fecha_nota,
            ];

            // --- PASO 1.3: LÓGICA DE DECISIÓN (EL NÚCLEO) ---
            // Aquí decidimos a qué tabla/modelo enviar los datos
            
            DB::beginTransaction();
            
            if ($request->tipo_habilitacion == 'Pring' || $request->tipo_habilitacion == 'Prinv') {
                
                // R2.13.3: Es Pring/Prinv, usamos el modelo 'Proyecto' [cite: 31, 126]
                // Los campos (Titulo, RUT_Profesor_G, etc.) coinciden con tu migración '...create_proyectos_table.php' 
                
                Proyecto::create($common_data + [
                    'Titulo' => $request->titulo,
                    'RUT_Profesor_G' => $request->rut_profesor_g,
                    'RUT_Profesor_Comision' => $request->rut_profesor_comision,
                    'RUT_Profesor_CG' => $request->rut_profesor_cg, // Puede ser NULL
                ]);

            } elseif ($request->tipo_habilitacion == 'PrTut') {
                
                // R2.14.2: Es PrTut, usamos el modelo 'PracticaTutelada' [cite: 28, 150]
                // Los campos (RUT_Profesor_Tutor, Nombre_Empresa, etc.) coinciden con tu migración '...create_practica_tutelada_table.php' 
                
                PracticaTutelada::create($common_data + [
                    'RUT_Profesor_Tutor' => $request->rut_profesor_tutor,
                    'Nombre_Empresa' => $request->nombre_empresa,
                    'Nombre_Supervisor' => $request->nombre_supervisor,
                ]);
            }
            
            DB::commit();

            // R2.16: Mensaje de éxito [cite: 158]
            return redirect()->route('habilitacion.create')
                         ->with('success', 'Se ha ingresado exitosamente la Habilitación Profesional del alumno.');
        
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Si algo falla (ej. R2.13.1.1.1, límite de profesores [cite: 134]), mostramos el error
            return back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()])->withInput();
        }
    }
}