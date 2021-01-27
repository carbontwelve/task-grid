<?php

namespace App\Http\Requests;

use App\Models\Task;
use App\Models\Worksheet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'worksheet_id' => ['required', Rule::exists('worksheets')]
        ];
    }

    public function getModel(): Task
    {
        return $this->route()->hasParameter('milestone')
            ? (new Task())->newQuery()->findOrFail($this->route()->parameter('task'))
            : new Task();
    }

    function persist(?Worksheet $worksheet): Task
    {
        $model = $this->getModel();
        // TODO: Implement persist() method.
    }
}
