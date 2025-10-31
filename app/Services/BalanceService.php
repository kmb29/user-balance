<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BalanceService
{

    public function deposit(int $userId, float $amount, ?string $comment = null): User
    {
        return DB::transaction(function () use ($userId, $amount, $comment) {
            $user = User::lockForUpdate()->find($userId);

            if (!$user) {
                $user = User::create([
                    'id' => $userId,
                    'name' => 'User ' . $userId,
                    'balance' => 0,
                ]);
            }

            $user->balance = $user->balance + $amount;
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'deposit',
                'amount' => $amount,
                'comment' => $comment,
            ]);


            return $user->refresh();
        });
    }

    public function withdraw(int $userId, float $amount, ?string $comment = null): User
    {
        return DB::transaction(function () use ($userId, $amount, $comment) {
            $user = User::lockForUpdate()->find($userId);

            if (!$user) {
                throw new NotFoundHttpException('User not found');
            }

            if (bccomp((string)$user->balance, (string)$amount, 2) === -1) {
                throw new HttpException(409, 'Insufficient funds');
            }

            $user->balance = $user->balance - $amount;
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'withdraw',
                'amount' => $amount,
                'comment' => $comment,
            ]);

            return $user->refresh();
        });
    }

    public function transfer(int $fromUserId, int $toUserId, float $amount, ?string $comment = null): array
    {
        if ($fromUserId === $toUserId) {
            throw new HttpException(422, 'from_user_id and to_user_id must be different');
        }

        return DB::transaction(function () use ($fromUserId, $toUserId, $amount, $comment) {
            $from = User::lockForUpdate()->find($fromUserId);
            $to = User::lockForUpdate()->find($toUserId);

            if (!$from || !$to) {
                throw new NotFoundHttpException('User not found');
            }

            if (bccomp((string)$from->balance, (string)$amount, 2) === -1) {
                throw new HttpException(409, 'Insufficient funds');
            }

            $from->balance = $from->balance - $amount;
            $from->save();

            Transaction::create([
                'user_id' => $from->id,
                'type' => 'transfer_out',
                'amount' => $amount,
                'comment' => $comment,
                'related_user_id' => $to->id,
            ]);

            $to->balance = $to->balance + $amount;
            $to->save();

            Transaction::create([
                'user_id' => $to->id,
                'type' => 'transfer_in',
                'amount' => $amount,
                'comment' => $comment,
                'related_user_id' => $from->id,
            ]);

            return [$from->refresh(), $to->refresh()];
        });
    }
}
