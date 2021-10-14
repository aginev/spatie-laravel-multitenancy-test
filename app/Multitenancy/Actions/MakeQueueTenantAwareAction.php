<?php

namespace App\Multitenancy\Actions;

use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobRetryRequested;
use Spatie\Multitenancy\Exceptions\CurrentTenantCouldNotBeDeterminedInTenantAwareJob;
use Spatie\Multitenancy\Models\Tenant;

class MakeQueueTenantAwareAction extends \Spatie\Multitenancy\Actions\MakeQueueTenantAwareAction
{

    public function execute()
    {
        $this
            ->listenForJobsBeingQueued()
            ->listenForJobsBeingProcessed()
            ->listenForJobsRetryRequested();
    }

    protected function listenForJobsRetryRequested(): self
    {
        app('events')->listen(JobRetryRequested::class, function (JobRetryRequested $event) {
            if (!array_key_exists('tenantId', $event->payload())) {
                return;
            }

            $this->findTenant($event)->makeCurrent();
        });

        return $this;
    }

    public function getEventPayload($event): ?array
    {
        return match (true) {
            $event instanceof JobProcessing => $event->job->payload(),
            $event instanceof JobRetryRequested => $event->payload(),
            default => null,
        };
    }

    protected function findTenant(JobProcessing|JobRetryRequested $event): Tenant
    {
        $tenantId = $this->getEventPayload($event)['tenantId'];

        if (!$tenantId) {
            $event->job->delete();

            throw CurrentTenantCouldNotBeDeterminedInTenantAwareJob::noIdSet($event);
        }

        /** @var \Spatie\Multitenancy\Models\Tenant $tenant */
        if (!$tenant = $this->getTenantModel()::find($tenantId)) {
            $event->job->delete();

            throw CurrentTenantCouldNotBeDeterminedInTenantAwareJob::noTenantFound($event);
        }

        return $tenant;
    }
}
