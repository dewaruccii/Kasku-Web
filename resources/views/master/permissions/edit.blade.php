@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Permission Group</strong> Edit</h1>
        <a href="{{ route('master.permissions.index') }}" class="btn btn-primary">Back</a>

    </div>
    <div class="card">

        <div class="card-body">
            <form action="{{ route('master.permissions.update', $permissionGroup->uuid) }}" method="POST">
                @csrf
                @method('PUT')
                @include('master.permissions.form')
                <div class="d-flex mt-4">
                    <button class="btn btn-primary"> Update Permission Group</button>
                </div>
            </form>
        </div>

    </div>
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Permission </strong> Create & Edit</h1>
        <a href="javascript:;" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Create
            Permission</a>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table id="datatables" class="table " style="width:100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Permission Name</th>
                                <th>Guard Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissionGroup?->Permission ?? [] as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->guard_name }}</td>

                                    <td>
                                        <a href="{{ route('master.permissions.edit', $item->uuid) }}"
                                            class="btn btn btn-primary"><i class="fas fa-edit"></i></a>
                                        <a href="javascript:;" class="btn btn btn-danger btnRemove"
                                            data-id={{ $item->uuid }}><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal CREATE -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="createPermission">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Create Permission</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <x-textInput name="name" label="Permission Name" :mandatory="true" col="6" />
                            <x-textInput name="guard_name" label="Guard Name" :mandatory="true" col="6" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary btnSave">Create Permission</button>
                    </div>
                </div>
            </form>
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
            $("#createPermission").submit(function(e) {
                e.preventDefault();
                $(".errorValidation").text("");
                loader(true, 'Create', 'Create...');
                let data = new FormData(this);
                data.append('permission_group_id', '{{ $permissionGroup->uuid }}');

                $.ajax({
                    type: "POST",
                    url: "{{ route('master.permissions.store') }}",
                    data: data,
                    enctype: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    cache: false,
                    dataType: "json",
                    success: function(response) {
                        if (response.status == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Successfuly',
                                showConfirmButton: false,
                                timer: 1500,
                                type: "success",
                            }).then(function() {
                                window.location.reload();
                                loader(false, 'Create', 'Create...');
                            });
                        }
                        if (response.status === 422) {
                            loader(false, 'Create', 'Create...');
                            Swal.fire({
                                position: "top-end",
                                icon: "error",
                                title: response.message,
                                showConfirmButton: false,
                                timer: 1500,
                                toast: true,
                            });
                        }
                    },
                    error: function(xhr) {
                        loader(false, 'Create', 'Create...');


                        if (xhr.status == 422) {
                            $.each(xhr.responseJSON.errors, function(index, value) {
                                // console.log(index, value);
                                let part = index.substring(index.indexOf('.') + 1);
                                // console.log(part, 'part');
                                let dom;
                                let val;
                                if (part == 0) {
                                    dom = '.error-' + index.split('.')[0];
                                    // console.log(dom, 'dom', ' | ', part, 'part');

                                } else {
                                    dom = '.error-' + index.split('.').join('');
                                    // console.log(dom, 'dom');

                                }
                                val = value[0].split('.').join('');
                                // console.log(index, 'index');
                                $(dom).text(val);
                                // $('.error-' + index).text(value[0]);
                                // $('#' + index).addClass('is-invalid');
                            });
                        }
                    }

                });
            })
        });
    </script>
@endpush
