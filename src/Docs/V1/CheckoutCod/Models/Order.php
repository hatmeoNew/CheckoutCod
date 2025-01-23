<?php
namespace NexaMerchant\CheckoutCod\Docs\V1\CheckoutCod\Models;

/**
 * @OA\Schema(
 *     title="Order",
 *     description="Order model",
 * )
 */
class Order {

    /**
     * @OA\Property(
     *     title="Cart ID",
     *     description="Cart ID",
     *     format="int64",
     *     example=1
     * )
     *
     * @var int
     */
    private $cart_id;

    /**
     * @OA\Property(
     *     title="Customer ID",
     *     description="Customer ID",
     *     format="int64",
     *     example=1
     * )
     *
     * @var int
     */
    private $customer_id;

    /**
     * @OA\Property(
     *     title="Return Insurance",
     *     description="Return Insurance",
     *     format="int64",
     *     example=1
     * )
     *
     * @var int
     */
    private $return_insurance;
}