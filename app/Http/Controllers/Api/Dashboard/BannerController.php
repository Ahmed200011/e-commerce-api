<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $panner = Banner::with('product')->get();
        if (!$panner) {

            return ApiResponse::sendResponse(400, 'no banners found', []);
        }
        return ApiResponse::sendResponse(200, 'all banners retrieved', $panner);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'product_id' => 'exists:products,id',

        ]);
        if ($data == false) {
            return ApiResponse::sendResponse(422, 'fail to register, please try again', $data->errors()->all());
        }
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = $request->file('image')->getClientOriginalName();
            // $file = Image::read($file);
            // $resizedImage = $file->scale(height: 300, width: 248);
            $image_name = uniqid() . $name;
            $storing = $file->move(public_path('dashboard/assets/images/banner'), $image_name);
            $image = $storing->getFilename();
        }
        // dd($image);
        if ($image) {
            $banner = Banner::create([
                'image' => $image,
                'product_id' => $request->product_id,

            ]);
            return ApiResponse::sendResponse(200, 'the banner created successfully', $banner);
        } else
            return ApiResponse::sendResponse(400, 'no image found',[]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $data = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5048',
            'product_id' => 'exists:products,id',

        ]);
        if ($data == false) {
            return ApiResponse::sendResponse(422, 'fail to register, please try again', $data->errors()->all());
        }
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = $request->file('image')->getClientOriginalName();
            // $file = Image::read($file);
            // $resizedImage = $file->scale(height: 300, width: 248);
            $image_name = uniqid() . $name;
            $storing = $file->move(public_path('dashboard/assets/images/banner'), $image_name);
            $image = $storing->getFilename();
        }
        $banner->update([
            'image' => $image,
            'product_id' => $request->product_id,

        ]);
        return ApiResponse::sendResponse(200, 'the banner updated successfully', $banner);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            $imagePath = public_path('dashboard/assets/images/banners/' . $banner->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $deleted = $banner->delete();

        if ($deleted) {
            return ApiResponse::sendResponse(200, 'banner deleted successfully', []);
        } else {
            return ApiResponse::sendResponse(500, 'banner to delete user', []);
        }
    }
}
