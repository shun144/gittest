<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use \GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use \GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Schedule;
use App\Models\Line;
use App\Models\Store;
use App\Models\Message;
use App\Models\Image;
use App\Models\History;
use App\Models\Template;
use GuzzleHttp\Pool;
use Mavinoo\Batch\BatchFacade as Batch;

class OwnerController extends Controller
{
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 配信履歴一覧表示
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function viewPostHistory()
    {
        try {
            $store_id = Auth::user()->store_id;
            $posts = DB::table('histories')
            ->where('store_id', $store_id )
            ->select(
                'id',
                'start_at',
                // 'end_at',
                'title',
                'content',
                'img_url',
                'status',
                'err_info'
            )
            ->latest('created_at')
            ->get();

            return view('owner.postHistory', compact('posts'));
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:配信履歴表示 【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
            
            return view('owner.postHistory');
        }
    }


    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 配信履歴詳細表示
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function viewPostHistoryInfo(Request $request)
    {
        try {

            $history_id = $request->query('history_id');
            $post = DB::table('histories')
            ->where('id',$history_id)
            ->select(
                'start_at',
                // 'end_at',
                'title',
                'content',
                'img_url',
                'status',
                'err_info'
            )
            ->first();
            return view('owner.postHistory_info', compact('post'));
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:配信履歴詳細表示 【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
            return view('owner.postHistory_info');
        }
    }


    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 配信スケジュール表示
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function viewSchedule()
    {
        try {
            $templates = DB::table('templates')
            ->whereNull('templates.deleted_at')
            ->join('messages','message_id','=','messages.id')
            ->where('messages.store_id', Auth::user()->store_id)
            ->select(
                'messages.id as id',
                'messages.title as title',
                'messages.title_color as title_color')
            ->latest('templates.updated_at')
            ->get();
            return view('owner.schedule', compact('templates'));
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:配信スケジュール表示 【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());

            $get_template_error_flushMsg = '定型メッセージ取得に失敗しました';
            return view('owner.schedule', compact('get_template_error_flushMsg'));
        }
    }


    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 連携LINEユーザ一覧表示
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function viewLineUsers()
    {
        try {

            $store_id = Auth::user()->store_id;
            $lines = DB::table('lines')
            ->select('id','user_name', 'is_valid','created_at')
            ->whereNull('deleted_at')
            ->where('store_id', $store_id)
            ->orderBy('created_at')
            ->get();
            $url_name = DB::table('stores')->find($store_id)->url_name;

            $valid_count = DB::table('lines')
            ->whereNull('deleted_at')
            ->where('store_id', $store_id)
            ->where('is_valid', true)
            ->count();

            $reg_url = url($url_name) . '/entry';
            return view('owner.line_users', compact('lines', 'reg_url', 'valid_count'));
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:連携LINEユーザ一覧表示 【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());

            $get_lineuser_error_flushMsg = '連携LINEユーザ取得に失敗しました';
            return view('owner.line_users', compact('get_lineuser_error_flushMsg'));
        }
    }


    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 連携LINEユーザ更新
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function updateLineUser(Request $request)
    {
        $post = $request->only(['line_user_id','new_valid']);

        try {
            $now = Carbon::now();
            DB::table('lines')
            ->where('id',$post['line_user_id'])
            ->update([
                'is_valid' => $post['new_valid'],
                'updated_at' => $now
                ]
            );
            return redirect(route('owner.line_users'));
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:連携LINEユーザ更新 【店舗ID:'.Auth::user()->store_id.'/LINEユーザID:'.$post['line_user_id'].'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());

            return redirect(route('owner.line_users'))->with('edit_lineuser_error_flushMsg','連携LINEユーザ更新に失敗しました');
        }
    }

    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 退会済み友達更新
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function updateStatusLineUser(Request $request)
    {
        try {
            
            // タイムアウトしない(ini_set()は関数が実行されたスクリプト内「だけ」その設定を有効にする関数)
            ini_set("max_execution_time",0);

            $now = Carbon::now();
            $API = 'https://notify-api.line.me/api/status';
            $client = new Client();

            $store_id = Auth::user()->store_id;

            // 有効なLINEユーザのみ取得
            $lines = DB::table('lines')
            ->select('id','token')
            ->whereNull('deleted_at')
            ->where('store_id', $store_id)
            ->where('is_valid', 1)
            ->get();

            // 有効なLINEユーザが0件のため処理終了
            if ($lines->count() == 0){
                return redirect(route('owner.line_users'))->with('edit_lineuser_success_flushMsg','有効なLINEユーザが0件のため更新せず終了しました');
            }


            // 非同期リクエストパラメータリスト作成
            $requests_param = [];

            // lineテーブルのIDをキーにしてリクエストパラメータリストを作成
            foreach($lines as $line){
                array_push($requests_param,
                [
                    'key' => $line->id,
                    'params' =>  [
                        'headers' => ['Authorization'=> 'Bearer '.$line->token, ],
                        'http_errors' => false
                    ]
                ]);
            }

            // リクエストパラメータを元にリクエスト変数を作成
            // yieldにすることでメモリリークを防ぐ
            $requests = function ($requests_param) use ($client, $API) {
                foreach ($requests_param as $param) {
                    yield function() use ($client, $API, $param) {
                        return $client->requestAsync('GET', $API, $param['params']);
                    };
                }
            };

            $contents = [];
            $pool = new Pool($client, $requests($requests_param), [
                // 最大同時実行数
                'concurrency' => 50,

                // レスポンスが正常だった場合（401などAPIの結果がエラーの場合でもこちらを通る）
                'fulfilled' => function ($response, $index) use ($requests_param, &$contents) 
                {
                    $contents[$requests_param[$index]['key']] = [
                        'line_id'          => $requests_param[$index]['key'],
                        'html'             => $response->getBody()->getContents(),
                        'status_code'      => $response->getStatusCode(),
                        'response_header'  => $response->getHeaders()
                    ];
                },
                // レスポンスが異常だった場合
                'rejected' => function ($reason, $index) use ($requests_param, &$contents) 
                {
                    $contents[$requests_param[$index]['key']] = [
                        'line_id'=> $requests_param[$index]['key'],
                        'html'   => '',
                        'reason' => $reason
                    ];
                },
            ]);
            $promise = $pool->promise();

            // 全ての並列処理が終わるまで待機
            $promise->wait();


            $upd_user_list = array();
            foreach($contents as $content){
                // 退会済み(接続が切れている)ユーザのみ、状態を無効に更新する
                if ($content['status_code'] != 200){
                    array_push($upd_user_list, ['id' =>$content['line_id'], 'is_valid' => false, 'updated_at' => $now]);
                }
            }

            $lineInstance = new Line;
            $index = 'id';

            // 100件ずつBalk Update
            foreach (array_chunk($upd_user_list, 100) as $chunk) {
                Batch::update($lineInstance, $chunk, $index);
            }
            
            // foreach($upd_user_list as $upd_user){
            //     DB::table('lines')
            //     ->where('id',$upd_user['id'])
            //     ->update([
            //         'is_valid' => $upd_user['is_valid'],
            //         'updated_at' => $upd_user['updated_at']
            //         ]
            //     );          
            // }
            return redirect(route('owner.line_users'))->with('edit_lineuser_success_flushMsg','退会済み友達更新が完了しました');
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:退会済み友達更新 【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
            return redirect(route('owner.line_users'))->with('edit_lineuser_error_flushMsg','退会済み友達更新に失敗しました');
        }
    }

    
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // あいさつメッセージ画面表示
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function viewGreeting()
    {
        try {
            $user = Auth::user();
            $post = DB::table('greetings')
            ->where('greetings.store_id',$user->store_id)
            ->join('messages','message_id','=','messages.id')
            ->leftJoin('images','images.message_id','=','messages.id')
            ->select(
                'messages.content as content',
                'images.org_name as org_name',
                'images.save_name as save_name'
                )
            ->first();

            // 画像URLプロパティ追加
            if (!empty($post)){
                if ($post->save_name != Null){
                    $post->img_url = Storage::disk('greeting')->url($post->save_name);
                    $post->has_file = '1';
                }
                else {
                    $post->img_url = Null;
                    $post->has_file = '0';
                }
            }
            return view('owner.greeting', compact('post'));
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:あいさつメッセージ画面表示 【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
        }
    }

    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // LINE連携時あいさつメッセージ更新
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function updateLinkGreeting(Request $request)
    {
        try {

            $user = Auth::user();
            $post = $request->only(['content','has_file']);
            // $content = empty($post['content']) ? '' :  $post['content'];
            $images = $request->file('imagefile');
            $now = Carbon::now();
        
            $greet = DB::table('greetings')
            ->where('store_id',$user->store_id)
            ->first();

            if (empty($greet)) {
                // 新規作成
                DB::transaction(function () use($user, $post, $images, $now){
                    $msg_id = Message::insertGetId(
                        [
                            'user_id' => $user->id,
                            'store_id' => $user->store_id,
                            'title'=> 'LINE連携時あいさつメッセージ',
                            'title_color' => '#E60012',
                            'content'=> $post['content'],
                            'created_at'=> $now,
                            'updated_at'=> $now
                        ]
                    );

                    DB::table('greetings')->insert(
                        [
                            'category'=> 'LINK',
                            'store_id'=> $user->store_id,
                            'message_id' => $msg_id,
                            'created_at'=>$now,
                            'updated_at'=>$now,
                    ]);

                    if($images)
                    {
                        $img = $images[0];
                        $save_name = Storage::disk('greeting')->put('', $img);
                        $org_name = $img->getClientOriginalName();                         
                        Image::insert([
                            'message_id' => $msg_id, 
                            'save_name' => $save_name, 
                            'org_name' => $org_name,
                            'created_at'=> $now,
                        ]);
                    }
                });
            }
            else {

                // 更新
                DB::transaction(function() use($greet, $user, $post,  $images, $now){
                    DB::table('messages')
                    ->where('id', $greet->message_id)
                    ->update([
                        'content' => $post['content'],
                        'updated_at' => $now
                    ]);
    
                    DB::table('greetings')
                    ->where('id', $greet->id)
                    ->update([
                        'updated_at' => $now
                    ]);
    
                    $dt_images = DB::table('images')->where('message_id', $greet->message_id);

                    // ファイル保持フラグあり
                    if ($post['has_file'] == '1'){
                        
                        if ($images) {
                            if ($dt_images->count())
                            {
                                $old_file = Storage::disk('greeting')->path($dt_images->first()->save_name);
                                $new_file = Storage::disk('garbage')->path($dt_images->first()->save_name);
                                \File::move($old_file, $new_file);
                                $dt_images->delete();
                            }
    
                            foreach ($images as $img){
                                $save_name = Storage::disk('greeting')->put('', $img);
                                $org_name = $img->getClientOriginalName();             
    
                                DB::table('images')->insert([
                                    'message_id' => $greet->message_id,
                                    'save_name' => $save_name,
                                    'org_name' => $org_name,
                                    'created_at' => $now
                                ]);
                            }
                        }
                    // ファイル保持フラグなし
                    } else {
                        if ($dt_images->count())
                        {
                            $old_file = Storage::disk('greeting')->path($dt_images->first()->save_name);
                            $new_file = Storage::disk('garbage')->path($dt_images->first()->save_name);
                            \File::move($old_file, $new_file);
                            $dt_images->delete();
                        }
                    }
                });
            }
            return response()->json(['status' => 'OK'], 200);
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:LINE連携時あいさつメッセージ更新【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
            $data = ['error' => $e->getMessage()];
            return response()->json($data, 500);
        }
    }
}
