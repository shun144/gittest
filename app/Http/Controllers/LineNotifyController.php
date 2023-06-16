<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use \GuzzleHttp\Client;
use App\Models\Line;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LineNotifyController extends Controller
{
    public function register(Request $request)
    {
        $url_name = $request->route('url_name');

        $store = DB::table('stores')->select(['name', 'url_name', 'client_id'])
        ->where('url_name', $url_name)
        ->first();

        return view('owner.register', compact('store'));
        

        // $store = Store::select('name', 'url_name', 'client_id')->where('url_name', '=', $request->route('url_name'))->first();
        // if ($store === null) {
        //     dd('なし!');
        // }
        // return view('line_notify.register', compact('store'));
    }

    public function viewLineAuth(Request $request)
    {
        $post = $request->only(['url_name','client_id']);        
        $redirect_url = url($post['url_name'] . '/callback');

        $uri = 'https://notify-bot.line.me/oauth/authorize?' .
            'response_type=code' . '&' .
            'client_id=' . $post['client_id'] . '&' .
            'redirect_uri=' . $redirect_url . '&' .
            'scope=notify' . '&' .
            'state=' . csrf_token() . '&' .
            'response_mode=form_post';
        return redirect($uri);
    }


    public function auth_callback(Request $request)
    {
        $url_name = $request->route('url_name');
        $redirect_url = url($url_name . '/callback');
        // $redirect_url = url('notify/callback/' . $url_name);

        $store = Store::where('url_name', '=', $url_name)->first();
        $uri = 'https://notify-bot.line.me/oauth/token';
        $client = new Client();
        $response = $client->post($uri, [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type'    => 'authorization_code',
                'code'          => request('code'),
                'redirect_uri'  => $redirect_url,
                'client_id'     => $store->client_id,
                'client_secret' => $store->client_secret
            ]
        ]);
        $access_token = json_decode($response->getBody())->access_token;


        $get_status_url = 'https://notify-api.line.me/api/status';
        $status_client = new Client();
        $status_res = $status_client->get($get_status_url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization'    => 'Bearer ' . $access_token,
            ]
        ]);
        $status_json = json_decode($status_res->getBody());
        
        Line::insert([
            'user_name' => $status_json->target,
            'token' => $access_token,
            'is_valid' => ($status_json->status == 200) ? true : false,
            'store_id' => $store->id,
            'created_at' => Carbon::now()
        ]);
        return redirect(url($url_name . '/register'))->with('flash_message', 'LINE連携が完了しました。');
        // return redirect(url($url_name . '/register'));

        // \Session::set('access_token', $access_token);
        // return redirect(url('notify/register/' . $url_name));



        // $response = $client->post($uri, [
        //     'headers' => [
        //         'Content-Type' => 'application/x-www-form-urlencoded',
        //     ],
        //     'form_params' => [
        //         'grant_type'    => 'authorization_code',
        //         'code'          => request('code'),
        //         'redirect_uri'  => config('services.line_notify.redirect_uri'),
        //         'client_id'     => config('services.line_notify.client_id'),
        //         'client_secret' => config('services.line_notify.secret')
        //     ]
        // ]);
        // // この環境DBとか入れてないんでとりあえずセッションに入れときます
        // $access_token = json_decode($response->getBody())->access_token;
        // Line::insert(['token' => $access_token, 'store_id' => 1]);
        // // \Session::set('access_token', $access_token);
        // return redirect('/notify');

    }

    public function send(Request $request)
    {
        $uri = 'https://notify-api.line.me/api/notify';
        $tokens = Line::where('store_id', 1)->get();
        $client = new Client();
        $client->post($uri, [
            'headers' => [
                'Content-Type'  => 'application/x-www-form-urlencoded',
                'Authorization' => 'Bearer ' . $tokens[0]->token,
            ],
            'form_params' => [
                'message' => $request->message,
                'stickerPackageId' => 1,
                'stickerId' => 1
            ]
        ]);
        return redirect('/notify');
    }

    // public function send()
    // {
    //     $uri = 'https://notify-api.line.me/api/notify';
    //     $client = new Client();
    //     $client->post($uri, [
    //         'headers' => [
    //             'Content-Type'  => 'application/x-www-form-urlencoded',
    //             'Authorization' => 'Bearer ' . session('access_token'),
    //         ],
    //         'form_params' => [
    //             'message' => request('message', 'Hello World!!')
    //         ]
    //     ]);
    //     return redirect('/notify');
    // }

    public function sendImage(Request $request)
    {
        $uri = 'https://notify-api.line.me/api/notify';
        $tokens = Line::where('store_id', 1)->get();
        $client = new Client();


        // $file = $request->file('image');
        $file = storage_path() . '\app\sample\login.jpg';

        // ob_start();
        // imagepng($file, null, 9); // png画像をminify
        // $image_binary = ob_get_clean();
        $image = fopen($file,'rb');
        $img = fread($image);
        fclose($image);
        dd($img);

        // dd($file);
        // dd($file->openFile()->getRealPath());

        // $client->post($uri, [
        //     'headers' => [
        //         'Content-Type'  => 'multipart/form-data',
        //         'Authorization' => 'Bearer ' . $tokens[0]->token,
        //     ],
        //     'multipart' => [
        //         [
        //             'name' => 'message',
        //             'contents' => request('message', '画像テスト'),
        //         ],
        //         [
        //             'name' => 'imageFile',
        //             'contents' => $file
        //         ],
        //         [
        //             'name' => 'notificationDisabled',
        //             'contents' => false
        //         ]
        //     ]
        // ]);

        // $client->post($uri, [
        //     'headers' => [
        //         'Content-Type'  => 'multipart/form-data',
        //         'Authorization' => 'Bearer ' . $tokens[0]->token,
        //     ],
        //     'form_params' => [
        //         'message' => request('message', 'Hello World!!'),
        //         'imageFile' => $file,
        //         'notificationDisabled' => false
        //     ]
        // ]);


        $client->post($uri, [
            'headers' => [
                'Content-Type'  => 'application/x-www-form-urlencoded',
                'Authorization' => 'Bearer ' . $tokens[0]->token,
            ],
            'form_params' => [
                'message' => request('message', 'Hello World!!'),
                'imageFile' => fopen($file,'rb'),
                'notificationDisabled' => false
            ]
        ]);
        return redirect('/notify');

        // ディレクトリ名
        // $dir = 'sample';
        // dd($request -> image);

        // アップロードされたファイル名を取得
        // $file_name = $request->file('image')->getClientOriginalName();
        // $request->file('image')->storeAs('sample', $file_name);
        // dd($request->all());
        // dd($request->file('image'));
        // 取得したファイル名で保存
        // $request->file('image')->storeAs('public/' . $dir, $file_name);
        
        return redirect('/notify'); 
    }



    public function broadcastSend()
    {
        // $uri = 'https://notify-api.line.me/api/status';
        // $tokens = Line::where('store_id', 1)->get();

        // $client = new Client();
        // $response = $client->get($uri,[
        //     'headers' => [
        //         'Content-Type'  => 'application/x-www-form-urlencoded',
        //         'Authorization' => 'Bearer ' . $tokens[0]->token,
        //     ]
        // ]);

        // // $header = $response->getHeaderLine('X-RateLimit-ImageRemaining');
        // $list = json_decode($response->getBody()->getContents());
        // dd($list);

        // return redirect('/notify'); 

        $uri = 'https://notify-api.line.me/api/notify';
        $tokens = Line::where('store_id', 1)->get();
        foreach ($tokens as $token)
        {                       
            $client = new Client();
            $client->post($uri, [
                'headers' => [
                    'Content-Type'  => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Bearer ' . $token->token,
                ],
                'form_params' => [
                    'message' => 'よっしゃ!!'
                ]
            ]);
        }         
        return redirect('/notify'); 

    }
}
