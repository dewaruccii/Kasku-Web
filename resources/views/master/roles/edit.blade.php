@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Role</strong> Edit</h1>
        <a href="{{ route('master.roles.index') }}" class="btn btn-primary">Back</a>

    </div>
    <div class="card">

        <div class="card-body">
            <form action="{{ route('master.roles.update', $role->uuid) }}" method="POST">
                @csrf
                @method('PUT')
                @include('master.roles.form')
                <div class="d-flex mt-4">
                    <button class="btn btn-primary"> Update Role</button>
                </div>
            </form>
        </div>
    </div>
@endsection
