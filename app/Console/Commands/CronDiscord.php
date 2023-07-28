<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CronDiscord extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cronDiscord';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $webhookUrlDiscord = "https://discord.com/api/webhooks/1134417472452300830/YtrW5cuS1mXTHaHi7weUNh1gkAHjWL1yY2yEl4RvK_n6MT0C11GJYg4xJGjAc5cbYm55";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->sendToDiscord();
    }

    public function sendToDiscord()
    {
        date_default_timezone_set("Asia/Jakarta");
        $timestamp = date("Y-m-d H:i:s");

        Http::post($this->webhookUrlDiscord, [
            "content" => $timestamp . ":sent by server",
            "embeds" => [
                [
                    "title" => "Jadwal minum obat",
                    "description" => "Jangan lupa makan dulu ya :grinning:",
                    "color" => hexdec("00FF00")
                ]
            ]
        ]);
    }
}
