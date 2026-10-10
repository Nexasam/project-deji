<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessSettlementAccount;
use App\Models\SettlementBatch;
use App\Services\Finance\SettlementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettlementController extends Controller
{
    public function index(Request $request, SettlementService $service): View
    {
        $businesses=Business::query()->orderBy('name')->get();
        $business=$request->filled('business') ? $businesses->firstWhere('id',$request->string('business')->toString()) : $businesses->first();
        return view('admin.settlements.index',['businesses'=>$businesses,'business'=>$business,'account'=>$business?->settlementAccount,'available'=>$business?$service->available($business):collect(),'batches'=>$business?SettlementBatch::query()->with('account')->where('business_id',$business->id)->latest()->get():collect()]);
    }

    public function account(Request $request, Business $business): RedirectResponse
    {
        $data=$request->validate(['bank_name'=>['required','string','max:120'],'account_name'=>['required','string','max:160'],'account_number'=>['required','digits_between:10,16']]);
        BusinessSettlementAccount::query()->updateOrCreate(['business_id'=>$business->id],$data+['account_number_last4'=>substr($data['account_number'],-4),'status'=>'verified','verified_by'=>$request->user()->id,'verified_at'=>now()]);
        return back()->with('status','Settlement account verified.');
    }

    public function store(Request $request, Business $business, SettlementService $service): RedirectResponse
    {
        $data=$request->validate(['allocations'=>['required','array','min:1'],'allocations.*'=>['uuid']]);
        $account=$business->settlementAccount()->where('status','verified')->firstOrFail();
        $service->create($business,$account,$data['allocations'],$request->user());
        return back()->with('status','Settlement batch created for independent approval.');
    }

    public function approve(Request $request, SettlementBatch $settlement, SettlementService $service): RedirectResponse { $service->approve($settlement,$request->user()); return back()->with('status','Settlement approved.'); }
    public function complete(Request $request, SettlementBatch $settlement, SettlementService $service): RedirectResponse { $data=$request->validate(['transfer_reference'=>['required','string','max:191','unique:platform_settlement_batches,transfer_reference']]); $service->complete($settlement,$data['transfer_reference'],$request->user()); return back()->with('status','Settlement marked completed.'); }
    public function reverse(Request $request, SettlementBatch $settlement, SettlementService $service): RedirectResponse { $data=$request->validate(['reason'=>['required','string','min:10','max:1000']]); $service->reverse($settlement,$data['reason'],$request->user()); return back()->with('status','Settlement reversed with an audit reason.'); }
}
