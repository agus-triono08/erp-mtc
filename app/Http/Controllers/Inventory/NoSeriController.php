<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Inventory\NoSeri;
use App\Models\Inventory\Tools;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NoSeriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $noseri = NoSeri::all();
        return response()->json($noseri);
    }

    // /**
    //  * Show the form for creating a new resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $noseri = NoSeri::find($id);
        if ($noseri) {
            return response()->json($noseri);
        } else {
            return response()->json(['message' => 'NoSeri not found'], 404);
        }
    }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $tools_id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($tools_id)
    // {
    //     $noseri = NoSeri::where('tools_id', $tools_id)->get();
    //     if ($noseri->isEmpty()) {
    //         return response()->json(['message' => 'Data tidak ditemukan'], 404);
    //     }
    //     return response()->json($noseri);
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getNoSeri($kodeAlat) {
        $tools = Tools::where('kode', $kodeAlat)->first();

        if (!$tools) {
            return response()->json(['message' => 'Tool not found'], 404);
        }

        $noseri = NoSeri::where('tools_id', $tools->id)->get();

        if ($noseri->isEmpty()) {
            return response()->json(['message' => 'NoSeri not found'], 404);
        }

        return response()->json($noseri);
    }
}
