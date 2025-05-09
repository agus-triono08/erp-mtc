<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Error;
use App\Models\Inventory\ErrorActivity;
use Carbon\Carbon;
use App\Models\User; // pastikan ini di atas
use Illuminate\Support\Arr;

class ErrorActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $all = ErrorActivity::with('error')
            ->orderBy('changed_at', 'desc')
            ->get()
            ->map(function ($item) {
                $item->changed_at = Carbon::parse($item->changed_at)->format('Y-m-d');

                // Ambil nama-nama PIC
                if ($item->pic) {
                    $picIds = explode(',', $item->pic); // pecah jadi array ID
                    $users = User::whereIn('id', $picIds)->pluck('nama', 'id');

                    // Urutkan nama sesuai urutan ID dalam pic
                    $item->nama_pic = collect($picIds)->map(function ($id) use ($users) {
                        return $users[$id] ?? 'Tidak Ditemukan';
                    })->toArray();
                } else {
                    $item->nama_pic = [];
                }

                return $item;
            });

        return response()->json([
            'all' => $all,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

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
        $all = ErrorActivity::with('error')
            ->orderBy('changed_at', 'desc')
            ->findOrFail($id);

        return response()->json([
            'all' => $all,
        ]);
    }

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
}
