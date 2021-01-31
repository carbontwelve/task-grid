<?php

namespace App\Http\Requests;

use App\Models\Milestone;
use App\Models\Worksheet;
use Illuminate\Foundation\Http\FormRequest;

class MilestoneRequest extends FormRequest
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

    function persist(?Worksheet $worksheet = null): Milestone
    {
        $model = $this->getModel();
        $model->name = $this->input('name', $model->name);

        if (!$model->exists) {
            $model->authored_by = $this->user()->id;
        }

        if (!$model->exists && !is_null($worksheet)) {
            $worksheet->milestones()->save($model);
        } else {
            $model->save();
        }

        return $model;
    }

    public function getModel(): Milestone
    {
        return $this->route()->hasParameter('milestone')
            ? (new Milestone())->newQuery()->findOrFail($this->route()->parameter('milestone'))
            : new Milestone();
    }
}
