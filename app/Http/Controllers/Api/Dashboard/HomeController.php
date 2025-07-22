<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        // $Categories = Category::all();
        $banner=Banner::all();
        $Products=Product::all();

        return ApiResponse::sendResponse(200,'all date retrieved',['banner'=>$banner,'products'=>$Products]);
    }
}
