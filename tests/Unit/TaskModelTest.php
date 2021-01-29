<?php

namespace Tests\Unit;

use App\Models\Milestone;
use App\Models\Task;
use App\Models\User;
use App\Models\Workbook;
use App\Models\Worksheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskModelTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @var Worksheet
     */
    private $worksheet;

    /**
     * @var Task
     */
    private $model;

    private function doSeeding(): void
    {
        /** @var Workbook $workbook */
        $workbook = Workbook::factory()
            ->for(User::factory(), 'author')
            ->create();

        /** @var Worksheet $model */
        $this->worksheet = Worksheet::factory()->create([
            'authored_by' => $workbook->authored_by,
            'workbook_id' => $workbook->id
        ]);

        $this->model = $this->worksheet->tasks()->save(
            Task::factory()->make([
                'authored_by' => $workbook->authored_by
            ])
        );
    }

    public function testItHasMilestoneRelationship()
    {
        $this->doSeeding();
        $this->assertEquals(0, $this->model->milestones()->count());

        /** @var Milestone $milestone */
        $milestone = Milestone::factory()->create([
            'authored_by' => $this->worksheet->authored_by,
            'worksheet_id' => $this->worksheet->id,
        ]);

        $milestone->tasks()->attach($this->model, ['urgency' => Task::UrgencyShowStopper]);
        $this->assertEquals(1, $this->model->milestones()->count());

        /** @var Milestone $found */
        $found = $this->model->milestones()->first();
        $this->assertTrue($milestone->is($found));
        $this->assertEquals(Task::UrgencyShowStopper, $found->pivot->urgency);
    }

    public function testItHasAuthorRelationship()
    {
        $this->doSeeding();
        $this->assertEquals(1, $this->model->author()->count());
    }
}
