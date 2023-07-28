<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CronController extends Controller
{
    public function createLog()
    {
        $action = "Login";
        $data = "User " . Str::random(5) . " berhasil login.";
        $this->logAccess($action, $data);
    }

    private function logAccess($action, $data)
    {
        $logFile = 'access_log.csv';

        // Mendapatkan waktu saat ini dalam format yang diinginkan (misalnya, ISO 8601)
        $timestamp = date('Y-m-d H:i:s');

        // Membuat baris log dalam format CSV
        $logLine = "$timestamp,$action,$data\n";

        // Menuliskan log ke file
        if (file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX) !== false) {
            echo "Log access berhasil dicatat.";
        } else {
            echo "Gagal mencatat log access.";
        }
    }
}
