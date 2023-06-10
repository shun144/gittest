<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ClientException;

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

    public function getTemplateOverview()
    {
        // $data = Message::select(
        //     'messages.id',
        //     'messages.title as title',
        //     // 'posts.plan_at as start',
        //     'messages.title_color as backgroundColor',
        //     'messages.title_color as borderColor',
        //     DB::raw("'true' as allDay"),
        //     'messages.content as content',
        // )
        // ->join('posts','posts.message_id','=','messages.id')
        // ->where('messages.store_id', Auth::user()->store_id)
        // ->with(['images' => function ($query) {
        //     $query->select('message_id','images.id as image_id', 'save_name', 'org_name');
        // }])
        // ->get();


        // $data = Message::select(
        //     'messages.id as id',
        //     'messages.title as title',
        //     'posts.plan_at as start',
        //     'messages.title_color as backgroundColor',
        //     'messages.title_color as borderColor',
        //     DB::raw("'true' as allDay"),
        //     'messages.content as content',
        // )->where('messages.store_id', Auth::user()->store_id)
        // ->join('posts','posts.message_id','=','messages.id')
        // ->whereNull('posts.deleted_at')
        // ->whereNotNull('posts.plan_at')
        // ->with(['images' => function ($query) {
        //     $query->select('message_id','images.id as image_id', 'save_name', 'org_name');
        // }])
        // ->get();

        // dd( $data);



        $templates = DB::table('templates')
        ->whereNull('templates.deleted_at')
        ->join('messages','message_id','=','messages.id')
        ->where('messages.store_id', Auth::user()->store_id)
        ->select(
            'messages.id as id',
            'messages.title as title',
            'messages.title_color as title_color')
        ->get();
        return view('owner.schedule', compact('templates'));
    }


    
    public function line_users()
    {
        $lines = Line::select('user_name', 'is_valid')->where('store_id', Auth::user()->store_id)->get();

        return view('owner.line_users', compact('lines'));
    }

    // public function message()
    // {
    //     $messages = DB::table('messages')
    //     ->select('id', 'title', 'title_color')
    //     ->where('store_id', Auth::user()->store_id)
    //     ->where('is_fix', true)
    //     ->whereNull('deleted_at')
    //     ->get();
    //     return view('owner.message', compact('messages'));
    // }




    // public function message()
    // {
    //     $messages = Message::select('id', 'title', 'title_color')
    //     ->where('store_id', Auth::user()->store_id)
    //     ->where('is_fix', true)
    //     ->whereNull('deleted_at')
    //     ->with(['images' => function ($query) {$query->select('message_id', 'id as image_id', 'save_name', 'org_name');}])
    //     ->get();
    //     return view('owner.message', compact('messages'));
    // }






    


    public function send(Request $request)
    {
        dd($request);

        $uri = 'https://notify-api.line.me/api/notify';
        // $tokens = Line::select('token')->where('store_id', Auth::user()->store_id)->get();

        $store_id = Auth::user()->store_id;
        $lines = Line::select('id', 'token')->where('store_id', $store_id)->get();
        $title = $request->title;
        $message = $request->content;

        $client = new Client();
            foreach ($lines as $line){

                try {
                    $res = $client->post($uri, 
                        [
                            'headers' => [
                                'Content-Type'  => 'application/x-www-form-urlencoded',
                                'Authorization' => 'Bearer ' . $line->token,
                            ],
                            'form_params' => [
                                'message' => PHP_EOL . $message,
                            ]
                        ]
                    );

                    \Log::debug('正常');
                    History::insert(
                        [
                            'store_id' => $store_id,
                            'title'=> $title,
                            'content'=> $message,
                            'send_at'=> date("Y/m/d H:i:s"),
                        ]
                    );
                }
                catch (ClientException $e) {
                    Line::where('id', $line->id)->delete();
                }
            }
        return redirect(route('owner.message'));
    }

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
