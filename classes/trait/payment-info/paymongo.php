<?php

/**
 * PayMongo Trait
 * 
 * Provides PayMongo payment processing functionality
 * Can be used in any class that needs payment processing
 * 
 * Usage:
 * class YourClass {
 *     use PayMongoTrait;
 *     
 *     public function processPayment() {
 *         $result = $this->processPayMongoPayment(...);
 *     }
 * }
 */

trait PayMongoTrait {
    
    /**
     * Get PayMongo secret key from environment
     */
    private function getPayMongoSecretKey() {
        return getenv('PAYMONGO_SECRET_KEY') ?: $_ENV['PAYMONGO_SECRET_KEY'] ?? null;
    }

    /**
     * Get PayMongo public key from environment
     */
    private function getPayMongoPublicKey() {
        return getenv('PAYMONGO_PUBLIC_KEY') ?: $_ENV['PAYMONGO_PUBLIC_KEY'] ?? null;
    }

    /**
     * Main method to process PayMongo payment
     * Returns payment result with intent ID if successful
     */
    public function processPayMongoPayment(
        $amount,
        $currency,
        $cardNumber,
        $expMonth,
        $expYear,
        $cvc,
        $billingName,
        $billingEmail,
        $billingPhone,
        $billingLine1,
        $billingCity,
        $billingPostalCode,
        $billingCountry,
        $description = '',
        $metadata = []
    ) {
        try {
            // Step 1: Create Payment Method
            $paymentMethod = $this->createPayMongoPaymentMethod(
                $cardNumber,
                $expMonth,
                $expYear,
                $cvc,
                $billingName,
                $billingEmail,
                $billingPhone,
                $billingLine1,
                $billingCity,
                $billingPostalCode,
                $billingCountry
            );

            if (!$paymentMethod['success']) {
                return $paymentMethod;
            }

            // Step 2: Create Payment Intent
            $paymentIntent = $this->createPayMongoPaymentIntent(
                $amount,
                $currency,
                $description,
                $metadata
            );

            if (!$paymentIntent['success']) {
                return $paymentIntent;
            }

            // Step 3: Attach Payment Method and Process
            $result = $this->attachPayMongoPaymentMethod(
                $paymentIntent['data']['id'],
                $paymentMethod['data']['id']
            );

            return $result;

        } catch (Exception $e) {
            error_log("[processPayMongoPayment] " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create PayMongo Payment Method (Tokenize card)
     */
    private function createPayMongoPaymentMethod(
        $cardNumber,
        $expMonth,
        $expYear,
        $cvc,
        $name,
        $email,
        $phone,
        $line1,
        $city,
        $postalCode,
        $country
    ) {
        try {
            $url = 'https://api.paymongo.com/v1/payment_methods';
            
            $data = [
                'data' => [
                    'attributes' => [
                        'type' => 'card',
                        'details' => [
                            'card_number' => $cardNumber,
                            'exp_month' => intval($expMonth),
                            'exp_year' => intval($expYear),
                            'cvc' => $cvc
                        ],
                        'billing' => [
                            'name' => $name,
                            'email' => $email,
                            'phone' => $phone,
                            'address' => [
                                'line1' => $line1,
                                'city' => $city,
                                'postal_code' => $postalCode,
                                'country' => $country
                            ]
                        ]
                    ]
                ]
            ];

            $response = $this->makePayMongoRequest($url, 'POST', $data);

            if (isset($response['data']['id'])) {
                return [
                    'success' => true,
                    'data' => $response['data']
                ];
            }

            return [
                'success' => false,
                'error' => $response['errors'][0]['detail'] ?? 'Failed to create payment method'
            ];

        } catch (Exception $e) {
            error_log("[createPayMongoPaymentMethod] " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create PayMongo Payment Intent
     */
    private function createPayMongoPaymentIntent(
        $amount,
        $currency = 'PHP',
        $description = '',
        $metadata = []
    ) {
        try {
            $url = 'https://api.paymongo.com/v1/payment_intents';
            
            // Convert amount to centavos/cents
            $amountInCents = intval($amount * 100);

            $data = [
                'data' => [
                    'attributes' => [
                        'amount' => $amountInCents,
                        'payment_method_allowed' => ['card'],
                        'currency' => strtoupper($currency),
                        'description' => $description,
                        'statement_descriptor' => 'Payment',
                        'metadata' => $metadata
                    ]
                ]
            ];

            $response = $this->makePayMongoRequest($url, 'POST', $data);

            if (isset($response['data']['id'])) {
                return [
                    'success' => true,
                    'data' => $response['data']
                ];
            }

            return [
                'success' => false,
                'error' => $response['errors'][0]['detail'] ?? 'Failed to create payment intent'
            ];

        } catch (Exception $e) {
            error_log("[createPayMongoPaymentIntent] " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Attach Payment Method to Payment Intent
     */
    private function attachPayMongoPaymentMethod($paymentIntentId, $paymentMethodId) {
        try {
            $url = "https://api.paymongo.com/v1/payment_intents/{$paymentIntentId}/attach";
            
            $data = [
                'data' => [
                    'attributes' => [
                        'payment_method' => $paymentMethodId,
                        'return_url' => getenv('PAYMENT_RETURN_URL') ?: $_ENV['PAYMENT_RETURN_URL'] ?? ''
                    ]
                ]
            ];

            $response = $this->makePayMongoRequest($url, 'POST', $data);

            if (isset($response['data']['attributes']['status'])) {
                $status = $response['data']['attributes']['status'];
                
                return [
                    'success' => $status === 'succeeded',
                    'status' => $status,
                    'payment_intent_id' => $response['data']['id'],
                    'data' => $response['data'],
                    'error' => $status !== 'succeeded' 
                        ? ($response['data']['attributes']['last_payment_error']['failed_message'] ?? 'Payment failed')
                        : null
                ];
            }

            return [
                'success' => false,
                'error' => $response['errors'][0]['detail'] ?? 'Failed to attach payment method'
            ];

        } catch (Exception $e) {
            error_log("[attachPayMongoPaymentMethod] " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Make HTTP request to PayMongo API
     */
    private function makePayMongoRequest($url, $method = 'GET', $data = null) {
        $secretKey = $this->getPayMongoSecretKey();
        
        if (!$secretKey) {
            throw new Exception("PayMongo secret key not configured");
        }

        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($secretKey . ':')
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);

        $decoded = json_decode($response, true);

        if ($httpCode >= 400) {
            error_log("[PayMongo API Error] HTTP {$httpCode}: " . $response);
        }

        return $decoded;
    }

    /**
     * Retrieve Payment Intent status
     */
    public function getPayMongoPaymentIntent($paymentIntentId) {
        try {
            $url = "https://api.paymongo.com/v1/payment_intents/{$paymentIntentId}";
            $response = $this->makePayMongoRequest($url, 'GET');

            if (isset($response['data']['id'])) {
                return [
                    'success' => true,
                    'data' => $response['data'],
                    'status' => $response['data']['attributes']['status']
                ];
            }

            return [
                'success' => false,
                'error' => 'Payment intent not found'
            ];

        } catch (Exception $e) {
            error_log("[getPayMongoPaymentIntent] " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create PayMongo Source (for non-card payments like GCash, GrabPay)
     */
    public function createPayMongoSource($amount, $type = 'gcash', $currency = 'PHP', $description = '', $metadata = []) {
        try {
            $url = 'https://api.paymongo.com/v1/sources';
            
            // Convert amount to centavos/cents
            $amountInCents = intval($amount * 100);

            $data = [
                'data' => [
                    'attributes' => [
                        'amount' => $amountInCents,
                        'currency' => strtoupper($currency),
                        'type' => $type,
                        'redirect' => [
                            'success' => getenv('PAYMENT_SUCCESS_URL') ?: $_ENV['PAYMENT_SUCCESS_URL'] ?? '',
                            'failed' => getenv('PAYMENT_FAILED_URL') ?: $_ENV['PAYMENT_FAILED_URL'] ?? ''
                        ],
                        'description' => $description,
                        'metadata' => $metadata
                    ]
                ]
            ];

            $response = $this->makePayMongoRequest($url, 'POST', $data);

            if (isset($response['data']['id'])) {
                return [
                    'success' => true,
                    'data' => $response['data'],
                    'checkout_url' => $response['data']['attributes']['redirect']['checkout_url']
                ];
            }

            return [
                'success' => false,
                'error' => $response['errors'][0]['detail'] ?? 'Failed to create source'
            ];

        } catch (Exception $e) {
            error_log("[createPayMongoSource] " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}