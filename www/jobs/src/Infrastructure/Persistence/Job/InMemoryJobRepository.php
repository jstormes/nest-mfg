<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Job;

use App\Domain\Job\Job;
use App\Domain\Job\JobNotFoundException;
use App\Domain\Job\JobRepository;

class InMemoryJobRepository implements JobRepository
{
    private array $jobs;

    public function __construct(array $jobs = [])
    {
        $this->jobs = $jobs;
        if (empty($jobs)) {
            $this->jobs = [
                1 => new Job(1, 'Assembly Job 1', 'Assemble product components', 'Not Started'),
                2 => new Job(2, 'Quality Check', 'Perform quality inspection', 'Started'),
                3 => new Job(3, 'Packaging', 'Package finished products', 'Paused'),
                4 => new Job(4, 'Maintenance', 'Equipment maintenance', 'Problem'),
                5 => new Job(5, 'Shipping', 'Prepare for shipping', 'Finished'),
                6 => new Job(6, 'Cancelled Job', 'Cancelled due to issues', 'Canceled'),
            ];
        }
    }

    public function findAll(): array
    {
        return array_values($this->jobs);
    }

    public function findJobOfId(int $id): Job
    {
        if (!isset($this->jobs[$id])) {
            throw new JobNotFoundException($id);
        }

        return $this->jobs[$id];
    }

    public function save(Job $job): void
    {
        $this->jobs[$job->getId()] = $job;
    }

    public function delete(int $id): void
    {
        if (!isset($this->jobs[$id])) {
            throw new JobNotFoundException($id);
        }

        unset($this->jobs[$id]);
    }

    public function findByStatus(string $status): array
    {
        return array_values(array_filter($this->jobs, function (Job $job) use ($status) {
            return $job->getStatus() === $status;
        }));
    }
} 