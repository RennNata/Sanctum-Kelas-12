<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    protected $fillable = [
        'judul',
        'slug',
        'tanggal_rilis',
        'id_genre',
        'id_aktor',
        'deskripsi',
        'durasi',
        'rating',
        'sutradara',
        'poster'
    ];

    public $timestamps = true;

    public function genre()
    {
        return $this->belongsTo(Genre::class, 'id_genre');
    }

    public function aktors()
    {
        return $this->belongsToMany(Aktor::class, 'aktor_film', 'id_film', 'id_aktor');
    }
}
