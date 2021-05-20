<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Exceptions\SupervisorDoesNotExistException;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Resources\V1\User\UserResource;
use App\Models\Channel;
use App\Models\User;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class UserController extends BaseApiController
{
    /**
     * Get logged in user detail
     *
     * Get the user resource of the currently logged in user
     *
     * @return mixed
     */
    #[CustomOpenApi\Operation(id: 'userMe', tags: [Tags::User, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: UserResource::class)]
    public function me(): UserResource
    {
        return new UserResource(auth()->user()->loadMissing('company'));
    }

    /**
     * Get detail of supervisor
     *
     * Get the detail of logged in user's supervisor (direct parent)
     *
     * @return mixed
     * @throws SupervisorDoesNotExistException
     */
    #[CustomOpenApi\Operation(id: 'userSupervisor', tags: [Tags::User, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: UserResource::class)]
    #[CustomOpenApi\ErrorResponse(exception: SupervisorDoesNotExistException::class)]
    public function supervisor(): UserResource
    {
        if (!$supervisor = auth()->user()->supervisor) throw new SupervisorDoesNotExistException();
        return new UserResource($supervisor);
    }

    /**
     * Set default channel
     *
     * Set the default channel for this user. Default channel must be set
     * before user can access tenanted resources.
     *
     * @param Channel $channel
     * @return mixed
     * @throws UnauthorisedTenantAccessException
     */
    #[CustomOpenApi\Operation(id: 'userSetDefaultChannel', tags: [Tags::User, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function channel(Channel $channel)
    {
        if (!tenancy()->hasTenantId($channel->id)) {
            throw new UnauthorisedTenantAccessException();
        }

        auth('sanctum')->user()->update(['channel_id' => $channel->id]);

        return GenericSuccessMessageResponse::getResponse();
    }

    /**
     * Get user detail
     *
     * A user can view user data about itself, its supervisor (parent),
     * and all its children/grand children nodes (all depths)
     *
     * @param User $user
     * @return mixed
     */
    #[CustomOpenApi\Operation(id: 'userShow', tags: [Tags::User, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: UserResource::class)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function show(User $user): UserResource
    {
        if (!auth()->user()->canView($user)) {
            throw new UnauthorisedTenantAccessException();
        }

        return new UserResource($user);
    }

    /**
     * Show all users.
     *
     * Show all users registered in the system. This is currently unfiltered, but in the
     * future we may filter to limit user visibility.
     */
    #[CustomOpenApi\Operation(id: 'userIndex', tags: [Tags::User, Tags::V1])]
    #[CustomOpenApi\Parameters(model: User::class)]
    #[CustomOpenApi\Response(resource: UserResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(User::class, UserResource::class);
    }

    /**
     * Show all supervised users.
     *
     * Show all users that is supervised by this user (all child and grandchild nodes).
     * Does not include data of currently logged in user and data of supervisor.
     *
     */
    #[CustomOpenApi\Operation(id: 'userSupervised', tags: [Tags::User, Tags::V1])]
    #[CustomOpenApi\Parameters(model: User::class)]
    #[CustomOpenApi\Response(resource: UserResource::class, isCollection: true)]
    public function supervised()
    {
        $filter = fn($query) => $query->whereDescendantOf(auth()->user());
        return CustomQueryBuilder::buildResource(User::class, UserResource::class, $filter);
    }
}
