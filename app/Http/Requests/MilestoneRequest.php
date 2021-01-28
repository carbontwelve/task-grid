<?php

namespace App\Http\Requests;

use App\Models\Milestone;
use App\Models\Workbook;
use App\Models\Worksheet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MilestoneRequest extends FormRequest
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
            // 'worksheet_id' => ['required', Rule::exists('worksheets')]
        ];
    }

    public function getModel(): Milestone
    {
        return $this->route()->hasParameter('milestone')
            ? (new Milestone())->newQuery()->findOrFail($this->route()->parameter('milestone'))
            : new Milestone();
    }

    function persist(?Worksheet $worksheet = null): Milestone
    {
        $model = $this->getModel();
        $model->name = $this->input('name', $model->name);

        if (!$model->exists){
            $model->authored_by = $this->user()->id;
        }

        if (!$model->exists && !is_null($worksheet)) {
            $worksheet->milestones()->save($model);
        } else {
            $model->save();
        }

        return $model;
    }
}
