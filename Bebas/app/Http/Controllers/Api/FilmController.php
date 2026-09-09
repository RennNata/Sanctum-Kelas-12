<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Film;
use App\Models\Aktor;
use Illuminate\Support\Str;
use Exception;

class FilmController extends Controller
{
    public function index () {
        try {
            $films = Film::with(['genre', 'aktors'])->get();
            return response()->json([
                'status' => true,
                'message' => "Data film berhasil diambil",
                'data' => $films,
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
                'judul' => 'required|string|unique:films,judul',
                'tanggal_rilis' => 'required|date',
                'id_genre' => 'required|integer',
                'deskripsi' => 'required|string',
                'durasi' => 'required|integer',
                'rating' => 'required|numeric|min:0|max:10',
                'sutradara' => 'required|string',
                'poster' => 'nullable|string',
                'id_aktor' => 'required|array',
                'id_aktor.*' => 'exists:aktors,id',
            ]);
            
            $film = new Film();
            $film->judul = $request->judul;
            $film->slug = Str::slug($request->judul) . Str::random(10);
            $film->tanggal_rilis = $request->tanggal_rilis;
            $film->id_genre = $request->id_genre;
            $film->deskripsi = $request->deskripsi;
            $film->durasi = $request->durasi;
            $film->rating = $request->rating;
            $film->sutradara = $request->sutradara;
            $film->poster = $request->poster;
            $film->save();

            $film->aktors()->attach($request->id_aktor);

            return response()->json([
                'status' => true,
                'message' => 'Data film berhasil dibuat',
                'data' => $film->load(['genre', 'aktors'])
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id) {
        try {
            $film = Film::with(['genre', 'aktors'])->find($id);
            if (!$film) {
                return response()->json([
                    'status' => false,
                    'message' => 'Film tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data film berhasil diambil',
                'data' => $film,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id) {
        try {
            $film = Film::find($id);
            if (!$film) {
                return response()->json([
                    'status' => false,
                    'message' => 'Film tidak ditemukan',
                ], 404);
            }

            $request->validate([
                'judul' => 'required|string|unique:films,judul,' . $id,
                'tanggal_rilis' => 'required|date',
                'id_genre' => 'required|integer',
                'deskripsi' => 'required|string',
                'durasi' => 'required|integer',
                'rating' => 'required|numeric|min:0|max:10',
                'sutradara' => 'required|string',
                'poster' => 'nullable|string',
                'id_aktor' => 'required|array',
                'id_aktor.*' => 'exists:aktors,id',
            ]);
            
            $film->judul = $request->judul;
            $film->slug = Str::slug($request->judul) . Str::random(10);
            $film->tanggal_rilis = $request->tanggal_rilis;
            $film->id_genre = $request->id_genre;
            $film->deskripsi = $request->deskripsi;
            $film->durasi = $request->durasi;
            $film->rating = $request->rating;
            $film->sutradara = $request->sutradara;
            $film->poster = $request->poster;
            $film->save();

            $film->aktors()->sync($request->id_aktor);

            return response()->json([
                'status' => true,
                'message' => 'Data film berhasil diperbarui',
                'data' => $film->load(['genre', 'aktors'])
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id) {
        try {
            $film = Film::find($id);
            if (!$film) {
                return response()->json([
                    'status' => false,
                    'message' => 'Film tidak ditemukan',
                ], 404);
            }

            $film->aktors()->detach();
            $film->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data film berhasil dihapus',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
