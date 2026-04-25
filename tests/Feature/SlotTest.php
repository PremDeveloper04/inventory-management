<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\Slot;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlotTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_create_slot_with_materials_and_workers()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $material = Material::create(['name' => 'Clay', 'price' => 10]);
        $worker = Worker::create(['name' => 'John']);

        $response = $this->post(route('slots.store'), [
            'total_bricks' => 1000,
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-10',
            'status' => 'in_progress',
            'materials' => [
                [
                    'id' => $material->id,
                    'quantity' => 50,
                    'price' => 9.5,
                    'added_at' => '2026-01-01',
                ],
            ],
            'workers' => [
                [
                    'id' => $worker->id,
                    'start_time' => '2026-01-01 08:00:00',
                    'end_time' => '2026-01-01 17:00:00',
                    'amount' => 100,
                ],
            ],
        ]);

        $response->assertRedirect(route('slots.index'));

        $this->assertDatabaseHas('slots', ['total_bricks' => 1000, 'status' => 'in_progress']);
        $slot = Slot::first();
        $this->assertEquals(1, $slot->materials()->count());
        $this->assertEquals(1, $slot->workers()->count());
    }
}
