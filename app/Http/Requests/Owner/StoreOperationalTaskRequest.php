<?php
namespace App\Http\Requests\Owner;
use Illuminate\Foundation\Http\FormRequest;
class StoreOperationalTaskRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()!==null; }
    public function rules(): array { return ['property_id'=>['required','uuid'],'assigned_employee_id'=>['nullable','uuid'],'title'=>['required','string','max:255'],'task_type'=>['required','in:cleaning,maintenance,inspection,inventory_check,guest_welcome,photography,deep_cleaning,repair'],'priority'=>['required','in:low,normal,high,urgent'],'due_at'=>['required','date','after_or_equal:today'],'notes'=>['nullable','string','max:2000']]; }
}
