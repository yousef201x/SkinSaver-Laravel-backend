<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymobService;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class PaymentController extends Controller
{
    protected $paymob;

    public function __construct(PaymobService $paymob)
    {
        $this->paymob = $paymob;
    }

    public function initiatePayment(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        // Retrieve the authenticated user's ID
        $user_id = Auth::id();

        // Alternatively, you can get the authenticated user object and extract the ID
        // $user = Auth::user();
        // $user_id = $user->id;

        // Fetch the product based on the request
        $product = Product::find($request->product_id);

        // Authenticate with Paymob and create the order
        $token = $this->paymob->authenticate();
        $order = $this->paymob->createOrder($token, $product->price);

        // Create payment key and get iframe URL
        $paymentKey = $this->paymob->createPaymentKey($token, $order['id'], $product->price, [
            'user_id' => $user_id,
            'email' => Auth::user()->email, // Assuming you want to use the email of the authenticated user
        ]);
        $iframeUrl = $this->paymob->getIframeUrl($paymentKey);

        // Prepare the response data
        $responseData = [
            'payment_url' => $iframeUrl,
            'order_id' => $order['id'],
            'product_id' => $product->id,
            'user_id' => $user_id, // Include the user ID in the response
            // Add any other relevant data you want to return
        ];

        // Return the response as JSON
        return response()->json($responseData);
    }
}
