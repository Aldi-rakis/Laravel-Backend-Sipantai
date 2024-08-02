<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaduanDetail extends Model
{
    use HasFactory;

      /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'user_id','pengaduan_id', 'content','image'
    ];

    
    // public function Pengaduan()
    // {
    //     return $this->hasMany(Pengaduan::class);
    // }


      /**
     * getImageAttribute
     *
     * @param  mixed $image
     * @return void
     */
    public function getImageAttribute($image)
    {
        return url('storage/pengaduan_details/' . $image);
    }
}

