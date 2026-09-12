<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Operations\OwnerOperationsWorkspace;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerOperationsController extends Controller
{
    public function __invoke(Request $request,ActiveBusinessContext $context,OwnerOperationsWorkspace $workspace): View
    {
        $filters=$request->validate(['property'=>['nullable','uuid'],'status'=>['nullable','in:pending,assigned,in_progress,blocked,completed,cancelled'],'priority'=>['nullable','in:low,normal,high,urgent'],'q'=>['nullable','string','max:100']]);
        $business=$context->business;
        return view('owner.operations',$workspace->build($business,$filters)+['business'=>$business,'filters'=>$filters,'properties'=>$business->properties()->orderBy('name')->get(['id','name']),'employees'=>$business->employees()->with('businessMembership.user')->where('employment_status','active')->get()]);
    }
}
