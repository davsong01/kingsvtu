<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\TransactionLog;
use App\Services\ResponseService;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Validator;

class ExternalApiController extends Controller
{
    public function __construct()
    {
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

    public function verifyBiller(Request $request){
        $validator = Validator::make($request->all(), [
            "variation_slug" => "required|string",
            "billersCode" => "required|string",
            "product_slug" => "required|string"
        ]);

        if ($validator->fails()) {
            return $this->responseService->formatServiceResponse("error", "", $validator->errors()->all(), null);
        }

        $verifyBiller = $this->apiResponseService->verifyBiller($request);
       
        return $this->toJson($verifyBiller);
    }

    public function getBalance(){
        $balance = $this->apiResponseService->getBalance(auth()->user());
        return $this->toJson($balance);
    }

    public function makePayment(Request $request){
        $validator = Validator::make($request->all(), [
            'product_slug' => 'nullable|exists:products,slug',
            "variation_slug" => "sometimes|string",
            "billersCode" => "required|string",
            "request_id" => "required|string",
            "phone" => "required|string",
        ]);

        if ($validator->fails()) {
            return $this->toJson($this->responseService->formatServiceResponse("error", "", $validator->errors()->all(), null));
        }

        // Check request id format
      
        if (app('App\Http\Controllers\Controller')->checkRequestIDFormat($request->request_id) == false) {
            $log = "IMPROPER REQUEST ID";
            //get full message
            if (strlen($request->request_id) < 13) {
                $log .= "- DOES NOT CONTAIN DATE";
            } elseif (!is_numeric(substr($request->request_id, 0, 8))) {
                $log .= ": IMPROPER DATE FORMAT – FIRST 8 CHARACTERS MUST BE DATE (TODAY’S DATE – YYYYMMDD)";
            } elseif (substr($request->request_id, 0, 8) != date("Ymd")) {
                $log .= "- NOT TODAY’S DATE – FIRST 8 CHARACTERS MUST BE TODAY’S DATE IN THIS FORMAT: YYYYMMDD";
            } elseif (substr($request->request_id, 8, 2) != date("H")) {
                $log .= "-  INCORRECT TIME – MAKE SURE YOU ARE USING GMT+1 AND YOUR HOUR IS IN 24 HOURLY FORMAT";
            }
            return $this->responseService->formatServiceResponse("failed", '', [$log], null);
        }

        $request_id = $request['request_id'];
        $year = substr($request_id, 0, 4);
        $month = substr($request_id, 4, 2);
        $day = substr($request_id, 6,2);

        $from = $year . "-" . $month . "-" . $day . " 00:00:00";
        $to = $year."-".$month."-".$day." 23:59:59";
        $from = Carbon::parse($from);
        $to = Carbon::parse($to);

        $check = TransactionLog::whereBetween('created_at', [$from, $to])->where('reference_id', $request->request_id)->first();

        if (!empty($check)) {
            return $this->responseService->formatServiceResponse("failed", '', ['DUPLICATE REQUEST ID DETECTED'], null);
        }
    
        return $this->toJson($this->apiResponseService->initializeTransaction($request));
    }

    public function queryTransaction(Request $request){
        $validator = Validator::make($request->all(), [
            "request_id" => "required",
        ]);

        if ($validator->fails()) {
            return $this->toJson($this->responseService->formatServiceResponse("error", "", $validator->errors()->all(), null));
        }
        return $this->toJson($this->apiResponseService->query($request->request_id));
    }
    
}
