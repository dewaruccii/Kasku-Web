@extends('layouts.homeLayout')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-3"><strong>Contract List</strong> <i>{{ $contract->contract_name }}</i></h1>
        <div>

            <a href="{{ route('contracts.index') }}" class="btn btn-primary">Back</a>
            <a href="{{ route('contracts.list.create', $contract->uuid) }}" class="btn btn-primary">Create <i
                    class="fas fa-plus"></i></a>
        </div>

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
                                <th>Selisih</th>
                                <th>Bonus</th>
                                <th>Action</th>
                                @canany(['Reimbursement Edit', 'Reimbursement Delete'])
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contractLists as $item)
                                @php
                                    $nilaiContractIDR = 0;
                                    $nilaiContractUSD = 0;
                                    $jumlahDibayarIDR = 0;
                                    $jumlahDibayarUSD = 0;
                                    $jumlahBonusIDR = 0;
                                    $jumlahBonusUSD = 0;
                                @endphp
                                @foreach ($item->Detail?->GroupBy('jurnal_balance_id') as $item1)
                                    @foreach ($item1 as $item2)
                                        @if ($item2->is_bonus != 1)
                                            @if ($item2->type == 1)
                                                @if ($item2?->JurnalBalance?->Kurs?->code == 'IDR')
                                                    @php
                                                        $nilaiContractIDR = $nilaiContractIDR + $item2->total;
                                                    @endphp
                                                @elseif ($item2?->JurnalBalance?->Kurs?->code == 'USD')
                                                    @php
                                                        $nilaiContractUSD = $nilaiContractUSD + $item2->total;

                                                    @endphp
                                                @endif
                                            @else
                                                @if ($item2?->JurnalBalance?->Kurs?->code == 'IDR')
                                                    @php
                                                        $jumlahDibayarIDR = $jumlahDibayarIDR + $item2->total;
                                                    @endphp
                                                @elseif ($item2?->JurnalBalance?->Kurs?->code == 'USD')
                                                    @php
                                                        $jumlahDibayarUSD = $jumlahDibayarUSD + $item2->total;

                                                    @endphp
                                                @endif
                                            @endif
                                        @else
                                            @if ($item2?->JurnalBalance?->Kurs?->code == 'IDR')
                                                @php
                                                    $jumlahBonusIDR = $jumlahBonusIDR + $item2->total;
                                                @endphp
                                            @elseif ($item2?->JurnalBalance?->Kurs?->code == 'USD')
                                                @php
                                                    $jumlahBonusUSD = $jumlahBonusUSD + $item2->total;

                                                @endphp
                                            @endif
                                        @endif
                                    @endforeach
                                @endforeach
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->materi_kuasa }}</td>
                                    <td>{{ formatTime($item->tanggal_kuasa) }}</td>
                                    <td>{{ formatTime($item->tanggal_kontrak) }}</td>
                                    <td>
                                        <div class="d-flex flex-column">

                                            @if ($nilaiContractIDR != 0)
                                                <span>IDR:
                                                    <b>{{ formatCurrency($nilaiContractIDR, 'IDR', 'Rp') }}</b>
                                                </span>
                                            @endif
                                            @if ($nilaiContractUSD != 0)
                                                <span>USD:
                                                    <b>{{ formatCurrency($nilaiContractUSD, 'USD', '$') }}</b>
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex flex-column">

                                            <span>IDR:
                                                <b>{{ formatCurrency($jumlahDibayarIDR, 'IDR', 'Rp') }}</b>
                                            </span>
                                            @if ($jumlahDibayarIDR != 0)
                                            @endif
                                            <span>USD:
                                                <b>{{ formatCurrency($jumlahDibayarUSD, 'USD', '$') }}</b>
                                            </span>
                                            @if ($jumlahDibayarUSD != 0)
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-2">
                                            @php
                                                $selisihIDR = $jumlahDibayarIDR - $nilaiContractIDR;
                                                $selisihUSD = $jumlahDibayarUSD - $nilaiContractUSD;
                                            @endphp
                                            @if ($jumlahDibayarIDR != 0)
                                                <span class="badge bg-{{ $selisihIDR >= 0 ? 'success' : 'danger' }}">IDR:
                                                    <b>{{ formatCurrency($selisihIDR, 'IDR', 'Rp') }}</b>
                                                </span>
                                            @endif
                                            @if ($jumlahDibayarUSD != 0)
                                                <span class="badge bg-{{ $selisihUSD >= 0 ? 'success' : 'danger' }}">USD:
                                                    <b>{{ formatCurrency($selisihUSD, 'USD', '$') }}</b>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-2">

                                            @if ($jumlahDibayarIDR != 0)
                                                <span
                                                    class="badge bg-{{ $jumlahBonusIDR >= 0 ? 'success' : 'danger' }}">IDR:
                                                    <b>{{ formatCurrency($jumlahBonusIDR, 'IDR', 'Rp') }}</b>
                                                </span>
                                            @endif
                                            @if ($jumlahDibayarUSD != 0)
                                                <span
                                                    class="badge bg-{{ $jumlahBonusUSD >= 0 ? 'success' : 'danger' }}">USD:
                                                    <b>{{ formatCurrency($jumlahBonusUSD, 'USD', '$') }}</b>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-2">
                                            <a href="{{ route('contracts.list.detail.index', [$contract->uuid, $item->uuid]) }}"
                                                class="btn btn btn-success"><i class="fas fa-eye"></i></a>
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
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $("#datatables").DataTable({
                scrollX: true,
                "columnDefs": [{
                    "className": "text-center",
                    "targets": "_all"
                }, {
                    width: '25%',
                    targets: [4, 5, 6, 7]
                }, {
                    width: '30%',
                    targets: [1]
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
