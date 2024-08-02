<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasFactory;

    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'image', 'content','title'
    ];

    public function getImageAttribute($image)
    {
        return url('storage/pengaduan/' . $image);
    }


    public function Pengaduan_detail()
    {
        return $this->hasMany(PengaduanDetail::class);
    }
}
