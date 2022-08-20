<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cars extends Model
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

    public function users()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
}
