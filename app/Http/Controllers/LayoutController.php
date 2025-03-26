<?php

namespace App\Http\Controllers;

use App\Models\Layout;
use Illuminate\Http\Request;

class LayoutController extends Controller
{
    public function index()
    {
        $layouts = Layout::all();
        return response()->json($layouts);
    }

    public function store(Request $request)
    {
        $layout = new Layout();
        $layout->ruang = $request->input('ruang');
        $layout->rak = $request->input('rak');
        $layout->lantai = $request->input('lantai');
        $layout->koordinat = $request->input('koordinat');
        $layout->save();
        return response()->json($layout, 201);
    }

    public function show($id)
    {
        $layout = Layout::find($id);
        if (!$layout) {
            return response()->json(['message' => 'Layout tidak ditemukan'], 404);
        }
        return response()->json($layout);
    }

    public function update(Request $request, $id)
    {
        $layout = Layout::find($id);
        if (!$layout) {
            return response()->json(['message' => 'Layout tidak ditemukan'], 404);
        }
        $layout->ruang = $request->input('ruang');
        $layout->rak = $request->input('rak');
        $layout->lantai = $request->input('lantai');
        $layout->koordinat = $request->input('koordinat');
        $layout->save();
        return response()->json($layout);
    }

    public function destroy($id)
    {
        $layout = Layout::find($id);
        if (!$layout) {
            return response()->json(['message' => 'Layout tidak ditemukan'], 404);
        }
        $layout->delete();
        return response()->json(['message' => 'Layout berhasil dihapus']);
    }
}
