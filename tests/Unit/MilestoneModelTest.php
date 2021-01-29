<?php

namespace Tests\Unit;

use App\Models\Milestone;
use App\Models\Task;
use App\Models\User;
use App\Models\Workbook;
use App\Models\Worksheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MilestoneModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Workbook
     */
    private $workbook;

    /**
     * @var Worksheet
     */
    private $worksheet;

    /**
     * @var Milestone
     */
    private $model;

    private function doSeeding(): void
    {
        /** @var Workbook $workbook */
        $this->workbook = Workbook::factory()
            ->for(User::factory(), 'author')
            ->create();

        /** @var Worksheet $model */
        $this->worksheet = Worksheet::factory()->create([
            'authored_by' => $this->workbook->authored_by,
            'workbook_id' => $this->workbook->id
        ]);

        $this->model = $this->worksheet->milestones()->save(
            Milestone::factory()->make([
                'authored_by' => $this->workbook->authored_by
            ])
        );
    }

    public function testItHasWorksheetRelationship()
    {
        $this->doSeeding();
        $this->assertTrue($this->worksheet->is($this->model->worksheet));
    }

    public function testItHasTasksRelationship()
    {
        $this->doSeeding();
        $this->assertEquals(0, $this->model->tasks()->count());

        /** @var Task $task */
        $task = Task::factory()->create([
            'authored_by' => $this->worksheet->authored_by,
            'worksheet_id' => $this->worksheet->id,
        ]);

        $this->model->tasks()->attach($task, ['urgency' => Task::UrgencyShowStopper]);

        $this->assertEquals(1, $this->model->tasks()->count());

        /** @var Task $found */
        $found = $this->model->tasks()->first();
        $this->assertTrue($task->is($found));

        $this->assertEquals(Task::UrgencyShowStopper, $found->pivot->urgency);
    }

    public function testItHasAuthorRelationship()
    {
        $this->doSeeding();
        $this->assertEquals(1, $this->model->author()->count());
    }
}
