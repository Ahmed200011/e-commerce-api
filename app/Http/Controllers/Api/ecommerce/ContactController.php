<?php

namespace App\Http\Controllers\Api\ecommerce;

use App\Events\ContactUsEvent;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function contactUs(Request $request)
    {
        $data = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);
        // dd($data);
        if ($data->fails()) {
            return ApiResponse::sendResponse(422, 'fail to register, please try again', $data->errors()->all());
        }
        $mail = ContactUs::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);
        event(new ContactUsEvent($mail));

        return ApiResponse::sendResponse( 200, 'your massage is send', $mail);
    }
}
