<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use HasFactory,SoftDeletes;

    // Definir la tabla asociada
    protected $table = 'Personas';

    // Definir la clave primaria
    protected $primaryKey = 'IdPersona';

    // Si la clave primaria no es un entero auto-incremental
    public $incrementing = true;

    // Si la clave primaria es un entero
    protected $keyType = 'int';

    // Si la tabla tiene timestamps (created_at y updated_at)
    public $timestamps = true;
    const ESTADO_ACTIVO = 'A';
    const ESTADO_INACTIVO = 'I';

    // Definir los atributos que son asignables en masa
    protected $fillable = [
        'CUIT',
        'Apellido',
        'Nombre',
        'Nacionalidad',
        'Actividad',
        'Domicilio',
        'Email',
        'Telefono',
        'Movil',
        'SituacionFiscal',
        'FNacimiento',
        'DNI',
        'Alias',
        'CodPostal',
        'PEP',
        'EstadoPersona',
    ];
    public static function esEstadoValido($estado)
    {
        return in_array($estado, [
            self::ESTADO_ACTIVO,
            self::ESTADO_INACTIVO,
        ]);
    }

    public function setEstadoPersonaAttribute($value)
    {
        if (!self::esEstadoValido($value)) {
            throw new \InvalidArgumentException("EstadoPersona no es válido.");
        }
        $this->attributes['EstadoPersona'] = $value;
    }
    
}
