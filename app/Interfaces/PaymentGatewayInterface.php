<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    public function sendPayment( $request);

    public function callBack(Request $request);
}
