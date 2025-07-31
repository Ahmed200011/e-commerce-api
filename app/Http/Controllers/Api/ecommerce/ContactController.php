<?php

namespace App\Http\Controllers\Api\ecommerce;

use App\Events\ContactUsEvent;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;


class ContactController extends Controller
{

    /**
 * @OA\Post(
 *     path="/e_commerce/contact_us",
 *     tags={"E-Commerce - Contact Us"},
 *     summary="Send a contact us message",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email", "message"},
 *             @OA\Property(property="name", type="string", example="Ahmed Mohamed"),
 *             @OA\Property(property="email", type="string", format="email", example="ahmed@example.com"),
 *             @OA\Property(property="subject", type="string", example="Support request"),
 *             @OA\Property(property="message", type="string", example="I have an issue with my order...")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Your message is sent"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */

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
