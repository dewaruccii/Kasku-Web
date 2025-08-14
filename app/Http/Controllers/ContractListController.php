<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($contract_id)
    {
        $contract = Contract::where('uuid', $contract_id)->first();
        if (!$contract) {
            return redirect()->route('contracts.index')->with('error', 'Contract not found');
        }

        $contractLists = ContractList::with(['Detail', 'Detail.JurnalBalance', 'Detail.JurnalBalance.Kurs'])->where('contract_id', $contract->uuid)->get();
        return view('contract.contract_list.index', compact('contract', 'contractLists'));
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($contract_id)
    {
        //
        $contract = Contract::where('uuid', $contract_id)->first();
        if (!$contract) {
            return redirect()->route('contracts.index')->with('error', 'Contract not found');
        }
        return view('contract.contract_list.create', compact('contract'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($contract_id, Request $request)
    {
        //
        $contract = Contract::where('uuid', $contract_id)->first();
        if (!$contract) {
            return redirect()->route('contracts.index')->with('error', 'Contract not found');
        }
        $request->validate([
            'materi_kuasa' => 'required',
            'tanggal_kuasa' => 'required',
            'tanggal_contract' => 'required',

        ]);

        $contractLists = new ContractList();
        $contractLists->uuid = uuidGenerator();
        $contractLists->contract_id = $contract->uuid;
        $contractLists->user_id = Auth::user()->uuid;
        $contractLists->materi_kuasa = $request->materi_kuasa;
        $contractLists->tanggal_kuasa = formatToTimeNow($request->tanggal_kuasa);
        $contractLists->tanggal_contract = formatToTimeNow($request->tanggal_contract);
        $contractLists->save();

        return redirect()->route('contracts.list.index', $contract->uuid)->with('success', 'Contract has been Created');
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
    public function edit(string $contract_id, string $id)
    {

        $contract = Contract::where('uuid', $contract_id)->first();
        if (!$contract) {
            return redirect()->route('contracts.index')->with('error', 'Contract not found');
        }
        $contractList = ContractList::where('uuid', $id)->first();
        if (!$contractList) {
            return redirect()->route('contracts.list.index', $contract->uuid)->with('error', 'Contract List not found');
        }

        return view('contract.contract_list.edit', compact('contract', 'contractList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $contract_id, string $id)
    {
        //
        $contract = Contract::where('uuid', $contract_id)->first();
        if (!$contract) {
            return redirect()->route('contracts.index')->with('error', 'Contract not found');
        }
        $contractList = ContractList::where('uuid', $id)->first();
        if (!$contractList) {
            return redirect()->route('contracts.list.index', $contract->uuid)->with('error', 'Contract List not found');
        }
        $request->validate([
            'materi_kuasa' => 'required',
            'tanggal_kuasa' => 'required',
            'tanggal_contract' => 'required',

        ]);
        $contractList->materi_kuasa = $request->materi_kuasa;
        $contractList->tanggal_kuasa = $request->tanggal_kuasa;
        $contractList->tanggal_contract = $request->tanggal_contract;
        $contractList->save();
        return redirect()->route('contracts.list.index', $contract->uuid)->with('success', 'Contract has been Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
