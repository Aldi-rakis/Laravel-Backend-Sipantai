<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Berita extends Model
{
    use HasFactory;
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'image',  'waktu_upload'
    ];


    /**
     * getImageAttribute
     *
     * @param  mixed $image
     * @return void
     */
    public function getImageAttribute($image)
    {
        return url('storage/berita/' . $image);
    }

    protected $dates = ['waktu_upload'];

    public function getWaktuUploadAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }
}
