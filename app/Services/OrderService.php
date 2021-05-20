<?php

namespace App\Services;

use App\Contracts\ExceptionMessage;
use App\Enums\DiscountScope;
use App\Enums\DiscountType;
use App\Interfaces\Discountable;
use App\Interfaces\DiscountableBase;
use App\Models\CustomerDiscountUse;
use App\Models\Discount;
use App\Pipes\Discountable\CalculateDiscountForDiscountableClass;
use App\Pipes\Discountable\CalculateDiscountForDiscountableLineClass;
use App\Pipes\Discountable\CheckDiscountActive;
use App\Pipes\Discountable\CheckDiscountApplied;
use App\Pipes\Discountable\CheckDiscountMinOrderPrice;
use App\Pipes\Discountable\CheckDiscountUseLimit;
use App\Pipes\Discountable\CheckMaxDiscountLimit;
use Exception;
use Illuminate\Pipeline\Pipeline;

class OrderService
{
    /**
     * Calculate the discount price for a given discountable class.
     *
     * @param DiscountableBase $discountable
     * @param Discount $discount
     * @return int
     * @throws Exception
     */
    public static function calculateTotalDiscount(DiscountableBase $discountable, Discount $discount): int
    {
        if (!$discount->type->in([DiscountType::PERCENTAGE, DiscountType::NOMINAL])) {
            throw new Exception('Unknown discount type is being handled at calculateTotalDiscount method.');
        }

        if ($discount->type->is(DiscountType::PERCENTAGE)) {
            $value = $discountable->getTotalPrice() * $discount->value / 100;
        }

        if ($discount->type->is(DiscountType::NOMINAL)) {

            if ($discount->scope->is(DiscountScope::QUANTITY)) {
                // dont let discount to be greater than the total price
                $value = min($discount->value * $discountable->getQuantity(), $discountable->getTotalPrice());
            }

            if ($discount->scope->in([DiscountScope::TYPE, DiscountScope::TRANSACTION])) {
                // dont let discount to be greater than the total price
                $value = min($discount->value, $discountable->getTotalPrice());
            }
        }

        return $value ?? 0;
    }

    /**
     * @param Discountable $discountable
     * @param Discount $discount
     */
    public static function setDiscount(Discountable $discountable, Discount $discount)
    {
        // set discount, before it is ready to be processed in pipeline
        $discountable->discount_id = $discount->id;
        $discountable->discount    = $discount;

        app(Pipeline::class)
            ->send($discountable)
            ->through([
                CheckDiscountActive::class,
                CheckDiscountMinOrderPrice::class,
                CheckDiscountUseLimit::class,
                CalculateDiscountForDiscountableClass::class,
                CalculateDiscountForDiscountableLineClass::class,
                CheckMaxDiscountLimit::class,
                CheckDiscountApplied::class,
            ])
            ->thenReturn();
    }

    /**
     * Record the discount used into the customer count.
     * @param Discountable $discountable
     * @throws Exception
     */
    public static function recordDiscountUse(Discountable $discountable)
    {
        if (!$discount = $discountable->getDiscount()) {
            throw new Exception(ExceptionMessage::DiscountableNeedDiscount);
        }

        if (!$customer_id = $discountable->getCustomerId()) {
            throw new Exception(ExceptionMessage::DiscountableNeedCustomer);
        }

        if (!$discountable_id = $discountable->getId()) {
            throw new Exception(ExceptionMessage::DiscountableNeedId);
        }

        $customerUse = CustomerDiscountUse::firstOrCreate(
            [
                'customer_id' => $customer_id,
                'discount_id' => $discount->id,
            ]
        );

        // add the new order id
        $discountable_ids = collect($customerUse->order_ids)->push($discountable_id)->all();
        $customerUse->update(['order_ids' => $discountable_ids]);

        // add to the counter
        CustomerDiscountUse::where('id', $customerUse->id)
                           ->increment('use_count');
    }
}
