<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\ContractDetail;
use App\Models\ContractList;
use App\Models\Jurnal;
use App\Models\JurnalAttachment;
use App\Models\JurnalBalance;
use App\Models\JurnalHistory;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ContractService
{
    public static function create($data, $contractListId, $balanceId)
    {
        try {
            DB::beginTransaction();
            $description = $data['description'];
            $jurnalBalanceId = $balanceId;
            $total = (int)$data['total_raw'];
            $type = $data['jurnal_type'];
            $transactionDate = $data['transaction_date'];
            $user = Auth::user();
            $isBonus = $data['is_bonus'];
            $attachment = $data['evidence'];
            $jurnalBalance = JurnalBalance::where('uuid', $balanceId)->first();
            if (!$jurnalBalance) {
                throw new Exception('Jurnal tidak ditemukan');
            }
            $contractList = ContractList::where('uuid', $contractListId)->first();
            if (!$contractList) {
                throw new Exception('Contract List saldo tidak ditemukan');
            }


            $getContractByDate = ContractDetail::where('contract_list_id', $contractListId)->where('jurnal_balance_id', $jurnalBalanceId)->whereDate('transaction_date', '>', $transactionDate)->orderBy('ordering', 'asc')->get();
            if ($getContractByDate->count() > 0) {
                $newSisaBalance = 0;
                $ordering = 0;
                foreach ($getContractByDate as $key => $value) {
                    if ($key == 0) {

                        $oldBalance =  $value->sisa_payment;
                        // Get Old balance
                        if ($value->jurnal_type == 0) {
                            $oldBalance = $value->sisa_payment - $value->total;
                        } elseif ($value->jurnal_type == 1) {
                            $oldBalance = $value->sisa_payment + $value->total;
                        } else {
                            return redirect()->back()->with('error', 'Something went wrong');
                        }
                        // new jurnal
                        if ($type == 0) {
                            $newSisaBalance = $oldBalance + $total;
                        } elseif ($type == 1) {
                            $newSisaBalance = $oldBalance - $total;
                        } else {
                            return redirect()->back()->with('error', 'Something went wrong');
                        }

                        $ordering = $value->ordering;
                        $contract = new ContractDetail();
                        $contract->uuid = uuidGenerator();
                        $contract->contract_list_id = $contractListId;
                        $contract->description = $description;
                        $contract->user_id = $user->id;

                        $contract->jurnal_balance_id = $jurnalBalanceId;
                        $contract->total = $total;
                        $contract->type = $type;
                        $contract->transaction_date = $transactionDate;
                        $contract->is_bonus = $isBonus;
                        $contract->ordering = $ordering;
                        $ordering = $ordering + 1;
                        if ($transactionDate != null) {
                            $contract->transaction_date =  $transactionDate;
                        } else {
                            $contract->transaction_date = Carbon::now();
                        }
                        // if ($request->has('lainnya')) {
                        //     $jurnal->lainnya = $request->lainnya;
                        // }

                        $contract->total = $total;
                        $contract->sisa_payment = $newSisaBalance;
                        $contract->jurnal_balance_id = $jurnalBalanceId;
                        $contract->save();

                        // history

                        // $jurnalHistory = new JurnalHistory();
                        // $jurnalHistory->uuid = uuidGenerator();
                        // $jurnalHistory->user_id = Auth::user()->uuid;
                        // $jurnalHistory->reference_id = $jurnal->uuid;
                        // $jurnalHistory->type = 0;
                        // $jurnalHistory->save();

                        // endhistory

                        // File
                        // if (count($attachment) > 0) {

                        //     $files = $attachment;

                        //     $path = '/jurnal/' . uuidGenerator() . '/upload/';
                        //     foreach ($files as $key => $file) {
                        //         if ($file == null) {
                        //             continue;
                        //         }
                        //         $random_name = Str::random(12);
                        //         Storage::disk('local')->put('/public' . $path . $random_name . '_' . time() . '.' . $file->getClientOriginalExtension(), file_get_contents($file));
                        //         $attach = new JurnalAttachment();
                        //         $attach->file_name = $file->getClientOriginalName();
                        //         $attach->ext = $file->getClientOriginalExtension();
                        //         $attach->jurnal_id = $jurnal->uuid;
                        //         $attach->size = $file->getSize();
                        //         $attach->upload_by = Auth::user()->uuid;
                        //         $attach->path = $path . $random_name . '_' . time() . '.' . $file->getClientOriginalExtension();
                        //         $attach->save();
                        //     }
                        // }

                        // End File

                        // proccess one up of date
                        if ($value->type == 0) {
                            $newSisaBalance = $newSisaBalance + $value->total;
                        } elseif ($value->type == 1) {
                            $newSisaBalance =  $newSisaBalance - $value->total;
                            // dd($newSisaBalance . ' - ' . $value->balance . ' = ' . $newSisaBalance - $value->balance . ' || ' . $newSisaBalance);
                        } else {
                            return redirect()->back()->with('error', 'Something went wrong');
                        }
                        $value->sisa_payment = $newSisaBalance;
                        $value->ordering = $ordering;
                        $value->save();
                        $ordering = $ordering + 1;
                        continue;
                    }
                    // procces if more then one jurnal 
                    if ($value->type == 0) {

                        $newSisaBalance = $newSisaBalance + $value->total;
                    } elseif ($value->type == 1) {
                        $newSisaBalance = $newSisaBalance - $value->total;
                    } else {
                        return redirect()->back()->with('error', 'Something went wrong');
                    }
                    $value->ordering = $ordering;
                    $value->sisa_payment = $newSisaBalance;
                    $value->save();
                    $ordering = $ordering + 1;
                }
                if ($jurnalBalance->Kurs?->code === 'IDR') {
                    $contractList->nilai_contract_idr = $newSisaBalance;
                } elseif ($jurnalBalance->Kurs?->code === 'USD') {
                    $contractList->nilai_contract_usd = $newSisaBalance;
                } else {
                    throw new Exception('Something wen wrong when Fetching Kurs');
                }
            } else {
                $getLatestContract = ContractDetail::where('contract_list_id', $contractListId)->where('jurnal_balance_id', $jurnalBalanceId)->orderBy('ordering', 'desc')->first();
                if (!$getLatestContract) {
                    # code...
                    $oldBalance = 0;
                    $orderingOne = 1;
                } else {

                    $oldBalance = $getLatestContract->sisa_payment;
                    $orderingOne = $getLatestContract->ordering + 1;
                }
                // new jurnal
                if ($type == 0) {
                    $newSisaBalance = $oldBalance + $total;
                } elseif ($type == 1) {
                    $newSisaBalance = $oldBalance - $total;
                } else {
                    return redirect()->back()->with('error', 'Something went wrong');
                }

                $contract = new ContractDetail();
                $contract->uuid = uuidGenerator();
                $contract->contract_list_id = $contractListId;
                $contract->user_id = $user->id;
                $contract->description = $description;
                $contract->jurnal_balance_id = $jurnalBalanceId;
                $contract->total = $total;
                $contract->type = $type;
                $contract->transaction_date = $transactionDate;
                $contract->is_bonus = $isBonus;
                $contract->ordering = $orderingOne;
                if ($transactionDate != null) {
                    $contract->transaction_date =  $transactionDate;
                } else {
                    $contract->transaction_date = Carbon::now();
                }
                // if ($request->has('lainnya')) {
                //     $jurnal->lainnya = $request->lainnya;
                // }

                $contract->total = $total;
                $contract->sisa_payment = $newSisaBalance;
                $contract->jurnal_balance_id = $jurnalBalanceId;
                $contract->save();
                // if ($request->has('lainnya')) {
                //     $jurnal->lainnya = $request->lainnya;
                // }


                // history

                // $jurnalHistory = new JurnalHistory();
                // $jurnalHistory->uuid = uuidGenerator();
                // $jurnalHistory->user_id = Auth::user()->uuid;
                // $jurnalHistory->reference_id = $jurnal->uuid;
                // $jurnalHistory->type = 0;
                // $jurnalHistory->save();

                // endhistory

                // File
                // if (count($attachment) > 0) {

                //     $files = $attachment;

                //     $path = '/jurnal/' . uuidGenerator() . '/upload/';
                //     foreach ($files as $key => $file) {
                //         if ($file == null) {
                //             continue;
                //         }
                //         $random_name = Str::random(12);
                //         Storage::disk('local')->put('/public' . $path . $random_name . '_' . time() . '.' . $file->getClientOriginalExtension(), file_get_contents($file));
                //         $attach = new JurnalAttachment();
                //         $attach->file_name = $file->getClientOriginalName();
                //         $attach->ext = $file->getClientOriginalExtension();
                //         $attach->jurnal_id = $jurnal->uuid;
                //         $attach->size = $file->getSize();
                //         $attach->upload_by = Auth::user()->uuid;
                //         $attach->path = $path . $random_name . '_' . time() . '.' . $file->getClientOriginalExtension();
                //         $attach->save();
                //     }
                // }
                if ($jurnalBalance->Kurs?->code === 'IDR') {
                    $contractList->nilai_contract_idr = $newSisaBalance;
                } elseif ($jurnalBalance->Kurs?->code === 'USD') {
                    $contractList->nilai_contract_usd = $newSisaBalance;
                } else {
                    throw new Exception('Something wen wrong when Fetching Kurs');
                }
            }

            $contractList->save();
            DB::commit();
            return $contract;
        } catch (QueryException $e) {
            DB::rollBack();
            dd($e);
            return ['message' => 'Error creating Contract. ' . $e->getMessage()];
        }
    }
    public static function edit($data, $contractDetailId, $contractListId)
    {
        try {
            DB::beginTransaction();
            $contractList = ContractList::where('uuid', $contractListId)->first();
            if (!$contractList) {
                throw new Exception('Kurs saldo tidak ditemukan');
            }
            $contractDetail = ContractDetail::where('uuid', $contractDetailId)->first();
            if (!$contractDetail) {
                throw new Exception('Jurnal kas tidak ditemukan');
            }
            $now = Carbon::now();
            $oldJurnalData = $contractDetail;
            $jurnalType = $data['jurnal_type'];
            $date = $data['transaction_date'];
            $nominal = $data['total_raw'];
            $description = $data['description'];
            $evidence = isset($data['evidence']) ? $data['evidence'] : [];
            $oldBalance = $contractDetail->total;
            $newJurnalBalance = $contractList->balance;




            // if (count($evidence) > 0) {

            //     $files = $evidence;
            //     $path = '/jurnal/' . uuidGenerator() . '/upload/';
            //     foreach ($files as $key => $file) {
            //         $random_name = Str::random(12);
            //         Storage::disk('local')->put('public/' . $path . $random_name . '_' . time() . '.' . $file->getClientOriginalExtension(), file_get_contents($file));
            //         $attach = new JurnalAttachment();
            //         $attach->file_name = $file->getClientOriginalName();
            //         $attach->ext = $file->getClientOriginalExtension();
            //         $attach->jurnal_id = $jurnal->id;
            //         $attach->size = $file->getSize();
            //         $attach->upload_by = Auth::user()->id;
            //         $attach->path = $path . $random_name . '_' . time() . '.' . $file->getClientOriginalExtension();
            //         $attach->save();
            //     }
            // }
            $contractDetail->total = $nominal;
            $contractDetail->description = $description;

            $contractDetail->save();



            // $jurnalHistory = new JurnalHistory();
            // $jurnalHistory->uuid = uuidGenerator();
            // $jurnalHistory->user_id = Auth::user()->uuid;
            // $jurnalHistory->reference_id = $jurnal->uuid;
            // $jurnalHistory->data = json_encode($oldJurnalData);
            // $jurnalHistory->type = 1;
            // $jurnalHistory->save();
            if ($contractDetail->balance != $nominal || $contractDetail->jurnal_type != $jurnalType || formatTimeRaw($contractDetail->date) != $date) {


                $contractDetail->transaction_date = $date . ' ' . $now->hour . ':' . $now->minute . ':' . $now->second;
                $contractDetail->total = $nominal;
                $contractDetail->type = $jurnalType;
                $contractDetail->save();
                ContractService::hardSyncContract($contractList->uuid);
            }

            DB::commit();
            return true;
        } catch (QueryException $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function delete($contractDetailId, $contractListId)
    {
        try {
            DB::beginTransaction();
            $contractList = ContractList::where('uuid', $contractListId)->first();
            if (!$contractList) {
                throw new Exception('Kurs saldo tidak ditemukan');
            }
            $contractDetail = ContractDetail::where('uuid', $contractDetailId)->first();
            if (!$contractDetail) {
                throw new Exception('Jurnal kas tidak ditemukan');
            }
            if ($contractDetail->is_bonus == 1) {
                # code...
                $contractDetail->delete();
            } else {
                $contractDetail->delete();
                ContractService::hardSyncContract($contractList->uuid);
            }
            DB::commit();
            return true;
        } catch (QueryException $e) {
            DB::rollBack();
            throw $e;
        }
    }
    private static function hardSyncContract($contractListId, $veryHardSync = false)
    {
        try {
            DB::beginTransaction();
            if (!$veryHardSync) {
                # code...
                $contractList = ContractList::where('uuid', $contractListId)->first();
                if (!$contractList) {
                    throw new Exception('Kurs saldo tidak ditemukan');
                }
                $contractDetail = ContractDetail::where('contract_list_id', $contractListId)->orderBy('transaction_date', 'asc')->where('is_bonus', '!=', 1)->get();

                foreach (
                    $contractDetail->groupBy(fn($q) => $q->JurnalBalance?->Kurs?->code) as $key => $item
                ) {
                    $ordering = 1;
                    $newBalance = 0;
                    foreach ($item as $key1 => $value) {
                        # code...
                        $value->ordering = $ordering;
                        if ($value->type == 0) {
                            $newBalance += $value->total;
                        } else if ($value->type == 1) {
                            $newBalance -= $value->total;
                        } else {

                            return redirect()->back()->with('error', 'Something went wrong');
                        }
                        $value->sisa_payment = $newBalance;
                        $value->save();
                        $ordering++;
                    }
                    if ($key === 'IDR') {
                        # code...
                        $contractList->nilai_contract_idr = $newBalance;
                    } else if ($key === 'USD') {
                        # code...
                        $contractList->nilai_contract_usd = $newBalance;
                    } else {
                        throw new Exception('Something went wrong');
                    }
                    $contractList->save();
                }
            }
            DB::commit();
            return true;
        } catch (QueryException $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
