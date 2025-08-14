<?php

namespace App\Http\Controllers;

use App\Models\Kur;
use App\Models\KurExchange;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


class KursController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $kurs = Kur::get();
        return view('master.kurs.index', compact('kurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('master.kurs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'symbol' => 'required',
            'code' => 'required|unique:kurs,code',
        ]);
        try {
            DB::beginTransaction();

            $exchange = Http::get(env('EXCHANGE_RATE_URL') . env('EXCHANGE_RATE_API_KEY') . '/pair/USD/' . $request->code);
            $exchange = $exchange->json();
            if ($exchange['result'] === 'error') {
                # code...
                return redirect()->back()->with('error', 'Kurs Error ' . $exchange['error-type'])->withInput($request->all());
            }
            $kurs = new Kur();
            $kurs->uuid = Str::uuid();
            $kurs->symbol = $request->symbol;
            $kurs->code = $request->code;
            $kurs->save();

            $kurs_exchange = new KurExchange();
            $kurs_exchange->uuid = Str::uuid();
            $kurs_exchange->kurs_id = $kurs->uuid;
            $kurs_exchange->exchange = $exchange['conversion_rate'];
            $kurs_exchange->time = Carbon::now();
            $kurs_exchange->save();

            DB::commit();
            return redirect()->route('master.kurs.index')->with('success', 'Kurs Berhasil di Tambahkan');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Kurs Error ' . $e->getMessage());
        }
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
