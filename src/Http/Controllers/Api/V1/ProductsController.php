<?php

namespace NexaMerchant\CheckoutCod\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param string $slug
     * 
     * @access public
     * @return \Illuminate\Http\Response
     */
    public function details($slug, Request $request)
    {
        $data = [];
        $data['code'] = 200;
        $data['message'] = "success";
        return response()->json($data);
        
    }

    /**
     * Display a listing of the recommended products.
     *
     * @param Request $request
     * @param string $slug
     * 
     * @access public
     * @return \Illuminate\Http\Response
     */
    public function recommens($slug, Request $request)
    {
        $data = [];
        $data['code'] = 200;
        $data['message'] = "success";
        return response()->json($data);
    }
}