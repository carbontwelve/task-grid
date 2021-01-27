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
            'name' => ['required', 'max:255']
        ];
    }

    public function getModel(): Workbook
    {
        return $this->route()->hasParameter('workbook')
            ? (new Workbook())->newQuery()->findOrFail($this->route()->parameter('workbook'))
            : new Workbook();
    }

    function persist(): Workbook
    {
        $model = $this->getModel();
        // TODO: Implement persist() method.
    }
}
