<?php
namespace App\Services\Dashboard;
use App\Models\Business;
final class OwnerDashboardSummary
{
    public function build(Business $business): array
    {
        $bookings=$business->bookings(); $tasks=$business->operationalTasks();
        $completed=$business->payments()->where('status','completed');
        $revenue=(float)(clone $completed)->where('purpose','!=','refund')->sum('amount')-(float)(clone $completed)->where('purpose','refund')->sum('amount');
        return ['propertyCount'=>$business->properties()->count(),'publishedProperties'=>$business->properties()->where('publication_status','published')->count(),'upcomingArrivals'=>(clone $bookings)->whereIn('status',['reserved','awaiting_payment','confirmed'])->whereBetween('arrival_date',[today(),today()->addDays(30)])->count(),'upcomingDepartures'=>(clone $bookings)->whereIn('status',['confirmed','checked_in'])->whereBetween('departure_date',[today(),today()->addDays(30)])->count(),'receivedRevenue'=>$revenue,'unpaidBookings'=>(clone $bookings)->whereIn('payment_status',['unpaid','partially_paid'])->whereNotIn('status',['cancelled','refunded'])->count(),'openTasks'=>(clone $tasks)->whereNotIn('status',['completed','cancelled'])->count(),'urgentTasks'=>(clone $tasks)->where('priority','urgent')->whereNotIn('status',['completed','cancelled'])->count()];
    }
}
