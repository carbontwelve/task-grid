<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Models\Workbook;
use App\Models\Worksheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function testTaskIndex()
    {
        $user = User::factory()->create();

        /** @var Workbook $workbook */
        $workbook = Workbook::factory()
            ->create(['authored_by' => $user->id]);
        /** @var Worksheet $worksheet */
        $worksheet = $workbook->worksheets()
            ->save(Worksheet::factory()->make(['authored_by' => $user->id]));

        $this->assertEquals(0, $worksheet->tasks()->count());
        $this->actingAs($user, 'api');

        for ($i = 0; $i < 10; $i++) {
            $worksheet->tasks()
                ->save(
                    Task::factory()
                        ->make(['authored_by' => $user->id]));
        }

        $this->assertEquals(10, $worksheet->tasks()->count());

        $this->withoutExceptionHandling();
        $response = $this->getJson(route('worksheet.tasks', ['worksheet' => $worksheet]))
            ->assertStatus(200);

        $this->assertCount(10, $response->json('data'));
    }

    public function testTaskPersist()
    {
        $user = User::factory()->create();

        /** @var Workbook $workbook */
        $workbook = Workbook::factory()
            ->create(['authored_by' => $user->id]);
        /** @var Worksheet $worksheet */
        $worksheet = $workbook->worksheets()
            ->save(Worksheet::factory()->make(['authored_by' => $user->id]));

        $this->actingAs($user, 'api');

        $this->withoutExceptionHandling();

        // Store
        $response = $this->postJson(route('task.store', ['worksheet' => $worksheet]), [
            'name' => 'Hello world',
        ])->assertStatus(201);

        $this->assertEquals('Hello world', $response->json('data.attributes.name'));

        // Update
        $response = $this->putJson(route('task.update', ['task' => $response->json('data.id')]), [
            'name' => 'World Hello',
        ])->assertStatus(200);

        $this->assertEquals('World Hello', $response->json('data.attributes.name'));
    }

    public function testTaskShow()
    {
        $user = User::factory()->create();

        /** @var Workbook $workbook */
        $workbook = Workbook::factory()
            ->create(['authored_by' => $user->id]);
        /** @var Worksheet $worksheet */
        $worksheet = $workbook->worksheets()
            ->save(Worksheet::factory()->make(['authored_by' => $user->id]));
        /** @var Task $task */
        $task = $worksheet->tasks()->save(Task::factory()->make([
            'authored_by' => $user->id,
            'name' => 'Howdy'
        ]));

        $this->actingAs($user, 'api');

        $response = $this->getJson(route('task.show', ['task' => $task->id]))
            ->assertStatus(200);

        $response->assertJsonFragment([
            'type' => 'task',
            'id' => $task->id,
            'name' => 'Howdy'
        ]);
    }

    public function testTaskDestroyRestore()
    {
        $user = User::factory()->create();

        /** @var Workbook $workbook */
        $workbook = Workbook::factory()
            ->create(['authored_by' => $user->id]);
        /** @var Worksheet $worksheet */
        $worksheet = $workbook->worksheets()
            ->save(Worksheet::factory()->make(['authored_by' => $user->id]));

        for ($i = 0; $i<3; $i++) {
            $worksheet->tasks()->save(Task::factory()->make([
                'authored_by' => $user->id
            ]));
        }

        $this->assertEquals(3, (new Task)->newQuery()->count());

        $found = (new Task)->newQuery()->first();

        $this->actingAs($user, 'api');

        $this->deleteJson(route('task.destroy', ['task' => $found->id]))
            ->assertStatus(204);

        $this->assertEquals(2, (new Task)->newQuery()->count());

        $this->patchJson(route('task.restore', ['task' => $found->id]))
            ->assertStatus(204);

        $this->assertEquals(3, (new Task)->newQuery()->count());
    }
}
