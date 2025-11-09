<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; // ¡IMPORTANTE! Importamos el Validador
use App\Models\SimAlumno;
use App\Models\SimProfesor;
use App\Models\SimNota;
use App\Models\Proyecto;
use App\Models\PracticaTutelada;

class SimSyncService
{
    /**
     * Carga y VALIDA los alumnos (R1.1, R1.2, R1.5)
     */
    public function syncAlumnos(): int
    {
        Log::info('[SYNC] Iniciando validación y sincronización de alumnos...');
        $totalProcesados = 0;
        $totalValidos = 0;
        $reglas = $this->getReglasAlumno(); // Traemos las reglas de R1

        try {
            // Leemos la BD de simulación en trozos (chunks)
            SimAlumno::query()->orderBy('rut')->chunk(500, function ($chunk) use (&$totalProcesados, &$totalValidos, $reglas) {
                
                $filasValidasParaInsertar = [];
                $totalProcesados += $chunk->count();

                foreach ($chunk as $alumnoSimulado) {
                    // 1. Convertimos el modelo a un array simple
                    $datos = $alumnoSimulado->toArray();

                    // 2. VALIDAMOS los datos contra las reglas de R1
                    $validator = Validator::make($datos, $reglas);

                    if ($validator->fails()) {
                        // Si la API nos manda basura, lo registramos y lo saltamos
                        Log::warning("[SYNC] Alumno inválido (RUT: {$datos['rut']}). Saltando.", $validator->errors()->toArray());
                        continue; // Pasa al siguiente alumno
                    }

                    // 3. Si es válido, lo preparamos para el 'upsert'
                    $filasValidasParaInsertar[] = [
                        'rut_alumno'    => $datos['rut'],
                        'nombre_alumno' => $datos['nombre'],
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                    $totalValidos++;
                }

                // 4. Insertamos/Actualizamos el chunk de datos válidos
                if (!empty($filasValidasParaInsertar)) {
                    DB::table('alumnos')->upsert(
                        $filasValidasParaInsertar,
                        ['rut_alumno'], // Si el RUT ya existe...
                        ['nombre_alumno', 'updated_at'] // ...actualiza estos campos
                    );
                }
            });

            Log::info("[SYNC] Sincronización de alumnos completada. Total: $totalValidos / $totalProcesados válidos.");
            return $totalValidos;

        } catch (\Throwable $e) {
            Log::error('[SYNC] Error en sincronización de alumnos', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Carga y VALIDA los profesores (R1.3, R1.4)
     */
    public function syncProfesores(): int
    {
        Log::info('[SYNC] Iniciando validación y sincronización de profesores...');
        $totalProcesados = 0;
        $totalValidos = 0;
        $reglas = $this->getReglasProfesor(); // Traemos las reglas de R1

        try {
            SimProfesor::query()->orderBy('rut')->chunk(500, function ($chunk) use (&$totalProcesados, &$totalValidos, $reglas) {
                
                $filasValidasParaInsertar = [];
                $totalProcesados += $chunk->count();

                foreach ($chunk as $profesorSimulado) {
                    $datos = $profesorSimulado->toArray();
                    $validator = Validator::make($datos, $reglas);

                    if ($validator->fails()) {
                        Log::warning("[SYNC] Profesor inválido (RUT: {$datos['rut']}). Saltando.", $validator->errors()->toArray());
                        continue;
                    }

                    $filasValidasParaInsertar[] = [
                        'rut_profesor'    => $datos['rut'],
                        'nombre_profesor' => $datos['nombre'],
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                    $totalValidos++;
                }

                if (!empty($filasValidasParaInsertar)) {
                    DB::table('profesores')->upsert(
                        $filasValidasParaInsertar,
                        ['rut_profesor'],
                        ['nombre_profesor', 'updated_at']
                    );
                }
            });

            Log::info("[SYNC] Sincronización de profesores completada. Total: $totalValidos / $totalProcesados válidos.");
            return $totalValidos;
            
        } catch (\Throwable $e) {
            Log::error('[SYNC] Error en sincronización de profesores', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Carga y VALIDA las notas (R1.6, R1.8)
     */
    public function syncNotas(): int
    {
        Log::info('[SYNC] Iniciando validación y sincronización de notas...');
        $totalProcesados = 0;
        $totalValidos = 0;
        $reglas = $this->getReglasNota(); // Traemos las reglas de R1

        try {
            SimNota::query()->orderBy('rut_alumno')->chunk(500, function ($chunk) use (&$totalProcesados, &$totalValidos, $reglas) {
                
                $totalProcesados += $chunk->count();

                foreach ($chunk as $notaSimulada) {
                    $datos = $notaSimulada->toArray();
                    $validator = Validator::make($datos, $reglas);

                    if ($validator->fails()) {
                        Log::warning("[SYNC] Nota inválida (RUT: {$datos['rut_alumno']}). Saltando.", $validator->errors()->toArray());
                        continue;
                    }

                    $datosNota = [
                        'nota_final' => $datos['nota_final'],
                        'fecha_nota' => $datos['fecha_nota'], // Laravel convertirá el formato
                        'updated_at' => now(),
                    ];

                    // Actualizamos Proyectos
                    $actualizado = Proyecto::where('alumno_rut', $datos['rut_alumno'])->update($datosNota);
                    // Si no, actualizamos Prácticas
                    if ($actualizado == 0) {
                        PracticaTutelada::where('alumno_rut', $datos['rut_alumno'])->update($datosNota);
                    }
                    $totalValidos++;
                }
            });

            Log::info("[SYNC] Sincronización de notas completada. Total: $totalValidos / $totalProcesados válidas.");
            return $totalValidos;

        } catch (\Throwable $e) {
            Log::error('[SYNC] Error en sincronización de notas', ['error' => $e->getMessage()]);
            return 0;
        }
    }
    
    /*
     * Esta función está deprecada. La lógica de 'syncNotas' es más limpia y
     * la de 'semestre_inscrito' debe manejarse al CREAR la habilitación (en R2),
     * no en la carga de datos.
     */
    public function syncHabilitacionesCampos(): array
    {
        Log::info('[SYNC] (DEPRECADO) syncHabilitacionesCampos no hizo nada.');
        return ['proyectos' => 0, 'practicas' => 0];
    }


    // --- FUNCIONES HELPER CON LAS REGLAS DE R1 ---

    private function getReglasAlumno(): array
    {
        // Validaciones de R1.1, R1.2, R1.5
        return [
            'rut' => 'required|integer|digits_between:7,8', // R1.1
            'nombre' => 'required|string|min:13|max:100', // R1.2 (Simplificado)
            'semestre_inscrito' => [ // R1.5
                'required',
                'string',
                'size:6',
                'regex:/^(20(2[5-9]|3[0-9]|4[0-5]))-(1|2)$/' // Formato AAAA-Y y rangos
            ],
        ];
    }

    private function getReglasProfesor(): array
    {
        // Validaciones de R1.3, R1.4
        return [
            'rut' => 'required|integer|digits_between:7,8', // R1.3
            'nombre' => 'required|string|min:13|max:100', // R1.4 (Simplificado)
        ];
    }

    private function getReglasNota(): array
    {
        // Validaciones de R1.1 (para rut), R1.6, R1.8
        return [
            'rut_alumno' => 'required|integer|digits_between:7,8', // R1.1
            'nota_final' => [ // R1.6
                'required',
                'numeric',
                'between:1.0,7.0',
                'regex:/^\d\.\d$/' // Asegura 1 solo decimal
            ],
            'fecha_nota' => 'required|date', // R1.8 (Simplicado, asumimos que la API la manda en formato Y-m-d)
        ];
    }
}