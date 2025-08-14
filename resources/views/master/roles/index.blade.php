@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Master</strong> Roles</h1>
        @can('Roles Create')
            <a href="{{ route('master.roles.create') }}" class="btn btn-primary">Create <i class="fas fa-plus"></i></a>
        @endcan

    </div>
    <div class="card">

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table id="datatables" class="table " style="width:100%">
                        <thead>
                            <tr>
                                <th>Identifier</th>
                                <th>Name</th>
                                <th>Guard</th>
                                @canany(['Roles Edit', 'Roles Delete'])
                                    <th>Action</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->guard_name }}</td>
                                    @canany(['Roles Edit', 'Roles Delete'])
                                        <td>
                                            @can('Roles Edit')
                                                <a href="{{ route('master.roles.edit', $item->uuid) }}"
                                                    class="btn btn btn-primary"><i class="fas fa-edit"></i></a>
                                            @endcan
                                            @can('Roles Delete')
                                                <a href="javascript:;" class="btn btn btn-danger btnRemove"
                                                    data-id={{ $item->uuid }}><i class="fas fa-trash"></i></a>
                                            @endcan
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
            $(".btnRemove").on('click', function() {
                let id = $(this).data('id');
                let url = '{{ route('master.roles.destroy', ':id') }}'
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
        });
    </script>
@endpush
