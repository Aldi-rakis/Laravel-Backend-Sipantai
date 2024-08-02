<?php

namespace App\Http\Controllers\Api\Admin;


use App\Http\Resources\PengaduanDetailResource;
use App\Models\PengaduanDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Validator;


class PengaduanDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get pengaduan
        $pengaduanDetail = PengaduanDetail::when(request()->q, function ($pengaduanDetail) {
            $pengaduanDetail = $pengaduanDetail->where('name', 'like', '%' . request()->q . '%');
        })->latest()->paginate(5);

        //return with Api Resource
        return new PengaduanDetailResource(true, 'List Data Tanggapan', $pengaduanDetail);
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
            'pengaduan_id' => 'required|exists:pengaduans,id',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Upload image if exists
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/pengaduan_details', $image->hashName());
            $imagePath = $image->hashName();
        }

        // Create PengaduanDetail
        $pengaduanDetail = PengaduanDetail::create([
            'user_id' => auth()->guard('api')->user()->id,
            'pengaduan_id' => $request->pengaduan_id,
            'content' => $request->content,
            'image' => $imagePath,
        ]);

        return new PengaduanDetailResource(true, 'Data Respon pengaduan Berhasil disimpan!', $pengaduanDetail);
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pengaduan  $pengaduanDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PengaduanDetail $pengaduanDetail)

    {

        // Check if the authenticated user is the owner of the pengaduan
        if ($pengaduanDetail->user_id !== auth()->guard('api')->user()->id) {
            return response()->json(['error' => 'Anda Tidak di izinkan balas  .'], 403);
        }

        $validator = Validator::make($request->all(), [
            'image'    => 'nullable|image|mimes:jpeg,jpg,png|max:2000',
            'content'  => 'required|unique:pengaduans,content,' . $pengaduanDetail->id,
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
            if ($pengaduanDetail->image) {
                Storage::disk('local')->delete('public/pengaduan/' . basename($pengaduanDetail->image));
            }

            // Upload new image
            $image = $request->file('image');
            $image->storeAs('public/pengaduan', $image->hashName());

            // Add new image to update data
            $dataToUpdate['image'] = $image->hashName();
        }

        // Update Pengaduan
        $updateSuccessful = $pengaduanDetail->update($dataToUpdate);

        if ($updateSuccessful) {
            // Return success with Api Resource
            return new PengaduanDetailResource(true, 'Data pengaduan Berhasil Diupdate!', $pengaduanDetail);
        }

        // Return failed with Api Resource
        return new PengaduanDetailResource(false, 'Data pengaduan Gagal Diupdate!', null);
    }
}
