<?php
namespace NexaMerchant\CheckoutCod\Http\Controllers\Api\V1;

use Illuminate\Http\Request;


class OrdersController extends Controller {

    /**
     * Create a new OrdersController instance.
     *
     * @param Request $request
     * 
     * @access public
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $data = [];
        $data['code'] = 200;
        $data['message'] = "success";
        return response()->json($data);
    }

}