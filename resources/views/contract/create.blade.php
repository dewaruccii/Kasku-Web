@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Contract</strong> Create</h1>
        <a href="{{ route('contracts.index') }}" class="btn btn-primary">Back</a>

    </div>
    <div class="card">

        <div class="card-body">
            <form action="{{ route('contracts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('contract.form')
                <div class="d-flex mt-4">
                    <button class="btn btn-primary"> Create Contract</button>
                </div>
            </form>
        </div>
    </div>
@endsection
