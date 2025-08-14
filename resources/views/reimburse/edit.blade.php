@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Reimburse</strong> Edit</h1>
        <a href="{{ route('reimburses.index') }}" class="btn btn-primary">Back</a>

    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('reimburses.update', $reimburse->uuid) }}" method="POST">
                @csrf
                @method('PUT')
                @include('reimburse.form')
                <div class="d-flex mt-4">
                    <button class="btn btn-primary"> Update Reimburse</button>
                </div>
            </form>
        </div>
    </div>
@endsection
