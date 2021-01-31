<?php

namespace App\Http\Requests;

use App\Models\Workbook;
use Illuminate\Foundation\Http\FormRequest;

class WorkbookRequest extends FormRequest
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
            'name' => ['required', 'max:255']
        ];
    }

    function persist(): Workbook
    {
        $model = $this->getModel();
        $model->name = $this->input('name', $model->name);
        if (!$model->exists) {
            $model->authored_by = $this->user()->id;
        }
        $model->save();
        return $model;
    }

    public function getModel(): Workbook
    {
        return $this->route()->hasParameter('workbook')
            ? (new Workbook())->newQuery()->findOrFail($this->route()->parameter('workbook'))
            : new Workbook();
    }
}
