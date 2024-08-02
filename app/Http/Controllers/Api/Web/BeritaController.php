<?php

namespace App\Http\Controllers\Api\web;

use App\Http\Controllers\Controller;
use App\Http\Resources\BeritaResource;
use App\Models\Berita;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    //
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         //get berita
         $berita = Berita::when(request()->q, function ($berita) {
            $berita = $berita->where('name', 'like', '%' . request()->q . '%');
        })->latest()->paginate(10);

        //return with Api Resource
        return new BeritaResource(true, 'List Data Tanggapan', $berita);
    }

 /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function show($id)
   {
       $berita = Berita::whereId($id)->first();

       
       if($berita) {
           //return success with Api Resource
           return new BeritaResource(true, 'Detail Data Berita!', $berita);
       }

       //return failed with Api Resource
       return new BeritaResource(false, 'Detail Data Berita Tidak Ditemukan!', null);
   }

}
