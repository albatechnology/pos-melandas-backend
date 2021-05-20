<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Classes\DocGenerator\OpenApi\GetFrontEndFormResponse;
use App\Enums\LeadStatus;
use App\Exceptions\SalesOnlyActionException;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Requests\API\V1\Lead\CreateLeadRequest;
use App\Http\Requests\API\V1\Lead\UpdateLeadRequest;
use App\Http\Resources\V1\Lead\LeadResource;
use App\Http\Resources\V1\Lead\LeadWithLatestActivityResource;
use App\Models\Lead;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use App\OpenApi\Responses\Custom\GenericSuccessMessageResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class LeadController extends BaseApiController
{
    const load_relation = ['customer', 'user', 'channel'];

    /**
     * Show all user's lead.
     *
     * The leads displayed depends on the type of the authenticated user:
     * 1. Sales will see all leads that is directly under him
     * 2. Supervisor will see all of his supervised sales' leads
     * 3. Director will see all leads in his active/default channel
     *
     */
    #[CustomOpenApi\Operation(id: 'leadIndex', tags: [Tags::Lead, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Lead::class)]
    #[CustomOpenApi\Response(resource: LeadResource::class, isCollection: true)]
    public function index()
    {
        $query = fn($q) => $q->tenanted()->with(self::load_relation);
        return CustomQueryBuilder::buildResource(Lead::class, LeadResource::class, $query);
    }

    /**
     * Show create product lead
     *
     * Show the validation rules for creating lead
     */
    #[CustomOpenApi\Operation(id: 'leadCreate', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function create(): JsonResponse
    {
        return CreateLeadRequest::frontEndRuleResponse();
    }

    /**
     * Create new Lead
     *
     * Create a new Lead. Currently only sales are allowed to perform
     * this action. This is because lead must be related to a sales. If
     * we want to allow supervisor to add a new lead, they must pick which
     * sales to assign this sales to (which is not supported yet).
     *
     * @param CreateLeadRequest $request
     * @return LeadWithLatestActivityResource
     * @throws SalesOnlyActionException
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'leadStore', tags: [Tags::Lead, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: CreateLeadRequest::class)]
    #[CustomOpenApi\Response(resource: LeadWithLatestActivityResource::class, statusCode: 201)]
    #[CustomOpenApi\ErrorResponse(exception: SalesOnlyActionException::class)]
    public function store(CreateLeadRequest $request): LeadWithLatestActivityResource
    {
        if (!tenancy()->getUser()->is_sales) throw new SalesOnlyActionException();

        $data = array_merge($request->validated(), [
            'channel_id' => tenancy()->getActiveTenant()->id,
            'user_id'    => tenancy()->getUser()->id,
            'status'     => LeadStatus::GREEN()
        ]);

        $lead = Lead::create($data);
        $lead->queueStatusChange();
        $lead->refresh()->loadMissing(self::load_relation);

        return $this->show($lead);
    }

    /**
     * Get lead
     *
     * Returns lead by id
     *
     * @param Lead $lead
     * @return  LeadWithLatestActivityResource
     */
    #[CustomOpenApi\Operation(id: 'leadShow', tags: [Tags::Lead, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: LeadWithLatestActivityResource::class, statusCode: 200)]
    public function show(Lead $lead): LeadWithLatestActivityResource
    {
        return new LeadWithLatestActivityResource($lead->loadMissing(self::load_relation));
    }

    /**
     * Delete Lead
     *
     * Delete a lead by its id
     *
     * @param Lead $lead
     * @return JsonResponse
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'leadDestroy', tags: [Tags::Lead, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GenericSuccessMessageResponse::class)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function destroy(Lead $lead): JsonResponse
    {
        $lead->checkTenantAccess()->delete();
        return GenericSuccessMessageResponse::getResponse();
    }

    /**
     * Show edit lead rules
     *
     * Show the validation rules for editing lead
     *
     * @throws Exception
     */
    #[CustomOpenApi\Operation(id: 'leadEdit', tags: [Tags::Rule, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[OpenApi\Response(factory: GetFrontEndFormResponse::class, statusCode: 200)]
    public function edit(): JsonResponse
    {
        return UpdateLeadRequest::frontEndRuleResponse();
    }

    /**
     * Update a lead
     *
     * Update a given lead
     *
     * @param Lead $lead
     * @param UpdateLeadRequest $request
     * @return LeadResource
     * @throws UnauthorisedTenantAccessException
     */
    #[CustomOpenApi\Operation(id: 'leadUpdate', tags: [Tags::Lead, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\RequestBody(request: UpdateLeadRequest::class)]
    #[CustomOpenApi\Response(resource: LeadWithLatestActivityResource::class)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function update(Lead $lead, UpdateLeadRequest $request): LeadWithLatestActivityResource
    {
        $lead->checkTenantAccess()->update($request->validated());
        return $this->show($lead->refresh()->loadMissing(self::load_relation));
    }
}