<?php
namespace App\Helpers;

class ApiResponse{

    static public function sendResponse($status ,$message ,$data){
        $response=[
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $status);
        }
}
