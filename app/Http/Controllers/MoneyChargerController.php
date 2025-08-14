<?php

namespace App\Http\Controllers;

use App\Models\JurnalBalance;
use App\Models\MoneyCharger;
use App\Services\JurnalService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MoneyChargerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $money_chargers = MoneyCharger::with(['From', 'From.Kurs', 'From.Kurs.KursExhcange'])->latest()->get();
        return view('money-charger.index', compact('money_chargers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $jurnalBalance = JurnalBalance::get();
        return view('money-charger.create', compact('jurnalBalance'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'type' => 'required',
            'date' => 'required',
            'from' => 'required',
            'to' => 'required',
            'from_total' => 'required',
            'to_total' => 'required',
            'evidence' => 'required',
            'tanggal_kesepakatan' => 'required_if:type,0',
            // 'total_kesepakatan' => 'required_if:type,0'

        ]);
        try {
            DB::beginTransaction();
            $now = Carbon::now();
            // dd(ExchangeService::getKursFromGlobal('IDR', $request->tanggal_kesepakatan));
            $money_charger = new MoneyCharger();
            $money_charger->uuid = uuidGenerator();
            $money_charger->type = $request->type;
            $money_charger->tanggal_kesepakatan = $request->tanggal_kesepakatan .  ' ' . $now->hour . ':' . $now->minute . ':' . $now->second;

            $money_charger->from = $request->from;
            $money_charger->to = $request->to;
            $money_charger->total_from = $request->from_total_raw;
            $money_charger->total_to = $request->to_total_raw;
            $money_charger->total_fee = $request->fee_total_raw == null ? 0 : $request->fee_total_raw;
            $money_charger->exchange_date = $request->date . ' ' . $now->hour . ':' . $now->minute . ':' . $now->second;
            $money_charger->save();

            if ($money_charger->type == 0) {

                $data = [
                    'jurnal_type' => 0,
                    'jurnal_category_id' => '6f4c8b18-4f5d-45d0-9b0b-e31e57cc1490',
                    'date' => $money_charger->exchange_date,
                    'balance_raw' => $money_charger->total_to,
                    'keterangan' => 'Money Charger',
                    'kegiatan' => 'money Charger',
                    'evidence' => $request->file('evidence')
                ];

                $jurnal = JurnalService::create($data, $money_charger->to);
                $money_charger->jurnal_to_id = $jurnal->uuid;
                $money_charger->save();
            } elseif ($money_charger->type == 1) {

                $dataFrom = [
                    'jurnal_type' => 1,
                    'jurnal_category_id' => '6f4c8b18-4f5d-45d0-9b0b-e31e57cc1490',
                    'date' => $money_charger->exchange_date,
                    'balance_raw' => $money_charger->total_from,
                    'keterangan' => 'Money Charger',
                    'kegiatan' => 'money Charger',
                    'evidence' => $request->file('evidence')
                ];
                $dataTo = [
                    'jurnal_type' => 0,
                    'jurnal_category_id' => '6f4c8b18-4f5d-45d0-9b0b-e31e57cc1490',
                    'date' => $money_charger->exchange_date,
                    'balance_raw' => $money_charger->total_to,
                    'keterangan' => 'Money Charger',
                    'kegiatan' => 'money Charger',
                    'evidence' => $request->file('evidence')
                ];
                $jurnalFrom = JurnalService::create($dataFrom, $money_charger->from);
                $jurnalTo = JurnalService::create($dataTo, $money_charger->to);
                $money_charger->jurnal_from_id = $jurnalFrom->uuid;
                $money_charger->jurnal_to_id = $jurnalTo->uuid;
                $money_charger->save();
            } else {
                return redirect()->back()->with('error', 'Type ERROR')->withInput($request->all());
            }
            DB::commit();
            return redirect()->route('money-chargers.index')->with('success', 'Money Charger Added Success');
        } catch (QueryException $e) {
            DB::rollBack();
            dd($e);
        }


        return redirect()->route('money-chargers.index')->with('success', 'Input succes');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
}
