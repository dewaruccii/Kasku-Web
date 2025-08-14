@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Permission</strong> Create</h1>
        <a href="{{ route('master.permissions.index') }}" class="btn btn-primary">Back</a>

    </div>
    <div class="card">

        <div class="card-body">
            <form action="{{ route('master.permissions.store') }}" method="POST">
                @csrf
                @include('master.permissions.form')
                <div class="d-flex mt-4">
                    <button class="btn btn-primary"> Create Permission Group</button>
                </div>
            </form>
        </div>
    </div>
@endsection
