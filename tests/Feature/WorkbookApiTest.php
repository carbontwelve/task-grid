<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Worksheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkbookApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testWorkbookIndex()
    {
        $user = User::factory()->create();

        /** @var Worksheet $worksheet */
        $worksheet = Worksheet::factory()
            ->create(['authored_by' => $user->id]);

        $this->actingAs($user, 'api');

        $response = $this->getJson(route('worksheet.worksheets', ['worksheet' => $worksheet]))
            ->assertStatus(200);

        $this->assertCount(0, $response->json('data'));

        for ($i = 0; $i < 10; $i++) {
            $worksheet->worksheets()->save(Worksheet::factory()->make([
                'authored_by' => $user->id
            ]));
        }

        $this->withoutExceptionHandling();

        $response = $this->getJson(route('worksheet.worksheets', ['worksheet' => $worksheet]))
            ->assertStatus(200);

        $this->assertCount(10, $response->json('data'));

    }

    public function testWorkbookPersist()
    {
        $user = User::factory()->create();

        /** @var Worksheet $worksheet */
        $worksheet = Worksheet::factory()
            ->create(['authored_by' => $user->id]);

        $this->assertEquals(0, $worksheet->worksheets()->count());

        $this->actingAs($user, 'api');

        // Store
        $response = $this->postJson(route('worksheet.store', ['worksheet' => $worksheet]), [
            'name' => 'Hello world',
        ])->assertStatus(201);

        $this->assertEquals('Hello world', $response->json('data.attributes.name'));

        // Update
        $response = $this->putJson(route('worksheet.update', ['worksheet' => $response->json('data.id')]), [
            'name' => 'World Hello',
        ])->assertStatus(200);

        $this->assertEquals('World Hello', $response->json('data.attributes.name'));
    }

    public function testWorkbookDestroyRestore()
    {
        $user = User::factory()->create();

        /** @var Worksheet $worksheet */
        $worksheet = Worksheet::factory()
            ->create(['authored_by' => $user->id]);

        $this->assertEquals(0, $worksheet->worksheets()->count());

        for ($i = 0; $i<3; $i++) {
            $worksheet->worksheets()->save(Worksheet::factory()->make([
                'authored_by' => $user->id,
            ]));
        }

        $this->assertEquals(3, $worksheet->worksheets()->count());

        $found = $worksheet->worksheets()->first();

        $this->actingAs($user, 'api');

        $this->deleteJson(route('worksheet.destroy', ['worksheet' => $found->id]))
            ->assertStatus(204);

        $this->assertEquals(2, $worksheet->worksheets()->count());

        $this->patchJson(route('worksheet.restore', ['worksheet' => $found->id]))
            ->assertStatus(204);

        $this->assertEquals(3, $worksheet->worksheets()->count());
    }
}
