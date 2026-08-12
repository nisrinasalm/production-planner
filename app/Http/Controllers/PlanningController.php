<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidPlanningInputException;
use App\Http\Requests\StorePlanningRequest;
use App\Models\Planning;
use App\Services\BalancingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PlanningController extends Controller
{
    public function store(StorePlanningRequest $request, BalancingService $balancingService): JsonResponse
    {
        $validated = $request->validated();

        $existing = Planning::where('request_code', $validated['request_code'])->first();
        if ($existing) {
            return $this->planningResponse(
                $existing,
                200,
                'RequestCode ini sudah pernah diproses sebelumnya; tidak dibuat transaksi baru.'
            );
        }
 
        try {
            $planning = DB::transaction(function () use ($validated, $balancingService) {
                $sortedSlots = collect($validated['slots'])->sortBy('slot_order')->values();
                $originalQuantities = $sortedSlots->pluck('original_quantity')->all();

                $balancedQuantities = $balancingService->balance($originalQuantities);

                $planning = Planning::create([
                    'request_code' => $validated['request_code'],
                    'candidate_token' => $validated['candidate_token'],
                    'status' => 'success',
                ]);

                foreach ($sortedSlots as $i => $slotInput) {
                    $planning->slots()->create([
                        'slot_order' => $slotInput['slot_order'],
                        'slot_name' => $slotInput['slot_name'],
                        'original_quantity' => $slotInput['original_quantity'],
                        'balanced_quantity' => $balancedQuantities[$i],
                        'is_active' => $slotInput['original_quantity'] > 0,
                    ]);
                }

                return $planning;
            });
        } catch (InvalidPlanningInputException $e) {
            
            return response()->json([
                'message' => 'Proses balancing gagal: input tidak valid.',
                'error' => $e->getMessage(),
            ], 422);
        }

        return $this->planningResponse($planning, 201, 'Planning berhasil diproses.');
    }

    public function index(): JsonResponse
    {
        $plannings = Planning::with('slots')
            ->latest()
            ->paginate(15)
            ->through(fn (Planning $planning) => [
                'planning_id' => $planning->id,
                'request_code' => $planning->request_code,
                'status' => $planning->status,
                'created_at' => $planning->created_at,
                'original_total' => $planning->originalTotal(),
                'balanced_total' => $planning->balancedTotal(),
            ]);

        return response()->json($plannings);
    }

    public function show(Planning $planning): JsonResponse
    {
        return $this->planningResponse($planning);
    }

    private function planningResponse(Planning $planning, int $status = 200, ?string $message = null): JsonResponse
    {
        $planning->loadMissing('slots');

        return response()->json([
            'message' => $message,
            'data' => [
                'planning_id' => $planning->id,
                'request_code' => $planning->request_code,
                'status' => $planning->status,
                'created_at' => $planning->created_at,
                'original_total' => $planning->originalTotal(),
                'balanced_total' => $planning->balancedTotal(),
                'slots' => $planning->slots->sortBy('slot_order')->values(),
            ],
        ], $status);
    }
}
