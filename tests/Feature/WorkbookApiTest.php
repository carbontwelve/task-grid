<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workbook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkbookApiTest extends TestCase
{
    use RefreshDatabase;

    public function testWorkbookIndex()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        for ($i = 0; $i < 10; $i++) {
            Workbook::factory()
                ->create(['authored_by' => $user->id]);
        }

        $this->withoutExceptionHandling();

        $response = $this->getJson(route('workbook.index'))
            ->assertStatus(200);

        $this->assertCount(10, $response->json('data'));
    }

    public function testWorkbookPersist()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $this->withoutExceptionHandling();

        // Store
        $response = $this->postJson(route('workbook.store'), [
            'name' => 'Hello world',
        ])->assertStatus(201);

        $this->assertEquals('Hello world', $response->json('data.attributes.name'));

        // Update
        $response = $this->putJson(route('workbook.update', ['workbook' => $response->json('data.id')]), [
            'name' => 'World Hello',
        ])->assertStatus(200);

        $this->assertEquals('World Hello', $response->json('data.attributes.name'));
    }

    public function testWorkbookShow()
    {
        $user = User::factory()->create();

        /** @var Workbook $workbook */
        $workbook = Workbook::factory()
            ->create([
                'authored_by' => $user->id,
                'name' => 'Test Workbook Show'
            ]);

        $this->actingAs($user, 'sanctum');

        $this->getJson(route('workbook.show', $workbook))
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Test Workbook Show'
            ]);
    }

    public function testWorkbookDestroyRestore()
    {
        $user = User::factory()->create();

        for ($i = 0; $i<3; $i++) {
            Workbook::factory()
                ->create(['authored_by' => $user->id]);
        }

        $this->assertEquals(3, (new Workbook)->newQuery()->count());

        $found = (new Workbook)->newQuery()->first();

        $this->actingAs($user, 'sanctum');

        $this->deleteJson(route('workbook.destroy', ['workbook' => $found->id]))
            ->assertStatus(204);

        $this->assertEquals(2, (new Workbook)->newQuery()->count());

        $this->withoutExceptionHandling();
        $this->patchJson(route('workbook.restore', ['workbook' => $found->id]))
            ->assertStatus(204);

        $this->assertEquals(3, (new Workbook)->newQuery()->count());
    }
}
