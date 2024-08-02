<?php

namespace App\Http\Controllers\Api\Admin;





use App\Models\Berita;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BeritaResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:1000',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Upload image if exists
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/berita', $image->hashName());
            $imagePath = $image->hashName();
        }

        // Create berita
        $berita = Berita::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

            // Update waktu_upload dengan tanggal dari created_at
        $berita->waktu_upload = $berita->created_at->format('Y-m-d');
        $berita->save();


        return new BeritaResource(true, 'Data Respon pengaduan Berhasil disimpan!', $berita);
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




/**
 * Update the specified resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \App\Models\Berita  $berita
 * @return \Illuminate\Http\Response
 */
public function update(Request $request, Berita $berita)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,jpg,png|max:5000',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Update the berita attributes
    $berita->update([
        'title' => $request->title,
        'description' => $request->description,
    ]);

    // Check if request has file
    if ($request->hasFile('image')) {
        // Delete the old image from storage
        if ($berita->image) {
            Storage::disk('local')->delete('public/berita/' . $berita->image);
        }

        // Get the new image from request
        $image = $request->file('image');

        // Move the new image to storage folder
        $image->storeAs('public/berita', $image->hashName());

        // Update the berita's image attribute
        $berita->update([
            'image' => $image->hashName(),
        ]);
    }

    // Return success with Api Resource
    return new BeritaResource(true, 'Data Berita Berhasil Diupdate!', $berita);

    
}


public function destroy($id)
{
    // Cari berita berdasarkan ID
    $berita = Berita::find($id);

    if (!$berita) {
        // Kembalikan respon gagal jika berita tidak ditemukan
        return new BeritaResource(false, 'Data Berita Tidak Ditemukan!', null);
    }

    try {
        // Periksa apakah gambar ada dan hapus jika ada
        if ($berita->image && Storage::disk('local')->exists('public/berita/' . $berita->image)) {
            Storage::disk('local')->delete('public/berita/' . $berita->image);
        }

        // Coba hapus data berita dari database
        if ($berita->delete()) {
            // Kembalikan respon sukses
            return new BeritaResource(true, 'Data Berita Berhasil Dihapus!', null);
        } else {
            // Kembalikan respon gagal jika penghapusan gagal
            return new BeritaResource(false, 'Data Berita Gagal Dihapus!', null);
        }
    } catch (\Exception $e) {
        // Kembalikan respon gagal dengan pesan kesalahan
        return new BeritaResource(false, 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage(), null);
    }
}


}
