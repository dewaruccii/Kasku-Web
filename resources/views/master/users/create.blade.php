@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>User</strong> Create</h1>
        <a href="{{ route('master.users.index') }}" class="btn btn-primary">Back</a>

    </div>
    <div class="card">

        <div class="card-body">
            <form action="{{ route('master.users.store') }}" method="POST">
                @csrf
                @include('master.users.form')
                <div class="d-flex mt-4">
                    <button class="btn btn-primary"> Create User</button>
                </div>
            </form>
        </div>
    </div>
@endsection
