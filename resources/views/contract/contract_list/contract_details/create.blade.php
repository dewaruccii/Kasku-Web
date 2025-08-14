@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Contract List Detail</strong> Create</h1>
        <a href="{{ route('contracts.list.detail.index', [$contract->uuid, $contractList->uuid]) }}"
            class="btn btn-primary">Back</a>

    </div>
    <div class="card">

        <div class="card-body">
            <form action="{{ route('contracts.list.detail.store', [$contract->uuid, $contractList->uuid]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @include('contract.contract_list.contract_details.form')
                <div class="d-flex mt-4">
                    <button class="btn btn-primary"> Create Contract List Detail</button>
                </div>
            </form>
        </div>
    </div>
@endsection
