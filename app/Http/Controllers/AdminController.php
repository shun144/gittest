<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Schedule;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ClientException;
use App\Models\Line;
use App\Models\Store;
use App\Models\Message;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function store()
    {
        $stores = Store::select('stores.*')
        ->get();

        return view('admin.store', compact('stores'));
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
        return redirect(route('admin.store'));
    }

}
