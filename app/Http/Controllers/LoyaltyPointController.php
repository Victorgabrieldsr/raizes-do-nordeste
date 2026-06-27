<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoyaltyPointController extends Controller
{
    public function index()
    {
        $user = JWTAuth::user();

        $points = LoyaltyPoint::where('user_id', $user->id)
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalEarned = LoyaltyPoint::where('user_id', $user->id)
            ->where('type', 'EARNED')
            ->sum('points');

        $totalRedeemed = LoyaltyPoint::where('user_id', $user->id)
            ->where('type', 'REDEEMED')
            ->sum('points');

        return response()->json([
            'balance' => $totalEarned - $totalRedeemed,
            'total_earned' => $totalEarned,
            'total_redeemed' => $totalRedeemed,
            'history' => $points,
        ]);
    }

    public function redeem(Request $request)
    {
        $request->validate([
            'points' => 'required|integer|min:1',
            'order_id' => 'required|exists:orders,id',
        ]);

        $user = JWTAuth::user();

        $totalEarned = LoyaltyPoint::where('user_id', $user->id)
            ->where('type', 'EARNED')
            ->sum('points');

        $totalRedeemed = LoyaltyPoint::where('user_id', $user->id)
            ->where('type', 'REDEEMED')
            ->sum('points');

        $balance = $totalEarned - $totalRedeemed;

        if ($balance < $request->points) {
            return response()->json([
                'error' => 'SALDO_INSUFICIENTE',
                'message' => 'Saldo de pontos insuficiente.',
                'balance' => $balance,
            ], 409);
        }

        $redemption = LoyaltyPoint::create([
            'user_id' => $user->id,
            'order_id' => $request->order_id,
            'points' => $request->points,
            'type' => 'REDEEMED',
        ]);

        return response()->json([
            'message' => 'Pontos resgatados com sucesso.',
            'redeemed_points' => $request->points,
            'new_balance' => $balance - $request->points,
            'redemption' => $redemption,
        ]);
    }
}