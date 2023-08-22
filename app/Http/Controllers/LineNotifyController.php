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
use App\Jobs\ActionMessageJob;

class LineNotifyController extends Controller
{
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // LINE連携登録画面表示
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function entry(Request $request)
    {
        $url_name = $request->route('url_name');
        try {
            $store = DB::table('stores')->select(['name', 'url_name', 'client_id'])
            ->where('url_name', $url_name)
            ->whereNull('deleted_at')
            ->first();

            if ($store)
            {
                return view('owner.entry', compact('store'));
                // return view('owner.register', compact('store'));
            }
            else 
            {
                return view('errors.404');
            }
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:LINE連携登録画面表示【URL:'.$request->url().'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
        }
    }

    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // LineNotify登録画面遷移
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function viewLineAuth(Request $request)
    {
        $post = $request->only(['url_name','client_id']);

        $redirect_url = url($post['url_name'] . '/callback');

        try {
            $uri = 'https://notify-bot.line.me/oauth/authorize?' .
                'response_type=code' . '&' .
                'client_id=' . $post['client_id'] . '&' .
                'redirect_uri=' . $redirect_url . '&' .
                'scope=notify' . '&' .
                'state=' . csrf_token() . '&' .
                'response_mode=form_post';
            return redirect($uri);
        }
        catch (\Exception $e) {
            \Log::error('エラー機能: LineNotify登録画面遷移【URL:'.$redirect_url.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
        }
    }

    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // LineNotify CallBack遷移
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function auth_callback(Request $request)
    {
        $url_name = $request->route('url_name');
        $redirect_url = url($url_name . '/callback');

        try {
            $now = Carbon::now();

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
                'created_at' => $now
            ]);

            // LINE連携時あいさつメッセージ

            $greet = DB::table('greetings')
            ->where('greetings.store_id',$store->id)
            ->whereNull('greetings.deleted_at')
            ->join('messages','message_id','=','messages.id')
            ->leftJoin('images','images.message_id','=','messages.id')
            ->select(
                'messages.content as content',
                'images.org_name as org_name',
                'images.save_name as save_name'
                )
            ->first();

            if (!empty($greet)){
                $inputs = array(
                    'store_id'=>$store->id, 
                    'token'=>$access_token,
                    'content'=> $greet->content,
                    'img_path' => $greet->save_name == Null ? '' : \Storage::disk('greeting')->url($greet->save_name)
                );
                ActionMessageJob::dispatch($inputs);         
            }
            return redirect('https://line.me/R/ti/p/@linenotify');
            // return redirect(url($url_name . '/entry'))->with('success_flash_message', 'LINE連携が完了しました。');
        }
        catch (\Exception $e) {
            \Log::error('エラー機能: LineNotify CallBack遷移【URL:'.$redirect_url.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
            return redirect(url($url_name . '/entry'))->with('error_flash_message', 'LINE連携が失敗しました。');
        }
    }
}
