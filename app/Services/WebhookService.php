<?php
namespace App\Services;

use App\Exports\SingleSheetExport;
use App\Exports\MultipleSheetExport;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Rap2hpoutre\FastExcel\SheetCollection;

define('LIMIT', 50000);

class WebhookService {

    public function analyzeWebhookResponse($request, $provider_id){
        $provider = API::where('id', $provider_id)->first();

        if($provider){

        }
    }
}
