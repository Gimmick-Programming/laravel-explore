<?php

namespace App\Console\Commands;

use DateTime;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


class CronJobCreateLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:CronJobCreateLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $webhookUrl = "https://discord.com/api/webhooks/1133978699037941870/_IZZ89eafdf8P5BAoKL_H1UIrgIwdYWZd4qK3SOWdXjgbfDX_IU5UprwECTcedp3BSFV";

    // public function __construct()
    // {
    //     $this->webhookUrl = ;
    // }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = "Login Terminal";
        echo PHP_EOL;

        $data = "User " . Str::random(5) . " berhasil login.";
        echo PHP_EOL;

        echo $action;
        echo PHP_EOL;

        echo $data;
        echo PHP_EOL;

        // $this->CreateSyncQueue("aaa", ["id" => "2"]);
        echo PHP_EOL;

        // $this->logAccess($action, $data);
        $this->consumeQueue("aaa");
        echo PHP_EOL;

        echo "end cron";
        echo PHP_EOL;
    }

    private function logAccess($action, $data)
    {
        $logFile = storage_path('app/create-log/') . 'access_log_2.csv';

        // Mendapatkan waktu saat ini dalam format yang diinginkan (misalnya, ISO 8601)
        $timestamp = date('Y-m-d H:i:s');
        $dateTime = new DateTime($timestamp, new DateTimeZone('UTC'));

        // Set the target timezone you want to convert to (e.g., 'Asia/Tokyo')
        $targetTimezone = 'Asia/Jakarta';

        // Create a new DateTimeZone object for the target timezone
        $dateTimezoneTarget = new DateTimeZone($targetTimezone);

        // Set the timezone of the DateTime object to the target timezone
        $dateTime->setTimezone($dateTimezoneTarget);

        // Get the converted date and time in the target timezone
        $convertedTimestamp = $dateTime->format('Y-m-d H:i:s');

        // Membuat baris log dalam format CSV
        $logLine = "$convertedTimestamp,$action,$data\n";
        // $min = 1; // Minimum value
        // $max = 100; // Maximum value
        // $randomNumber = random_int($min, $max);

        // $responseImage = Http::get("https://picsum.photos/v2/list");

        // $responseImageJson = $responseImage->json();


        $response = Http::post($this->webhookUrl, [
            'content' => $logLine,
            'embeds' => [
                [
                    'title' => ':coffee: Bring me a Coffe',
                    'description' => 'Santuy Dev!',
                    'color' => hexdec('00FF00'), // Green color (you can use other HEX color codes)
                    'thumbnail' => ['url' => "https://plus.unsplash.com/premium_photo-1670897798053-27b9dd0c2036?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=MnwxfDB8MXxyYW5kb218MHx8fHx8fHx8MTY5MDQzODg1MQ&ixlib=rb-4.0.3&q=80&w=1080"], // Replace with your image URL

                ],
            ],
        ]);

        // Menuliskan log ke file
        if (file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX) !== false) {
            echo "Log access berhasil dicatat.";
        } else {
            echo "Gagal mencatat log access.";
        }
    }

    function CreateSyncQueue(string $keyQueue, $data, $setDelay = 0)
    {
        echo "start create Queue";
        echo PHP_EOL;
        try {
            $factory = new \Enqueue\Redis\RedisConnectionFactory(
                [
                    'host'              => config('database.redis.default.host'),
                    'port'              => config('database.redis.default.port'),
                    'scheme_extensions' => ['predis'],
                ]
            );

            $context  = $factory->createContext();
            $newQueue = $context->createQueue($keyQueue);
            $message  = $context->createMessage(json_encode($data));

            echo "create Queue";
            echo PHP_EOL;
            if ($setDelay) {
                $setDelay = $setDelay * 1000;
                $context->createProducer()
                    ->setDeliveryDelay($setDelay)
                    ->send($newQueue, $message);
            } else {
                $context->createProducer()
                    ->send($newQueue, $message);
            }
        } catch (\Throwable $th) {
            $message = "Terjadi kesalahan : " . $th->getMessage();
            $jsonData = [
                'content' => "Create Queue : $keyQueue",
                'embeds' => [
                    [
                        'description' => json_encode($data),
                        'color'       => 0xFF0000,
                        'fields'      => [
                            [
                                'name'   => "MESSAGE : ",
                                'value'  => ":rage: " . $message,
                                'inline' => false
                            ],
                        ]
                    ]
                ]
            ];

            try {
                $client = new \GuzzleHttp\Client();
                $client->post($this->webhookUrl, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode($jsonData, JSON_PRESERVE_ZERO_FRACTION),
                ]);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        echo "end create Queue";
        echo PHP_EOL;
    }

    function consumeQueue(string $keyQueue)
    {
        echo "start consume Queue";
        echo PHP_EOL;
        try {
            $factory = new \Enqueue\Redis\RedisConnectionFactory(
                [
                    'host'              => config('database.redis.default.host'),
                    'port'              => config('database.redis.default.port'),
                    'scheme_extensions' => ['predis'],
                ]
            );

            $context  = $factory->createContext();
            $newQueue = $context->createQueue($keyQueue);


            $consumer = $context->createConsumer($newQueue);


            echo "get data Queue";
            echo PHP_EOL;
            echo time();
            echo PHP_EOL;
            $message = $consumer->receive();
            // var_dump($consumer->receive());
            var_dump($message);
            echo PHP_EOL;
            echo time();
            echo PHP_EOL;
            echo PHP_EOL;

            echo (json_decode($message->getBody())->id);
            // process a message
            echo PHP_EOL;

            echo "proses ack";
            echo PHP_EOL;

            $consumer->acknowledge($message);
            echo PHP_EOL;

            echo "ack Queue";
            echo PHP_EOL;
        } catch (\Throwable $th) {
            $message = "Terjadi kesalahan : " . $th->getMessage();
            $jsonData = [
                'content' => "Create Queue : $keyQueue",
                'embeds' => [
                    [
                        'description' => json_encode([]),
                        'color'       => 0xFF0000,
                        'fields'      => [
                            [
                                'name'   => "MESSAGE : ",
                                'value'  => ":rage: " . $message,
                                'inline' => false
                            ],
                        ]
                    ]
                ]
            ];

            try {
                $client = new \GuzzleHttp\Client();
                $client->post($this->webhookUrl, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode($jsonData, JSON_PRESERVE_ZERO_FRACTION),
                ]);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        echo "end consume Queue";
        echo PHP_EOL;
    }
}
