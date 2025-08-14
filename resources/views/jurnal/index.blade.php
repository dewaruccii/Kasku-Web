@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Jurnal Balance</strong> </h1>
        @can('Jurnal Balance Create')
            <a href="{{ route('jurnal.create') }}" class="btn btn-primary">Create Jurnal Balance <i class="fas fa-plus"></i></a>
        @endcan
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table id="datatables" class="table " style="width:100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                @can('Jurnal Balance Detail')
                                    <th>Code</th>
                                    <th>Prefix</th>
                                    <th>Balance Sekarang</th>
                                @endcan
                                @canany(['Jurnal'])
                                    <th>Action</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jurnals as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->name }}</td>
                                    @can('Jurnal Balance Detail')
                                        <td>{{ $item->Kurs?->code }}</td>
                                        <td>{{ $item->Kurs?->symbol }}</td>
                                        <td>{{ formatCurrency($item->balance, $item->Kurs?->code, $item->Kurs?->symbol) }}</td>
                                    @endcan

                                    @canany(['Jurnal'])
                                        <td>
                                            <a href="{{ route('jurnal.show', $item->uuid) }}" class="btn btn btn-success"><i
                                                    class="fas fa-eye"></i></a>

                                        </td>
                                    @endcanany
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $("#datatables").DataTable({
                "columnDefs": [{
                    "className": "text-center",
                    "targets": "_all"
                }],
            });

        });
    </script>
@endpush
