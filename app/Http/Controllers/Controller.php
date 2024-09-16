<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="News Aggregator API",
 *     version="1.0.0",
 *     description="This is the API documentation for the News Aggregator project."
 * ),
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 */
    /**
     * @OA\Info(
     *    title="API Documentation",
     *    version="1.0.0",
     * )
     *
     * @OA\SecurityScheme(
     *    securityScheme="sanctum",
     *    type="http",
     *    scheme="bearer",
     *    bearerFormat="JWT",
     *    name="Authorization",
     *    in="header"
     * )
     */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
