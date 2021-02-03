<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workbook;
use App\Models\Worksheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorksheetApiTest extends TestCase
{
    use RefreshDatabase;

    public function testWorksheetIndex()
    {
        $user = User::factory()->create();

        /** @var Workbook $workbook */
        $workbook = Workbook::factory()
            ->create(['authored_by' => $user->id]);

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson(route('workbook.worksheets', ['workbook' => $workbook]))
            ->assertStatus(200);

        $this->assertCount(0, $response->json('data'));

        for ($i = 0; $i < 10; $i++) {
            $workbook->worksheets()->save(Worksheet::factory()->make([
                'authored_by' => $user->id
            ]));
        }

        $this->withoutExceptionHandling();

        $response = $this->getJson(route('workbook.worksheets', ['workbook' => $workbook]))
            ->assertStatus(200);

        $this->assertCount(10, $response->json('data'));

    }

    public function testWorksheetPersist()
    {
        $user = User::factory()->create();

        /** @var Workbook $workbook */
        $workbook = Workbook::factory()
            ->create(['authored_by' => $user->id]);

        $this->assertEquals(0, $workbook->worksheets()->count());

        $this->actingAs($user, 'sanctum');

        // Store
        $response = $this->postJson(route('worksheet.store', ['workbook' => $workbook]), [
            'name' => 'Hello world',
        ])->assertStatus(201);

        $this->assertEquals('Hello world', $response->json('data.attributes.name'));

        // Update
        $response = $this->putJson(route('worksheet.update', ['worksheet' => $response->json('data.id')]), [
            'name' => 'World Hello',
        ])->assertStatus(200);

        $this->assertEquals('World Hello', $response->json('data.attributes.name'));
    }

    public function testWorksheetShow()
    {
        $user = User::factory()->create();

        /** @var Workbook $workbook */
        $workbook = Workbook::factory()
            ->create(['authored_by' => $user->id]);
        /** @var Worksheet $worksheet */
        $worksheet = $workbook->worksheets()
            ->save(Worksheet::factory()->make([
                'authored_by' => $user->id,
                'name' => 'Test Worksheet Show'
            ]));

        $this->actingAs($user, 'sanctum');

        $this->getJson(route('worksheet.show', $worksheet))
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Test Worksheet Show'
            ]);
    }

    public function testWorksheetDestroyRestore()
    {
        $user = User::factory()->create();

        /** @var Workbook $workbook */
        $workbook = Workbook::factory()
            ->create(['authored_by' => $user->id]);

        $this->assertEquals(0, $workbook->worksheets()->count());

        for ($i = 0; $i<3; $i++) {
            $workbook->worksheets()->save(Worksheet::factory()->make([
                'authored_by' => $user->id,
            ]));
        }

        $this->assertEquals(3, $workbook->worksheets()->count());

        $found = $workbook->worksheets()->first();

        $this->actingAs($user, 'sanctum');

        $this->deleteJson(route('worksheet.destroy', ['worksheet' => $found->id]))
            ->assertStatus(204);

        $this->assertEquals(2, $workbook->worksheets()->count());

        $this->patchJson(route('worksheet.restore', ['worksheet' => $found->id]))
            ->assertStatus(204);

        $this->assertEquals(3, $workbook->worksheets()->count());
    }
}
