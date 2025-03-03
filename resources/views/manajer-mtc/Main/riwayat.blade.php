<!--Template-->
@extends('manajer-mtc.app')

<!--Title-->
@section('title', 'Riwayat')

<!--Main Content-->
@section('content')
    
    <div class="card shadow mb-4" style="border-radius: 20px">
        <div id="app" class="card-body" style="border-radius: 20px">
            <router-view></router-view> 
        </div>
    </div>

@endsection