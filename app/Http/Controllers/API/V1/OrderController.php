<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Requests\API\V1\Order\CreateOrderRequest;
use App\Http\Resources\V1\Order\OrderResource;
use App\Models\Order;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\Pipes\Order\AddAdditionalFees;
use App\Pipes\Order\ApplyDiscount;
use App\Pipes\Order\CheckExpectedOrderPrice;
use App\Pipes\Order\CreateActivity;
use App\Pipes\Order\FillOrderAttributes;
use App\Pipes\Order\FillOrderRecord;
use App\Pipes\Order\GenerateInvoiceNumber;
use App\Pipes\Order\MakeOrderLines;
use App\Pipes\Order\ProcessInvoiceNumber;
use App\Pipes\Order\SaveOrder;
use App\Pipes\Order\UpdateDiscountUse;
use Illuminate\Pipeline\Pipeline;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class OrderController extends BaseApiController
{
    const load_relation = [];

    /**
     * Get order
     *
     * Returns order by id
     *
     * @param Order $order
     * @return  OrderResource
     */
    #[CustomOpenApi\Operation(id: 'OrderShow', tags: [Tags::Order, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: OrderResource::class, statusCode: 200)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function show(Order $order)
    {
        $order->loadMissing(['customer', 'order_details']);
        return new OrderResource($order);
        //$this->authorize('show', $order);
        //return new OrderResource($order->loadMissing(self::load_relation)->checkTenantAccess());
    }

    /**
     * Show all order.
     *
     * Show all order
     *
     */
    #[CustomOpenApi\Operation(id: 'OrderIndex', tags: [Tags::Order, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Order::class)]
    #[CustomOpenApi\Response(resource: OrderResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(Order::class, OrderResource::class, fn($query) => $query->with(self::load_relation)->tenanted());
    }
//
//    /**
//     * Get create order rule
//     *
//     * Show the validation rules for creating order
//     *
//     * @throws \Exception
//     */
//    #[CustomOpenApi\Operation(id: 'OrderCreate', tags: [Tags::Rule, Tags::V1])]
//    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
//    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
//    public function create(): \Illuminate\Http\JsonResponse
//    {
//        return CreateOrderRequest::frontEndRuleResponse();
//    }

    /**
     * Create new Order
     *
     * Create a new order
     *
     * @param CreateOrderRequest $request
     * @return OrderResource
     */
    #[CustomOpenApi\Operation(id: 'OrderStore', tags: [Tags::Order, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreateOrderRequest::class)]
    #[CustomOpenApi\Response(resource: OrderResource::class, statusCode: 201)]
    public function store(CreateOrderRequest $request): OrderResource
    {
        $order = app(Pipeline::class)
            ->send(Order::make(['raw_source' => $request->all()]))
            ->through(
                [
                    FillOrderAttributes::class,
                    FillOrderRecord::class,
                    MakeOrderLines::class,
                    ApplyDiscount::class,
                    AddAdditionalFees::class,
                    CheckExpectedOrderPrice::class,
                    SaveOrder::class,
                    UpdateDiscountUse::class,
                    CreateActivity::class,
                    ProcessInvoiceNumber::class
                ]
            )
            ->thenReturn();

        $order->refresh()->loadMissing(['customer', 'order_details']);

        return new OrderResource($order);
    }

    /**
     * Order preview
     *
     * Creates a dummy order for preview purposes. Use this endpoint to check
     * how the order will look like with the discount applied.
     *
     * @param CreateOrderRequest $request
     * @return OrderResource
     */
    #[CustomOpenApi\Operation(id: 'OrderPreview', tags: [Tags::Order, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreateOrderRequest::class)]
    #[CustomOpenApi\Response(resource: OrderResource::class, statusCode: 200)]
    public function preview(CreateOrderRequest $request): OrderResource
    {
        $order = app(Pipeline::class)
            ->send(Order::make(['raw_source' => $request->all()]))
            ->through(
                [
                    FillOrderAttributes::class,
                    FillOrderRecord::class,
                    MakeOrderLines::class,
                    ApplyDiscount::class,
                    AddAdditionalFees::class,
                    CheckExpectedOrderPrice::class,
                ]
            )
            ->thenReturn();

        $order->loadMissing(['customer', 'order_details']);

        return new OrderResource($order);
    }

//    /**
//     * Delete Order
//     *
//     * Delete a order by its id
//     *
//     * @param Order $order
//     * @return \Illuminate\Http\JsonResponse
//     * @throws \Exception
//     */
//    #[CustomOpenApi\Operation(id: 'OrderDestroy', tags: [Tags::Order, Tags::V1])]
//    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
//    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
//    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
//    public function destroy(Order $order)
//    {
//        $this->authorize('destroy', $order);
//        $order->checkTenantAccess()->delete();
//        return GenericSuccessMessageResponse::getResponse();
//    }
//
//    /**
//     * Show edit order rules
//     *
//     * Show the validation rules for editing order
//     *
//     * @throws \Exception
//     */
//    #[CustomOpenApi\Operation(id: 'OrderEdit', tags: [Tags::Rule, Tags::V1])]
//    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
//    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
//    public function edit(): \Illuminate\Http\JsonResponse
//    {
//        return UpdateOrderRequest::frontEndRuleResponse();
//    }
//
//    /**
//     * Update a order
//     *
//     * Update a given order
//     *
//     * @param Order $order
//     * @param UpdateOrderRequest $request
//     * @return OrderResource
//     */
//    #[CustomOpenApi\Operation(id: 'OrderUpdate', tags: [Tags::Order, Tags::V1])]
//    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
//    #[CustomOpenApi\RequestBody(request: UpdateOrderRequest::class)]
//    #[CustomOpenApi\Response(resource: OrderResource::class)]
//    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
//    public function update(Order $order, UpdateOrderRequest $request): OrderResource
//    {
//        $this->authorize('update', $order);
//        $order->checkTenantAccess()->update($request->validated());
//        return $this->show($order->loadMissing(self::load_relation));
//    }
}