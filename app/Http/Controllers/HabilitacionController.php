<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyecto; 
use App\Models\PracticaTutelada; 
use Illuminate\Support\Facades\Log; // Para registrar errores
use App\Support\HabilProfValidator;
use Carbon\Carbon; // Para manejar fechas


class HabilitacionController extends Controller
{
    /*Almacena una nueva habilitacion profesional.*/
    public function store(Request $request)
    {
        // El RUT ahora viene directamente del campo oculto.
        $rutAlumno = (int)$request->input('rut_alumno');

        // Combinamos año y período en el formato 'YYYY-S'
        $semestreInicio = $request->input('semestre-ano') . '-' . $request->input('semestre-periodo');
        
        //Logica(Proyecto vs Práctica)

        $tipo = $request->input('tipo_habilitacion');

        if ($tipo == 'PrInv' || $tipo == 'PrIng') {
            
            //Esto es para que no salga un mensaje de error gigante cuando se trata de agregar una
            //practica a un alumno que ya la tiene en ese semestre
            $yaExisteProyecto = Proyecto::where('alumno_rut', $rutAlumno)
                                        ->where('semestre_inicio', $semestreInicio)
                                        ->exists(); // exists() es más rápido que first()

            if ($yaExisteProyecto) {
                return back()->with('error', 'Error: Este alumno ya tiene un proyecto registrado para el semestre ' . $semestreInicio . '.');
            }
            // Saca el rut de los profesores
            $profesorGuiaRut = (int)$request->input('profesor_guia_rut');
            $profesorComisionRut = (int)$request->input('profesor_comision_rut');
            
            $profesorCoguiaRut = null;
            if ($request->input('toggle_coguia') == 'si') {
                 // Convertimos a (int) solo si existe
                $profesorCoguiaRut = (int)$request->input('profesor_coguia_rut');
                 // Si es 0 (porque el campo estaba vacío pero se marcó 'si'), lo volvemos null
                if ($profesorCoguiaRut === 0) {
                    $profesorCoguiaRut = null;
                }
            }

            // 1. Validar Guia vs Comision
            if ($profesorGuiaRut === $profesorComisionRut) {
                return back()->with('error', 'Error: El Profesor Guía y el Profesor de Comisión no pueden ser la misma persona.');
            }

            // Valida Co-guia (si existe) contra los otros dos
            if ($profesorCoguiaRut !== null) {
                if ($profesorCoguiaRut === $profesorGuiaRut) {
                    return back()->with('error', 'Error: El Profesor Co-Guía no puede ser el mismo que el Profesor Guía.');
                }
                if ($profesorCoguiaRut === $profesorComisionRut) {
                    return back()->with('error', 'Error: El Profesor Co-Guía no puede ser el mismo que el Profesor de Comisión.');
                }
            }

            $validacionProyecto = HabilProfValidator::validarProyecto($request->all());

            if (!$validacionProyecto['ok']) {
                // Convertimos el array de errores en un string legible
                $errores = implode(' ', \Illuminate\Support\Arr::flatten($validacionProyecto['errors']));
                return back()->with('error', 'Error en datos del proyecto: ' . $errores)->withInput();
            }
            
            $proyectosActivosGuia = Proyecto::where('profesor_guia_rut', $profesorGuiaRut)
                                            ->whereNull('nota_final') // Solo cuenta proyectos no finalizados
                                            ->count();

            if ($proyectosActivosGuia >= 5) {
                return back()->with('error', 'Error: Límite de asignaciones alcanzado.');
            }

            // Guarda el proyecto
            try {
                Proyecto::create([
                    'alumno_rut' => $rutAlumno,
                    'semestre_inicio' => $semestreInicio,
                    'tipo_proyecto' => $tipo, // 'PrInv' o 'PrIng'
                    'descripcion' => $request->input('descripcion'),
                    'fecha_inicio' => Carbon::now(), // se pone la fecha actual
                    'nota_final' => NULL, 
                    'titulo' => $request->input('titulo'),
                    'profesor_guia_rut' => $profesorGuiaRut,
                    'profesor_comision_rut' => $profesorComisionRut,
                    'profesor_coguia_rut' => $profesorCoguiaRut // Será null si no se selecciono o estaba vacio
                ]);
                

            } catch (\Exception $e) {
                // Registrar el error para depuracion
                Log::error('Error al guardar Proyecto: ' . $e->getMessage());
                // Redirigir con un mensaje de error
                return back()->with('error', 'Error real: ' . $e->getMessage());
            }

        } elseif ($tipo == 'PrTut') {
            
            // Obtener el RUT del profesor tutor (reutilizando el campo guía)
            $profesorTutorRut = (int)$request->input('profesor_guia_rut');
            
            //Esto es para que no salga un mensaje de error gigante cuando se trata de agregar una
            //practica a un alumno que ya la tiene en ese semestre
            $yaExistePractica = PracticaTutelada::where('alumno_rut', $rutAlumno)
                                            ->where('semestre_inicio', $semestreInicio)
                                            ->exists();

            if ($yaExistePractica) {
                return back()->with('error', 'Error: Este alumno ya tiene una práctica registrada para el semestre ' . $semestreInicio . '.');
            }
            
            // Inicio de la validacion
            $dataToValidate = [
                'nombre_empresa'       => $request->input('nombre-empresa'),
                'nombre_supervisor'    => $request->input('nombre_supervisor'),
                'descripcion_practica' => $request->input('descripcion_practica'),
                'profesor_tutor_rut'   => $profesorTutorRut,
                'semestre_inicio'      => $semestreInicio,
            ];

            $validationResult = HabilProfValidator::validarPracticaTutelada($dataToValidate);

            if (!$validationResult['ok']) {
                // Si la validación falla, redirigimos con un mensaje de error
                $errorMessage = 'Error de validación';
                
                Log::error('Validación Práctica fallida: ' . $errorMessage);
                return back()->with('error', $errorMessage)->withInput();
            }

            // Logica para PRTUT
            try {
                PracticaTutelada::create([
                    'alumno_rut' => $rutAlumno,
                    'semestre_inicio' => $semestreInicio,
                    'nombre_empresa' => $request->input('nombre-empresa'),
                    'nombre_supervisor' => $request->input('nombre_supervisor'),
                    'profesor_tutor_rut' => $profesorTutorRut,
                    'descripcion' => $request->input('descripcion_practica'),
                    'fecha_inicio' => Carbon::now(),
                    'nota_final' => NULL
                ]);
                Log::info('Intento de registro de Práctica Tutelada (lógica comentada en HabilitacionController).');
                

            } catch (\Exception $e) {
                Log::error('Error al guardar Práctica: ' . $e->getMessage());
                return back()->with('error', 'Error real: ' . $e->getMessage());
            }
        }

        // Si todo sale bien, redirige de vuelta al formulario con un mensaje de exito.
        return redirect('/formulario')->with('success', 'Habilitación registrada con éxito.');
    }
}