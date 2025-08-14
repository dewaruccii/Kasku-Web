@extends('layouts.homeLayout')

@section('content')
    @can('Jurnal Balance Detail')
        <div class="d-flex justify-content-end">
            <h4> Balance:
                <b>{{ formatCurrency($jurnalBalance->balance, $jurnalBalance->Kurs?->code, $jurnalBalance->Kurs?->symbol) }}</b>
            </h4>
        </div>
    @endcan
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Jurnal</strong> ({{ $jurnalBalance->name }})</h1>
        <div>
            <a href="{{ route('jurnal.index') }}" class="btn btn-primary">Back </a>
            <a href="{{ route('jurnal.export', $jurnalBalance->uuid) }}" class="btn btn-primary">Export </a>
            @can('Jurnal Create')
                <a href="{{ route('jurnal.detail.create', $jurnalBalance->uuid) }}" class="btn btn-primary">Create Jurnal <i
                        class="fas fa-plus"></i></a>
            @endcan
        </div>

    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table id="datatables" class="table " style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category</th>
                                <th>Kegiatan</th>
                                <th>Evidence</th>
                                <th>Tanggal</th>
                                <th>Dana Masuk</th>
                                <th>Dana Keluar</th>
                                @can('Jurnal Balance Detail')
                                    <th>Sisa Balance</th>
                                @endcan
                                <th>Keterangan</th>
                                @canany(['Jurnal Edit', 'Jurnal Delete'])
                                    <th>Action</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jurnals as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->Category?->name }}</td>
                                    <td>{{ $item->kegiatan }}</td>
                                    <td>File</td>
                                    <td>{{ formatTime($item->date) }}</td>
                                    <td>{{ $item->jurnal_type == 0 ? formatCurrency($item->balance, $item->Kurs?->code, $item->Kurs?->symbol) : '-' }}
                                    </td>
                                    <td>{{ $item->jurnal_type == 1 ? formatCurrency($item->balance, $item->Kurs?->code, $item->Kurs?->symbol) : '-' }}
                                    </td>
                                    @can('Jurnal Balance Detail')
                                        <td>{{ formatCurrency($item->sisa_balance, $item->Kurs?->code, $item->Kurs?->symbol) }}
                                        </td>
                                    @endcan
                                    <td>{{ $item->keterangan }}</td>

                                    @canany(['Jurnal Edit', 'Jurnal Delete'])
                                        <td>

                                            <div class="d-flex flex-column gap-2">

                                                @can('Jurnal')
                                                    <a href="{{ route('jurnal.detail.show', [$jurnalBalance->uuid, $item->uuid]) }}"
                                                        class="btn btn btn-success"><i class="fas fa-eye"></i></a>
                                                @endcan
                                                @can('Jurnal Edit')
                                                    <a href="{{ route('jurnal.detail.edit', [$jurnalBalance->uuid, $item->uuid]) }}"
                                                        class="btn btn btn-primary"><i class="fas fa-edit"></i></a>
                                                @endcan
                                                @can('Jurnal Delete')
                                                    <a href="javascript:;" class="btn btn btn-danger btnRemove"
                                                        data-id={{ $item->uuid }}><i class="fas fa-trash"></i></a>
                                                @endcan
                                            </div>
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
                scrollX: true,
                "columnDefs": [{
                    "className": "text-center",
                    "targets": "_all"
                }],
            });

            $(".btnRemove").on('click', function() {
                Swal.fire({
                    title: "Input Reason",
                    input: "textarea",
                    inputAttributes: {
                        autocapitalize: "off",
                        required: true
                    },
                    showCancelButton: true,
                    confirmButtonText: "Delete",
                    showLoaderOnConfirm: true,
                    confirmButtonColor: "#b02a37",

                }).then((result) => {
                    console.log(result);
                    let url = '{{ route('jurnal.detail.delete', [$jurnalBalance->uuid, ':id']) }}'
                    url = url.replace(':id', $(this).data('id'));


                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE',
                                reason: result.value
                            },
                            success: function(response) {
                                if (response.success) {
                                    if (response.status == 200) {
                                        Swal.fire({
                                            position: "top-end",
                                            icon: "success",
                                            title: response.message,
                                            showConfirmButton: false,
                                            timer: 1500,
                                            toast: true,
                                        }).then(function() {
                                            location.reload();
                                        });
                                    }
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log(xhr.responseText);
                            }
                        })

                    }
                });
            });

        });
    </script>
@endpush
