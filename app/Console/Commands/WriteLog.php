<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ClientException;
use App\Models\History;
use App\Models\Image;
use Carbon\Carbon;


class WriteLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'writelog:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'write info messages in log file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $str = date('i:s');

        logger()->info($str.' command処理開始');

        sleep(80);
        logger()->info($str.' command処理終了');
    }
}
