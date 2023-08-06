<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Message;
use \GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use \GuzzleHttp\Exception\ClientException;
use App\Models\History;
use App\Models\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class ActionMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $inputs;
    public function __construct(array $inputs)
    {
        $this->inputs = $inputs;
    }

    /**
     * Execute the job.
     */
    // LINE連携時アクションメッセージ配信
    public function handle(): void
    {
        $store_id = $this->inputs['store_id'];
        $token = PHP_EOL . $this->inputs['token'];
        // $token = $this->inputs['token'];
        $message = PHP_EOL . $this->inputs['content'];
        $img_path = '';

        try {
            $API = 'https://notify-api.line.me/api/notify';

            $client = new Client();

            ini_set("max_execution_time",0);

            if ($img_path != '') {
                $res = $client->request('POST', $API, [
                    'headers' => ['Authorization'=> 'Bearer '.$token, ],
                    'http_errors' => false,
                    'multipart' => [
                        [ 
                            'name' => 'message',
                            'contents' => $message
                        ],
                        [ 
                            'name'=> 'imageFile',
                            'contents' => Psr7\Utils::tryFopen($img_path, 'r')
                        ]
                    ]
                ]);
                
                $res_body = json_decode($res->getBody()); 
                if ($res_body->status != 200){  
                    \Log::error('エラー機能:LINE連携時アクションメッセージ配信 【店舗ID:'.$store_id.'】');
                    \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
                    \Log::error('エラー内容:'.'【リターンコード:'.$res_body->status.'】'.$res_body->message);          
                }
            }
            else {
                $res = $client->request('POST', $API, [
                    'headers' => ['Authorization'=> 'Bearer '.$token, ],
                    'http_errors' => false,
                    'multipart' => [
                        [ 
                            'name' => 'message',
                            'contents' => $message
                        ]
                    ]
                ]);

                $res_body = json_decode($res->getBody()); 
                if ($res_body->status != 200){  
                    \Log::error('エラー機能:LINE連携時アクションメッセージ配信 【店舗ID:'.$store_id.'】');
                    \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
                    \Log::error('エラー内容:'.'【リターンコード:'.$res_body->status.'】'.$res_body->message);          
                }
            }      
        } 
        catch (\Exception $e) {
            \Log::error('エラー機能:LINE連携時アクションメッセージ配信 【店舗ID:'.$store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
        }
    }
}
