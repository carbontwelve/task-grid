<?php

namespace Tests\Feature;

use App\Models\Milestone;
use App\Models\User;
use App\Models\Workbook;
use App\Models\Worksheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MilestoneApiTest extends TestCase
{
    use RefreshDatabase;

    public function testMilestoneIndex()
    {
        $user = User::factory()->create();

        /** @var Workbook $workbook */
        $workbook = Workbook::factory()
            ->create(['authored_by' => $user->id]);
        /** @var Worksheet $worksheet */
        $worksheet = $workbook->worksheets()
            ->save(Worksheet::factory()->make(['authored_by' => $user->id]));

        $this->assertEquals(0, $worksheet->milestones()->count());
        $this->actingAs($user, 'api');

        for ($i = 0; $i < 10; $i++) {
            $worksheet->milestones()
                ->save(
                    Milestone::factory()
                        ->make(['authored_by' => $user->id]));
        }

        $this->assertEquals(10, $worksheet->milestones()->count());

        $this->withoutExceptionHandling();
        $response = $this->getJson(route('worksheet.milestones', ['worksheet' => $worksheet]))
            ->assertStatus(200);

        $this->assertCount(10, $response->json('data'));
    }

    public function testMilestonePersist()
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
        $response = $this->postJson(route('milestone.store', ['worksheet' => $worksheet]), [
            'name' => 'Hello world',
        ])->assertStatus(201);

        $this->assertEquals('Hello world', $response->json('data.attributes.name'));

        // Update
        $response = $this->putJson(route('milestone.update', ['milestone' => $response->json('data.id')]), [
            'name' => 'World Hello',
        ])->assertStatus(200);

        $this->assertEquals('World Hello', $response->json('data.attributes.name'));
    }

    public function testMilestoneShow()
    {
        $user = User::factory()->create();

        /** @var Workbook $workbook */
        $workbook = Workbook::factory()
            ->create(['authored_by' => $user->id]);
        /** @var Worksheet $worksheet */
        $worksheet = $workbook->worksheets()
            ->save(Worksheet::factory()->make([
                'authored_by' => $user->id,
            ]));

        $milestone = $worksheet->milestones()
            ->save(Milestone::factory()->make([
                'authored_by' => $user->id,
                'name' => 'Test Milestone Show'
            ]));

        $this->actingAs($user, 'api');

        $this->getJson(route('milestone.show', $workbook))
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Test Milestone Show'
            ]);
    }

    public function testMilestoneDestroyRestore()
    {
        $user = User::factory()->create();

        /** @var Workbook $workbook */
        $workbook = Workbook::factory()
            ->create(['authored_by' => $user->id]);
        /** @var Worksheet $worksheet */
        $worksheet = $workbook->worksheets()
            ->save(Worksheet::factory()->make(['authored_by' => $user->id]));

        for ($i = 0; $i<3; $i++) {
            $worksheet->milestones()->save(Milestone::factory()->make([
                'authored_by' => $user->id
            ]));
        }

        $this->assertEquals(3, (new Milestone)->newQuery()->count());

        $found = (new Milestone)->newQuery()->first();

        $this->actingAs($user, 'api');

        $this->deleteJson(route('milestone.destroy', ['milestone' => $found->id]))
            ->assertStatus(204);

        $this->assertEquals(2, (new Milestone)->newQuery()->count());

        $this->withoutExceptionHandling();
        $this->patchJson(route('milestone.restore', ['milestone' => $found->id]))
            ->assertStatus(204);

        $this->assertEquals(3, (new Milestone)->newQuery()->count());
    }
}
