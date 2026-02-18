<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CustomerController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_customers', only: ['index ' , 'show']),
            new Middleware('permission:create_customers', only: ['store']),
            new Middleware('permission:update_customers', only: ['update', 'toggle']),
        ];
    }


    public function index()
    {
        return response()->json(Customer::all(), 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_customer' => 'required|string|max:255',
            'document_number_customer' => 'required|string|unique:customers,document_number_customer',
            'email_customer' => 'nullable|email',
            'phone_customer' => 'nullable|string',
        ]);

        $customer = Customer::create($data);
        return response()->json(['message' => 'Cliente creado', 'customer' => $customer], 201);
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer, 200);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $data = $request->validate([
            'name_customer' => 'string|max:255',
            'document_number_customer' => "string|unique:customers,document_number_customer,$id,id_customer",
            'email_customer' => 'nullable|email',
            'phone_customer' => 'nullable|string',
        ]);

        $customer->update($data);
        return response()->json(['message' => 'Cliente actualizado', 'customer' => $customer]);
    }

    public function toggle($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->state_customer = !$customer->state_customer;
        $customer->save();

        return response()->json([
            'message' => 'Estado actualizado',
            'state' => $customer->state_customer
        ]);
    }
}
