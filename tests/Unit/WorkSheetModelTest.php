<?php

namespace Tests\Unit;

use App\Models\Milestone;
use App\Models\Task;
use App\Models\User;
use App\Models\Workbook;
use App\Models\Worksheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkSheetModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Workbook
     */
    private $workbook;

    /**
     * @var Worksheet
     */
    private $model;

    private function doSeeding(): void
    {
        /** @var Workbook $workbook */
        $this->workbook = Workbook::factory()
            ->for(User::factory(), 'author')
            ->create();

        /** @var Worksheet $model */
        $this->model = Worksheet::factory()->create([
            'authored_by' => $this->workbook->authored_by,
            'workbook_id' => $this->workbook->id
        ]);
    }

    public function testItHasWorkBookRelationship()
    {
        $this->doSeeding();
        $this->assertTrue($this->workbook->is($this->model->workbook));
    }

    public function testItHasTasksRelationship()
    {
        $this->doSeeding();
        $this->assertEquals(0, $this->model->tasks()->count());

        $this->model->tasks()->save(Task::factory()->make([
            'authored_by' => $this->model->authored_by,
        ]));

        $this->assertEquals(1, $this->model->tasks()->count());
    }

    public function testItHasMilestonesRelationship()
    {
        $this->doSeeding();
        $this->assertEquals(0, $this->model->milestones()->count());

        $this->model->milestones()->save(Milestone::factory()->make([
            'authored_by' => $this->model->authored_by,
        ]));

        $this->assertEquals(1, $this->model->milestones()->count());
    }
}
