<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractDetail;
use App\Models\ContractList;
use App\Models\JurnalBalance;
use App\Services\ContractService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($contract_id, $contractListId)
    {
        //
        $contract = Contract::where('uuid', $contract_id)->first();
        if (!$contract) {
            return redirect()->route('contracts.index')->with('error', 'Contract not found');
        }
        $contractList = ContractList::where('uuid', $contractListId)->first();
        if (!$contractList) {
            return redirect()->route('contracts.list.index', $contract->uuid)->with('error', 'Contract List not found');
        }

        $contractDetails = ContractDetail::with(['JurnalBalance', 'JurnalBalance.Kurs'])->where('contract_list_id', $contractList->uuid)->where('is_bonus', '!=', 1)->orderBy('ordering', 'desc')->get();


        $contractDetailBonus = ContractDetail::with(['JurnalBalance', 'JurnalBalance.Kurs'])->where('contract_list_id', $contractList->uuid)->where('is_bonus', 1)->get();

        return view('contract.contract_list.contract_details.index', compact('contract', 'contractList', 'contractDetails', 'contractDetailBonus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($contract_id, $contractListId)
    {
        //
        $contract = Contract::where('uuid', $contract_id)->first();
        if (!$contract) {
            return redirect()->route('contracts.index')->with('error', 'Contract not found');
        }
        $contractList = ContractList::where('uuid', $contractListId)->first();
        if (!$contractList) {
            return redirect()->route('contracts.list.index', $contract->uuid)->with('error', 'Contract List not found');
        }
        $jurnalBalance = JurnalBalance::get();
        return view('contract.contract_list.contract_details.create', compact('contract', 'contractList', 'jurnalBalance'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $contract_id, $contractListId)
    {
        //

        try {
            DB::beginTransaction();
            $contract = Contract::where('uuid', $contract_id)->first();
            if (!$contract) {
                return redirect()->route('contracts.index')->with('error', 'Contract not found');
            }
            $contractList = ContractList::where('uuid', $contractListId)->first();
            if (!$contractList) {
                return redirect()->route('contracts.list.index', $contract->uuid)->with('error', 'Contract List not found');
            }

            $request->validate([
                'description' => 'required',
                'jurnal_type' => 'required_if:is_bonus,0',
                'kurs_id' => 'required',
                'transaction_date' => 'required',
                'total' => 'required',
                'evidence' => 'required',
                'is_bonus' => 'required',
            ]);

            if ($request->is_bonus != 1) {
                $createDetail = ContractService::create($request->all(), $contractList->uuid, $request->kurs_id);
                // if ($contractDetail->type == 0) {
                //     if ($contractDetail->JurnalBalance?->Kurs?->code == 'IDR') {
                //         $contractList->nilai_contract_idr = $contractList->nilai_contract_idr + $contractDetail->total;
                //     } elseif ($contractDetail->JurnalBalance?->Kurs?->code == 'USD') {
                //         $contractList->nilai_contract_usd = $contractList->nilai_contract_usd + $contractDetail->total;
                //     } else {
                //         return redirect()->route('contracts.list.detail.index', [$contract->uuid, $contractList->uuid])->with('error', 'SOMETHING ERROR');
                //     }
                // } elseif ($contractDetail->type == 1) {
                //     if ($contractDetail->JurnalBalance?->Kurs?->code == 'IDR') {
                //         $contractList->nilai_contract_idr = $contractList->nilai_contract_idr - $contractDetail->total;
                //     } elseif ($contractDetail->JurnalBalance?->Kurs?->code == 'USD') {
                //         $contractList->nilai_contract_usd = $contractList->nilai_contract_usd - $contractDetail->total;
                //     } else {
                //         return redirect()->route('contracts.list.detail.index', [$contract->uuid, $contractList->uuid])->with('error', 'SOMETHING ERROR');
                //     }
                // } else {
                //     return redirect()->route('contracts.list.detail.index', [$contract->uuid, $contractList->uuid])->with('error', 'SOMETHING ERROR');
                // }
                // $contractList->save();
            } else {
                $contractDetail = new ContractDetail();
                $contractDetail->uuid = uuidGenerator();
                $contractDetail->contract_list_id = $contractList->uuid;
                $contractDetail->description = $request->description;
                $contractDetail->total = $request->total_raw;
                $contractDetail->jurnal_balance_id = $request->kurs_id;
                $contractDetail->user_id = Auth::user()->uuid;
                if ($request->is_bonus != 1) {
                    $contractDetail->type = $request->jurnal_type;
                }
                $contractDetail->is_bonus = $request->is_bonus == null ? 0 : $request->is_bonus;
                $contractDetail->transaction_date = formatToTimeNow($request->transaction_date);
                $contractDetail->save();
            }
            DB::commit();
            return redirect()->route('contracts.list.detail.index', [$contract->uuid, $contractList->uuid])->with('success', 'Contract List Detail has been Created');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route('contracts.list.contract_details.create', [$contract_id, $contractListId])->with('error', 'Failed to create Contract Detail. Please try again.');
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
    public function edit($contract_id, $contractListId, string $contractDetailId)
    {
        //
        $contract = Contract::where('uuid', $contract_id)->first();
        if (!$contract) {
            return redirect()->route('contracts.index')->with('error', 'Contract not found');
        }
        $contractList = ContractList::where('uuid', $contractListId)->first();
        if (!$contractList) {
            return redirect()->route('contracts.list.index', $contract->uuid)->with('error', 'Contract List not found');
        }
        $contractDetail = ContractDetail::where('uuid', $contractDetailId)->first();
        if (!$contractDetail) {
            return redirect()->route('contracts.list.detail.index', [$contract_id, $contractListId])->with('error', 'Contract Detail Not Found');
        }
        $jurnalBalance = JurnalBalance::get();

        return view('contract.contract_list.contract_details.edit', compact('contract', 'jurnalBalance', 'contractList', 'contractDetail'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $contract_id, $contractListId, string $contractDetailId)
    {

        try {
            DB::beginTransaction();
            $contract = Contract::where('uuid', $contract_id)->first();
            if (!$contract) {
                return redirect()->route('contracts.index')->with('error', 'Contract not found');
            }
            $contractList = ContractList::where('uuid', $contractListId)->first();
            if (!$contractList) {
                return redirect()->route('contracts.list.index', $contract->uuid)->with('error', 'Contract List not found');
            }
            $contractDetail = ContractDetail::where('uuid', $contractDetailId)->first();
            if (!$contractList) {
                return redirect()->route('contracts.list.detail.index', [$contract_id, $contractListId])->with('error', 'Contract List not found');
            }

            $request->validate([
                'description' => 'required',
                'jurnal_type' => 'required_if:is_bonus,0',
                'kurs_id' => 'required',
                'transaction_date' => 'required',
                'total' => 'required',
                'evidence' => 'required',
                'is_bonus' => 'required',
            ]);
            ContractService::edit($request->all(), $contractDetail->uuid, $contractList->uuid);
            DB::commit();
            return redirect()->route('contracts.list.detail.index', [$contract->uuid, $contractList->uuid])->with('success', 'Contract List Detail has been Created');
        } catch (QueryException $e) {
            DB::rollBack();
            dd($e);
            return redirect()->route('contracts.list.contract_details.create', [$contract_id, $contractListId])->with('error', 'Failed to create Contract Detail. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, $contractListId, string $contractDetailId)
    {
        //
        try {
            DB::beginTransaction();
            $contractList = ContractList::where('uuid', $contractListId)->first();
            if (!$contractList) {
                return response()->json(['message' => 'Contract List Not Found', 'success' => false], 404);
            }
            $contractDetail = ContractDetail::where('uuid', $id)->first();
            if (!$contractDetail) {
                return response()->json(['message' => 'Contract Detail Not Found', 'success' => false], 404);
            }
            ContractService::delete($contractDetailId, $contractListId);
            DB::commit();
            return response()->json(['message' => 'Contract List Detail has been deleted', 'success' => true]);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to delete Contract List Detail', 'success' => false], 500);
        }
    }
}
