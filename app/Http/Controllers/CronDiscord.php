<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CronDiscord extends Controller
{
    //
    protected $webhookUrlDiscord = "https://discord.com/api/webhooks/1134417472452300830/YtrW5cuS1mXTHaHi7weUNh1gkAHjWL1yY2yEl4RvK_n6MT0C11GJYg4xJGjAc5cbYm55";

    public function index()
    {
        return $this->sendToDiscord();
    }

    public function sendToDiscord()
    {
        date_default_timezone_set("Asia/Jakarta");
        $timestamp = date("Y-m-d H:i:s");

        $data =
            [
                "content" => $timestamp . ":sent by server",
                "embeds" => [
                    [
                        "title" => "Jadwal Minum Obat",
                        "description" => "Jangan lupa makan ya :grinning:"
                    ]
                ]
            ];

        Http::post($this->webhookUrlDiscord, $data);
    }
}
