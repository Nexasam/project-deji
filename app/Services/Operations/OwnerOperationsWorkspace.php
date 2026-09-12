<?php
namespace App\Services\Operations;
use App\Models\Business;
final class OwnerOperationsWorkspace
{
    public function build(Business $business,array $filters): array
    {
        $base=$business->operationalTasks();
        $tasks=(clone $base)->with(['property','assignedEmployee.businessMembership.user'])
            ->when($filters['property']??null,fn($q,$v)=>$q->where('property_id',$v))
            ->when($filters['status']??null,fn($q,$v)=>$q->where('status',$v))
            ->when($filters['priority']??null,fn($q,$v)=>$q->where('priority',$v))
            ->when($filters['q']??null,fn($q,$v)=>$q->where(fn($q)=>$q->where('title','like',"%{$v}%")->orWhere('reference','like',"%{$v}%")))
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 ELSE 4 END")->orderBy('due_at')->paginate(20)->withQueryString();
        return ['tasks'=>$tasks,'stats'=>['open'=>(clone $base)->whereNotIn('status',['completed','cancelled'])->count(),'urgent'=>(clone $base)->where('priority','urgent')->whereNotIn('status',['completed','cancelled'])->count(),'inProgress'=>(clone $base)->where('status','in_progress')->count(),'completed'=>(clone $base)->where('status','completed')->count()]];
    }
}
