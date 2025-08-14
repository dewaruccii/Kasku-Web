@extends('layouts.homeLayout')
@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/uploader/uploader.css') }}">
@endpush
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Jurnal</strong> ({{ $jurnalBalance->name }}) Create</h1>
        <a href="{{ route('jurnal.show', $jurnalBalance->uuid) }}" class="btn btn-primary">Back </a>

        @can('User Create')
        @endcan
    </div>
    <div class="card">

        <div class="card-body">
            <form action="{{ route('jurnal.detail.store', $jurnalBalance->uuid) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @include('jurnal.detail.form')
                <div class="d-flex mt-4">
                    <button class="btn btn-primary"> Create Jurnal</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('js')
    @include('jurnal.detail.script')
@endpush
