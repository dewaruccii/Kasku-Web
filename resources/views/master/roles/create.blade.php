@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Role</strong> Create</h1>
        <a href="{{ route('master.roles.index') }}" class="btn btn-primary">Back</a>

    </div>
    <div class="card">

        <div class="card-body">
            <form action="{{ route('master.roles.store') }}" method="POST">
                @csrf
                @include('master.roles.form')
                <div class="d-flex mt-4 justify-content-end">
                    <button class="btn btn-primary"> Create Role</button>
                </div>
            </form>
        </div>
    </div>
@endsection
