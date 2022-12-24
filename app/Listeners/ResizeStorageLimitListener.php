<?php

namespace App\Listeners;

use App\Repositories\OrganizationRepository;
use App\Services\FileSystem\Contracts\FileBagInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ResizeStorageLimitListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the queued listener may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * @var \App\Repositories\OrganizationRepository
     */
    protected $organRepository;

    /**
     * @param \App\Repositories\OrganizationRepository $organRepository
     */
    public function __construct(OrganizationRepository $organRepository)
    {
        $this->organRepository = $organRepository;
    }

    /**
     * Handle the event.
     *
     * @param FileBagInterface|mixed $event
     *
     * @return void
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function handle($event)
    {
        if ($event instanceof FileBagInterface) {
            return $this->incrementUsed($event->getFileBag());
        }

        return $this->decrementUsed($event->organId, $event->size);
    }

    /**
     * Action when delete file
     *
     * @param string $organId
     * @param int $size
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    protected function decrementUsed($organId, $size)
    {
        $organ = $this->getOrganization($organId);
        if (is_null($organ)) {
            return;
        }

        $this->organRepository->updateByModel($organ, ['storage_used' => $organ->storage_used - $size]);
    }

    /**
     * Action when upload file
     *
     * @param FileBagInterface $fileBag
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    protected function incrementUsed($fileBag)
    {
        $organ = $this->getOrganization($fileBag->organId());
        if (is_null($organ)) {
            return;
        }

        $this->organRepository->updateByModel($organ, ['storage_used' => $organ->storage_used + $fileBag->fileSize()]);
    }

    /**
     * @param string $organId
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed|null
     */
    private function getOrganization($organId)
    {
        try {
            return $this->organRepository->find($organId);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
