@extends('layouts.homeLayout')

@section('content')
    @can('Dashboard Analytics')
        <h1 class="h3 mb-3"><strong>Analytics</strong> Dashboard</h1>

        <div class="row">
            <div class="col-xl-6 col-xxl-5 d-flex">
                <div class="w-100">
                    <div class="row">
                        @foreach ($balance as $item)
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col mt-0">
                                                <h5 class="card-title">Balance {{ $item->Kurs?->code }}</h5>
                                            </div>

                                            <div class="col-auto">
                                                <div class="stat text-primary">
                                                    <i class="align-middle" data-feather="truck"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <h3 class="mt-1 mb-3">
                                            {{ formatCurrency($item->balance, $item->Kurs?->code, $item->Kurs?->symbol) }}</h3>
                                        <div class="mb-0">
                                            {{-- <span class="text-danger"> <i class="mdi mdi-arrow-bottom-right"></i> -3.65% </span> --}}
                                            <span
                                                class="text-muted">{{ formatCurrency($item->balance * $item->Kurs?->KursExhcange?->exchange, $item->Kurs?->code == 'IDR' ? 'USD' : 'IDR', $item->Kurs?->code == 'IDR' ? '$' : 'Rp. ') }}</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-xxl-7">
                <div class="w-100">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title">Reimburse</h5>
                                        </div>

                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <i class="align-middle" data-feather="truck"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3">{{ $reimburse }}</h1>


                                </div>
                            </div>

                        </div>
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title">Money Charger</h5>
                                        </div>

                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <i class="align-middle" data-feather="dollar-sign"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3">{{ $moneyCharge }}</h1>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endcan
    @can('Dashboard Analytics')
        <h1 class="h3 mb-3"><strong>Contracts</strong> Dashboard</h1>

        <div class="row">

            <div class="col-md-2">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Total Contracts</h5>
                            </div>


                        </div>
                        <h1 class="mt-1 mb-3">{{ $contract->count() }}</h1>


                    </div>
                </div>

            </div>
            @php
                $estimatedIDR = 0;
                $estimatedUSD = 0;

                foreach ($contract as $key => $value) {
                    foreach ($value->List as $key1 => $item) {
                        # code...
                        $estimatedIDR += $item->nilai_contract_idr;
                        $estimatedUSD += $item->nilai_contract_usd;
                    }
                }
            @endphp
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Estimated Earn (IDR)</h5>
                            </div>

                            <div class="col-auto">
                                <div class="stat text-primary">
                                    <i class="align-middle" data-feather="dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                        <h1 class="mt-1 mb-3">{{ formatCurrency(abs($estimatedIDR), 'IDR', 'Rp') }}</h1>

                    </div>
                </div>

            </div>

            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Estimated Earn (USD)</h5>
                            </div>

                            <div class="col-auto">
                                <div class="stat text-primary">
                                    <i class="align-middle" data-feather="dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                        <h1 class="mt-1 mb-3"> {{ formatCurrency(abs($estimatedUSD), 'USD', '$') }}</h3>
                        </h1>

                    </div>
                </div>

            </div>

        </div>
    @endcan
    <div class="d-flex justify-content-end align-items-center">
        <div>
            <input type="month" name="date" id="tanggal" class="form-control">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <h1 class="mx-2 my-2">IDR Category Chart</h1>
                <div class="card-body">

                    <canvas id="pieChartIDR"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <h1 class="mx-2 my-2">USD Category Chart</h1>
                <div class="card-body">

                    <div>
                        <canvas id="pieChartUSD"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <h1 class="mx-2 my-2">Bar Chart</h1>
                <div class="card-body">

                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    @can('Dashboard Latest Jurnal')
        <div class="row">
            <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill p-3">
                    <div class="card-header">

                        <h5 class="card-title mb-0">Latest Jurnal</h5>
                    </div>
                    <table id="datatables" class="table " style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category</th>
                                <th>Kegiatan</th>
                                <th>Kurs</th>
                                <th>Tanggal</th>
                                <th>Dana Masuk</th>
                                <th>Dana Keluar</th>
                                @can('Jurnal Balance Detail')
                                    <th>Sisa Balance</th>
                                @endcan
                                <th>Keterangan</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jurnals as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->Category?->name }}</td>
                                    <td>{{ $item->kegiatan }}</td>
                                    <td>{{ $item->Kurs?->code }}</td>
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


                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    @endcan
@endsection
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"
        integrity="sha512-CQBWl4fJHWbryGE+Pc7UAxWMUMNMWzWxF4SQo9CgkJIN1kx6djDQZjh3Y8SZ1d+6I+1zze6Z7kHXO7q3UyZAWw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        const rupiah = (number) => {
            return new Intl.NumberFormat("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(number);
        }

        function fetchDataFromDatabase(url, data = {}) {
            return new Promise((resolve, reject) => {
                // Lakukan permintaan AJAX ke server
                // Misalnya, jika Anda menggunakan jQuery:
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        data: data
                    },
                    success: function(response) {
                        // Panggil resolve dengan data yang diperoleh dari server
                        resolve(response);
                    },
                    error: function(xhr, status, error) {
                        // Panggil reject jika terjadi kesalahan
                        reject(error);
                    }
                });
            });
        }
    </script>
    {{-- PIE CHART IDR --}}
    <script>
        const typeC = document.getElementById('pieChartIDR');

        let typeChart = null;

        async function createTypeChart(tanggal) {
            try {
                const dataFromDatabase = await fetchDataFromDatabase(
                    '{{ route('jurnal.getJurnalChart') }}', {
                        type: 'categoryChart',
                        tanggal: tanggal,
                        id: 'e4422bf9-beb5-4d82-8b39-d5492f098073'
                    }
                );

                const labels = dataFromDatabase.labels;
                const data = dataFromDatabase.data;
                const color = dataFromDatabase.color;
                const borderColors = ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)',
                    'rgba(142, 68, 173, 0.2)'
                ];

                const backgroundColors = color;
                if (!typeChart) {
                    typeChart = new Chart(typeC, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: backgroundColors,
                                // borderColor: borderColors,
                                borderWidth: 1,
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                datalabels: {
                                    formatter: (value, categories) => {

                                        let sum = 0;
                                        let dataArr = categories.chart.data.datasets[0].data;
                                        dataArr.map(data => {
                                            sum += data;
                                        });
                                        // Jika sum (total) adalah 0, kembalikan string kosong
                                        let percentage = (value * 100 / sum).toFixed(2);
                                        if (percentage < 1) {
                                            return '';
                                        }


                                        return percentage + "%";


                                    },

                                    color: '#fff',
                                }
                            },
                            onClick: (event, elements) => {
                                // console.log(event, elements);
                                if (elements.length > 0) {
                                    const firstElement = elements[0];
                                    // const label = barChart.data.labels[firstElement.index];
                                    // const value = barChart.data.datasets[firstElement.datasetIndex].data[
                                    //     firstElement.index];
                                    // console.log(firstElement);
                                    filterChart(firstElement.index + 1);

                                    // alert(
                                    //     `Label: ${label}\nValue: ${value}\nId:${dataFromDatabase.encryptions[firstElement.datasetIndex]}`
                                    // );

                                }
                            },
                        },
                        plugins: [ChartDataLabels]
                    });
                } else {

                    typeChart.data.labels = labels;
                    typeChart.data.datasets[0].data = data;
                    typeChart.update();

                }
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }
        createTypeChart('');
        document.getElementById('tanggal').addEventListener('change', function() {
            const selectedDate = this.value; // Mendapatkan nilai tanggal yang dipilih
            createTypeChart(selectedDate); // Memperbarui chart dengan tanggal yang dipilih
        });
    </script>
    <script>
        const typeCUSD = document.getElementById('pieChartUSD');

        let typeChartUSD = null;

        async function createTypeChartUsd(tanggal) {
            try {
                const dataFromDatabase = await fetchDataFromDatabase(
                    '{{ route('jurnal.getJurnalChart') }}', {
                        type: 'categoryChart',
                        tanggal: tanggal,
                        id: '988847ae-17fd-4469-ac6c-e44b8d6548bd'
                    }
                );

                const labels = dataFromDatabase.labels;
                const data = dataFromDatabase.data;
                const color = dataFromDatabase.color;
                const borderColors = ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)',
                    'rgba(142, 68, 173, 0.2)'
                ];

                const backgroundColors = color;
                if (!typeChartUSD) {
                    typeChartUSD = new Chart(typeCUSD, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: backgroundColors,
                                // borderColor: borderColors,
                                borderWidth: 1,
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                datalabels: {
                                    formatter: (value, categories) => {

                                        let sum = 0;
                                        let dataArr = categories.chart.data.datasets[0].data;
                                        dataArr.map(data => {
                                            sum += data;
                                        });
                                        // Jika sum (total) adalah 0, kembalikan string kosong
                                        let percentage = (value * 100 / sum).toFixed(2);
                                        if (percentage < 1) {
                                            return '';
                                        }


                                        return percentage + "%";


                                    },

                                    color: '#fff',
                                }
                            },
                            onClick: (event, elements) => {
                                // console.log(event, elements);
                                if (elements.length > 0) {
                                    const firstElement = elements[0];
                                    // const label = barChart.data.labels[firstElement.index];
                                    // const value = barChart.data.datasets[firstElement.datasetIndex].data[
                                    //     firstElement.index];
                                    // console.log(firstElement);
                                    filterChart(firstElement.index + 1);

                                    // alert(
                                    //     `Label: ${label}\nValue: ${value}\nId:${dataFromDatabase.encryptions[firstElement.datasetIndex]}`
                                    // );

                                }
                            },
                        },
                        plugins: [ChartDataLabels]
                    });
                } else {

                    typeChartUSD.data.labels = labels;
                    typeChartUSD.data.datasets[0].data = data;
                    typeChartUSD.update();

                }
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }
        createTypeChartUsd('');
        document.getElementById('tanggal').addEventListener('change', function() {
            const selectedDate = this.value; // Mendapatkan nilai tanggal yang dipilih
            createTypeChartUsd(selectedDate); // Memperbarui chart dengan tanggal yang dipilih
        });
    </script>

    {{-- Bar chart --}}
    <script>
        let barChart = null;
        const canvas = document.getElementById('barChart');
        const ctx = document.getElementById('barChart').getContext('2d');

        async function createBarChart(tanggal) {
            try {
                const dataFromDatabase = await fetchDataFromDatabase(
                    '{{ route('jurnal.getJurnalChart') }}', {
                        type: 'barChart',
                        tanggal: tanggal
                    }
                );

                const labels = dataFromDatabase.labels;
                // console.log(dataFromDatabase.data);

                const datasets = Object.keys(dataFromDatabase.data).map((key, index) => {

                    const formattedData = labels.map(label => dataFromDatabase.data[key][label] || 0);
                    return {
                        label: key,
                        data: formattedData
                    };
                });

                // console.log(datasets);


                if (!barChart) {
                    barChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: datasets
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            },
                            layout: {
                                padding: {
                                    top: 70
                                }
                            },
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                datalabels: {
                                    display: true,

                                    anchor: 'end',
                                    align: 'top',
                                    rotation: -90, // Rotate labels to vertical

                                    formatter: (value, categories) => {
                                        // Format label datalabels

                                        if (value > 0) {
                                            // return `(${categories.dataset.label})\n${rupiah(value)}`;
                                            return `${rupiah(value)}`;
                                            // return `<span>(${categories.dataset.label})<br>${rupiah(value)}</span>`
                                        }
                                        return '';

                                    },
                                    color: '#2c3e50',
                                }
                            },
                            onClick: (event, elements) => {
                                // console.log(event, elements);
                                if (elements.length > 0) {
                                    const firstElement = elements[0];
                                    const label = barChart.data.labels[firstElement.index];
                                    const value = barChart.data.datasets[firstElement.datasetIndex].data[
                                        firstElement.index];
                                    // console.log(firstElement);

                                    // alert(
                                    //     `Label: ${label}\nValue: ${value}\nId:${dataFromDatabase.encryptions[firstElement.datasetIndex]}`
                                    // );
                                    let url = '';
                                    const id = dataFromDatabase.encryptions[firstElement.datasetIndex];
                                    url = url.replace(':id', id);
                                    // console.log(id);
                                    clicker(url)
                                }
                            },
                        },

                        plugins: [ChartDataLabels]
                    });
                } else {
                    barChart.data.labels = labels;
                    barChart.data.datasets = datasets;
                    barChart.update();
                }
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        // Fungsi untuk mendapatkan warna acak
        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        createBarChart('');

        document.getElementById('tanggal').addEventListener('change', function() {
            const selectedDate = this.value; // Mendapatkan nilai tanggal yang dipilih
            createBarChart(selectedDate); // Memperbarui chart dengan tanggal yang dipilih
        });
    </script>
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
@endpush
