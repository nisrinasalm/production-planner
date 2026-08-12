<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\BalancingService;
use App\Exceptions\InvalidPlanningInputException;

class BalancingServiceTest extends TestCase
{
    private BalancingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BalancingService();
    }

    public function test_sample_dari_dokumen(): void
    {
        $result = $this->service->balance([4, 5, 1, 7, 6, 4, 0]);

        $this->assertSame([4, 5, 4, 5, 5, 4, 0], $result);
        $this->assertSame(27, array_sum($result));
    }

    public function test_total_habis_dibagi(): void
    {
        $result = $this->service->balance([2, 2, 2, 2]);

        $this->assertSame([2, 2, 2, 2], $result);
        $this->assertSame(8, array_sum($result));
    }

    public function test_total_bersisa(): void
    {
        $result = $this->service->balance([1, 2, 4]);

        $this->assertSame([2, 2, 3], $result);
        $this->assertSame(7, array_sum($result));
    }

    public function test_semua_nol(): void
    {
        $result = $this->service->balance([0, 0, 0, 0]);

        $this->assertSame([0, 0, 0, 0], $result);
    }

    public function test_hanya_satu_slot_aktif(): void
    {
        $result = $this->service->balance([0, 5, 0]);

        $this->assertSame([0, 5, 0], $result);
    }

    public function test_tie_break_index_lebih_awal_menang(): void
    {
        $result = $this->service->balance([5, 5, 3]);

        $this->assertSame([5, 4, 4], $result);
        $this->assertSame(13, array_sum($result));
    }

    public function test_input_tidak_valid_ditolak(): void
    {
        $this->expectException(InvalidPlanningInputException::class);
        $this->service->balance([4, -1, 2]);
    }

    public function test_input_pecahan_ditolak(): void
    {
        $this->expectException(InvalidPlanningInputException::class);
        $this->service->balance([4, 2.5, 3]);
    }

    public function test_edge_banyak_slot_nilai_sama_dengan_remainder(): void
    {
        $result = $this->service->balance([3, 3, 3, 3, 3, 3, 3, 3, 3, 4]);

        $this->assertSame([3, 3, 3, 3, 3, 3, 3, 3, 3, 4], $result);
        $this->assertSame(31, array_sum($result));
    }

    public function test_edge_remainder_hampir_sama_dengan_jumlah_slot_aktif(): void
    {
        $result = $this->service->balance([5, 3, 2, 1]);

        $this->assertSame([3, 3, 3, 2], $result);
        $this->assertSame(11, array_sum($result));

        $this->assertLessThanOrEqual(1, max($result) - min($result));
    }

    public function test_edge_input_satu_slot_saja(): void
    {
        $result = $this->service->balance([7]);

        $this->assertSame([7], $result);
    }
}

