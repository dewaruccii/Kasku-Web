@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Reimburse</strong> </h1>
        @can('Reimbursement Create')
            <a href="{{ route('reimburses.create') }}" class="btn btn-primary">Create <i class="fas fa-plus"></i></a>
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
                                <th>Tanggal</th>
                                <th>Justifikasi</th>
                                <th>Total</th>
                                <th> Reimburse Type</th>
                                <th> Kurs</th>
                                <th> Jurnal Reference</th>
                                @canany(['Reimbursement Edit', 'Reimbursement Delete'])
                                    <th>Action</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reimburses as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ formatTime($item->date) }}</td>
                                    <td>{{ $item->justifikasi }}</td>
                                    <td>{{ formatCurrency($item->total, $item->JurnalBalance?->Kurs?->code, $item->JurnalBalance?->Kurs?->symbol) }}
                                    </td>
                                    <td>{{ $item->type === 0 ? 'From Clients' : 'To Employee' }}
                                    </td>
                                    <td>{{ $item->JurnalBalance?->Kurs?->code }}
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route('jurnal.detail.show', [$item->jurnal_id, $item->jurnal_reference]) }}">
                                            Lihat Jurnal</a>
                                    </td>
                                    @canany(['Reimbursement Edit', 'Reimbursement Delete'])
                                        <td>

                                            <div class="d-flex flex-column gap-2">
                                                @can('Reimbursement Edit')
                                                    <a href="{{ route('reimburses.edit', $item->uuid) }}"
                                                        class="btn btn btn-primary"><i class="fas fa-edit"></i></a>
                                                @endcan
                                                @can('Reimbursement Delete')
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
                "columnDefs": [{
                    "className": "text-center",
                    "targets": "_all"
                }],
            });

        });
    </script>
    @can('Reimbursement Delete')
        <script>
            $(document).ready(function() {
                $(".btnRemove").on('click', function() {
                    let id = $(this).data('id');
                    let url = '{{ route('reimburses.destroy', ':id') }}'
                    url = url.replace(':id', id);

                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won't be able to revert this!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: url,
                                method: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                },
                                success: function(data) {
                                    console.log(data);
                                    Swal.fire({
                                        position: "top-end",
                                        icon: "success",
                                        title: data.message,
                                        showConfirmButton: false,
                                        timer: 1500,
                                        toast: true,
                                    }).then(function() {
                                        location.reload();
                                    });
                                }
                            })

                        }
                    });
                });
            })
        </script>
    @endcan
@endpush
