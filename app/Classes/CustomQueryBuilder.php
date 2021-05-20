<?php

namespace App\Classes;

use App\Classes\DocGenerator\Enums\DataFormat;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Enums\ActivityFollowUpMethod;
use App\Enums\ActivityStatus;
use App\Enums\AddressType;
use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Enums\ProductCategoryType;
use App\Enums\UserType;
use App\Models\Activity;
use App\Models\ActivityComment;
use App\Models\Address;
use App\Models\Channel;
use App\Models\Colour;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductCategoryCode;
use App\Models\ProductModel;
use App\Models\ProductTag;
use App\Models\ProductUnit;
use App\Models\ProductVersion;
use App\Models\QaMessage;
use App\Models\QaTopic;
use App\Models\User;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CustomQueryBuilder extends QueryBuilder
{
    const DEFAULT_PER_PAGE = 15;
    const MAX_PER_PAGE     = 100;
    const MIN_PER_PAGE     = 1;

    const TYPE_EXACT = 'exact';
    const TYPE_SCOPE = 'scope';
    const TYPE_ENUM  = 'enum';

    const KEY_ID_NAME = 'GetIdName';

    public static function getKeys($class)
    {
        return collect(self::getAllowedFilterMap()[$class])->pluck('key')->toArray();
    }

    public static function getAllowedFilterMap($resource = null)
    {
        $data = [

            self::KEY_ID_NAME => [
                self::ids(),
                self::id('user_id'),
                self::id('lead_id'),
                self::id('customer_id'),
                self::id('channel_id'),
            ],

            Order::class => [
                self::ids(),
                self::ids('product_brand_id', 'whereProductBrandId'),
                self::string('name', 'Product name'),
            ],

            ProductModel::class        => [
                self::ids(),
                self::ids('product_brand_id', 'whereProductBrandId'),
                self::string('name', 'Product name'),
            ],
            ProductVersion::class      => [
                self::ids(),
                self::ids('product_brand_id', 'whereProductBrandId'),
                self::ids('product_model_id', 'whereProductModelId'),
            ],
            ProductCategoryCode::class => [
                self::ids(),
                self::ids('product_brand_id', 'whereProductBrandId'),
                self::ids('product_model_id', 'whereProductModelId'),
                self::ids('product_version_id', 'whereProductVersionId'),
            ],
            Product::class             => [
                self::ids(),
                self::ids('product_brand_id'),
                self::ids('product_model_id'),
                self::ids('product_version_id'),
                self::ids('product_category_code_id'),
                self::string('name', 'Product ABC'),
                self::scope('tags', 'whereTags', 'tag1,tag2,tag3', 'Searches tags by slug. Allow multiple tags separated by comma'),
            ],
            ProductUnit::class         => [
                self::ids(),
                self::id('product_id'),
                self::id('colour_id'),
                self::id('covering_id'),
                self::string('name', 'Product ABC'),
            ],
            Customer::class            => [
                self::ids(),
                self::string('first_name', 'Barrack'),
                self::string('last_name', 'Obama'),
                self::string('email', ApiDataExample::EMAIL),
                self::string('phone', '083123123'),
                self::scope('search', 'whereSearch', 'test@test.com', 'Searches name, email and phone.'),
            ],
            Address::class  => [
                self::ids(),
                self::id('customer_id'),
                self::string('address_line_1', 'address line 1'),
                self::string('address_line_2', 'address line 2'),
                self::string('city', 'Jakarta'),
                self::string('country', 'indonesia'),
                self::string('province', 'Jawa Barat'),
                self::string('phone', '312213213'),
                self::enum('type', AddressType::class),
            ],
            User::class     => [
                self::ids(),
                self::ids('supervisor_id', 'whereSupervisorId'),
                self::ids('supervisor_type_id', 'whereSupervisorTypeId'),
                self::string('name', 'Barrack obama'),
                self::enum('type', UserType::class),
            ],
            Channel::class  => [
                self::ids(),
                self::string('name', 'Toko ABC'),
            ],
            Discount::class => [
                self::ids(),
                self::string('name', 'Discount ABC'),
            ],
            Lead::class     => [
                self::ids(),
                self::enum('type', LeadType::class),
                self::enum('status', LeadStatus::class),
                self::string('label', 'my lead'),
                self::scope('customer_name', 'customerName', 'Customer A'),
                self::scope('customer_search', 'customerSearch', 'Customer A', 'Search by customer name, email and phone'),
                self::scope('channel_name', 'channelName', 'Channel A'),
                [
                    'key'         => 'is_new_customer',
                    'type'        => self::TYPE_EXACT,
                    'data_format' => DataFormat::BOOLEAN,
                    'schema'      => Schema::boolean('is_new_customer')->example(true),
                ],
            ],

            Activity::class        => [
                self::ids(),
                self::id('order_id'),
                self::enum('follow_up_method', ActivityFollowUpMethod::class),
                self::enum('status', ActivityStatus::class),
                self::string('feedback', 'my activity'),
                self::scope('follow_up_datetime_before', 'followUpDatetimeBefore', ApiDataExample::TIMESTAMP),
                self::scope('follow_up_datetime_after', 'followUpDatetimeAfter', ApiDataExample::TIMESTAMP),
            ],
            Company::class         => [
                self::ids(),
                self::string('name', 'test company'),
            ],
            ActivityComment::class => [
                self::ids(),
                self::id('user_id'),
                self::id('activity_id'),
                self::id('activity_comment_id', 'The parent comment if this comment is a reply comment'),
                self::string('content', 'comment content'),
            ],
            ProductTag::class      => [
                self::ids(),
                self::string('name', 'test tag'),
                self::string('slug', 'test-tag'),
            ],
            ProductCategory::class => [
                self::ids(),
                self::string('name', 'test category'),
                self::string('description'),
                self::enum('type', ProductCategoryType::class),
                self::id('parent_id'),
            ],
            QaTopic::class         => [
                self::ids(),
                self::string('subject'),
                self::id('creator_id'),
            ],
            QaMessage::class       => [
                self::ids(),
                self::string('content'),
                self::ids('topic_id'),
                self::ids('sender_id'),
                [
                    'key'         => 'is_unread',
                    'alias'       => 'isUnread',
                    'type'        => self::TYPE_SCOPE,
                    'data_format' => DataFormat::BOOLEAN,
                    'schema'      => Schema::boolean('is_unread')->example(true),
                ],
            ],
            Colour::class          => [
                self::ids(),
                self::string('name', 'white'),
                self::string('code', '54h8'),
                self::id('product_brand_id'),
                [
                    'key'         => 'product_id',
                    'alias'       => 'whereProduct',
                    'type'        => self::TYPE_SCOPE,
                    'data_format' => DataFormat::NUMERIC,
                    'schema'      => Schema::integer('product_id')
                                           ->example(1)
                                           ->description('Get the colours available for a given product id'),
                ],
            ],
        ];

        return $resource ? $data[$resource] : $data;
    }

    // region Helper Class
    protected static function ids($key = 'id', string $scopeAlias = null)
    {
        if (is_null($scopeAlias)) {
            return [
                'key'         => $key,
                'type'        => self::TYPE_EXACT,
                'schema'      => Schema::string($key)->example('1,2,3')->description('Set of ids, comma separated'),
                'data_format' => DataFormat::CSV
            ];
        } else {
            return [
                'key'         => $key,
                'alias'       => $scopeAlias,
                'type'        => self::TYPE_SCOPE,
                'schema'      => Schema::string($key)->example('1,2,3')->description('Set of ids, comma separated'),
                'data_format' => DataFormat::CSV
            ];
        }
    }

    protected static function string($key, $example = null, $description = null)
    {
        $example = $example ?? 'test ' . $key;
        $schema  = Schema::string($key)->example($example);

        if ($description) $schema = $schema->description($description);

        return [
            'key'    => $key,
            'schema' => $schema,
        ];
    }

    protected static function scope($key, $alias = null, $example = null, $description = null)
    {
        return [
            'key'    => $key,
            'alias'  => $alias ?? $key,
            'type'   => self::TYPE_SCOPE,
            'schema' => Schema::string($key)->example($example ?? 'test ' . $key)->description($description),
        ];
    }

    protected static function id($key = 'id', $description = null)
    {
        $schema = Schema::integer($key)->example(1);
        if ($description) $schema = $schema->description($description);

        return [
            'key'         => $key,
            'type'        => self::TYPE_EXACT,
            'schema'      => $schema,
            'data_format' => DataFormat::NUMERIC
        ];
    }

    protected static function enum($key, $enum_class, $description = null)
    {
        // For enums, we provide the enum key to the API instead of value
        // So here we need to take the key and transform it to enum value

        return [
            'key'         => $key,
            'type'        => self::TYPE_ENUM,
            'data_format' => DataFormat::ENUM,
            'enum_class'  => $enum_class,
            'schema'      => Schema::string($key)
                                   ->example($enum_class::getDefaultInstance()->key)
                                   ->enum(...$enum_class::getKeys())
                                   ->description($description),
        ];
    }

    //endregion

    public static function getSchemas($class)
    {
        return collect(self::getAllowedFilterMap()[$class])->pluck('schema')->toArray();
    }

    public static function buildResource(string $model_class, string $resource_class, callable $closure = null, string $filter_key = null)
    {
        $query = $resource_class::collection(self::build($model_class, $closure, $resource_class, $filter_key));
        $query->additional(self::getQueryMetadata($filter_key ?? $model_class, $resource_class));
        return $query;
    }

    public static function build(string $class, $closure = null, string $resource_class = null, string $filter_key = null)
    {
        $query = self::for($class)->allowedFilters(self::makeAllowedFilters($filter_key ?? $class));
        if ($resource_class) {
            $sortables = self::getSortableFields($resource_class);
            if (!empty($sortables)) $query = $query->allowedSorts(...$sortables);
        }

        if (!request('sort')) {
            $query = $query->orderBy('id', 'desc');
        }

        if ($closure) {
            $query = $closure($query);
        }

        return $query->simplePaginate(self::getQueryPerPage());
    }

    public static function makeAllowedFilters($class)
    {
        return collect(self::getAllowedFilterMap()[$class])->map(function ($data) {
            $type = $data['type'] ?? 'partial';

            if ($type == self::TYPE_ENUM) {
                return AllowedFilter::callback($data['key'], function ($query, $values) use ($data) {

                    $values = is_array($values) ? $values : explode(',', $values);

                    $values = collect($values)
                        ->map(function ($value) use ($data) {
                            return $data['enum_class']::fromKey($value)->value;
                        })
                        ->all();

                    $query->whereIn($data['key'], $values);
                });
            }

            if (isset($data['alias'])) {
                return AllowedFilter::$type($data['key'], $data['alias']);
            } else {
                return AllowedFilter::$type($data['key']);
            }
        })->toArray();
    }

    protected static function getSortableFields(string $resource_class)
    {
        return method_exists($resource_class, 'getSortableFields') ? $resource_class::getSortableFields() : [];
    }

    public static function getQueryPerPage()
    {
        $perPage = request('perPage') ?? self::DEFAULT_PER_PAGE;
        return max(self::MIN_PER_PAGE, min(self::MAX_PER_PAGE, (int)$perPage));
    }

    protected static function getQueryMetadata(string $model_class, string $resource_class)
    {
        $filters = collect(self::getAllowedFilterMap($model_class))->map(function ($data) {
            $result = [
                'key'        => $data['key'],
                'dataFormat' => $data['data_format'] ?? DataFormat::DEFAULT
            ];

            if (!empty($data['type']) && $data['type'] === self::TYPE_ENUM) {
                $result['options'] = collect($data['enum_class']::getInstances())->map(function ($data) {
                    return ['value' => $data->key, 'label' => $data->description];
                })->values()->all();
            }

            return $result;
        })->toArray();

        return [
            'query' => [
                'filters' => $filters,
                'sort'    => self::getSortableFields($resource_class)
            ]
        ];
    }


}
