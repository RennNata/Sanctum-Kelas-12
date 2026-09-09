<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Aktor;
use Illuminate\Support\Str;
use Exception;

class AktorController extends Controller
{
    public function index () {
        try {
            $aktors = Aktor::latest()->get();
            return response()->json([
                'status' => true,
                'message' => "Data aktor berhasil diambil",
                'data' => $aktors,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request) {
        try {
            $request->validate([
                'nama_aktor' => 'required|string|unique:aktors,nama_aktor',
                'gender' => 'required|in:Laki-laki,Perempuan',
                'tanggal_lahir' => 'required|date',
                'foto' => 'nullable|string',
            ]);
            $aktor = new Aktor();
            $aktor->nama_aktor = $request->nama_aktor;
            $aktor->gender = $request->gender;
            $aktor->tanggal_lahir = $request->tanggal_lahir;
            $aktor->foto = $request->foto;
            $aktor->save();

            return response()->json([
                'status' => true,
                'message' => 'Data aktor berhasil dibuat',
                'data' => $aktor,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id) {
        try {

            $aktor = Aktor::find($id);
            if (!$aktor) {
                return response()->json([
                    'status' => false,
                    'message' => 'Aktor tidak ditemukan',
                ], 404);
            }

            $request->validate([
                'nama_aktor' => 'required|string|unique:aktors,nama_aktor,' . $id,
                'gender' => 'required|in:Laki-laki,Perempuan',
                'tanggal_lahir' => 'required|date',
                'foto' => 'nullable|string',
            ]);
            
            $aktor->nama_aktor = $request->nama_aktor;
            $aktor->gender = $request->gender;
            $aktor->tanggal_lahir = $request->tanggal_lahir;
            $aktor->foto = $request->foto;
            $aktor->save();

            // $aktor->update([
            //     'nama_aktor' => $request->nama_aktor,
            //     'gender' => $request->gender,
            //     'umur' => $request->umur,
            //     'foto' => $request->foto,
            // ]);

            return response()->json([
                'status' => true,
                'message' => 'Data aktor berhasil diperbarui',
                'data' => $aktor,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id) {
        try {
            $aktor = Aktor::find($id);

            if (!$aktor) {
                return response()->json([
                    'status' => false,
                    'message' => 'Aktor tidak ditemukan',
                ], 404);
            }
            $aktor->delete();
            return response()->json([
                'status' => true,
                'message' => 'Data aktor berhasil dihapus',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    } 
}
