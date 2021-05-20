<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CartItem;
use App\Classes\DocGenerator\Enums\Tags;
use App\Http\Requests\API\V1\Cart\SyncCartRequest;
use App\Http\Resources\V1\Cart\CartResource;
use App\Models\Cart;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;


#[OpenApi\PathItem]
class CartController extends BaseApiController
{
    const load_relation = [];

    /**
     * Show user cart.
     *
     * Show cart of logged in user
     */
    #[CustomOpenApi\Operation(id: 'CartIndex', tags: [Tags::Cart, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: CartResource::class, statusCode: 200)]
    public function index()
    {
        return new CartResource(tenancy()->getUser()->cart ?? new Cart());
    }

    /**
     * Sync cart
     *
     * Sync user cart content
     *
     * @param SyncCartRequest $request
     * @return CartResource
     */
    #[CustomOpenApi\Operation(id: 'CartSync', tags: [Tags::Cart, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: SyncCartRequest::class)]
    #[CustomOpenApi\Response(resource: CartResource::class, statusCode: 200)]
    public function sync(SyncCartRequest $request): CartResource
    {
        $cart = tenancy()
            ->getUser()
            ->syncCart(
                CartItem::fromRequest($request),
                $request->customer_id ?? null,
                $request->discount_id ?? null,
            );

        return new CartResource($cart);
    }
}