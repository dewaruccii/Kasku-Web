@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Contract Lists</strong> </h1>
        <a href="{{ route('contracts.create') }}" class="btn btn-primary">Create <i class="fas fa-plus"></i></a>
        @can('Reimbursement Create')
        @endcan

    </div>
    <div class="card">

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table id="datatables" class="table " style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Materi Kuasa</th>
                                <th>Tanggal Kuasa</th>
                                <th>Tanggal Kontrak</th>
                                <th>Jumlah Kontrak</th>
                                <th>Jumlah Jumlah Dibayarkan</th>
                                <th>Action</th>
                                @canany(['Reimbursement Edit', 'Reimbursement Delete'])
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contract->List ?? [] as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->materi_kuasa }}</td>
                                    <td>{{ formatTime($item->tanggal_kuasa) }}</td>
                                    <td>{{ formatTime($item->tanggal_kontrak) }}</td>
                                    <td>{{ 0 }}</td>

                                    <td>{{ 0 }}</td>


                                    <td>

                                        <div class="d-flex flex-column gap-2">
                                            <a href="{{ route('contracts.show', $item->uuid) }}"
                                                class="btn btn btn-success"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('contracts.edit', $item->uuid) }}"
                                                class="btn btn btn-primary"><i class="fas fa-edit"></i></a>
                                            @can('Reimbursement Edit')
                                            @endcan
                                            <a href="javascript:;" class="btn btn btn-danger btnRemove"
                                                data-id={{ $item->uuid }}><i class="fas fa-trash"></i></a>
                                            @can('Reimbursement Delete')
                                            @endcan

                                        </div>
                                    </td>
                                    @canany(['Reimbursement Edit', 'Reimbursement Delete'])
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
