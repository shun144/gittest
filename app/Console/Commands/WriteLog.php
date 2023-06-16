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
        logger()->info('よっしゃ!!!!');
        $title = 'テスト実行';
        $message = 'スケジュール実行';
        // $images = $request->file('imagefile');

        $API = 'https://notify-api.line.me/api/notify';
        $store_id = Auth::user()->store_id;
        $lines = DB::table('lines')->select('id','token')->where('store_id', $store_id)->get();

        $client = new Client();
        foreach ($lines as $line){
            try {
                $res = $client->post($API, 
                    [
                        'headers' => [
                            'Content-Type'  => 'application/x-www-form-urlencoded',
                            'Authorization' => 'Bearer ' . $line->token,
                        ],
                        'form_params' => ['message' => $message ]
                    ]
                );

                History::insert(
                    [
                        'store_id' => $store_id,
                        'title'=> $title,
                        'content'=> $message,
                        'result'=> 'OK',
                        'err_info' => 'ー',
                        'created_at'=> Carbon::now()
                    ]
                );
            }
            catch (ClientException $e) {
                $err = json_decode($e->getResponse()->getBody()->getContents());
                $err_code = $err->status;
                $err_msg = $err->message;
                History::insert(
                    [
                        'store_id' => $store_id,
                        'title'=> $title,
                        'content'=> $message,
                        'result'=> 'NG',                        
                        'err_info'=> '['.$err_code.']' . $err_msg,
                        'created_at'=> Carbon::now(),
                    ]
                );
            }
        }
    }
}
