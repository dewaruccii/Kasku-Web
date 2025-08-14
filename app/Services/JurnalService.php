<?php

namespace App\Services;

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


class JurnalService
{
    public static function create($data, $balanceId)
    {
        // Implement database transaction logic to create a new journal entry
        // Return the journal ID if successful, or throw an exception otherwise
        try {
            DB::beginTransaction();
            $jurnalBalance = JurnalBalance::where('uuid', $balanceId)->first();
            if (!$jurnalBalance) {
                throw new Exception('Kurs saldo tidak ditemukan');
            }
            $jurnalType = $data['jurnal_type'];
            $jurnalCategoryId = $data['jurnal_category_id'];
            $date = $data['date'];
            $nominal = $data['balance_raw'];
            $keterangan = $data['keterangan'];
            $kegiatan = $data['kegiatan'];
            $evidence = $data['evidence'] ?? [];
            $getJurnalByDate = Jurnal::where('jurnal_balance_id', $balanceId)->whereDate('date', '>', $date)->orderBy('ordering', 'asc')->get();
            if ($getJurnalByDate->count() > 0) {
                $newSisaBalance = 0;
                $ordering = 0;
                foreach ($getJurnalByDate as $key => $value) {
                    if ($key == 0) {

                        $oldBalance =  $value->sisa_balance;
                        // Get Old balance
                        if ($value->jurnal_type == 0) {
                            $oldBalance = $value->sisa_balance - $value->balance;
                        } elseif ($value->jurnal_type == 1) {
                            $oldBalance = $value->sisa_balance + $value->balance;
                        } else {
                            return redirect()->back()->with('error', 'Something went wrong');
                        }
                        // new jurnal
                        if ($jurnalType == 0) {
                            $newSisaBalance = $oldBalance + $nominal;
                        } elseif ($jurnalType == 1) {
                            $newSisaBalance = $oldBalance - $nominal;
                        } else {
                            return redirect()->back()->with('error', 'Something went wrong');
                        }

                        $ordering = $value->ordering;
                        $jurnal = new Jurnal();
                        $jurnal->uuid = uuidGenerator();
                        $jurnal->users_id = Auth::user()->uuid;
                        $jurnal->jurnal_balance_id = $jurnalBalance->uuid;
                        $jurnal->keterangan = $keterangan;
                        $jurnal->ordering = $ordering;
                        $jurnal->jurnal_category_id = $jurnalCategoryId;
                        $jurnal->jurnal_type = $jurnalType;
                        $jurnal->kegiatan = $kegiatan;
                        $ordering = $ordering + 1;
                        if ($date != null) {
                            $jurnal->date =  $date;
                        } else {
                            $jurnal->date = Carbon::now();
                        }
                        // if ($request->has('lainnya')) {
                        //     $jurnal->lainnya = $request->lainnya;
                        // }

                        $jurnal->balance = $nominal;
                        $jurnal->sisa_balance = $newSisaBalance;
                        $jurnal->kurs_id = $jurnalBalance->kurs_id;
                        $jurnal->save();

                        // history

                        $jurnalHistory = new JurnalHistory();
                        $jurnalHistory->uuid = uuidGenerator();
                        $jurnalHistory->user_id = Auth::user()->uuid;
                        $jurnalHistory->reference_id = $jurnal->uuid;
                        $jurnalHistory->type = 0;
                        $jurnalHistory->save();

                        // endhistory

                        // File
                        if (count($evidence) > 0) {

                            $files = $evidence;

                            $path = '/jurnal/' . uuidGenerator() . '/upload/';
                            foreach ($files as $key => $file) {
                                if ($file == null) {
                                    continue;
                                }
                                $random_name = Str::random(12);
                                Storage::disk('local')->put('/public' . $path . $random_name . '_' . time() . '.' . $file->getClientOriginalExtension(), file_get_contents($file));
                                $attach = new JurnalAttachment();
                                $attach->file_name = $file->getClientOriginalName();
                                $attach->ext = $file->getClientOriginalExtension();
                                $attach->jurnal_id = $jurnal->uuid;
                                $attach->size = $file->getSize();
                                $attach->upload_by = Auth::user()->uuid;
                                $attach->path = $path . $random_name . '_' . time() . '.' . $file->getClientOriginalExtension();
                                $attach->save();
                            }
                        }

                        // End File

                        // proccess one up of date
                        if ($value->jurnal_type == 0) {
                            $newSisaBalance = $newSisaBalance + $value->balance;
                        } elseif ($value->jurnal_type == 1) {
                            $newSisaBalance =  $newSisaBalance - $value->balance;
                            // dd($newSisaBalance . ' - ' . $value->balance . ' = ' . $newSisaBalance - $value->balance . ' || ' . $newSisaBalance);
                        } else {
                            return redirect()->back()->with('error', 'Something went wrong');
                        }
                        $value->sisa_balance = $newSisaBalance;
                        $value->ordering = $ordering;
                        $value->save();
                        $ordering = $ordering + 1;
                        continue;
                    }
                    // procces if more then one jurnal 
                    if ($value->jurnal_type == 0) {

                        $newSisaBalance = $newSisaBalance + $value->balance;
                    } elseif ($value->jurnal_type == 1) {
                        $newSisaBalance = $newSisaBalance - $value->balance;
                    } else {
                        return redirect()->back()->with('error', 'Something went wrong');
                    }
                    $value->ordering = $ordering;
                    $value->sisa_balance = $newSisaBalance;
                    $value->save();
                    $ordering = $ordering + 1;
                }
                $jurnalBalance->balance = $newSisaBalance;
            } else {
                $getLatestJurnal = Jurnal::where('jurnal_balance_id', $balanceId)->orderBy('ordering', 'desc')->first();
                if (!$getLatestJurnal) {
                    # code...
                    $oldBalance = 0;
                    $orderingOne = 1;
                } else {

                    $oldBalance = $getLatestJurnal->sisa_balance;
                    $orderingOne = $getLatestJurnal->ordering + 1;
                }
                // new jurnal
                if ($jurnalType == 0) {
                    $newSisaBalance = $oldBalance + $nominal;
                } elseif ($jurnalType == 1) {
                    $newSisaBalance = $oldBalance - $nominal;
                } else {
                    return redirect()->back()->with('error', 'Something went wrong');
                }

                $jurnal = new Jurnal();
                $jurnal->uuid = uuidGenerator();
                $jurnal->keterangan = $keterangan;
                $jurnal->users_id = Auth::user()->uuid;
                $jurnal->ordering = $orderingOne;
                $jurnal->jurnal_category_id = $jurnalCategoryId;
                $jurnal->jurnal_balance_id = $jurnalBalance->uuid;
                $jurnal->jurnal_type = $jurnalType;
                $jurnal->kegiatan = $kegiatan;
                if ($date != null) {
                    $jurnal->date =  $date;
                } else {
                    $jurnal->date = Carbon::now();
                }
                // if ($request->has('lainnya')) {
                //     $jurnal->lainnya = $request->lainnya;
                // }

                $jurnal->balance = $nominal;
                $jurnal->sisa_balance = $newSisaBalance;
                $jurnal->kurs_id = $jurnalBalance->kurs_id;
                $jurnal->save();
                $ordering = $orderingOne;
                $ordering = $ordering;

                // history

                $jurnalHistory = new JurnalHistory();
                $jurnalHistory->uuid = uuidGenerator();
                $jurnalHistory->user_id = Auth::user()->uuid;
                $jurnalHistory->reference_id = $jurnal->uuid;
                $jurnalHistory->type = 0;
                $jurnalHistory->save();

                // endhistory

                // File
                if (count($evidence) > 0) {

                    $files = $evidence;

                    $path = '/jurnal/' . uuidGenerator() . '/upload/';
                    foreach ($files as $key => $file) {
                        if ($file == null) {
                            continue;
                        }
                        $random_name = Str::random(12);
                        Storage::disk('local')->put('/public' . $path . $random_name . '_' . time() . '.' . $file->getClientOriginalExtension(), file_get_contents($file));
                        $attach = new JurnalAttachment();
                        $attach->file_name = $file->getClientOriginalName();
                        $attach->ext = $file->getClientOriginalExtension();
                        $attach->jurnal_id = $jurnal->uuid;
                        $attach->size = $file->getSize();
                        $attach->upload_by = Auth::user()->uuid;
                        $attach->path = $path . $random_name . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $attach->save();
                    }
                }

                // end file
                $jurnalBalance->balance = $newSisaBalance;
            }
            $jurnalBalance->save();
            DB::commit();
            return $jurnal;
        } catch (QueryException $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public static function update($data, $balanceId, $jurnalId)
    {
        try {
            DB::beginTransaction();
            $jurnalBalance = JurnalBalance::where('uuid', $balanceId)->first();
            if (!$jurnalBalance) {
                throw new Exception('Kurs saldo tidak ditemukan');
            }
            $jurnal = Jurnal::where('uuid', $jurnalId)->first();
            if (!$jurnal) {
                throw new Exception('Jurnal kas tidak ditemukan');
            }
            $now = Carbon::now();
            $oldJurnalData = $jurnal;
            $jurnalType = $data['jurnal_type'];
            $jurnalCategoryId = $data['jurnal_category_id'];
            $date = $data['date'];
            $nominal = $data['balance_raw'];
            $keterangan = $data['keterangan'];
            $kegiatan = $data['kegiatan'];
            $evidence = isset($data['evidence']) ? $data['evidence'] : [];
            $oldBalance = $jurnal->balance;
            $newJurnalBalance = $jurnalBalance->balance;




            if (count($evidence) > 0) {

                $files = $evidence;
                $path = '/jurnal/' . uuidGenerator() . '/upload/';
                foreach ($files as $key => $file) {
                    $random_name = Str::random(12);
                    Storage::disk('local')->put('public/' . $path . $random_name . '_' . time() . '.' . $file->getClientOriginalExtension(), file_get_contents($file));
                    $attach = new JurnalAttachment();
                    $attach->file_name = $file->getClientOriginalName();
                    $attach->ext = $file->getClientOriginalExtension();
                    $attach->jurnal_id = $jurnal->id;
                    $attach->size = $file->getSize();
                    $attach->upload_by = Auth::user()->id;
                    $attach->path = $path . $random_name . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $attach->save();
                }
            }

            $jurnal->keterangan = $keterangan;
            $jurnal->kegiatan = $kegiatan;

            $jurnal->jurnal_category_id = $jurnalCategoryId;

            $jurnal->save();

            $jurnalBalance->balance = $newJurnalBalance;
            $jurnalBalance->save();


            $jurnalHistory = new JurnalHistory();
            $jurnalHistory->uuid = uuidGenerator();
            $jurnalHistory->user_id = Auth::user()->uuid;
            $jurnalHistory->reference_id = $jurnal->uuid;
            $jurnalHistory->data = json_encode($oldJurnalData);
            $jurnalHistory->type = 1;
            $jurnalHistory->save();
            if ($jurnal->balance != $nominal || $jurnal->jurnal_type != $jurnalType || formatTimeRaw($jurnal->date) != $date) {


                $jurnal->date = $date . ' ' . $now->hour . ':' . $now->minute . ':' . $now->second;
                $jurnal->balance = $nominal;
                $jurnal->jurnal_type = $jurnalType;
                $jurnal->save();
                JurnalService::hardSyncJurnal($jurnalBalance->uuid);
            }

            DB::commit();
            return true;
        } catch (QueryException $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public static function delete($balanceId, $jurnalId)
    {
        try {
            DB::beginTransaction();
            $jurnalBalance = JurnalBalance::where('uuid', $balanceId)->first();
            if (!$jurnalBalance) {
                throw new Exception('Kurs saldo tidak ditemukan');
            }
            $jurnal = Jurnal::where('uuid', $jurnalId)->first();
            if (!$jurnal) {
                throw new Exception('Jurnal kas tidak ditemukan');
            }
            $jurnal->delete();

            $oldOrdering = $jurnal->ordering;
            $oldBalance = $jurnal->balance;
            $oldType = $jurnal->jurnal_type;
            $oldJurnal = $jurnal;
            $message = $data['message'] ?? '';


            if ($oldType == 0) {
                $oldBalance = $jurnal->sisa_balance - $oldBalance;
            } elseif ($oldType == 1) {
                $oldBalance = $jurnal->sisa_balance + $oldBalance;
            } else {
                return redirect()->back()->with('failed', 'something went wrong');
            }


            $newestJurnal = Jurnal::where('jurnal_balance_id', $jurnalBalance->uuid)->where('ordering', '>', $oldOrdering)->orderBy('ordering', 'asc')->get();
            $jurnal->delete();

            if ($newestJurnal->count() > 0) {
                # code...
                foreach ($newestJurnal as $key => $value) {
                    # code...
                    // procces if more then one jurnal 

                    if ($key == 0) {
                        # code...
                        if ($value->jurnal_type == 0) {
                            $newSisaBalance = $oldBalance + $value->balance;
                        } elseif ($value->jurnal_type == 1) {
                            $newSisaBalance = $oldBalance - $value->balance;
                        } else {
                            return redirect()->back()->with('error', 'Something went wrong');
                        }
                        // dd($newSisaBalance);

                        $value->ordering = $oldOrdering;
                        $value->sisa_balance = $newSisaBalance;
                        $value->save();
                        $oldOrdering = $oldOrdering + 1;
                        continue;
                    }
                    if ($value->jurnal_type == 0) {
                        $newSisaBalance = $newSisaBalance + $value->balance;
                    } elseif ($value->jurnal_type == 1) {
                        $newSisaBalance = $newSisaBalance - $value->balance;
                    } else {
                        return redirect()->back()->with('error', 'Something went wrong');
                    }
                    // dd($newSisaBalance);


                    $value->ordering = $oldOrdering;
                    $value->sisa_balance = $newSisaBalance;
                    $value->save();
                    $oldOrdering = $oldOrdering + 1;
                }
                $jurnalBalance->balance = $newSisaBalance;
            } else {
                $jurnalBalance->balance = $oldBalance;
            }
            $jurnalBalance->save();

            $jurnalHistory = new JurnalHistory();
            $jurnalHistory->uuid = uuidGenerator();
            $jurnalHistory->user_id = Auth::user()->uuid;
            $jurnalHistory->reference_id = $jurnal->uuid;
            $jurnalHistory->data = json_encode($oldJurnal);
            $jurnalHistory->message = $message;
            $jurnalHistory->type = 2;
            $jurnalHistory->save();
            DB::commit();
            return true;
        } catch (QueryException $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function changeAttachment($jurnalOld, $JurnalNew)
    {
        $jurnalAttachment = JurnalAttachment::where('jurnal_id', $jurnalOld)->get();
        if (!$jurnalAttachment) {
            throw new Exception('Lampiran jurnal kas tidak ditemukan');
        }
        foreach ($jurnalAttachment as $key => $value) {
            $value->jurnal_id = $JurnalNew;
            $value->save();
        }
    }

    private static function getHourNow($date, $add = 0)
    {
        $carbon = $date . ' ' . Carbon::now()->hour . ':' . Carbon::now()->minute . ':' . Carbon::now()->second;
        if ($add > 0) {
            $carbon = Carbon::parse($carbon);
            $carbon->addSecond($add);
        }
        return $carbon;
    }
    private static function hardSyncJurnal($jurnalBalanceId)
    {
        try {
            DB::beginTransaction();
            $jurnalBalance = JurnalBalance::where('uuid', $jurnalBalanceId)->first();
            if (!$jurnalBalance) {
                throw new Exception('Kurs saldo tidak ditemukan');
            }
            $jurnals = Jurnal::where('jurnal_balance_id', $jurnalBalanceId)->orderBy('date', 'asc')->get();
            $ordering = 1;
            $newBalance = 0;
            foreach ($jurnals as $key => $value) {

                $value->ordering = $ordering;
                if ($value->jurnal_type == 0) {
                    $newBalance += $value->balance;
                } else if ($value->jurnal_type == 1) {
                    $newBalance -= $value->balance;
                } else {
                    return redirect()->back()->with('error', 'Something went wrong');
                }
                $value->sisa_balance = $newBalance;
                $value->save();
                $ordering++;
            }
            $jurnalBalance->balance = $newBalance;
            $jurnalBalance->save();
            DB::commit();
            return true;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new $e;
        }
    }
}
