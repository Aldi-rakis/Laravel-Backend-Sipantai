<?php

namespace App\Http\Controllers\Api\Admin;


use App\Http\Resources\PengaduanResource;
use App\Models\Pengaduan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Validator;


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
        })->latest()->paginate(5);
        //return with Api Resource
        return new PengaduanResource(true, 'List Data Pengaduan', $pengaduan);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'    => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'content'     => 'required|:pengaduans',
            'title'     => 'required|:pengaduans',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/pengaduan', $image->hashName());

        //create pengaduan
        $pengaduan = Pengaduan::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
            'user_id'   => auth()->guard('api')->user()->id,
            // 'content' => Str::slug($request->content, '-'),
        ]);

        if ($pengaduan) {
            //return success with Api Resource
            return new PengaduanResource(true, 'Data pengaduan Berhasil Disimpan!', $pengaduan);
        }

        //return failed with Api Resource
        return new PengaduanResource(false, 'Data pengaduan Gagal Disimpan!', null);
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



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pengaduan  $pengaduan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pengaduan $pengaduan)

    {

        // Check if the authenticated user is the owner of the pengaduan
        if ($pengaduan->user_id !== auth()->guard('api')->user()->id) {
            return response()->json(['error' => 'Anda Tidak di izinkan pengaduan orang lain.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'image'    => 'nullable|image|mimes:jpeg,jpg,png|max:2000',
            'content'  => 'required|unique:pengaduans,content,' . $pengaduan->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Prepare data to be updated
        $dataToUpdate = [
            'content' => $request->content,
            'user_id' => auth()->guard('api')->user()->id,
        ];

        // Check if there is a new image
        if ($request->file('image')) {
            // Remove old image if it exists
            if ($pengaduan->image) {
                Storage::disk('local')->delete('public/pengaduan/' . basename($pengaduan->image));
            }

            // Upload new image
            $image = $request->file('image');
            $image->storeAs('public/pengaduan', $image->hashName());

            // Add new image to update data
            $dataToUpdate['image'] = $image->hashName();
        }

        // Update Pengaduan
        $updateSuccessful = $pengaduan->update($dataToUpdate);

        if ($updateSuccessful) {
            // Return success with Api Resource
            return new PengaduanResource(true, 'Data pengaduan Berhasil Diupdate!', $pengaduan);
        }

        // Return failed with Api Resource
        return new PengaduanResource(false, 'Data pengaduan Gagal Diupdate!', null);
    }

      /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //find place by ID
        $place = Pengaduan::findOrFail($id);

        //loop image from relationship
        foreach($place->Pengaduan_detail()->get() as $image) {
            
            //remove image
            Storage::disk('local')->delete('public/pengaduan/'.basename($image->image));

            //remove child relation
            $image->delete();

        }

        if($place->delete()) {
            //return success with Api Resource
            return new PengaduanResource(true, 'Data Pengaduan Berhasil Dihapus!', null);
        }

        //return failed with Api Resource
        return new PengaduanResource(false, 'Data Pengaduan Gagal Dihapus!', null);
    }
}
