<?php
namespace NexaMerchant\CheckoutCod\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Webkul\Checkout\Facades\Cart;
use Webkul\Checkout\Repositories\CartRepository;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Product\Repositories\ProductAttributeValueRepository;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\CartRule\Repositories\CartRuleCouponRepository;
use Webkul\CartRule\Repositories\CartRuleRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Shop\Http\Resources\CartResource;
use Illuminate\Support\Facades\Artisan;
use Nicelizhi\Shopify\Console\Commands\Order\Post;
use Nicelizhi\Shopify\Console\Commands\Order\PostOdoo;



class OrdersController extends Controller {


    public function __construct(
        protected CartRepository $cartRepository,
        protected CategoryRepository $categoryRepository,
        protected ProductRepository $productRepository,
        protected ProductAttributeValueRepository $productAttributeValueRepository,
        protected AttributeRepository $attributeRepository,
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository,
        protected CartRuleCouponRepository $cartRuleCouponRepository,
        protected CartRuleRepository $cartRuleRepository
    )
    {

    }
    /**
     * Create a new OrdersController instance.
     *
     * @param Request $request
     *
     * @access public
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $payment_method = $request->input('payment_method');
        $payment_method_input = $request->input('payment_method');
        $input = $request->all();
        $refer = isset($input['refer']) ? trim($input['refer']) : "";

        $ip_country = $request->server('HTTP_CF_IPCOUNTRY'); // add ip country to order

        $cart_id = $request->input('cart_id'); // when the cart_id is not empty, then use the cart_id to get the cart

        if(!empty($cart_id)) {
            $cart = $this->cartRepository->findOrFail($cart_id);
            Cart::setCart($cart);
        }else{
            $products = $request->input("products");
            //
            Cart::deActivateCart();
            foreach($products as $key=>$product) {
                //var_dump($product);
                $product['quantity'] = $product['amount'];
                $product['selected_configurable_option'] = $product['variant_id'];
                if(!empty($product['attr_id'])) {
                    $attr_ids = explode(',', $product['attr_id']);
                    foreach($attr_ids as $key=>$attr_id) {
                        $attr = explode('_', $attr_id);
                        $super_attribute[$attr[0]] = $attr[1];
                    }

                    $product['super_attribute'] = $super_attribute;
                }
                $product['variant_data'] = $this->productRepository->findOneWhere([
                    'id' => $product['variant_id'],
                ], ['sku', 'custom_sku']);
                //Log::info("add product into cart ". json_encode($product));
                $cart = Cart::addProduct($product['product_id'], $product);

                if (
                    is_array($cart)
                    && isset($cart['warning'])
                ) {
                    return new JsonResource([
                        'message' => $cart['warning'],
                    ]);
                }

            }
        }

        $this->returnInsurance($input, $cart);


        //
        $addressData = [];


        $addressData['billing'] = [];
        $address1 = [];
        array_push($address1, $input['address']);
        $addressData['billing']['city'] = $input['city'];
        $addressData['billing']['country'] = $input['country'];
        $addressData['billing']['email'] = $input['email'];
        $addressData['billing']['first_name'] = $input['first_name'];
        $addressData['billing']['last_name'] = $input['second_name'];
        $input['phone_full'] = str_replace('undefined+','', $input['phone_full']);
        $addressData['billing']['phone'] = $input['phone_full'];
        $addressData['billing']['postcode'] = $input['code'];
        $addressData['billing']['state'] = $input['province'];
        $addressData['billing']['use_for_shipping'] = true;
        $addressData['billing']['address1'] = $address1;

        $addressData['billing']['address1'] = implode(PHP_EOL, $addressData['billing']['address1']);

        $shipping = [];
        $address1 = [];
        array_push($address1, $input['address']);
        $shipping['city'] = $input['city'];
        $shipping['country'] = $input['country'];
        $shipping['email'] = $input['email'];
        $shipping['first_name'] = $input['first_name'];
        $shipping['last_name'] = $input['second_name'];
        //undefined+
        $input['phone_full'] = str_replace('undefined+','', $input['phone_full']);
        $shipping['phone'] = $input['phone_full'];
        $shipping['postcode'] = $input['code'];
        $shipping['state'] = $input['province'];
        $shipping['use_for_shipping'] = true;
        $shipping['address1'] = $address1;
        $shipping['address1'] = implode(PHP_EOL, $shipping['address1']);


        $addressData['shipping'] = $shipping;
        $addressData['shipping']['isSaved'] = false;
        $address1 = [];
        array_push($address1, $input['address']);
        $addressData['shipping']['address1'] = $address1;
        $addressData['shipping']['address1'] = implode(PHP_EOL, $addressData['shipping']['address1']);

        // customer bill address info
        if(@$input['shipping_address']=="other") {
            $address1 = [];
            array_push($address1, $input['bill_address']);
            $billing = [];
            $billing['city'] = $input['bill_city'];
            $billing['country'] = $input['bill_country'];
            $billing['email'] = $input['email'];
            $billing['first_name'] = $input['bill_first_name'];
            $billing['last_name'] = $input['bill_second_name'];
            //undefined+
            $input['phone_full'] = str_replace('undefined+','', $input['phone_full']);
            $billing['phone'] = $input['phone_full'];
            $billing['postcode'] = $input['bill_code'];
            $billing['state'] = $input['bill_province'];
            //$billing['use_for_shipping'] = true;
            $billing['address1'] = $address1;
            $billing['address1'] = implode(PHP_EOL, $billing['address1']);

            $addressData['billing'] = $billing;
        }


        Log::info("address" . json_encode($addressData));

        if (
            Cart::hasError()
            || ! Cart::saveCustomerAddress($addressData)
        ) {
            return new JsonResource([
                'redirect' => false,
                'data'     => Cart::getCart(),
            ]);
        }



        //
        $shippingMethod = "free_free"; // free shipping
        // $shippingMethod = "flatrate_flatrate";
        // $shippingMethod = "cod_flatrate";

        if (
            Cart::hasError()
            || ! $shippingMethod
            || ! Cart::saveShippingMethod($shippingMethod)
        ) {
            return response()->json([
                'redirect_url' => route('shop.checkout.cart.index'),
            ], Response::HTTP_FORBIDDEN);
        }

        Cart::collectTotals();


        $couponCode = $input['coupon_code'];
        try {
            if (strlen($couponCode)) {
                $coupon = $this->cartRuleCouponRepository->findOneByField('code', $couponCode);

                if (! $coupon) {
                    return (new JsonResource([
                        'data'     => new CartResource(Cart::getCart()),
                        'message'  => trans('Coupon not found.'),
                    ]))->response()->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                if ($coupon->cart_rule->status) {
                    if (Cart::getCart()->coupon_code == $couponCode) {
                        return (new JsonResource([
                            'data'     => new CartResource(Cart::getCart()),
                            'message'  => trans('shop::app.checkout.cart.coupon-already-applied'),
                        ]))->response()->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                    }

                    Cart::setCouponCode($couponCode);
                    //$this->validateOrder();
                    Cart::collectTotals();
                }
            }
        } catch (\Exception $e) {
            return (new JsonResource([
                'data'    => new CartResource(Cart::getCart()),
                'message' => trans('shop::app.checkout.cart.coupon.error'),
            ]))->response()->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // when enable the upselling and can config the upselling rule for carts

            //
        $payment = [];
        $payment['description'] = $payment_method."-".$refer.'-'.$ip_country;
        $payment['method'] = $payment_method;
        $payment['method_title'] = $payment_method."-".$refer."-".$ip_country;
        $payment['sort'] = "2";
        // Cart::savePaymentMethod($payment);

        if (
            Cart::hasError()
            || ! $payment
            || ! Cart::savePaymentMethod($payment)
        ) {
            return response()->json([
                'redirect_url' => route('shop.checkout.cart.index'),
            ], Response::HTTP_FORBIDDEN);
        }


        Cart::collectTotals();
        $this->validateOrder();
        $cart = Cart::getCart();


        $order = $this->orderRepository->create(Cart::prepareDataForOrder());
        // Cart::deActivateCart();
        // Cart::activateCartIfSessionHasDeactivatedCartId();
        $data['result'] = 200;
        $data['order'] = $order;

        // set the order status to processing

        $this->orderRepository->update(['status' => 'processing'], $order->id);

        // add the order id to ququeue
        $queue = config('app.name').':orders';
        if (config('onebuy.is_sync_erp')) {
            Artisan::queue((new PostOdoo())->getName(), ['--order_id'=> $order->id])->onConnection('rabbitmq')->onQueue(config('app.name') . ':odoo_order');
        } else {
            Artisan::queue((new Post())->getName(), ['--order_id'=> $order->id])->onConnection('rabbitmq')->onQueue($queue);
        }

        // add the ip address and ip country to order
        $order_cod = new \NexaMerchant\CheckoutCod\Models\OrderCods();
        $order_cod->order_id = $order->id;
        $order_cod->ip_address = $request->ip();
        $order_cod->ip_country = $ip_country;
        $order_cod->save();

        return response()->json($data);

    }

    private function returnInsurance($input, $cart) {
        // when return insurance eq 1 and auto add the insurance product into cart
        $input['return_insurance'] = isset($input['return_insurance']) ? $input['return_insurance'] : 0;
        if($input['return_insurance']==1) {

            if(empty(config('onebuy.return_shipping_insurance.product_id'))) {
                return;
            }

            Cart::addProduct(config('onebuy.return_shipping_insurance.product_id'), [
                'quantity' =>1 ,
                'product_sku' => config('onebuy.return_shipping_insurance.product_sku'),
                'selected_configurable_option' => '',
                'product_id' => config('onebuy.return_shipping_insurance.product_id'),
                'variant_id' => ''
            ]);


        }
    }

    /**
     * Validate order before creation.
     *
     * @return void|\Exception
     */
    public function validateOrder()
    {
        $cart = Cart::getCart();

        $minimumOrderAmount = core()->getConfigData('sales.order_settings.minimum_order.minimum_order_amount') ?: 0;

        if (
            auth()->guard('customer')->check()
            && auth()->guard('customer')->user()->is_suspended
        ) {
            throw new \Exception(trans('shop::app.checkout.cart.suspended-account-message'));
        }

        if (
            auth()->guard('customer')->user()
            && ! auth()->guard('customer')->user()->status
        ) {
            throw new \Exception(trans('shop::app.checkout.cart.inactive-account-message'));
        }

        if (! $cart->checkMinimumOrder()) {
            throw new \Exception(trans('shop::app.checkout.cart.minimum-order-message', ['amount' => core()->currency($minimumOrderAmount)]));
        }

        if ($cart->haveStockableItems() && ! $cart->shipping_address) {
            throw new \Exception(trans('shop::app.checkout.cart.check-shipping-address'));
        }

        if (! $cart->billing_address) {
            throw new \Exception(trans('shop::app.checkout.cart.check-billing-address'));
        }

        if (
            $cart->haveStockableItems()
            && ! $cart->selected_shipping_rate
        ) {
            throw new \Exception(trans('shop::app.checkout.cart.specify-shipping-method'));
        }

        if (! $cart->payment) {
            throw new \Exception(trans('shop::app.checkout.cart.specify-payment-method'));
        }
    }

}