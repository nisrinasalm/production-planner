<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanningApiTest extends TestCase
{
    use RefreshDatabase;

    private function samplePayload(string $requestCode): array
    {
        return [
            'request_code' => $requestCode,
            'candidate_token' => 'VEH-CANDIDATECODE',
            'slots' => [
                ['slot_order' => 1, 'slot_name' => 'Senin', 'original_quantity' => 4],
                ['slot_order' => 2, 'slot_name' => 'Selasa', 'original_quantity' => 5],
                ['slot_order' => 3, 'slot_name' => 'Rabu', 'original_quantity' => 1],
                ['slot_order' => 4, 'slot_name' => 'Kamis', 'original_quantity' => 7],
                ['slot_order' => 5, 'slot_name' => 'Jumat', 'original_quantity' => 6],
                ['slot_order' => 6, 'slot_name' => 'Sabtu', 'original_quantity' => 4],
                ['slot_order' => 7, 'slot_name' => 'Minggu', 'original_quantity' => 0],
            ],
        ];
    }

    public function test_create_planning_berhasil_dan_tersimpan_atomically(): void
    {
        $response = $this->postJson('/api/plannings', $this->samplePayload('REQ-001'));

        $response->assertStatus(201)
            ->assertJsonPath('data.request_code', 'REQ-001')
            ->assertJsonPath('data.status', 'success')
            ->assertJsonPath('data.original_total', 27)
            ->assertJsonPath('data.balanced_total', 27)
            ->assertJsonCount(7, 'data.slots');

        $this->assertDatabaseCount('plannings', 1);
        $this->assertDatabaseCount('planning_slots', 7);

        $this->assertDatabaseHas('planning_slots', ['slot_order' => 4, 'balanced_quantity' => 5]);
        $this->assertDatabaseHas('planning_slots', ['slot_order' => 7, 'balanced_quantity' => 0, 'is_active' => false]);
    }

    public function test_validasi_gagal_tidak_menyimpan_data_parsial(): void
    {
        $payload = $this->samplePayload('REQ-002');
        $payload['slots'][0]['original_quantity'] = -5;

        $response = $this->postJson('/api/plannings', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['slots.0.original_quantity']);

        $this->assertDatabaseCount('plannings', 0);
        $this->assertDatabaseCount('planning_slots', 0);
    }

    public function test_repeat_request_code_tidak_membuat_transaksi_duplikat(): void
    {
        $payload = $this->samplePayload('REQ-003');

        $first = $this->postJson('/api/plannings', $payload);
        $first->assertStatus(201);

        $second = $this->postJson('/api/plannings', $payload);
        $second->assertStatus(200)
            ->assertJsonPath('data.request_code', 'REQ-003');

        $this->assertDatabaseCount('plannings', 1);
        $this->assertDatabaseCount('planning_slots', 7);
    }

    public function test_list_history_urut_dari_terbaru(): void
    {
        $this->postJson('/api/plannings', $this->samplePayload('REQ-OLD'));

        $this->travel(1)->seconds();

        $this->postJson('/api/plannings', $this->samplePayload('REQ-NEW'));

        $response = $this->getJson('/api/plannings');

        $response->assertStatus(200);
        $this->assertEquals('REQ-NEW', $response->json('data.0.request_code'));
    }

    public function test_get_detail_planning_menampilkan_input_output_dan_status(): void
    {
        $create = $this->postJson('/api/plannings', $this->samplePayload('REQ-DETAIL'));
        $planningId = $create->json('data.planning_id');

        $response = $this->getJson("/api/plannings/{$planningId}");

        $response->assertStatus(200)
            ->assertJsonPath('data.request_code', 'REQ-DETAIL')
            ->assertJsonPath('data.status', 'success')
            ->assertJsonCount(7, 'data.slots');
    }
}