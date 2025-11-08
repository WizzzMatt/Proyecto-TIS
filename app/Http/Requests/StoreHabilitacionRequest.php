<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHabilitacionRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return true; // Permitimos que la validación se ejecute
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     * (Basado en R1.1, R1.5, R2.1, R2.2, R2.5, R2.7, R2.8, etc.)
     */
    public function rules(): array
    {
        // R2.10: Campos obligatorios base [cite: 120]
        $rules = [
            // R1.1: RUT Alumno [cite: 80] (usamos 'exists' para asegurar que el alumno esté en tu tabla 'alumnos')
            'rut_alumno' => 'required|integer|min:1000000|max:99999999|exists:alumnos,RUT_Alumno',
            
            // R1.5: Semestre Inicio (AAAA-Y, 2025-2045, Y=1 o 2) [cite: 34, 35, 36]
            'semestre_inicio' => ['required', 'string', 'regex:/^(202[5-9]|203[0-9]|204[0-5])-(1|2)$/'],
            
            // R2.1: Tipo Habilitación [cite: 99]
            'tipo_habilitacion' => 'required|string|in:Pring,Prinv,PrTut',
            
            // R2.2: Descripción (100-1000 caracteres) [cite: 101]
            'descripcion' => 'required|string|min:100|max:1000',
        ];

        $tipo = $this->input('tipo_habilitacion');

        // R2.13: Reglas para "Pring" o "Prinv" [cite: 126]
        if ($tipo == 'Pring' || $tipo == 'Prinv') {
            $rules += [
                // R2.5: Título (10-80 chars, solo letras y espacios) [cite: 106, 107]
                'titulo' => 'required|string|min:10|max:80|regex:/^[a-zA-Z\s]+$/',
                
                // R2.3: RUT Profesor Guía (validamos que exista en 'profesores') [cite: 102]
                'rut_profesor_g' => 'required|integer|min:1000000|max:99999999|exists:profesores,RUT_Profesor',
                
                // R2.4: RUT Profesor Comisión [cite: 104] (R2.13.1.1.1: no duplicado) [cite: 131]
                'rut_profesor_comision' => 'required|integer|min:1000000|max:99999999|exists:profesores,RUT_Profesor|different:rut_profesor_g',
                
                // R2.6: RUT Profesor Co-Guía (Opcional) [cite: 110]
                'rut_profesor_cg' => 'nullable|integer|min:1000000|max:99999999|exists:profesores,RUT_Profesor|different:rut_profesor_g|different:rut_profesor_comision',
            ];
        } 
        // R2.14: Reglas para "PrTut" [cite: 150]
        elseif ($tipo == 'PrTut') {
            $rules += [
                // R2.7: Nombre Empresa (1-50 chars, solo letras) [cite: 112]
                'nombre_empresa' => 'required|string|min:1|max:50|regex:/^[a-zA-Z\s]+$/',
                
                // R2.8: Nombre Supervisor (13-100 chars, formato nombre) [cite: 113, 114, 115]
                'nombre_supervisor' => ['required', 'string', 'min:13', 'max:100', 'regex:/^([A-ZÁÉÍÓÚÑ][a-záéíóúñ]+([\s\-][A-ZÁÉÍÓÚÑ][a-záéíóúñ]+)*)$/'],
                
                // R2.9: RUT Profesor Tutor [cite: 116]
                'rut_profesor_tutor' => 'required|integer|min:1000000|max:99999999|exists:profesores,RUT_Profesor',
            ];
        }
        
        return $rules;
    }

    /**
     * Mensajes de error personalizados (R2.13, R2.14).
     */
    public function messages(): array
    {
        return [
            // "Los datos ingresados no son válidos"
            'descripcion.min' => 'Los datos ingresados no son válidos: La descripción debe tener al menos 100 caracteres.',
            'titulo.min' => 'Los datos ingresados no son válidos: El título debe tener al menos 10 caracteres.',
            'titulo.regex' => 'Los datos ingresados no son válidos: El título solo puede contener letras y espacios.',
            'nombre_empresa.regex' => 'Los datos ingresados no son válidos: El nombre de empresa solo puede contener letras.',
            'nombre_supervisor.min' => 'Los datos ingresados no son válidos: El nombre de supervisor debe tener al menos 13 caracteres.',
            'nombre_supervisor.regex' => 'Los datos ingresados no son válidos: El nombre de supervisor debe tener formato de Nombre Propio (Ej: Juan Perez).',
            'semestre_inicio.regex' => 'El formato del semestre no es válido (Debe ser AAAA-Y, ej: 2025-1).',
            'rut_profesor_comision.different' => 'El profesor de comisión no puede ser el mismo que el profesor guía.',
        ];
    }
}
