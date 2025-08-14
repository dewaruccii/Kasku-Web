@extends('layouts.homeLayout')
@section('content')

    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Contract List </strong> <i>{{ $contract->contract_name }}</i> <strong>Detail</strong>
            ({{ $contractList->materi_kuasa }})</h1>
        <div>
            <a href="{{ route('contracts.list.index', $contract->uuid) }}" class="btn btn-primary">Back</a>
            <a href="{{ route('contracts.list.detail.create', [$contract->uuid, $contractList->uuid]) }}"
                class="btn btn-primary">Create <i class="fas fa-plus"></i></a>
        </div>

        @can('Reimbursement Create')
        @endcan

    </div>
    @if ($contractDetails->count() < 1)
        <h1 class="text-center mt-5 fst-italic">Contract List Detail is Empty!</h1>
    @endif
    @foreach ($contractDetails->groupBy(function ($q) {
            return $q->JurnalBalance?->Kurs?->code;
        }) as $key => $value)
        <div class="d-flex justify-content-between">
            <h4>Kurs : <b>{{ $key }}</b></h4>
            <div class="d-flex flex-column">
                @if ($key == 'IDR')
                    @if ($contractList->nilai_contract_idr != null)
                        <h4>IDR: <b>{{ formatCurrency($contractList->nilai_contract_idr, 'IDR', 'Rp ') }}</b></h4>
                    @endif
                @endif
                @if ($key == 'USD')
                    @if ($contractList->nilai_contract_usd != null)
                        <h4>USD: <b>{{ formatCurrency($contractList->nilai_contract_usd, 'USD', '$') }}</b></h4>
                    @endif
                @endif
            </div>

        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table id="datatables_{{ $key }}" class="table " style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Description</th>
                                    <th>Total</th>
                                    <th>Type</th>
                                    <th>Kurs</th>
                                    <th>Tanggal Transaksi</th>
                                    <th>Action</th>
                                    @canany(['Reimbursement Edit', 'Reimbursement Delete'])
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($value as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ formatCurrency($item->total, $item->JurnalBalance?->Kurs?->code, $item->JurnalBalance?->Kurs?->symbol) }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $item->type == 1 ? 'bg-danger' : 'bg-success' }} ">{{ $item->type == 1 ? 'Pemasukan' : 'Pembayaran' }}</span>

                                        </td>
                                        <td>{{ $item->JurnalBalance?->Kurs?->code }}</td>
                                        <td>{{ formatTime($item->transaction_date) }}</td>
                                        <td>

                                            <div class="d-flex flex-column gap-2">

                                                <a href="{{ route('contracts.list.detail.edit', [$contract->uuid, $contractList->uuid, $item->uuid]) }}"
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
        @push('js')
            <script>
                $(document).ready(function() {
                    $("#datatables_{{ $key }}").DataTable({
                        "columnDefs": [{
                            "className": "text-center",
                            "targets": "_all"
                        }],
                    });

                });
            </script>
        @endpush
    @endforeach
    @if ($contractDetailBonus->count() > 0)
        <div class="d-flex justify-content-between">
            <h3>Bonus</h3>
            <div class="d-flex flex-column">
                @php
                    $totalBonusIDR = 0;
                    $totalBonusUSD = 0;
                @endphp
                @foreach ($contractDetailBonus->groupBy(function ($q) {
            return $q->JurnalBalance?->Kurs?->code;
        }) as $key => $item)
                    <h4>{{ $key }}:
                        <b>{{ formatCurrency($item->sum('total'), $key, $key == 'IDR' ? 'Rp ' : '$') }}</b>
                    </h4>
                @endforeach
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
                                    <th>Description</th>
                                    <th>Total</th>
                                    <th>Type</th>
                                    <th>Kurs</th>
                                    <th>Tanggal Transaksi</th>
                                    <th>Action</th>
                                    @canany(['Reimbursement Edit', 'Reimbursement Delete'])
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contractDetailBonus as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ formatCurrency($item->total, $item->JurnalBalance?->Kurs?->code, $item->JurnalBalance?->Kurs?->symbol) }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $item->type == 1 ? 'bg-danger' : 'bg-success' }} ">{{ $item->type == 1 ? 'Pemasukan' : 'Pembayaran' }}</span>

                                        </td>
                                        <td>{{ $item->JurnalBalance?->Kurs?->code }}</td>
                                        <td>{{ formatTime($item->transaction_date) }}</td>
                                        <td>

                                            <div class="d-flex flex-column gap-2">

                                                <a href="{{ route('contracts.list.edit', [$contract->uuid, $item->uuid]) }}"
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
    @endif
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
