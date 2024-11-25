<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Caja extends Model
{
    use HasFactory, SoftDeletes;

    const ESTADO_ACTIVO = 'A';
    const ESTADO_INACTIVO = 'I';

    // Definir la tabla asociada
    protected $table = 'Cajas';

    // Definir la clave primaria
    protected $primaryKey = 'IdCaja';

    // Si la clave primaria no es un entero auto-incremental
    public $incrementing = true;

    // Si la clave primaria es un entero
    protected $keyType = 'int';

    // Si la tabla tiene timestamps (created_at y updated_at)
    public $timestamps = true;

    // Definir los atributos que son asignables en masa
    protected $fillable = [
        'NumeroCaja',
        'Tamaño',
        'Ubicacion',
        'Fila',
        'Columna',
        'Observaciones',
        'EstadoCaja',
    ];
    
    public static function esEstadoValido($estado)
    {
        return in_array($estado, [
            self::ESTADO_ACTIVO,
            self::ESTADO_INACTIVO,
        ]);
    }

    public function setEstadoAttribute($value)
    {
        if (!self::esEstadoValido($value)) {
            throw new \InvalidArgumentException("EstadoCaja no es válido.");
        }
        $this->attributes['EstadoCaja'] = $value;
    }
}
