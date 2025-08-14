@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex justify-content-end mb-2">
        <a href="{{ route('jurnal.show', $jurnalBalance->uuid) }}" class="btn btn-primary">Back</a>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">

                    <div class="form-group">
                        <label for="">Jurnal Type</label>
                        <h4>{{ $jurnal->jurnal_type == 0 ? 'Dana Masuk' : 'Dana Keluar' }}</h4>
                    </div>
                </div>
                <div class="col-md-3">

                    <div class="form-group">
                        <label for="">Category</label>
                        <h4>{{ $jurnal->Category?->name }}</h4>
                    </div>
                </div>
                <div class="col-md-3">

                    <div class="form-group">
                        <label for="">Kurs </label>
                        <h4>{{ $jurnal?->Kurs?->code }}</h4>
                    </div>
                </div>
                <div class="col-md-3">

                    <div class="form-group">
                        <label for="">User </label>
                        <h4>{{ $jurnal?->User?->name }}</h4>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-3">

                    <div class="form-group">
                        <label for="">Balance</label>
                        <h4>{{ formatCurrency($jurnal->balance, $jurnal->Kurs?->code, $jurnal->Kurs?->symbol) }}</h4>
                    </div>
                </div>
                <div class="col-md-3">

                    <div class="form-group">
                        <label for="">Kegiatan</label>
                        <h4>{{ $jurnal->kegiatan }}</h4>
                    </div>
                </div>

                <div class="col-md-3">

                    <div class="form-group">
                        <label for="">Keterangan</label>
                        <h4>{{ $jurnal->keterangan }}</h4>
                    </div>
                </div>
                <div class="col-md-3">

                    <div class="form-group">
                        <label for="">Date</label>
                        <h4>{{ formatTime($jurnal->date) }}</h4>
                    </div>
                </div>



            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="File">File Evidence</label>
                    <div class="row">
                        @foreach ($jurnal->Attachments as $item1)
                            <div class="col-md-12 border mt-2">
                                <div class="d-flex align-items-center justify-content-between p-2">
                                    <img class="rounded" src="{{ asset('file_icon/' . $item1->ext . '.png') }}"
                                        alt="" style="width: 50px">
                                    <div class="d-flex flex-column">
                                        <a href="{{ asset('storage') . $item1->path }}"
                                            data-title="{{ $item1->file_name }}"
                                            class="spotlight">{{ $item1->file_name }}</a>
                                        {{-- <h6 style="color:#95a5a6"></h6> --}}
                                        <small class="fst-italic"
                                            style="font-size:12px;font-weight:bold;color:#34495e">{{ formatSizeUnits($item1->size) }}</small>
                                    </div>
                                    {{-- <a href="{{ route('jurnal.balance.download', encString($item->id)) }}">
                                    <i class="fas fa-cloud-download-alt " style="font-size: 30px;cursor:pointer;"></i>
                                </a> --}}

                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
