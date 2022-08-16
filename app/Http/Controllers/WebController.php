<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\License;
use App\Models\Order;
use App\Mail\SendMail;
use App\Models\Category;
use App\Models\UserHasRole;
use Mail;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;
class WebController extends Controller
{
    public function categoryToProduct(){

        $data = Category::with('categoryToProduct')->get();

        return response()->json([
            'success'=>true,
            'data'=>$data,
            'message'=>'Kategorilere göre ürünler.'
        ],200);
    }

}
