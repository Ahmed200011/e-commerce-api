<?php

namespace App\Http\Controllers\Api\ecommerce;

use App\Events\ContactUsEvent;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\ContactUs;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index()
    {
        // $Categories = Category::all();
        $banner = Banner::all();
        $Products = Product::all();

        return ApiResponse::sendResponse(200, 'all date retrieved', ['banner' => $banner, 'products' => $Products]);
    }
    public function contactUs(Request $request)
    {
        $data = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'text' => 'required'
        ]);
        // dd($data);
        if ($data == false) {
            return ApiResponse::sendResponse(422, 'fail to register, please try again', $data->errors()->all());
        }
        $mail = ContactUs::create([
            'name' => $request->name,
            'email' => $request->email,
            'text' => $request->text,
        ]);
        event(new ContactUsEvent($mail));

        return ApiResponse::sendResponse(200, 'your massage is send', $mail);
    }
}
