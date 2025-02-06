<?php

namespace App\Jobs;

use Exception;
use Throwable;
use GuzzleHttp\Client;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Helpers\SendWhatsappNotification;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendWhatsappNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $message, $organisasi_id, $phone_number;
    public $tries = 3;
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct($message, $organisasi_id, $phone_number)
    {
        $this->message = $message;
        $this->organisasi_id = $organisasi_id;
        $this->phone_number = $phone_number;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $client = new Client();

        try {
            $url = $this->isBroadcast() ? env('API_URL_WHATSAPP') . 'send-broadcast' : env('API_URL_WHATSAPP') . 'send-message';
            $body = $this->createRequestBody();
            $headers = $this->createHeaders();

            $response = $client->post($url, [
                'headers' => $headers,
                'json' => $body,
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody();

            if ($statusCode >= 200 && $statusCode < 300) {
                activity('send_whatsapp_notification')->log("Sent Whatsapp Notification: {$this->message} to {$this->phone_number}");
            } else {
                $errorMessage = "Failed to send Whatsapp Notification: {$statusCode} - {$responseBody}";
                activity('send_whatsapp_notification')->log($errorMessage);
                throw new Exception($errorMessage);
            }

        } catch (GuzzleException $e) {
            $errorMessage = "Guzzle Exception: " . $e->getMessage() . " for phone number: " . $this->phone_number;
            activity('send_whatsapp_notification')->log($errorMessage);
        } catch (Exception $e) {
            $errorMessage = "General Exception: " . $e->getMessage() . " for phone number: " . $this->phone_number;
            activity('send_whatsapp_notification')->log($errorMessage);
        }
    }

    private function isBroadcast(): bool
    {
        return is_array($this->phone_number);
    }

    private function createRequestBody(): array
    {
        $clientId = $this->organisasi_id == 1 ? env('CLIENT_ID_TCF2') : env('CLIENT_ID_TCF3');
        if ($this->isBroadcast()) {
            return [
                'clientId' => $clientId,
                'phoneNumbers' => $this->phone_number,
                'message' => $this->message,
            ];
        } else {
            return [
                'clientId' => $clientId,
                'phoneNumber' => $this->phone_number,
                'message' => $this->message,
            ];
        }
    }

    private function createHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-api-key' => env('API_KEY_WHATSAPP'),
        ];
    }
}
