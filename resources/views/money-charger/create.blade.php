@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Money Charge</strong> Create</h1>
        <a href="{{ route('money-chargers.index') }}" class="btn btn-primary">Back</a>

    </div>
    <div class="card">

        <div class="card-body">
            <form action="{{ route('money-chargers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('money-charger.form')
                <div class="d-flex mt-4">
                    <button class="btn btn-primary"> Create Money Charger</button>
                </div>
            </form>
        </div>
    </div>
@endsection
