<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ZohoSignController extends Controller
{
    public function sendDocument(Request $request)
    {
        $clientId = env('ZOHO_CLIENT_ID');
        $clientSecret = env('ZOHO_CLIENT_SECRET');
        $refreshToken = env('ZOHO_REFRESH_TOKEN');
        $zohoBaseUrl = env('ZOHO_BASE_URL', 'https://sign.zoho.com');
        
        // Get recipient information from request or use defaults
        $recipientName = $request->input('recipient_name', 'John Doe');
        $recipientEmail = $request->input('recipient_email', 'aliasidiarraibrahima@gmail.com');
        $documentName = $request->input('document_name', 'Document for Signature - ' . date('Y-m-d H:i:s'));

        // Step 1: Refresh the Access Token
        $tokenResponse = Http::asForm()->post('https://accounts.zoho.com/oauth/v2/token', [
            'refresh_token' => $refreshToken,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'refresh_token',
        ]);

        if (!$tokenResponse->ok()) {
            return response()->json([
                'error' => 'Access token not returned by Zoho',
                'response' => $tokenResponse->json(),
            ], 400);
        }

        $accessToken = $tokenResponse['access_token'];

        // Step 2: Prepare file
        $filePath = public_path('docs/sample.pdf');
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'PDF file not found'], 404);
        }

        // Step 3: Prepare payload with recipients
        $requestData = [
            'requests' => [
                'request_name' => $documentName,
                'is_sequential' => true,
                'actions' => [
                    [
                        'action_type' => 'SIGN',
                        'recipient_name' => $recipientName,
                        'recipient_email' => $recipientEmail,
                        'signing_order' => 0,
                        'verify_recipient' => false,
                        'verification_type' => 'EMAIL'
                    ]
                ]
            ]
        ];
        
        // Convert to JSON string for the request
        $jsonData = json_encode($requestData);

        // Step 4: Prepare the multipart request
        $multipart = [
            [
                'name' => 'data',
                'contents' => $jsonData,
                'headers' => ['Content-Type' => 'application/json']
            ],
            [
                'name' => 'file',
                'contents' => file_get_contents($filePath),
                'filename' => 'sample.pdf',
                'headers' => ['Content-Type' => 'application/pdf']
            ]
        ];

        // Send to Zoho Sign
        $response = Http::withToken($accessToken)
            ->asMultipart()
            ->post($zohoBaseUrl . '/api/v1/requests', $multipart);

        // Step 5: Handle response
        if (!$response->ok()) {
            return response()->json([
                'error' => 'Failed to send document to Zoho Sign',
                'status' => $response->status(),
                'body' => $response->body(),
            ], 400);
        }

        $responseData = $response->json();
        
        // Log the full response for debugging
        \Log::info('Zoho Sign Response:', $responseData);
        
        // Extract the signing URL from the response
        $signingUrl = null;
        
        // Check in the actions array
        if (isset($responseData['requests']['actions']) && is_array($responseData['requests']['actions'])) {
            foreach ($responseData['requests']['actions'] as $action) {
                if (isset($action['signing_url'])) {
                    $signingUrl = $action['signing_url'];
                    break;
                }
            }
        }
        
        // Alternative locations for signing URL
        if (!$signingUrl && isset($responseData['requests']['request_url'])) {
            $signingUrl = $responseData['requests']['request_url'];
        }
        
        // If we found a signing URL, redirect to it
        if ($signingUrl) {
            return redirect()->away($signingUrl);
        }
        
        // If we can't find the signing URL, return the full response for debugging
        return response()->json([
            'message' => 'Document sent successfully, but could not find signing URL',
            'response' => $responseData,
        ], 200);
    }
    
    // Alternative method with multiple recipients
    public function sendDocumentMultipleRecipients()
    {
        $clientId = env('ZOHO_CLIENT_ID');
        $clientSecret = env('ZOHO_CLIENT_SECRET');
        $refreshToken = env('ZOHO_REFRESH_TOKEN');
        $zohoBaseUrl = env('ZOHO_BASE_URL', 'https://sign.zoho.com');

        // Get access token (same as above)
        $tokenResponse = Http::asForm()->post('https://accounts.zoho.com/oauth/v2/token', [
            'refresh_token' => $refreshToken,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'refresh_token',
        ]);

        if (!$tokenResponse->ok()) {
            return response()->json([
                'error' => 'Access token not returned by Zoho',
                'response' => $tokenResponse->json(),
            ], 400);
        }

        $accessToken = $tokenResponse['access_token'];

        // Prepare file
        $filePath = public_path('docs/sample.pdf');
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'PDF file not found'], 404);
        }

        // Payload with multiple recipients
        $requestData = [
            'requests' => [
                'request_name' => 'Document for Signature - ' . date('Y-m-d H:i:s'),
                'is_sequential' => false, // Set to true if you want sequential signing
                'actions' => [
                    [
                        'action_type' => 'SIGN',
                        'recipient_name' => 'Mohamed BENIAICH',
                        'recipient_email' => 'mohamedbeniaich00@gmail.com',
                        'signing_order' => 0,
                        'verify_recipient' => false,
                        'verification_type' => 'EMAIL'
                    ],
                    [
                        'action_type' => 'SIGN',
                        'recipient_name' => 'Second Signer',
                        'recipient_email' => 'second@example.com',
                        'signing_order' => 1,
                        'verify_recipient' => false,
                        'verification_type' => 'EMAIL'
                    ]
                ]
            ]
        ];
        
        $jsonData = json_encode($requestData);

        $multipart = [
            [
                'name' => 'data',
                'contents' => $jsonData,
                'headers' => ['Content-Type' => 'application/json']
            ],
            [
                'name' => 'file',
                'contents' => file_get_contents($filePath),
                'filename' => 'sample.pdf',
                'headers' => ['Content-Type' => 'application/pdf']
            ]
        ];

        $response = Http::withToken($accessToken)
            ->asMultipart()
            ->post($zohoBaseUrl . '/api/v1/requests', $multipart);

        if (!$response->ok()) {
            return response()->json([
                'error' => 'Failed to send document to Zoho Sign',
                'status' => $response->status(),
                'body' => $response->body(),
            ], 400);
        }

        return response()->json([
            'message' => 'Document sent successfully to multiple recipients',
            'response' => $response->json(),
        ], 200);
    }
    
    // Method to send document to specific recipient via form
    public function sendToRecipient(Request $request)
    {
        // Validate the input
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_email' => 'required|email',
            'document_name' => 'nullable|string|max:255'
        ]);
        
        return $this->sendDocument($request);
    }
    
    // Method to send document to multiple specific recipients
    public function sendToMultipleRecipients(Request $request)
    {
        // Validate the input
        $request->validate([
            'recipients' => 'required|array|min:1',
            'recipients.*.name' => 'required|string|max:255',
            'recipients.*.email' => 'required|email',
            'document_name' => 'nullable|string|max:255',
            'is_sequential' => 'nullable|boolean'
        ]);
        
        $clientId = env('ZOHO_CLIENT_ID');
        $clientSecret = env('ZOHO_CLIENT_SECRET');
        $refreshToken = env('ZOHO_REFRESH_TOKEN');
        $zohoBaseUrl = env('ZOHO_BASE_URL', 'https://sign.zoho.com');
        
        $recipients = $request->input('recipients');
        $documentName = $request->input('document_name', 'Document for Signature - ' . date('Y-m-d H:i:s'));
        $isSequential = $request->input('is_sequential', false);

        // Get access token
        $tokenResponse = Http::asForm()->post('https://accounts.zoho.com/oauth/v2/token', [
            'refresh_token' => $refreshToken,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'refresh_token',
        ]);

        if (!$tokenResponse->ok()) {
            return response()->json([
                'error' => 'Access token not returned by Zoho',
                'response' => $tokenResponse->json(),
            ], 400);
        }

        $accessToken = $tokenResponse['access_token'];

        // Prepare file
        $filePath = public_path('docs/sample.pdf');
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'PDF file not found'], 404);
        }

        // Build actions array from recipients
        $actions = [];
        foreach ($recipients as $index => $recipient) {
            $actions[] = [
                'action_type' => 'SIGN',
                'recipient_name' => $recipient['name'],
                'recipient_email' => $recipient['email'],
                'signing_order' => $index,
                'verify_recipient' => false,
                'verification_type' => 'EMAIL'
            ];
        }

        // Prepare payload
        $requestData = [
            'requests' => [
                'request_name' => $documentName,
                'is_sequential' => $isSequential,
                'actions' => $actions
            ]
        ];
        
        $jsonData = json_encode($requestData);

        $multipart = [
            [
                'name' => 'data',
                'contents' => $jsonData,
                'headers' => ['Content-Type' => 'application/json']
            ],
            [
                'name' => 'file',
                'contents' => file_get_contents($filePath),
                'filename' => 'sample.pdf',
                'headers' => ['Content-Type' => 'application/pdf']
            ]
        ];

        $response = Http::withToken($accessToken)
            ->asMultipart()
            ->post($zohoBaseUrl . '/api/v1/requests', $multipart);

        if (!$response->ok()) {
            return response()->json([
                'error' => 'Failed to send document to Zoho Sign',
                'status' => $response->status(),
                'body' => $response->body(),
            ], 400);
        }

        return response()->json([
            'message' => 'Document sent successfully to ' . count($recipients) . ' recipients',
            'response' => $response->json(),
        ], 200);
    }
}