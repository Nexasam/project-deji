<?php
namespace App\Http\Requests\Owner;
use Illuminate\Foundation\Http\FormRequest;
class TransitionOperationalTaskRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()!==null; }
    public function rules(): array { return ['action'=>['required','in:start,complete'],'completion_notes'=>['nullable','required_if:action,complete','string','max:2000'],'checklist_items'=>['nullable','array'],'checklist_items.*'=>['uuid'],'evidence'=>['nullable','array','max:8'],'evidence.*'=>['image','max:8192']]; }
}
