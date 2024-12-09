<?php

namespace NexaMerchant\CheckoutCod\Docs\V1\CheckoutCod\Controller\Products;

class ProductsController {
    
        /**
        * @OA\Get(
        *      path="/products/details/{slug}",
        *      operationId="getProducts Details",
        *      tags={"Products"},
        *      summary="Get product details",
        *      description="Returns product details",
        *      @OA\Response(
        *          response=200,
        *          description="Successful operation",
        *       ),
        *      @OA\Response(
        *          response=400,
        *          description="Bad request"
        *      ),
        *      @OA\Response(
        *          response=401,
        *          description="Unauthenticated"
        *      ),
        *      @OA\Response(
        *          response=403,
        *          description="Forbidden"
        *      ),
        *      @OA\Response(
        *          response=404,
        *          description="Resource Not Found"
        *      )
        * )
        */
        public function getProductDetail() {
        }
}