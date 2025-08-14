<?php

namespace App\Http\Controllers;

use App\Models\JurnalBalance;
use App\Models\Kur;
use App\Models\Reimburse;
use App\Services\JurnalService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReimburseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $reimburses = Reimburse::latest()->get();
        return view('reimburse.index', compact('reimburses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $kurs = JurnalBalance::get();
        return view('reimburse.create', compact('kurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'justifikasi' => 'required',
            'total' => 'required',
            'type' => 'required',
            'jurnal_id' => 'required',
            'evidence' => 'required|array',
        ]);
        $now = Carbon::now();
        try {
            DB::beginTransaction();
            $reimburse = new Reimburse();
            $reimburse->uuid = uuidGenerator();
            $reimburse->user_id = Auth::user()->uuid;
            $reimburse->justifikasi = $request->justifikasi;
            $reimburse->total = $request->total_raw;
            $reimburse->type = $request->type;
            $reimburse->jurnal_id = $request->jurnal_id;
            if ($request->date != null) {
                # code...
                $reimburse->date = $request->date . ' ' . $now->hour . ':' . $now->minute . ':' . $now->second;
            } else {

                $reimburse->date = $now;
            }
            $reimburse->save();

            $jurnal = JurnalService::create([
                'jurnal_type' => $reimburse->type,
                'jurnal_category_id' => 'c1fa1677-a07b-4a88-8a98-4ad932caed28',
                'date' => $reimburse->date,
                'balance_raw' => $reimburse->total,
                'keterangan' => 'Reimbursement',
                'kegiatan' => $reimburse->justifikasi,
                'evidence' => $request->evidence,
            ], $reimburse->jurnal_id);
            $reimburse->jurnal_reference = $jurnal->uuid;
            $reimburse->save();
            DB::commit();
            return redirect()->route('reimburses.index')->with('success', 'Reimburse has been successfully');
        } catch (QueryException $e) {
            DB::rollBack();
            dd($e);
            return redirect()->back()->withInput()->withErrors(['message' => 'Gagal melakukan reimburse. Mohon coba lagi.']);
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
        $reimburse = Reimburse::where('uuid', $id)->first();
        if (!$reimburse) {
            return redirect()->back()->with('error', 'Reimburse not found');
        }
        $kurs = JurnalBalance::get();

        return view('reimburse.edit', compact('reimburse', 'kurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $request->validate([
            'justifikasi' => 'required',
            'total' => 'required',
            'type' => 'required',
            'jurnal_id' => 'required',
            'evidence' => 'required|array',
        ]);
        $now = Carbon::now();

        try {
            DB::beginTransaction();
            $reimburse = Reimburse::where('uuid', $id)->first();
            if (!$reimburse) {
                return redirect()->back()->with('error', 'Reimburse not found');
            }
            $oldType = $reimburse->type;
            $oldJurnalBalance = $reimburse->jurnal_id;
            $oldTotal = $reimburse->total;
            $oldDate = $reimburse->date;
            $oldJurnalReference = $reimburse->jurnal_reference;
            $reimburse->justifikasi = $request->justifikasi;
            if ($oldTotal != $request->total_raw) {
                $reimburse->total = $request->total_raw;
            }
            if ($oldType != $request->type) {
                $reimburse->type = $request->type;
            }
            if ($oldJurnalBalance != $request->jurnal_id) {
                $reimburse->jurnal_id = $request->jurnal_id;
            }
            if (formatTimeRaw($oldDate) != $request->date) {
                if ($request->date != null) {
                    $reimburse->date = $request->date . ' ' . $now->hour . ':' . $now->minute . ':' . $now->second;
                } else {

                    $reimburse->date = $now;
                }
            }
            $reimburse->save();

            if ($oldTotal != $request->total_raw || $oldType != $request->type || $oldJurnalBalance != $request->jurnal_id || formatTimeRaw($oldDate) != $request->date) {
                $deleteJurnal = JurnalService::delete($reimburse->jurnal_id, $reimburse->jurnal_reference);
                $jurnal = JurnalService::create([
                    'jurnal_type' => $reimburse->type,
                    'jurnal_category_id' => 'c1fa1677-a07b-4a88-8a98-4ad932caed28',
                    'date' => $reimburse->date,
                    'balance_raw' => $reimburse->total,
                    'keterangan' => 'Reimbursement',
                    'kegiatan' => $reimburse->justifikasi,
                    'evidence' => $request->evidence,
                ], $reimburse->jurnal_id);
                $attachment = JurnalService::changeAttachment($oldJurnalReference, $jurnal->uuid);
                $reimburse->jurnal_reference = $jurnal->uuid;
                $reimburse->save();
            }

            DB::commit();
            return redirect()->route('reimburses.index')->with('success', 'Reimburse has been successfully');
        } catch (QueryException $e) {
            DB::rollBack();
            dd($e);
            return redirect()->back()->withInput()->withErrors(['message' => 'Gagal melakukan reimburse. Mohon coba lagi.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        try {
            DB::beginTransaction();
            $reimburse = Reimburse::where('uuid', $id)->first();
            if (!$reimburse) {
                return response()->json(['success' => false, 'message' => 'Reimburse Not Found']);
            }
            $deleteJurnal = JurnalService::delete($reimburse->jurnal_id, $reimburse->jurnal_reference);
            $reimburse->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Reimburse Delete Successfully', 'success' => true]);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
