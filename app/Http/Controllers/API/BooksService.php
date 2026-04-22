<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Books;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BooksService extends Controller
{
    public function get()
    {
        $books = Books::latest()->get();

        $data = [
            'status' => true,
            'message' => 'Berhasil',
            'data' => $books
        ];

        if ($books) {
            return response()->json($data);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function detail(int $id)
    {
        if (!$id) {
            return response()->json([
                'error' => 'ID buku dibutuhkan'
            ]);
        }

        $books = Books::find($id);
        $data = [
            'status' => true,
            'message' => 'Berhasil',
            'data' => $books
        ];

        if ($books) {
            return response()->json($data);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        if (!$request) return response()->json(['error' => 'Data buku baru dibutuhkan'], 422);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:books,title',
            'author' => 'required',
            'year' => 'required|integer|min:1900',
            'stock' => 'required|integer|min:0'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi penambahan data tidak sesuai ketentuan!',
                'data' => $validator->errors()
            ], 422);
        }

        $books = Books::create([
            'title' => $request->title,
            'author' => $request->author,
            'year' => $request->year,
            'stock' => $request->stock
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data buku berhasil ditambahkan',
            'data' => $books
        ]);
    }

    public function update(Request $request, int $id)
    {
        if (!$id) return response()->json(['error' => 'ID buku dibutuhkan'], 422);
        if (!$request) return response()->json(['error' => 'Data buku dibutuhkan'], 422);

        $books = Books::find($id);
        if (!$books) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:books,title,' . $id,
            'author' => 'required',
            'year' => 'required|integer|min:1900',
            'stock' => 'required|integer|min:0'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi pengubahan data tidak sesuai ketentuan!',
                'data' => $validator->errors()
            ], 422);
        }

        $data = [
            'title' => $request->title,
            'author' => $request->author,
            'year' => $request->year,
            'stock' => $request->stock
        ];

        if (
            $request->title !== $books->title ||
            $request->author !== $books->author ||
            $request->year !== $books->year ||
            $request->stock != $books->stock
        ) {
            $books->update($data);
        } else {
            return response()->json([
                'status' => true,
                'message' => 'Tidak ada data yang diupdate'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data buku berhasil diperbarui',
            'data' => $books->getChanges()
        ], 200);
    }

    public function destroy(int $id)
    {
        if (!$id) return response()->json(['error' => 'ID buku dibutuhkan'], 422);

        $books = Books::find($id);

        if (!$books) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $books->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data buku berhasil dihapus',
            'data' => $books
        ], 200);
    }
}
