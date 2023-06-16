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
        $posts = DB::table('histories')
        ->select(
            'send_at',
            'title',
            'content',
            'img_url',
            'status',
            'err_info'
        )
        ->get();
        return view('owner.postHistory', compact('posts'));
    }


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


    
    public function viewLineUsers()
    {
        $store_id = Auth::user()->store_id;

        $lines = DB::table('lines')
        ->select('user_name', 'is_valid','created_at')
        ->where('store_id', $store_id)->get();

        $url_name = DB::table('stores')->find($store_id)->url_name;
        $reg_url = url($url_name) . '/register';

        return view('owner.line_users', compact('lines', 'reg_url'));
    }


    public function send(Request $request)
    {

        $post = $request->only(['title','content']);
        $uri = 'https://notify-api.line.me/api/notify';
        $store_id = Auth::user()->store_id;
        $lines = Line::select('id','token')->where('store_id', $store_id)->get();

        $images = $request->file('imagefile');




        try {
            $uri = 'https://notify-api.line.me/api/notify';
            $line_token = $lines[0]->token;
            $image_path = 'C:\WebApp\work\20230430\work\login.jpg';
            if (!(is_file($image_path)))
            {
                dd('画像アクセス失敗'); 
            }
            $message = 'test message';
            $client = new Client();
            $res = $client->request('POST',$uri,[
                'headers' => [
                    'Authorization' => 'Bearer ' . $line_token ,
                    'Content-Type' => 'multipart/form-data'
                ],
                'multipart' => [
                    [
                        'name' => 'message',
                        'contents' => $message 
                    ],
                    [
                        'name' => 'imageFile',
                        'contents' => fopen($image_path, "rb")
                    ]
                ]
            ]);    
            dd($res);
        }
        catch (ClientException $e) {
            dd($e);
            // Line::where('id', $line->id)->delete();
        }



        // $curl_handle = curl_init();

        //  curl -s -X POST -H "Authorization: Bearer $ACCESS_TOKEN" -F "message=SLが通ります (通算$COUNT 回)" -F "imageThumbnail=https://url-to-sl/$n.jpg" -F "imageFullsize=https://url-to-sl/$n.jpg" https://notify-api.line.me/api/notify >  



        // $save_path = Storage::putFile(config('app.save_storage.image'), $images[0]);
        // $save_name = basename($save_path);


        // $image_path = url(config('app.access_storage.image')).'/'.$save_name;

        // dd(url(config('app.access_storage.image')).'/'.$save_name);

        // $image_path = 'C:\WebApp\work\20230430\work\login.jpg';

        // dd(fopen($image_path, "rb"));
        // dd($image_path);
        // $image_path = 'http://127.0.0.1:8000/storage/message/template/0vz2Mwz7D4kOcrVGXrhQkmaMXnd04tfLKRA4ktGX.jpg';
        // dd(fopen($image_path,'rb'));
        $client = new Client();
            foreach ($lines as $line){
                try {

                    // // リクエストヘッダの作成
                    // $message = '送るよ!!';
                    
                    // $cfile = new \CURLFile($image_path,'image/jpeg','test_name');
                    // $query = array('imageFile'=>$cfile);
                    // // $query = array(
                    // //     'message' => $message, 
                    // //     'imageFile'=>$cfile
                    // // );

                    // // $query = http_build_query([
                    // //     ['message' => $message],
                    // //     ['imageFile' => $cfile]
                    // // ]);

                    // $header = [
                    //     'Content-Type: application/x-www-form-urlencoded',
                    //     'Authorization: Bearer ' . $line->token,
                    //     // 'Content-Length: ' . strlen($query)
                    // ];


                    // $ch = curl_init('https://notify-api.line.me/api/notify');
                    // $options = [
                    //     CURLOPT_RETURNTRANSFER  => true,
                    //     CURLOPT_POST            => true,
                    //     CURLOPT_HTTPHEADER      => $header,
                    //     CURLOPT_POSTFIELDS      => $query
                    // ];

                    // curl_setopt_array($ch, $options);
                    // $res = curl_exec($ch);
                    // curl_close($ch);
                    // dd($res);

                    $message = $post['content'];
                    $image_path = 'C:\WebApp\work\20230430\work\login.jpg';
                    // $image_path = 'http://127.0.0.1:8000/storage/message/template/0vz2Mwz7D4kOcrVGXrhQkmaMXnd04tfLKRA4ktGX.jpg';
                    // $img_b = base64_encode(file_get_contents($image_path));
                    $img_b = fopen($image_path, 'rb');
                    // dd($img_b);
                                        
                    if (!(is_file($image_path)))
                    {
                        dd('画像アクセス失敗');
                    }
                    // dd($img_b);
                    $res = $client->post($uri, 
                        [
                            'headers' => [
                                // 'Content-Type'  => 'application/x-www-form-urlencoded',
                                'Authorization' => 'Bearer ' . $line->token,
                            ],
                            'form_params' => [
                                'message' => PHP_EOL . $message,
                                'imageFile' => $img_b
                            ],
                        ]   
                    );
                    dd($res);

                    // $message = '画像つきテスト送信';
                    // // $image_path = 'C:\WebApp\work\20230430\work\login.jpg';
                    // $image_path = 'C:\WebApp\work\20230430\work\sample\a.png';
                    // // $image_path = 'http://127.0.0.1:8000/storage/message/template/0vz2Mwz7D4kOcrVGXrhQkmaMXnd04tfLKRA4ktGX.jpg';
                    // if (!(is_file($image_path)))
                    // {
                    //     dd('画像アクセス失敗');
                    // }
                    // // dd('★★');
                    // // $img_b = Psr7\Utils::tryFopen($image_path, 'rb');
                    // // $img_b = fopen($image_path, 'rb');
                    // // dd($test);
                    // $res = $client->request('POST',$uri,[
                    //     'headers' => [
                    //         'Authorization' => 'Bearer ' . $line->token,
                    //         'Content-Type' => 'multipart/form-data',
                    //     ],
                    //     'multipart' => [
                    //         [
                    //             'name' => 'message',
                    //             'contents' =>  PHP_EOL . $message
                    //         ]
                    //     ]
                    // ]);


                    // dd($res);

                    // $message = '画像つきテスト送信';
                    // // $image_path = 'C:\WebApp\work\20230430\work\login.jpg';
                    // $image_path = 'C:\WebApp\work\20230430\work\sample\a.png';
                    // // $image_path = 'http://127.0.0.1:8000/storage/message/template/0vz2Mwz7D4kOcrVGXrhQkmaMXnd04tfLKRA4ktGX.jpg';
                    // if (!(is_file($image_path)))
                    // {
                    //     dd('画像アクセス失敗');
                    // }
                    // // $img_b = Psr7\Utils::tryFopen($image_path, 'rb');
                    // $img_b = fopen($image_path, 'rb');
                    // // dd($test);
                    // $res = $client->request('POST',$uri,[
                    //     'headers' => [
                    //         'Authorization' => 'Bearer ' . $line->token,
                    //         'Content-Type' => 'multipart/form-data'
                    //     ],
                    //     'multipart' => [
                    //         [
                    //             'name' => 'message',
                    //             'contents' =>  PHP_EOL . $message
                    //         ],
                    //         [
                    //             // 'name' => 'imageFile',
                    //             'name' => 'files',
                    //             'contents' => ['imageFile' => $img_b]
                    //             // 'contents' => fopen($image_path, "rb")
                    //         ]
                    //     ]
                    // ]);
                    // dd($res);






                    // $res = $client->request('POST',$uri,[
                    //     'headers' => [
                    //         'Authorization' => 'Bearer ' . $line->token,
                    //         'Content-Type' => 'multipart/form-data'
                    //     ],
                    //     'multipart' => [
                    //         [
                    //             'name' => 'message',
                    //             'contents' =>  PHP_EOL . $post['content']
                    //         ],
                    //         [
                    //             'name' => 'imageFile',
                    //             'contents' => Psr7\Utils::tryFopen($image_path, 'rb')
                    //             // 'contents' => fopen($image_path, "rb")
                    //         ]
                    //     ]
                    // ]);


                    // $image_path = 'http://127.0.0.1:8000/storage/message/template/0vz2Mwz7D4kOcrVGXrhQkmaMXnd04tfLKRA4ktGX.jpg';

                    // $res = $client->request('POST',$uri,[
                    //     'headers' => [
                    //         'Authorization' => 'Bearer ' . $line->token,
                    //         'Content-Type' => 'application/x-www-form-urlencoded'
                    //     ],
                    //     'form_params' => [
                    //         [
                    //             'message' => PHP_EOL . $post['content'],
                    //             'imageFile' => 
                    //         ]
                    //     ]
                    // ]);

                    // $res = $client->post($uri, 
                    //     [
                    //         'headers' => [
                    //             // 'Content-Type'  => 'application/x-www-form-urlencoded',
                    //             'Authorization' => 'Bearer ' . $line->token,
                    //         ],
                    //         'form_params' => [
                    //             'message' => PHP_EOL . $post['content'],
                    //             'imageFile' => $image_path
                    //         ]
                    //     ]
                    // );

                    // $aaa = fopen($image_path, 'rb');
                    // dd($image_path);
                    

                    // $res = $client->post($uri, 
                    //     [
                    //         'headers' => [
                    //             // 'Content-Type'  => 'application/x-www-form-urlencoded',
                    //             'Authorization' => 'Bearer ' . $line->token,
                    //         ],
                    //         'form_params' => [
                    //             'message' => PHP_EOL . $post['content'],
                    //             'imageFile' => fopen($image_path, 'rb')
                    //         ]
                    //     ]
                    // );
                    // dd($res);
                }
                catch (ClientException $e) {
                    dd($e);
                    // Line::where('id', $line->id)->delete();
                }
            }



        // $client = new Client();
        //     foreach ($lines as $line){
        //         try {
        //             $res = $client->post($uri, 
        //                 [
        //                     'headers' => [
        //                         'Content-Type'  => 'application/x-www-form-urlencoded',
        //                         'Authorization' => 'Bearer ' . $line->token,
        //                     ],
        //                     'form_params' => [
        //                         'message' => PHP_EOL . $post['content'],
        //                     ]
        //                 ]
        //             );

        //             \Log::debug('正常');
        //             History::insert(
        //                 [
        //                     'store_id' => $store_id,
        //                     'title'=> array_key_exists('title',$post) ? $post['title'] : null,
        //                     'content'=> $post['content'],
        //                     'send_at'=> date("Y/m/d H:i:s"),
        //                 ]
        //             );
        //         }
        //         catch (ClientException $e) {
        //             Line::where('id', $line->id)->delete();
        //         }
        //     }
        return redirect(route('owner.schedule'));
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
