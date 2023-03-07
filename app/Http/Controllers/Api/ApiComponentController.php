<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Utils\ModuleQueriesService as moduleQuery;
use Illuminate\Http\Request;

class ApiComponentController extends Controller
{
    /**
     * Default modules filter based on name attribute of records from database.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request, $value)
    {
        $response = (object)[
            'data'          => false,
            'status'        => false,
            'message'       => 'The filter got killed before the process start!',
            'detailMessage' => 'If you see this message, then this detail message was form before the filter code exeuted!',
        ];

        $entityName = $request['entityName'];
        $filterBy   = $request['filterBy'];

        if( $value == 'default_queries' ){
            $filtersData = moduleQuery::getAllModelRecords($entityName);
        } else {
            $filtersData = moduleQuery::findModelRecordByScopeLike($entityName, $filterBy, $value);
        }

        if( !$filtersData->data ){
            $response->message       = $filtersData->message;
            $response->detailMessage = $filtersData->detailMessage;

            return json_encode($response);
        }
        $filtersData->status = true;

        $response = $filtersData;

        return json_encode($response);
    }
}
