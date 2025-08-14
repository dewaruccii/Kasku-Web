@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Jurnal</strong> Create</h1>
        <a href="{{ route('jurnal.index') }}" class="btn btn-primary">Back</a>

    </div>
    <div class="card">

        <div class="card-body">
            <form action="{{ route('jurnal.store') }}" method="POST">
                @csrf
                @include('jurnal.form')
                <div class="d-flex mt-4">
                    <button class="btn btn-primary"> Create Jurnal</button>
                </div>
            </form>
        </div>
    </div>
@endsection
