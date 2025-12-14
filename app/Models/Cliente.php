<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
     public static function rules($id = null)
    {
        return [
            'nombre'    => 'required|string|max:255',
            'telefono'  => 'required|unique:clientes,telefono,' . $id . ',id',
            'direccion' => 'required|string',
            'credito'   => 'required|numeric|min:0',
            'correo'    => 'nullable|email',
        ];
    }

    protected $fillable = [
        'nombre',
        'telefono',
        'correo',
        'credito',
        'direccion'
    ];

    public function creditos()
    {
        return $this->hasMany(Creditoventa::class, 'id_cliente');
    }
}
