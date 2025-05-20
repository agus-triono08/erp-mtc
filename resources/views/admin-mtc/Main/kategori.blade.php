<!--Template-->
@extends('admin-mtc.app')

<!--Title-->
@section('title', 'Inventory Alat/Mesin')

<!--Main Content-->
@section('content')

    <div class="card shadow mb-4" style="border-radius: 20px; margin-top: 5rem;">
        <div id="app" class="card-body" style="border-radius: 20px">
            <router-view></router-view> 
        </div>
    </div>

@endsection