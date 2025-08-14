<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\JurnalBalance;
use App\Models\JurnalCategory;
use App\Models\Kur;
use App\Services\JurnalService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JurnalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $jurnals = JurnalBalance::get();
        return view('jurnal.index', compact('jurnals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $kurs = Kur::get();
        return view('jurnal.create', compact('kurs'));
    }
    public function jurnalCreate($id)
    {
        $jurnalBalance = JurnalBalance::where('uuid', $id)->first();
        if (!$jurnalBalance) {
            # code...
            return redirect()->route('jurnal.index')->with('error', 'Jurnal not found');
        }
        $category = JurnalCategory::where('is_active', 1)->get();
        return view('jurnal.detail.create', compact('jurnalBalance', 'category'));
    }
    public function jurnalDetailStore(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'jurnal_type' => 'required',
            'jurnal_category_id' => 'required',
            'date' => 'required',
            'balance' => 'required',
            'keterangan' => 'required',
            'kegiatan' => 'required',
            'balance_raw' => 'required',
            'evidence' => 'required|array',
        ]);
        $jurnal = JurnalService::create($request->all(), $id);
        return redirect()->route('jurnal.show', $id)->with('success', 'Jurnal created successfully');
    }

    public function jurnalEdit(Request $request, $balanceId, $jurnalId)
    {
        $jurnalBalance = JurnalBalance::where('uuid', $balanceId)->first();
        if (!$jurnalBalance) {
            # code...
            return redirect()->route('jurnal.index')->with('error', 'Jurnal not found');
        }
        $jurnal = Jurnal::where('uuid', $jurnalId)->first();
        if (!$jurnal) {
            # code...
            return redirect()->route('jurnal.show', $balanceId)->with('error', 'Jurnal not found');
        }
        $category = JurnalCategory::where('is_active', 1)->get();
        return view('jurnal.detail.edit', compact('jurnalBalance', 'category', 'jurnal'));
    }
    public function jurnalUpdate(Request $request, $balanceId, $jurnalId)
    {
        $request->validate([
            'jurnal_type' => 'required',
            'jurnal_category_id' => 'required',
            'date' => 'required',
            'balance' => 'required',
            'keterangan' => 'required',
            'kegiatan' => 'required',
            'balance_raw' => 'required',
        ]);
        $jurnalBalance = JurnalBalance::where('uuid', $balanceId)->first();
        if (!$jurnalBalance) {
            return redirect()->route('jurnal.index')->with('error', 'Jurnal not found');
        }
        $jurnal = Jurnal::where('uuid', $jurnalId)->first();
        if (!$jurnal) {
            return redirect()->route('jurnal.show', $balanceId)->with('error', 'Jurnal not found');
        }
        $service = JurnalService::update($request->all(), $balanceId, $jurnalId);
        return redirect()->route('jurnal.show', $balanceId)->with('success', 'Jurnal updated successfully');
    }
    public function jurnalDelete(string $balanceId, string $jurnalId)
    {
        $jurnalBalance = JurnalBalance::where('uuid', $balanceId)->first();
        if (!$jurnalBalance) {
            return response()->json(['success' => false, 'message' => 'Jurnal not found'], 404);
        }
        $jurnal = Jurnal::where('uuid', $jurnalId)->first();
        if (!$jurnal) {
            return response()->json(['success' => false, 'message' => 'Jurnal not found'], 404);
        }

        $service = JurnalService::delete($jurnalBalance->uuid, $jurnal->uuid);

        return response()->json(['success' => true, 'message' => 'Successfully deleted', 'status' => 200]);
    }
    public function jurnalShow($balanceId, $jurnalId)
    {
        $jurnalBalance = JurnalBalance::where('uuid', $balanceId)->first();
        if (!$jurnalBalance) {
            return response()->json(['success' => false, 'message' => 'Jurnal not found'], 404);
        }
        $jurnal = Jurnal::where('uuid', $jurnalId)->first();
        if (!$jurnal) {
            return response()->json(['success' => false, 'message' => 'Jurnal not found'], 404);
        }

        return view('jurnal.detail.show', compact('jurnalBalance', 'jurnal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'kurs_id' => 'required|unique:jurnal_balances,kurs_id',
        ]);
        $jurnalBalance = new JurnalBalance();
        $jurnalBalance->uuid = uuidGenerator();
        $jurnalBalance->name = $request->name;
        $jurnalBalance->kurs_id = $request->kurs_id;
        $jurnalBalance->balance = 0;
        $jurnalBalance->save();
        return redirect()->route('jurnal.index')->with('success', 'Jurnal Balance created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $jurnalBalance = JurnalBalance::where('uuid', $id)->first();
        if (!$jurnalBalance) {
            # code...
            return redirect()->route('jurnal.index')->with('error', 'Jurnal not found');
        }
        $jurnals = Jurnal::where('jurnal_balance_id', $jurnalBalance->uuid)->orderBy('ordering', 'desc')->get();
        return view('jurnal.show', compact('jurnalBalance', 'jurnals'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function getJurnalChart(Request $request)
    {
        if ($request->ajax()) {
            $date = $request->data['tanggal'] ?? null;
            $type = $request->data['type'] ?? null;
            $id = $request->data['id'] ?? null;


            if ($request->has('data') && $date !== null) {
                $now = Carbon::createFromFormat('Y-m', $request->data['tanggal'] ?? null)->startOfMonth();
            } else {
                $now = Carbon::now();
            }
            $return = [];


            if ($type == 'categoryChart') {
                # code...
                $type = JurnalCategory::where('is_active', 1)->where('id', '!=', 2)->get();
                $jurnals = Jurnal::where('jurnal_balance_id', $id)->orderBy('ordering')->whereYear('date', $now->year)->whereMonth('date', $now->month)->get()->groupBy(function ($q) {
                    return $q->date->format('Y-m');
                });
                $data = [];
                foreach ($type as $key => $t) {
                    $return['labels'][$key] = $t->name;
                    $data[$t->uuid] = 0;
                }
                foreach ($jurnals as $key => $j) {
                    $j = $j->groupBy(function ($q) {
                        return $q->jurnal_category_id;
                    });
                    foreach ($j as $key1 => $j1) {
                        foreach ($j1 as $key2 => $j2) {

                            if ($j2->jurnal_category_id != 'ad983832-5f2d-4770-bd16-712e92236a5f' && $j2->jurnal_category_id != null) {
                                $data[$j2->jurnal_category_id] += $j2->balance;
                            }
                        }
                    }
                }
                $i = 0;
                foreach ($data as $value) {
                    $return['color'][] = '#' . $this->random_color()[$i];
                    $return['data'][] = $value;
                    $i++;
                }
            } elseif ($type == 'barChart') {
                // Inisialisasi $date berdasarkan data request
                $date = $request->data['tanggal'] ?? null ? $request->data['tanggal'] . '-01' : null;

                // Tentukan tanggal 'now', baik akhir bulan dari tanggal yang diberikan atau tanggal sekarang
                $now = $date ? Carbon::createFromFormat('Y-m-d', $date)->endOfMonth() : Carbon::now();

                // Dapatkan tanggal mulai dari bulan pertama tahun ini
                $startOfYear = Carbon::create($now->year, 1, 1)->startOfDay();

                // Ambil dan kelompokkan jurnal
                $jurnals = Jurnal::orderBy('ordering')
                    ->where('jurnal_type', 1)
                    ->whereDate('date', '>=', $startOfYear)
                    ->whereDate('date', '<=', $now)
                    ->get()
                    ->groupBy(fn($q) => $q->jurnal_balance_id)
                    ->sortBy('date');

                // Ambil grup regional yang aktif
                $regional = JurnalBalance::get();

                // Inisialisasi array label
                $return['labels'] = [];
                $start_date = Carbon::now()->startOfYear();
                $end_date = $now->copy()->startOfMonth(); // Memastikan berakhir di awal bulan target

                // Isi array label dengan bulan dan tahun dari awal tahun hingga akhir bulan target
                while ($start_date->lessThanOrEqualTo($end_date)) {
                    $return['labels'][] = $start_date->format('F Y');
                    $start_date->addMonth();
                }

                // Inisialisasi array data
                $return['data'] = [];

                // Inisialisasi array id encryptions
                // $return['encryptions'] = [];

                // Kelompokkan dan format jurnal
                foreach ($jurnals as $reg => $value) {
                    // $return['encryptions'][] = $reg;
                    $value = $value->groupBy(fn($q) => $q->date->format('Y-m'));
                    foreach ($value as $tanggal => $jurnal) {
                        $formattedTanggal = $this->formatTanggal($tanggal);

                        // Pastikan semua regional memiliki entri untuk setiap tanggal
                        foreach ($regional as $r) {
                            if (!isset($return['data'][$r->name][$formattedTanggal])) {
                                $return['data'][$r->name][$formattedTanggal] = 0;
                            }
                        }

                        // Jumlahkan saldo untuk setiap entri jurnal

                        foreach ($jurnal as $j) {
                            $return['data'][$j->JurnalBalance?->name][$formattedTanggal] += $j->balance;
                        }
                    }
                }
                // $return['encryptions'] = $this->bubbleSort($return['encryptions']);
                // foreach ($return['encryptions'] as $key => $value) {
                //     $return['encryptions'][$key] = encString($value);
                // }
            }
        }
        return response()->json($return);
    }
    private function random_color()
    {
        return ['f39c12', 'd35400', 'c0392b', '16a085', '27ae60', '2980b9', '8e44ad', '2c3e50', '7f8c8d', '1B1464', 'B53471', '2c3e50', '2980b9'];
    }
    public function formatTanggal(string $tanggal): string
    {
        return $this->convertToIndonesianFormat($tanggal);
    }
    private function convertToIndonesianFormat(string $tanggal): string
    {
        // Mendapatkan bulan dan tahun dari input tanggal
        [$tahun, $bulan] = explode('-', $tanggal);

        // Nama bulan dalam bahasa Indonesia
        $nama_bulan = [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];

        // Menghasilkan format "Bulan YYYY"
        return $nama_bulan[$bulan] . ' ' . $tahun;
    }
}
