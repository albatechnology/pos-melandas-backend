<?php

namespace App\Contracts;

use App\Exceptions\CustomApiException;
use App\Exceptions\DefaultTenantRequiredException;
use App\Exceptions\GenericAuthorizationException;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\SalesOnlyActionException;
use App\Exceptions\SupervisorDoesNotExistException;
use App\Exceptions\UnauthorisedTenantAccessException;
use Exception;

/**
 * Contain custom errors with internal code and description.
 *
 * Class Errors
 */
class Errors
{
    const SupervisorDoesNotExistException     = 'US01';
    const DefaultTenantRequiredException      = 'AU01';
    const UnauthorisedTenantAccessException   = 'AU02';
    const SalesOnlyActionException            = 'AU03';
    const GenericAuthorizationException       = 'ER01';
    const ExpectedOrderPriceMismatchException = 'CH01';

    public static function getErrorByException(string $exception)
    {
        if (!is_a($exception, CustomApiException::class, true)) {
            throw new Exception("$exception must extend from CustomApiException class");
        }

        return collect(self::body())->keyBy('exception')->get($exception);
    }

    public static function body($errorCode = null)
    {
        $data = [
            self::SupervisorDoesNotExistException     => [
                'error_code'  => self::SupervisorDoesNotExistException,
                'label'       => 'Supervisor does not exist for the target user.',
                'exception'   => SupervisorDoesNotExistException::class,
                'description' => 'Occurs when requesting the supervisor detail of a user that does not have a supervisor.',
                'http_code'   => 422,
            ],
            self::DefaultTenantRequiredException      => [
                'error_code'  => self::DefaultTenantRequiredException,
                'label'       => 'User must have default channel to access this resource.',
                'exception'   => DefaultTenantRequiredException::class,
                'description' => 'Occurs when requesting a tenanted resource but user does not have a default channel_id.',
                'http_code'   => 403,
            ],
            self::UnauthorisedTenantAccessException   => [
                'error_code'  => self::UnauthorisedTenantAccessException,
                'label'       => 'User does not have tenant access for this action or resource.',
                'exception'   => UnauthorisedTenantAccessException::class,
                'description' => 'Occurs when user does not have tenant authority for the requested tenanted resource',
                'http_code'   => 403,
            ],
            self::GenericAuthorizationException       => [
                'error_code'  => self::GenericAuthorizationException,
                'label'       => 'Custom user friendly message will be generated depending on the context',
                'exception'   => GenericAuthorizationException::class,
                'description' => 'Occurs when users encounter a generic forbidden issue. Specific reason will be returned along with the exception',
                'http_code'   => 403,
            ],
            self::SalesOnlyActionException            => [
                'error_code'  => self::SalesOnlyActionException,
                'label'       => 'Only sales are allowed to perform this action!',
                'exception'   => SalesOnlyActionException::class,
                'description' => 'Occurs when a non sales attempting to perform sales only action',
                'http_code'   => 403,
            ],
            self::ExpectedOrderPriceMismatchException => [
                'error_code'  => self::ExpectedOrderPriceMismatchException,
                'label'       => 'Order price does not match the given expected price!',
                'exception'   => SalesOnlyActionException::class,
                'description' => 'Products and/or discounts could have been updated. App should re fetch cart and discount.',
                'http_code'   => 400,
            ],
        ];

        return $errorCode ? $data[$errorCode] : $data;
    }

    public static function getConstant(string $constant)
    {
        return constant('self::' . $constant);
    }
}
