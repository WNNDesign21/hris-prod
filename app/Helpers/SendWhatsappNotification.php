<?php

namespace App\Helpers;

use Throwable;
use GuzzleHttp\Client;

class SendWhatsappNotification
{

    /**
     * API Response
     *
     * @var array
     */
    protected static $result = true;
    public static function send($message, $organisasi_id, $phone_number)
    {
        $client = new Client();

        try {
            if (is_array($phone_number)) {
                $url = env('API_URL_WHATSAPP') . 'send-broadcast';
                $body = [
                    'clientId' => $organisasi_id == 1 ? env('CLIENT_ID_TCF2') : env('CLIENT_ID_TCF3'),
                    'phoneNumbers' => $phone_number,
                    'message' => $message
                ];
                
                $headers = [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'x-api-key' => env('API_KEY_WHATSAPP'),
                ];
        
                $response = $client->post($url, [
                    'headers' => $headers,
                    'json' => $body,
                ]);
                $responseBody = $response->getBody();
                $response = json_decode($responseBody->getContents());
                self::$result = true;
                return self::$result;
            } else {
                $url = env('API_URL_WHATSAPP').'send-message';
                $body = [
                    'clientId' => $organisasi_id == 1 ? env('CLIENT_ID_TCF2') : env('CLIENT_ID_TCF3'),
                    'phoneNumber' => $phone_number,
                    'message' => $message
                ];
                
                $headers = [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'x-api-key' => env('API_KEY_WHATSAPP'),
                ];
        
                $response = $client->post($url, [
                    'headers' => $headers,
                    'json' => $body,
                ]);
                self::$result = true;
                return self::$result;
            }
        } catch (Throwable $e) {
            self::$result = false;
            SendWhatsappNotificationJob::dispatch($message, $organisasi_id, $phone_number);
            return self::$result;
        }
    }
}