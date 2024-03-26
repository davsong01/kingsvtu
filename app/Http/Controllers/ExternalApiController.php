<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ResponseService;
use App\Services\ApiResponseService;

class ExternalApiController extends Controller
{
    public function __construct(private ApiResponseService $apiResponseService, private ResponseService $responseService)
    {
    }

    public function toJson($response)
    {
        if ($response['status'] == "success") {
            return response()->json($response, 200);
        } elseif ($response['status'] == "failed") {
            return response()->json($response, 422);
        } elseif ($response['status'] == "error") {
            return response()->json($response, 422);
        } elseif ($response['status'] == "unknown") {
            return response()->json($response, 500);
        }
    }

    public function getCategories()
    {
        $categories = $this->apiResponseService->getCategories();
        return $this->toJson($categories);
    }

    public function getProductsByCategory($category_slug)
    {
        $products = $this->apiResponseService->getProductsByCategory($category_slug);
        return $this->toJson($products);
    }

    public function getVariationsByProductSlug($product_slug)
    {
        $products = $this->apiResponseService->getVariationsByProductSlug($product_slug);
        return $this->toJson($products);
    }

    

    
}
