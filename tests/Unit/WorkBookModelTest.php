<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Workbook;
use App\Models\Worksheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkBookModelTest extends TestCase
{
    use RefreshDatabase;

    public function testItHasWorkSheetRelationship()
    {
        /** @var Workbook $model */
        $model = Workbook::factory()
            ->for(User::factory(), 'author')
            ->create();

        $this->assertEquals(0, $model->worksheets()->count());

        $model->worksheets()->save(
            Worksheet::factory()
                ->for(User::factory(), 'author')
                ->make()
        );

        $this->assertEquals(1, $model->worksheets()->count());
    }

    public function testItHasAuthorRelationship()
    {
        /** @var Workbook $model */
        $model = Workbook::factory()
            ->for(User::factory(), 'author')
            ->create();

        $this->assertEquals(1, $model->author()->count());
    }
}
