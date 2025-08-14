@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Contract</strong> Edit</h1>
        <a href="{{ route('contracts.index') }}" class="btn btn-primary">Back</a>

    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('contracts.update', $contract->uuid) }}" method="POST">
                @csrf
                @method('PUT')
                @include('contract.form')
                <div class="d-flex mt-4">
                    <button class="btn btn-primary"> Update Contract</button>
                </div>
            </form>
        </div>
    </div>
@endsection
