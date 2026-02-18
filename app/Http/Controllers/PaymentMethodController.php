<?php

namespace App\Http\Controllers;

use App\Models\TaxSetting;
use App\Http\Requests\StoreTaxSettingRequest;
use App\Http\Requests\UpdateTaxSettingRequest;
use App\Models\PaymentMethod;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PaymentMethodController extends Controller implements HasMiddleware
{
     public static function middleware(): array
    {
        return [
            new Middleware('permission:view_taxsetting', only: ['index']),
        ];
    }
    
    public function index()
    {
        return PaymentMethod::all();
    }
}
