<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="E-commerce API Documentation",
 *     version="1.0.0",
 *     description="Swagger Docs for Dashboard APIs (Admin Panel)",
 *     @OA\Contact(
 *         email="admin@example.com",
 *         name="API Support"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Local server"
 * )
 */
class SwaggerController extends Controller
{
    //
}
