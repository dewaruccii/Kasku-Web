@php
    use App\Services\ExchangeService;
@endphp
@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Money Chargers</strong> </h1>
        @can('Money Changer Create')
            <a href="{{ route('money-chargers.create') }}" class="btn btn-primary">Create <i class="fas fa-plus"></i></a>
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
                                <th>Tanggal Kesepakatan</th>
                                <th>Jumlah Kesepakatan</th>
                                <th>Type</th>
                                <th>Total From</th>
                                <th> Total To</th>
                                <th> Total Fee</th>
                                <th> Jurnal Reference</th>
                                @canany(['Money Changer Edit', 'Money Changer Delete'])
                                    <th>Action</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($money_chargers as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ formatTime($item->exchange_date) }}</td>
                                    <td>{{ formatTime($item->tanggal_kesepakatan) }}</td>
                                    <td>{{ formatCurrency(0, 'USD', '$') }}

                                    <td>{{ $item->type == 0 ? 'From Client' : 'From Jurnal' }}</td>
                                    <td>{{ formatCurrency($item->total_from, $item->From?->Kurs?->code, $item->From?->Kurs?->symbol) }}
                                    </td>
                                    <td>{{ formatCurrency($item->total_to, $item->To?->Kurs?->code, $item->To?->Kurs?->symbol) }}
                                    </td>
                                    <td>{{ formatCurrency($item->total_fee, 'IDR', 'Rp') }}
                                    </td>

                                    <td>
                                        @if ($item->type == 0)
                                            <a href="{{ route('jurnal.detail.show', [$item->to, $item->jurnal_to_id]) }}">
                                                Lihat Jurnal</a>
                                        @else
                                            <a
                                                href="{{ route('jurnal.detail.show', [$item->from, $item->jurnal_from_id]) }}">
                                                Lihat Jurnal From</a>
                                            <br>
                                            <a href="{{ route('jurnal.detail.show', [$item->to, $item->jurnal_to_id]) }}">
                                                Lihat Jurnal To</a>
                                        @endif
                                    </td>
                                    @canany(['Money Changer Edit', 'Money Changer Delete'])
                                        <td>

                                            <div class="d-flex flex-column gap-2">
                                                @can('Money Changer Edit')
                                                    <a href="{{ route('reimburses.edit', $item->uuid) }}"
                                                        class="btn btn btn-primary"><i class="fas fa-edit"></i></a>
                                                @endcan

                                                @can('Money Changer Delete')
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

        });
    </script>
    @can('Money Changer Delete')
        <script>
            $(document).ready(function() {
                $(".btnRemove").on('click', function() {
                    let id = $(this).data('id');
                    let url = '{{ route('master.users.destroy', ':id') }}'
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
