<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticaTutelada extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     * (Necesario porque el nombre no sigue la convención estándar de Laravel)
     *
     * @var string
     */
    protected $table = 'practica_tutelada';

    /**
     * Indica si la clave primaria es autoincremental.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     * (Lista blanca para el método create())
     *
     * @var array
     */
    protected $fillable = [
        'alumno_rut',
        'semestre_inicio',
        'descripcion',
        'fecha_inicio',
        'nota_final',
        'fecha_nota',
        'nombre_empresa',
        'nombre_supervisor',
        'profesor_tutor_rut'
    ];

    /**
     * Obtiene el alumno al que pertenece la práctica.
     */
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_rut', 'rut_alumno');
    }

    /**
     * Obtiene el profesor tutor de la práctica.
     */
    public function profesorTutor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_tutor_rut', 'rut_profesor');
    }
}