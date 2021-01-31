<?php

namespace App\Http\Requests;

use App\Models\Task;
use App\Models\Worksheet;
use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            // 'worksheet_id' => ['required', Rule::exists('worksheets')]
        ];
    }

    function persist(?Worksheet $worksheet = null): Task
    {
        $model = $this->getModel();
        $model->name = $this->input('name', $model->name);

        if (!$model->exists) {
            $model->authored_by = $this->user()->id;
        }

        if (!$model->exists && !is_null($worksheet)) {
            $worksheet->tasks()->save($model);
        } else {
            $model->save();
        }

        return $model;
    }

    public function getModel(): Task
    {
        return $this->route()->hasParameter('task')
            ? (new Task())->newQuery()->findOrFail($this->route()->parameter('task'))
            : new Task();
    }
}
