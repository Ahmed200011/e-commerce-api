<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\BannerResource;
use App\Http\Resources\Dashboard\ProductResource;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\UploadImageTrait;


class BannerController extends Controller
{
    use UploadImageTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banner = Banner::with('product')->get();
        if (!$banner) {

            return ApiResponse::sendResponse(400, 'no banners found', []);
        }
        return ApiResponse::sendResponse(200, 'all banners retrieved', BannerResource::collection($banner));
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
            $image_name = uniqid() . '_' . $file->getClientOriginalName();

            $this->uploadImage($file, 'dashboard/assets/images/banner/', $image_name, 1920, 800);
        } else {
            return ApiResponse::sendResponse(422,  'There is no image uploaded', []);
        }
        // dd($image);

        $banner = Banner::create([
            'image' => $image_name,
            'product_id' => $request->product_id,

        ]);
        return ApiResponse::sendResponse(200, 'the banner created successfully', new BannerResource($banner));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
                $banner = Banner::find($id);

        if (!$banner) {
            return ApiResponse::sendResponse(404, 'No product associated with this banner', []);
        }
        $product = $banner->product;
        return ApiResponse::sendResponse(200, 'product retrieved successfully', new ProductResource($product));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        // dd($request->all());
        $data = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10048',
            'product_id' => 'exists:products,id',

        ]);
        if ($data->fails()) {
            return ApiResponse::sendResponse(422, 'fail to register, please try again', $data->errors()->all());
        }
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image_name = uniqid() . '_' . $file->getClientOriginalName();

            $this->uploadImage($file, 'dashboard/assets/images/banner/', $image_name,  1920, 800);
        }else {
            return ApiResponse::sendResponse(422,  'There is no image uploaded', []);
        }
        if ($banner->image) {
            $this->deleteImage('dashboard/assets/images/banner/', $banner->image);
        }

        $banner->update([
            'image' => $image_name,
            'product_id' => $request->product_id,

        ]);
        return ApiResponse::sendResponse(200, 'the banner updated successfully', new BannerResource($banner));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return ApiResponse::sendResponse(404, 'Banner not found', []);
        }

        if ($banner->image) {
            $this->deleteImage('dashboard/assets/images/banner/', $banner->image);
        }
        $deleted = $banner->delete();

        if ($deleted) {
            return ApiResponse::sendResponse(200, 'banner deleted successfully', []);
        } else {
            return ApiResponse::sendResponse(500, 'Failed to delete the banner. Please try again later.', []);
        }
    }
}
