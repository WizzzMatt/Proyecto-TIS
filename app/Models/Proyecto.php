<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    /**

     * @var bool
     */
    public $incrementing = false;

    /**

     * @var array
     */
    protected $fillable = [
        'alumno_rut',
        'semestre_inicio',
        'tipo_proyecto',
        'descripcion',
        'fecha_inicio',
        'nota_final',
        'fecha_nota',
        'titulo',
        'profesor_guia_rut',
        'profesor_comision_rut',
        'profesor_coguia_rut'
    ];

    /**
     * Obtiene el alumno al que pertenece el proyecto.
     */
    public function alumno()
    {
        // Un Proyecto pertenece a un Alumno
        return $this->belongsTo(Alumno::class, 'alumno_rut', 'rut_alumno');
    }

    /**
     * Obtiene el profesor guía del proyecto.
     */
    public function profesorGuia()
    {
        return $this->belongsTo(Profesor::class, 'profesor_guia_rut', 'rut_profesor');
    }

    /**
     * Obtiene el profesor de comisión del proyecto.
     */
    public function profesorComision()
    {
        return $this->belongsTo(Profesor::class, 'profesor_comision_rut', 'rut_profesor');
    }

    /**
     * Obtiene el profesor co-guía (opcional) del proyecto.
     */
    public function profesorCoguia()
    {
        return $this->belongsTo(Profesor::class, 'profesor_coguia_rut', 'rut_profesor');
    }
}