<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentRedirectController extends Controller
{
    public function success(Request $request)
    {
        $orderId = $request->query('order_id');
        $externalId = $request->query('external_id');

        return view('payment.success', [
            'order_id' => $orderId,
            'external_id' => $externalId
        ]);
    }

    public function failed(Request $request)
    {
        $orderId = $request->query('order_id');
        $externalId = $request->query('external_id');

        return view('payment.failed', [
            'order_id' => $orderId,
            'external_id' => $externalId
        ]);
    }
}
