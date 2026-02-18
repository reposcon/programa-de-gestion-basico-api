<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReportController extends Controller implements HasMiddleware
{

 public static function middleware(): array
    {
        return [
            new Middleware('permission:view_dailyClosing', only: ['dailyClosing'])
        ];
    }

    public function dailyClosing()
    {
        $today = \Carbon\Carbon::today();

        $payments = DB::table('sale_payments')
            ->join('payment_methods', 'sale_payments.payment_method_id', '=', 'payment_methods.id_payment_method')
            ->join('sales', 'sale_payments.sale_id', '=', 'sales.id_sale')
            ->whereDate('sales.created_at', $today)
            ->select(
                'payment_methods.name_payment_method',
                DB::raw('SUM(amount_paid - change_returned) as net_total'),
                DB::raw('COUNT(id_sale_payment) as transaction_count')
            )
            ->groupBy('payment_methods.id_payment_method', 'payment_methods.name_payment_method')
            ->get();

        return response()->json([
            'status' => 'success',
            'date' => $today->toDateString(),
            'cash_count' => $payments
        ]);
    }
}
