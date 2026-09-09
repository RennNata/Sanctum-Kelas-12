<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Aktor extends Model
{
    protected $fillable = [
        'nama_aktor',
        'gender',
        'tanggal_lahir',
        'foto'
    ];

    protected $appends = ['umur'];

    public function getUmurAttribute() {
        if (!$this->tanggal_lahir) {
            return null;
        }

        return Carbon::parse($this->tanggal_lahir)->age;
    }

    public $timestamps = true;

    public function films()
    {
        return $this->belongsToMany(Film::class, 'aktor_film');
    }

}

