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
use OpenApi\Annotations as OA;


class BannerController extends Controller
{

    use UploadImageTrait;

    /**
     * Display a listing of the resource.
     */
    /**
 * @OA\Get(
 *     path="/dashboard/banner",
 *     tags={"Dashboard - Banner"},
 *     summary="List all banners with their products",
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="All banners retrieved successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="No banners found"
 *     )
 * )
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

    /**
 * @OA\Post(
 *     path="/dashboard/banner",
 *     tags={"Dashboard - Banner"},
 *     summary="Create new banner",
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"image"},
 *                 @OA\Property(property="image", type="file", format="binary"),
 *                 @OA\Property(property="product_id", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Banner created successfully"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error or no image uploaded"
 *     )
 * )
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

    /**
 * @OA\Get(
 *     path="/dashboard/banner/{id}",
 *     tags={"Dashboard - Banner"},
 *     summary="Get the product associated with a specific banner",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Banner ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product retrieved successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Banner not found"
 *     )
 * )
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

    /**
 * @OA\Put(
 *     path="/dashboard/banner/{id}",
 *     tags={"Dashboard - Banner"},
 *     summary="Update a banner",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Banner ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"image"},
 *                 @OA\Property(property="image", type="file", format="binary"),
 *                 @OA\Property(property="product_id", type="integer", example=2)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Banner updated successfully"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation failed or no image uploaded"
 *     )
 * )
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

    /**
 * @OA\Delete(
 *     path="/dashboard/banner/{id}",
 *     tags={"Dashboard - Banner"},
 *     summary="Delete a banner",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Banner ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Banner deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Banner not found"
 *     )
 * )
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
