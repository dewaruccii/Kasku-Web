<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $contracts = Contract::get();
        return view('contract.index', compact('contracts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('contract.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'contract_name' => 'required',
        ]);

        $contract = new Contract();
        $contract->uuid = uuidGenerator();
        $contract->contract_name = $request->contract_name;
        $contract->user_id = Auth::user()->uuid;
        $contract->save();
        return redirect()->route('contracts.index')->with('success', 'Contract created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $contract = Contract::where('uuid', $id)->first();

        if (!$contract) {
            return redirect()->route('contracts.index')->with('error', 'Contract not found');
        }
        $contractLists = ContractList::with(['Detail', 'Detail.JurnalBalance', 'Detail.JurnalBalance.Kurs'])->where('contract_id', $contract->uuid)->get();

        return view('contract.contract_list.index', compact('contract', 'contractLists'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $contract = Contract::where('uuid', $id)->first();
        if (!$contract) {
            return redirect()->route('contracts.index')->with('error', 'Contract not found');
        }
        return view('contract.edit', compact('contract'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $request->validate([
            'contract_name' => 'required',
        ]);
        $contract = Contract::where('uuid', $id)->first();
        if (!$contract) {
            return redirect()->route('contracts.index')->with('error', 'Contract not found');
        }
        $contract->contract_name = $request->contract_name;
        $contract->save();
        return redirect()->route('contracts.index')->with('success', 'Contract updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
