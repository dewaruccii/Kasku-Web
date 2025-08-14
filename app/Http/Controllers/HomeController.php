<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Jurnal;
use App\Models\JurnalBalance;
use App\Models\MoneyCharger;
use App\Models\Reimburse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function index()
    {
        $jurnals = Jurnal::latest()->limit(10)->get();
        $balance = JurnalBalance::get();
        $reimburse = Reimburse::count();
        $moneyCharge = MoneyCharger::count();
        $contract = Contract::with(['List', 'List.Detail'])->get();
        return view('home', compact('jurnals', 'balance', 'reimburse', 'moneyCharge', 'contract'));
    }
}
