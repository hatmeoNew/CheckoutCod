# Nexa Merchant Checkout COD

This package is a part of NexaMerchant, a Laravel E-commerce package. This package is used to provide a Cash on Delivery payment method for the NexaMerchant package.

# Product Details API
> This API is used to get the product details by slug.

## URL
```
GET api/v1/checkoutcod/products/details/{slug}
```

## Response

```
```



# Product Recommend API
> This API is used to get the recommended products.

## URL

```
GET api/v1/checkoutcod/products/recommend/{slug}
```

## Response

```
```

# Create Order API
> This API is used to create an order.

## URL

```
POST api/v1/checkoutcod/orders/create
```

## Request

```
{
    "user_id": 1,
    "product_id": 1,
    "quantity": 1,
    "total": 100,
    "status": "pending",
    "payment_method": "cod",
    "payment_status": "pending",
    "shipping_address": "Dhaka, Bangladesh",
    "shipping_method": "standard",
    "shipping_cost": 10,
    "tax": 5,
    "discount": 0,
    "grand_total": 115
}
```

## Response

```

```
