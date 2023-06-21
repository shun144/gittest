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

use App\Models\User;
use App\Models\Schedule;
use App\Models\Line;
use App\Models\Store;
use App\Models\Message;
use App\Models\Image;
use App\Models\History;
use App\Models\Template;


class OwnerController extends Controller
{
    public function viewPostHistory()
    {
        try {
            \Log::info('UserID:'. Auth::user()->id .' 配信履歴表示 開始');
            $store_id = Auth::user()->store_id;
            $posts = DB::table('histories')
            ->where('store_id', $store_id )
            ->select(
                'id',
                'start_at',
                'end_at',
                'title',
                'content',
                'img_url',
                'status',
                'err_info'
            )
            ->get();
            \Log::info('UserID:'. Auth::user()->id .' 配信履歴表示 終了');
            return view('owner.postHistory', compact('posts'));
        }
        catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

    }

    public function viewPostHistoryInfo(Request $request)
    {
        $history_id = $request->query('history_id');

        $posts = DB::table('histories')
        ->where('id',$history_id)
        ->select(
            'start_at',
            'end_at',
            'title',
            'content',
            'img_url',
            'status',
            'err_info'
        )
        ->first();
        return view('owner.postHistory_info', compact('posts'));
    }



    public function getTemplateOverview()
    {
        $templates = DB::table('templates')
        ->whereNull('templates.deleted_at')
        ->join('messages','message_id','=','messages.id')
        ->where('messages.store_id', Auth::user()->store_id)
        ->select(
            'messages.id as id',
            'messages.title as title',
            'messages.title_color as title_color')
        ->latest('templates.created_at')
        ->take(20)
        ->get();
        return view('owner.schedule', compact('templates'));
    }


    
    public function viewLineUsers()
    {
        try {
            \Log::info('UserID:'. Auth::user()->id .' LINEユーザ一覧表示 開始');
            $store_id = Auth::user()->store_id;
            $lines = DB::table('lines')
            ->select('id','user_name', 'is_valid','created_at')
            ->where('store_id', $store_id)->get();
            $url_name = DB::table('stores')->find($store_id)->url_name;
            $reg_url = url($url_name) . '/register';
            \Log::info('UserID:'. Auth::user()->id .' LINEユーザ一覧表示 終了');
            return view('owner.line_users', compact('lines', 'reg_url'));
        }
        catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

    }

    public function updateLineUser(Request $request)
    {
        try {
            \Log::info('UserID:'. Auth::user()->id .' LINEユーザ更新 開始');

            $post = $request->only(['line_user_id','new_valid']);
            DB::table('lines')
            ->where('id',$post['line_user_id'])
            ->update(['is_valid' => $post['new_valid']]
            );
            \Log::info('UserID:'. Auth::user()->id .' LINEユーザ更新 終了');
            return redirect(route('owner.line_users'));
        }
        catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }






    // public function send(Request $request)
    // {
    //     $uri = 'https://notify-api.line.me/api/notify';

    //     $store_id = Auth::user()->store_id;
    //     $lines = Line::select('id','token')->where('store_id', $store_id)->get();
    //     $title = $request->title;
    //     $message = $request->content;

    //     $client = new Client();
    //         foreach ($lines as $line){
    //             try {
    //                 $res = $client->post($uri, 
    //                     [
    //                         'headers' => [
    //                             'Content-Type'  => 'application/x-www-form-urlencoded',
    //                             'Authorization' => 'Bearer ' . $line->token,
    //                         ],
    //                         'form_params' => [
    //                             'message' => PHP_EOL . $message,
    //                         ]
    //                     ]
    //                 );

    //                 \Log::debug('正常');
    //                 History::insert(
    //                     [
    //                         'store_id' => $store_id,
    //                         'title'=> $title,
    //                         'content'=> $message,
    //                         'send_at'=> date("Y/m/d H:i:s"),
    //                     ]
    //                 );
    //             }
    //             catch (ClientException $e) {
    //                 Line::where('id', $line->id)->delete();
    //             }
    //         }
    //     return redirect(route('owner.message'));
    // }

    // public function send(Request $request)
    // {
    //     $uri = 'https://notify-api.line.me/api/notify';
    //     // $tokens = Line::select('token')->where('store_id', Auth::user()->store_id)->get();

    //     $lines = Line::select('id', 'token')->where('store_id', Auth::user()->store_id)->get();
    //     $message = $request->content;

    //     $client = new Client();

    //     foreach ($lines as $line){
            
    //         $res = $client->post($uri, [
    //             'headers' => [
    //                 'Content-Type'  => 'application/x-www-form-urlencoded',
    //                 'Authorization' => 'Bearer ' . $line->token,
    //             ],
    //             'form_params' => [
    //                 'message' => PHP_EOL . $message,
    //             ]
    //         ]);

    //         dd($res);
    
    //     }
    //     return redirect(route('owner.message'));
    // }

    // public function send(Request $request)
    // {
    //     $uri = 'https://notify-api.line.me/api/notify';
    //     $tokens = Line::where('store_id', 1)->get();
    //     $client = new Client();
    //     $client->post($uri, [
    //         'headers' => [
    //             'Content-Type'  => 'application/x-www-form-urlencoded',
    //             'Authorization' => 'Bearer ' . $tokens[0]->token,
    //         ],
    //         'form_params' => [
    //             'message' => PHP_EOL . $request->message_content,
    //         ]
    //     ]);
    //     return redirect(route('owner.message'));
    // }


    public function store()
    {
        $stores = Store::select('stores.*')
        ->get();

        return view('owner.store', compact('stores'));
    }

    public function add_store(Request $request)
    {
        $post = $request->all();
        Store::insert(
            [
                'name'=> $post['store_name'],
                'addr'=> $post['store_addr'],
                'tel'=> $post['store_tel'],
                'client_id'=> $post['client_id'],
                'client_secret'=> $post['client_secret'],
            ]
        );
        // return view('owner.store');
        return redirect(route('owner.store'));
    }

    public function member()
    {
        $members = User::select('users.*','stores.name AS store_name')
        ->join('stores', 'store_id', '=', 'stores.id')
        ->get();

        return view('owner.member', compact('members'));
    }


    // public function profile()
    // {
    //     // $store = User::select('users.*', 'stores.name AS store_name')
    //     //     ->leftjoin('stores', 'store_id', '=', 'stores.id')
    //     //     ->where('users.id', '=', \Auth::id())
    //     //     ->get();

    //     // $store = User::select()
    //     // ->where('users.id', '=', \Auth::id())
    //     // ->get();

    //     $profile = User::select('users.*','stores.name AS store_name')
    //     ->join('stores', 'store_id', '=', 'stores.id')
    //     ->where('users.id', '=', \Auth::id())
    //     ->get();

    //     // dd(\Auth::id());

    //     return view('owner.profile', compact('profile'));
    // }
}
