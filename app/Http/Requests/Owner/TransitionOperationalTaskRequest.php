<?php
namespace App\Http\Requests\Owner;
use Illuminate\Foundation\Http\FormRequest;
class TransitionOperationalTaskRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()!==null; }
    public function rules(): array { return ['action'=>['required','in:start,complete'],'completion_notes'=>['nullable','required_if:action,complete','string','max:2000']]; }
}
