<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_dailyClosing', only: ['checkStatus', 'openSession', 'closeSession', 'getHistory'])
        ];
    }

    public function checkStatus()
    {
        $session = DB::table('cash_sessions')
            ->where('status', 'open')
            ->first();

        return response()->json([
            'is_open' => !!$session,
            'session' => $session
        ]);
    }

    public function openSession(Request $request)
    {
        $request->validate([
            'opening_amount' => 'required|numeric|min:0'
        ]);

        $active = DB::table('cash_sessions')->where('status', 'open')->exists();

        if ($active) {
            return response()->json(['message' => 'Ya existe una sesión abierta'], 400);
        }

        // Usamos Auth::user()->id_user porque tu modelo User tiene esa llave primaria
        $id = DB::table('cash_sessions')->insertGetId([
            'id_user' => Auth::user()->id_user,
            'opening_amount' => $request->opening_amount,
            'opened_at' => \Carbon\Carbon::now(),
            'status' => 'open',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Caja abierta correctamente',
            'session_id' => $id
        ]);
    }
    public function closeSession()
    {
        $session = DB::table('cash_sessions')->where('status', 'open')->first();
        if (!$session) return response()->json(['message' => 'No hay sesión activa'], 400);

        $now = Carbon::now();

        $payments = DB::table('sale_payments')
            ->join('payment_methods', 'sale_payments.payment_method_id', '=', 'payment_methods.id_payment_method')
            ->join('sales', 'sale_payments.sale_id', '=', 'sales.id_sale')
            ->where('sales.created_at', '>=', $session->opened_at)
            ->select(
                'payment_methods.name_payment_method',
                DB::raw('SUM(amount_paid - change_returned) as net_total'),
                DB::raw('COUNT(id_sale_payment) as transaction_count')
            )
            ->groupBy('payment_methods.id_payment_method', 'payment_methods.name_payment_method')
            ->get();

        $totalSales = $payments->sum('net_total');

        DB::table('cash_sessions')->where('id', $session->id)->update([
            'closing_amount' => $totalSales,
            'closed_at' => $now,
            'status' => 'closed',
            'payment_details' => json_encode($payments),
            'updated_at' => $now
        ]);

        return response()->json([
            'status' => 'success',
            'summary' => [
                'session_id' => $session->id,
                'opening_amount' => (float)$session->opening_amount,
                'sales_amount' => (float)$totalSales,
                'total_expected' => (float)$session->opening_amount + $totalSales,
                'details' => $payments
            ]
        ]);
    }

    public function getHistory()
    {
        try {
            $history = DB::table('cash_sessions')
                ->join('users', 'cash_sessions.id_user', '=', 'users.id_user')
                ->select(
                    'cash_sessions.*',
                    'users.name_user as user_name'
                )
                ->orderBy('cash_sessions.opened_at', 'desc')
                ->limit(30)
                ->get();

            return response()->json([
                'status' => 'success',
                'history' => $history
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
