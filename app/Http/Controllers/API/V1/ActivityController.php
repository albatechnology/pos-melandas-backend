<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Classes\DocGenerator\OpenApi\GetFrontEndFormResponse;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Requests\API\V1\Activity\CreateActivityRequest;
use App\Http\Requests\API\V1\Activity\UpdateActivityRequest;
use App\Http\Resources\V1\Activity\ActivityCommentResource;
use App\Http\Resources\V1\Activity\ActivityResource;
use App\Models\Activity;
use App\Models\ActivityComment;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class ActivityController extends BaseApiController
{
    const load_relation = ['lead', 'user', 'customer', 'latestComment.user'];

    /**
     * Get activity
     *
     * Returns activity by id
     *
     * @param Activity $activity
     * @return  ActivityResource
     */
    #[CustomOpenApi\Operation(id: 'activityShow', tags: [Tags::Activity, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: ActivityResource::class, statusCode: 200)]
    public function show(Activity $activity): ActivityResource
    {
        return new ActivityResource($activity->loadMissing(self::load_relation));
    }

    /**
     * Show all activity posted by user
     *
     * Sales will get all activities directly created by him.
     * Supervisor will get all activities created by its supervised sales.
     * Director will get all activities scoped to its active channel setting.
     *
     */
    #[CustomOpenApi\Operation(id: 'activityIndex', tags: [Tags::Activity, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Activity::class)]
    #[CustomOpenApi\Response(resource: ActivityResource::class, isCollection: true)]
    public function index()
    {
        $query = fn($q) => $q->tenanted()->with(self::load_relation);
        return CustomQueryBuilder::buildResource(Activity::class, ActivityResource::class, $query);
    }

    /**
     * Show create product activity
     *
     * Show the validation rules for creating activity
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'activityCreate', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function create(): JsonResponse
    {
        return CreateActivityRequest::frontEndRuleResponse();
    }

    /**
     * Create new Activity
     *
     * Create a new activity
     *
     * @param CreateActivityRequest $request
     * @return ActivityResource
     */
    #[CustomOpenApi\Operation(id: 'activityStore', tags: [Tags::Activity, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreateActivityRequest::class)]
    #[CustomOpenApi\Response(resource: ActivityResource::class, statusCode: 201)]
    public function store(CreateActivityRequest $request): ActivityResource
    {
        $data = array_merge($request->validated(), [
            "user_id"    => tenancy()->getUser()->id,
        ]);
        return $this->show(Activity::create($data)->refresh());
    }

    /**
     * Delete Activity
     *
     * Delete a activity by its id
     *
     * @param Activity $activity
     * @return JsonResponse
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'activityDestroy', tags: [Tags::Activity, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    public function destroy(Activity $activity)
    {
        $activity->checkTenantAccess()->delete();
        return GenericSuccessMessageResponse::getResponse();
    }

    /**
     * Show edit activity rules
     *
     * Show the validation rules for editing activity
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'activityEdit', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function edit(): JsonResponse
    {
        return UpdateActivityRequest::frontEndRuleResponse();
    }

    /**
     * Update a activity
     *
     * Update a given activity
     *
     * @param Activity $activity
     * @param UpdateActivityRequest $request
     * @return ActivityResource
     * @throws UnauthorisedTenantAccessException
     */
    #[CustomOpenApi\Operation(id: 'activityUpdate', tags: [Tags::Activity, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: UpdateActivityRequest::class)]
    #[CustomOpenApi\Response(resource: ActivityResource::class)]
    public function update(Activity $activity, UpdateActivityRequest $request): ActivityResource
    {
        $activity->checkTenantAccess()->update($request->validated());
        return $this->show($activity->refresh());
    }

    /**
     * Show all activity comments of an activity.
     *
     * Show all activity comments for a given activity.
     */
    #[CustomOpenApi\Operation(id: 'activityGetComments', tags: [Tags::Customer, Tags::V1, Tags::ActivityComment])]
    #[CustomOpenApi\Parameters(model: ActivityComment::class)]
    #[CustomOpenApi\Response(resource: ActivityCommentResource::class, isCollection: true)]
    public function getActivityComments(int $activity)
    {
        $query = fn($q) => $q->where('activity_id', $activity)->with(ActivityCommentController::load_relation);
        return CustomQueryBuilder::buildResource(ActivityComment::class, ActivityCommentResource::class, $query);
    }
}