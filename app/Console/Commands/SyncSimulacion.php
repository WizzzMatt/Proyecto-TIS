<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SimSyncService;
use Illuminate\Support\Facades\Log;

class SyncSimulacion extends Command
{
    // Limpiamos la firma, ya que syncNotas ahora se encarga de todo
    protected $signature = 'simulacion:sync';
    protected $description = 'Valida y Sincroniza datos desde la BD simulada (alumnos, profesores y notas)';

    public function handle(SimSyncService $service)
    {
        $this.info('Iniciando VALIDACIÓN y Sincronización desde BD simulada...');
        Log::info('[simulacion:sync] Iniciando trabajo programado...');

        try {
            // --- 1. Sincroniza alumnos ---
            $al = $service->syncAlumnos();
            $this.info("Alumnos validados y sincronizados: {$al}");

            // --- 2. Sincroniza profesores ---
            $pr = $service->syncProfesores();
            $this.info("Profesores validados y sincronizados: {$pr}");

            // --- 3. Sincroniza notas ---
            // (Esto solo funcionará cuando R2 esté implementado y existan habilitaciones)
            $no = $service->syncNotas();
            $this.info("Notas validadas y sincronizadas: {$no}");

            // --- 4. Final ---
            $this.info('Sincronización OK');
            Log::info('[simulacion:sync] OK', [
                'alumnos' => $al,
                'profesores' => $pr,
                'notas' => $no
            ]);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this.error('Error en sincronización: '.$e->getMessage());
            Log::error('[simulacion:sync] ERROR', ['ex' => $e]);
            return self::FAILURE;
        }
    }
}