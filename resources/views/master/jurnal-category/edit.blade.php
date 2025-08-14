@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>User</strong> Edit</h1>
        <a href="{{ route('master.jurnal-category.index') }}" class="btn btn-primary">Back</a>

    </div>
    <div class="card">

        <div class="card-body">
            <form action="{{ route('master.jurnal-category.update', $category->uuid) }}" method="POST">
                @csrf
                @method('PUT')
                @include('master.jurnal-category.form')
                <div class="d-flex mt-4">
                    <button class="btn btn-primary"> Update Category</button>
                </div>
            </form>
        </div>
    </div>
@endsection
