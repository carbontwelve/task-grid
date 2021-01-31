<?php

namespace App\Http\Requests;

use App\Models\Workbook;
use App\Models\Worksheet;
use Illuminate\Foundation\Http\FormRequest;

class WorksheetRequest extends FormRequest
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
            'name' => ['required', 'max:255'],
        ];
    }

    function persist(?Workbook $workbook = null): Worksheet
    {
        $model = $this->getModel();
        $model->name = $this->input('name', $model->name);

        if (!$model->exists) {
            $model->authored_by = $this->user()->id;
        }

        if (!$model->exists && !is_null($workbook)) {
            $workbook->worksheets()->save($model);
        } else {
            $model->save();
        }

        return $model;
    }

    public function getModel(): Worksheet
    {
        return $this->route()->hasParameter('worksheet')
            ? (new Worksheet())->newQuery()->findOrFail($this->route()->parameter('worksheet'))
            : new Worksheet();
    }
}
