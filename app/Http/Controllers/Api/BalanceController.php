<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\{DepositRequest, WithdrawRequest, TransferRequest};
use App\Services\BalanceService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Throwable;

class BalanceController extends Controller
{
    public function __construct(private BalanceService $service) {}

    public function deposit(DepositRequest $request): JsonResponse
    {
        try {
            $user = $this->service->deposit(
                $request->user_id,
                $request->amount,
                $request->comment
            );
            return response()->json([
                'status' => 'success',
                'data' => ['balance' => $user->balance],
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function withdraw(WithdrawRequest $request): JsonResponse
    {
        try {
            $user = $this->service->withdraw(
                $request->user_id,
                $request->amount,
                $request->comment
            );
            return response()->json([
                'status' => 'success',
                'data' => ['balance' => $user->balance],
            ], 200);
        } catch (Throwable $e) {
            $status = str_contains($e->getMessage(), 'Insufficient funds') ? 409 : 404;
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], $status);
        }
    }

    public function transfer(TransferRequest $request): JsonResponse
    {
        try {
            $this->service->transfer(
                $request->from_user_id,
                $request->to_user_id,
                $request->amount,
                $request->comment
            );
            return response()->json([
                'status' => 'success',
                'message' => 'Transfer successful',
            ], 200);
        } catch (Throwable $e) {
            $status = str_contains($e->getMessage(), 'Insufficient funds') ? 409 : 404;
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], $status);
        }
    }

    public function balance($user_id): JsonResponse
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'user_id' => $user->id,
                'balance' => $user->balance,
            ],
        ], 200);
    }
}
