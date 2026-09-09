<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PublicController extends Controller
{
    public function films() {
        
        try {
            $films = DB::table('films')
                ->join('genres', 'films.id_genre', '=', 'genres.id')
                ->select(
                    'films.id',
                    'films.judul',
                    'films.slug',
                    'films.tanggal_rilis',
                    'films.durasi',
                    'films.rating',
                    'films.sutradara',
                    'films.poster',
                    'genres.nama_genre'
                )
                ->orderBy('films.id', 'desc')
                ->paginate(10);
            
            return response()->json([
              'status' => 'success',
              'message' => 'Data film berhasil diambil',
              'data' => $films  
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);

        }

    }

    public function detailFilm($id) {

        try {
            $film = DB::table('films')
                ->join('genres', 'films.id_genre', '=', 'genres.id')
                ->select(
                    'films.*',
                    'genres.nama_genre'
                )
                ->where('films.id', $id)
                ->first();

                if (!$film) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Film tidak ditemukan'
                    ], 404);
                }

                $aktors = DB::table('aktor_film')
                    ->join('aktors', 'aktor_film.id_aktor', '=', 'aktors.id')
                    ->where('aktor_film.id_film', $id)
                    ->select(
                        'aktors.id',
                        'aktors.nama_aktor'
                    )
                    ->get();

                return response()->json([
                    'status' => true,
                    'message' => 'Detail film berhasil diambil',
                    'film' => $film,
                    'aktor' => $aktors
                ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);

        }

    }

    public function genres() {

        try {

            $genres = DB::table('genres')
                ->select(
                    'id',
                    'nama_genre',
                    'slug'
                )
                ->orderBy('nama_genre', 'asc')
                ->paginate(10);

            return response()->json([
                'status' => true,
                'message' => 'Data genre berhasil diambil',
                'data' => $genres
            ], 200);

        } catch(Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);

        }

    }

    public function filmByGenre($id) {
        try {
            $genre = DB::table('genres')
            ->where('id', $id)
            ->first();

            if (!$genre) {
                return response()->json([
                    'status' => false,
                    'message' => 'Genre tidak ada'
                ], 404);
            }

            $films = DB::table('films')
                ->join('genres', 'films.id_genre', '=', 'genres.id')
                ->where('genres.id', $id)
                ->select(
                    'films.id',
                    'films.judul',
                    'genres.nama_genre'
                )
                ->paginate(10);

            return response()->json([
                'status' => true,
                'genre' => $genre,
                'film' => $films
            ], 200);
        } catch(Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);

        }

    }

    public function aktors() {
        try {

            $aktors = DB::table('aktors')
                ->select(
                    'aktors.id',
                    'aktors.nama_aktor',
                    'aktors.gender',
                    'aktors.tanggal_lahir',
                )
                ->orderBy('nama_aktor', 'asc')
                ->paginate(10);

            return response()->json([
                'status' => true,
                'message' => 'Data aktor berhasil diambil',
                'data' => $aktors
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function filmByAktor($id) {
        try {

            $aktor = DB::table('aktors')
                ->where('id', $id)
                ->first();

            if (!$aktor) {

                return response()->json([
                    'status' => false,
                    'message' => 'Aktor tidak ditemukan'
                ], 404);

            }

            $films = DB::table('aktor_film')
                ->join('films', 'aktor_film.id_film', '=', 'films.id')
                ->join('genres', 'films.id_genre', '=', 'genres.id')
                ->join('aktors', 'aktor_film.id_aktor', '=', 'aktors.id')
                ->where('aktors.id', $id)
                ->select(
                    'films.id',
                    'films.judul',
                    'films.slug',
                    'films.tanggal_rilis',
                    'films.durasi',
                    'films.rating',
                    'films.sutradara',
                    'films.poster',
                    'genres.nama_genre'
                )
                ->paginate(10);

            return response()->json([
                'status' => true,
                'aktor' => $aktor,
                'data' => $films
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function search(Request $request) {
    
        try {

            $keyword = $request->keyword;

            $films = DB::table('films')
                ->join('genres', 'films.id_genre', '=', 'genres.id')
                ->select(
                    'films.*',
                    'genres.nama_genre'
                )
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('films.judul', 'like', '%' . $keyword . '%');
                })
                ->orderBy('films.judul', 'asc')
                ->paginate(10);

            return response()->json([
                'status' => true,
                'message' => 'Data film berhasil ditemukan',
                'data' => $films
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    
    }
}