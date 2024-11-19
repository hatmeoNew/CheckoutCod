<?php

namespace NexaMerchant\CheckoutCod\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

class PushController extends Controller
{
    public function send(Request $request)
    {
        // send the push notification to feishu

        $message = $request->input('message');
        if(empty($message)) {
            return response()->json([
                'code' => 400,
                'message' => 'The message is required'
            ]);
        }

        // send the push notification to feishu

        \Nicelizhi\Shopify\Helpers\Utils::sendFeishu($message. " please check the log file for more details");


    }
}
