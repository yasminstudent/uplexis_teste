<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carros extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'nome_veiculo', 'link', 'ano', 'combustivel', 'portas', 'quilometragem', 'cambio', 'cor'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
