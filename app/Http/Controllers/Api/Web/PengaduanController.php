<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Pengaduan;
use App\Http\Controllers\Controller;
use App\Http\Resources\PengaduanResource;
use Illuminate\Http\Request;

class PengaduanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get pengaduan
        $pengaduan = Pengaduan::with('pengaduan_detail')->when(request()->q, function ($query) {
            $query->where('name', 'like', '%' . request()->q . '%');
        })->latest()->paginate(10);

        //return with Api Resource
        return new PengaduanResource(true, 'List Data Pengaduan', $pengaduan);
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pengaduan = Pengaduan::with('pengaduan_detail')->whereId($id)->first();

        if ($pengaduan) {
            //return success with Api Resource
            return new PengaduanResource(true, 'Detail Data Pengaduan!', $pengaduan);
        }

        //return failed with Api Resource
        return new PengaduanResource(false, 'Detail Data Pengaduan Tidak Ditemukan!', null);
    }


   
}
