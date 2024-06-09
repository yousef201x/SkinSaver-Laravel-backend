<?php

namespace App\Services;

use GuzzleHttp\Client;

class PaymobService
{
    protected $client;
    protected $apiKey;
    protected $integrationId;
    protected $iframeId;
    protected $hmacSecret;

    public function __construct()
    {
        // Disable SSL verification
        $this->client = new Client(['verify' => false]);

        // Retrieve Paymob credentials from environment variables or another source
        $this->apiKey = 'ZXlKaGJHY2lPaUpJVXpVeE1pSXNJblI1Y0NJNklrcFhWQ0o5LmV5SmpiR0Z6Y3lJNklrMWxjbU5vWVc1MElpd2ljSEp2Wm1sc1pWOXdheUk2T1RjMk9ETTRMQ0p1WVcxbElqb2lhVzVwZEdsaGJDSjkub0NiRE0tWFBGQk9NYnIycERMa21TWkxzZGNsbVpUUTZEV1c2UUhIejIxdTNEZHFsTXJfODdCOUlIc0gyNWhvSVBFUi0tUlQ4VDl2cDNYQ3Fvc1RiX3c';
        $this->integrationId = '4577244';
        $this->iframeId = '846803';
        $this->hmacSecret = '5CABF39E65CBFD5C223CEC04C6B2433B';
    }

    public function authenticate()
    {
        $response = $this->client->post('https://accept.paymobsolutions.com/api/auth/tokens', [
            'json' => [
                'username' => '01096959237',
                'password' => 'ap!wgZ6m?hwG?W_'
            ]
        ]);

        return json_decode($response->getBody(), true)['token'];
    }

    public function createOrder($token, $amount, $currency = 'EGP')
    {
        $response = $this->client->post('https://accept.paymobsolutions.com/api/ecommerce/orders', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'json' => [
                'auth_token' => $token,
                'delivery_needed' => 'false',
                'amount_cents' => $amount * 100,
                'currency' => $currency,
                'items' => []
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    public function createPaymentKey($token, $orderId, $amount, $userData)
    {
        $response = $this->client->post('https://accept.paymobsolutions.com/api/acceptance/payment_keys', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'json' => [
                'auth_token' => $token,
                'amount_cents' => $amount * 100,
                'expiration' => 3600,
                'order_id' => $orderId,
                'billing_data' => [
                    'apartment' => 'NA',
                    'email' => $userData['email'],
                    'floor' => 'NA',
                    'first_name' => 'NA',
                    'street' => 'NA',
                    'building' => 'NA',
                    'phone_number' => 'NA',
                    'shipping_method' => 'NA',
                    'postal_code' => 'NA',
                    'city' => 'NA',
                    'country' => 'NA',
                    'last_name' => 'NA',
                    'state' => 'NA'
                ],
                'currency' => 'EGP',
                'integration_id' => $this->integrationId
            ]
        ]);

        return json_decode($response->getBody(), true)['token'];
    }

    public function getIframeUrl($paymentKey)
    {
        return "https://accept.paymobsolutions.com/api/acceptance/iframes/{$this->iframeId}?payment_token={$paymentKey}";
    }
}
