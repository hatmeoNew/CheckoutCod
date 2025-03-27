<?php

namespace NexaMerchant\CheckoutCod\Http\Controllers\Api\V1;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Shop\Repositories\ThemeCustomizationRepository;
use Webkul\Product\Repositories\ProductAttributeValueRepository;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeOptionRepository;
use Webkul\Checkout\Facades\Cart;
use Webkul\Shop\Http\Resources\CartResource;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Shop\Http\Resources\ProductResource;
use Webkul\Paypal\Payment\SmartButton;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Product\Helpers\View;
use Nicelizhi\Airwallex\Payment\Airwallex;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Webkul\Payment\Facades\Payment;
use Illuminate\Support\Facades\Redis;
use Webkul\CMS\Repositories\CmsRepository;
use Webkul\CartRule\Repositories\CartRuleCouponRepository;
use Webkul\CartRule\Repositories\CartRuleRepository;
use \Webkul\Checkout\Repositories\CartRepository;

class ProductsController extends Controller
{

    private $faq_cache_key = "faq";

    private $checkout_v2_cache_key = "checkout_v2_cache_";

      /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Attribute\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Paypal\Helpers\Ipn  $ipnHelper
     * @return void
     */
    public function __construct(
        protected CartRepository $cartRepository,
        protected CategoryRepository $categoryRepository,
        protected ProductRepository $productRepository,
        protected ProductAttributeValueRepository $productAttributeValueRepository,
        protected AttributeRepository $attributeRepository,
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository,
        protected CmsRepository $cmsRepository,
        protected CartRuleCouponRepository $cartRuleCouponRepository,
        protected CartRuleRepository $cartRuleRepository,
        protected ThemeCustomizationRepository $themeCustomizationRepository
    )
    {
        
    }

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

        $ip_country = $request->server('HTTP_CF_IPCOUNTRY');

        // currency by ip
        $currency = \Nicelizhi\OneBuy\Helpers\Utils::getCurrencyByCountry($ip_country);


        $currency_get = $request->input('currency');
        if(!empty($currency_get)) {
            core()->setCurrentCurrency($currency_get);
            $currency = $currency_get;
        }

        $data = Cache::get($this->checkout_v2_cache_key.$slug.$currency);
        $env = config("app.env");

        if(empty($data)) {
            $product = $this->productRepository->findBySlug($slug);
            if(is_null($product)) {
                $data = [];
                $data['error'] = "This Product is Not Found!";
                return response()->json($data);
            }
            $data = [];
            $productViewHelper = new \Webkul\Product\Helpers\ConfigurableOption();
            $attributes = $productViewHelper->getConfigurationConfig($product);

            $redis = Redis::connection('default');

            $product_attr_sort_cache_key = "product_attr_sort_23_".$product->id;
            $product_attr_sort = $redis->hgetall($product_attr_sort_cache_key); // get sku sort
    
            foreach($attributes['attributes'] as $key=>$attribute) {

                $product_attr_sort_cache_key = "product_attr_sort_".$attribute['id']."_".$product->id;
                $product_attr_sort = $redis->hgetall($product_attr_sort_cache_key); // get sku sort
                $attributes['attributes'][$key]['attr_sort'] = $product_attr_sort;
            }

            foreach($attributes['index'] as $key=>$index) {
                
                $sku_products = $this->productRepository->where("id", $key)->select(['sku'])->first();
                $attributes['index'][$key]['sku'] = $sku_products->sku;
                $index2 = "";
                $total = count($index);
                $i = 0;
                foreach($index as $key2=>$ind) {
                    $i++;
                    if(empty($index2)) {
                        $index2=$key2."_".$ind;
                    } else {
                        $index2=$index2.",".$key2."_".$ind;
                    }
                    if($i==$total) $attributes['index2'][$index2] = [$key,$sku_products->sku];
                }
                //var_dump($index);

            }
    
            $package_products = [];
            $package_products = \Nicelizhi\OneBuy\Helpers\Utils::makeProducts($product, [2,1,3,4]);
            $product = new ProductResource($product);
            $data['product'] = $product;
            $data['package_products'] = $package_products;
            $data['sku'] = $product->sku;
            $data['attr'] = $attributes;
    
            $countries = config("countries");
    
            $default_country = config('onebuy.default_country');
    
            $airwallex_method = config('onebuy.airwallex.method');
    
            $payments = config('onebuy.payments'); // config the payments status
    
            $payments_default = config('onebuy.payments_default');
            $brand = config('onebuy.brand');
    
            $gtag = config('onebuy.gtag');
    
            $fb_ids = config('onebuy.fb_ids');
            $ob_adv_id = config('onebuy.ob_adv_id');
    
            $crm_channel = config('onebuy.crm_channel');
    
            $quora_adv_id = config('onebuy.quora_adv_id');
    
            $paypal_client_id = core()->getConfigData('sales.payment_methods.paypal_smart_button.client_id');
    
            $data['countries'] = $countries;
            $data['default_country'] = $default_country;
            $data['airwallex_method'] = $airwallex_method;
            $data['payments'] = $payments;
            $data['payments_default'] = $payments_default;
            $data['brand'] = $brand;
            $data['gtag'] = $gtag;
            $data['fb_ids'] = $fb_ids;
            $data['ob_adv_id'] = $ob_adv_id;
            $data['crm_channel'] = $crm_channel;
            $data['quora_adv_id'] = $quora_adv_id;
            $data['paypal_client_id'] = $paypal_client_id;
            $data['env'] = config("app.env");
            $data['sellPoints'] = $redis->hgetall("sell_points_".$slug);
            $data['sell-points'] = $redis->hgetall("sell_points_".$slug);
    
            $ads = []; // add ads
            
            $productBgAttribute = $this->productAttributeValueRepository->findOneWhere([
                'product_id'   => $product->id,
                'attribute_id' => 29,
            ]);
    
    
            $productBgAttribute_mobile = $this->productAttributeValueRepository->findOneWhere([
                'product_id'   => $product->id,
                'attribute_id' => 30,
            ]);
    
            $productSizeImage = $this->productAttributeValueRepository->findOneWhere([
                'product_id'   => $product->id,
                'attribute_id' => 32,
            ]);
    
            $ads['pc']['img'] = isset($productBgAttribute->text_value) ? $productBgAttribute->text_value : config("app.url")."/checkout/onebuy/banners/".$default_country."_pc.jpg";
            $ads['mobile']['img'] = isset($productBgAttribute_mobile->text_value) ? $productBgAttribute_mobile->text_value : config("app.url")."/checkout/onebuy/banners/".$default_country."_mobile.jpg";
            $ads['size']['img'] = isset($productSizeImage->text_value) ? $productSizeImage->text_value : "";
    
            $data['ads'] = $ads;

            // countdown
            //$countdown = $redis->hgetall("countdown_".$slug);
            $countdown = 5; // when 0 is not show
            $data['countdown'] = $countdown;

            // ad_message
            $data['ad_message']['text'] = "";
            // $data['ad_message']['color'] = "#FF0000";
            // $data['ad_message']['bg_color'] = "#FFFF00";
            // $data['ad_message']['href'] = "https://www.google.com";

            $data['ip_country'] = $ip_country;

            $data['currency'] = $currency;

            $data['customer_config'] = [];

            $checkoutItems = \Nicelizhi\Shopify\Helpers\Utils::getAllCheckoutVersion();
            $customer_config = [];
            foreach($checkoutItems as $key=>$item) {
                $cachek_key = "checkout_".$item."_".$slug;
                $cacheData = $redis->get($cachek_key);
                $customer_config[$item] = json_decode($cacheData);
            }

            $data['customer_config'] = $customer_config;


            Cache::put($this->checkout_v2_cache_key.$slug, json_encode($data));

            return response()->json($data);
        }

        $data = json_decode($data);
        $data = (array)$data;
        



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
    public function recommend($slug, Request $request)
    {
        $checkout_path = $request->input("checkout_path");

        //select four recommended products

        $shopify_store_id = config('shopify.shopify_store_id');


        $products = \Nicelizhi\Shopify\Models\ShopifyProduct::where("shopify_store_id",$shopify_store_id)->where("status", "active")->select(['product_id','title','handle',"variants","images"])->limit(100)->get();


        $recommended_info = [];

        $shopifyStore = Cache::get("shopify_store_".$shopify_store_id);

        if(empty($shopifyStore)){
            $shopifyStore = \Nicelizhi\Shopify\Models\ShopifyStore::where('shopify_store_id', $shopify_store_id)->first();
            Cache::put("shopify_store_".$shopify_store_id, $shopifyStore, 3600);
        }

        $i = 0;
        $max = 10;
        foreach($products as $key=> $product) {
            $images = $product->images;
            $variants = $product->variants;

            $online = \Webkul\Product\Models\Product::where("sku", $product->product_id)->first();
            if(is_null($online)) {
                continue;
            }

            if($i>=$max) {
                break;
            }

            $i++;

            $recommended_info[$key] = [
                "title" => $product->title,
                "handle" => $product->handle,
                "product_id" => $product->product_id,
                "discount_price" => $variants[0]['price'],
                "origin_price" => $variants[0]['compare_at_price'],
                "image_url" => $images[0]['src'],
                "url" => $shopifyStore->shopify_app_host_name . "/products/" . $product->handle
            ];
        }
 


        
        return new JsonResource([
            'checkout_path' => $checkout_path,
            'recommended_info' => $recommended_info,
            'currency_symbol' => core()->getCurrentCurrencyCode(),
            'recommended_info_title' => __('onebuy::app.You may also like')
        ]);

        
    }
}
