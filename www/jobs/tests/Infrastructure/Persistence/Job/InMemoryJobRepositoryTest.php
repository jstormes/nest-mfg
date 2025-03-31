<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\Job;

use App\Domain\Job\Job;
use App\Domain\Job\JobNotFoundException;
use App\Infrastructure\Persistence\Job\InMemoryJobRepository;
use Tests\TestCase;

class InMemoryJobRepositoryTest extends TestCase
{
    public function testFindAll()
    {
        $job = new Job(1, 'Assembly Job 1', 'Assemble product components', 'Not Started');

        $jobRepository = new InMemoryJobRepository([1 => $job]);

        $this->assertEquals([$job], $jobRepository->findAll());
    }

    public function testFindAllJobsByDefault()
    {
        $jobs = [
            1 => new Job(1, 'Assembly Job 1', 'Assemble product components', 'Not Started'),
            2 => new Job(2, 'Quality Check', 'Perform quality inspection', 'Started'),
            3 => new Job(3, 'Packaging', 'Package finished products', 'Paused'),
            4 => new Job(4, 'Maintenance', 'Equipment maintenance', 'Problem'),
            5 => new Job(5, 'Shipping', 'Prepare for shipping', 'Finished'),
            6 => new Job(6, 'Cancelled Job', 'Cancelled due to issues', 'Canceled'),
        ];

        $jobRepository = new InMemoryJobRepository();

        $this->assertEquals(array_values($jobs), $jobRepository->findAll());
    }

    public function testFindJobOfId()
    {
        $job = new Job(1, 'Assembly Job 1', 'Assemble product components', 'Not Started');

        $jobRepository = new InMemoryJobRepository([1 => $job]);

        $this->assertEquals($job, $jobRepository->findJobOfId(1));
    }

    public function testFindJobOfIdThrowsNotFoundException()
    {
        $jobRepository = new InMemoryJobRepository([]);
        
        $this->expectException(JobNotFoundException::class);
        $jobRepository->findJobOfId(999);
    }

    public function testSaveJob()
    {
        $job = new Job(1, 'Assembly Job 1', 'Assemble product components', 'Not Started');
        $jobRepository = new InMemoryJobRepository([]);

        $jobRepository->save($job);

        $this->assertEquals($job, $jobRepository->findJobOfId(1));
    }

    public function testUpdateJob()
    {
        $job = new Job(1, 'Assembly Job 1', 'Assemble product components', 'Not Started');
        $updatedJob = new Job(1, 'Assembly Job 1', 'Updated description', 'Started');
        
        $jobRepository = new InMemoryJobRepository([1 => $job]);
        $jobRepository->save($updatedJob);

        $this->assertEquals($updatedJob, $jobRepository->findJobOfId(1));
    }

    public function testDeleteJob()
    {
        $job = new Job(1, 'Assembly Job 1', 'Assemble product components', 'Not Started');
        $jobRepository = new InMemoryJobRepository([1 => $job]);

        $jobRepository->delete(1);

        $this->expectException(JobNotFoundException::class);
        $jobRepository->findJobOfId(1);
    }

    public function testFindJobsByStatus()
    {
        $jobs = [
            1 => new Job(1, 'Assembly Job 1', 'Assemble product components', 'Not Started'),
            2 => new Job(2, 'Quality Check', 'Perform quality inspection', 'Started'),
            3 => new Job(3, 'Packaging', 'Package finished products', 'Paused'),
            4 => new Job(4, 'Maintenance', 'Equipment maintenance', 'Problem'),
            5 => new Job(5, 'Shipping', 'Prepare for shipping', 'Finished'),
            6 => new Job(6, 'Cancelled Job', 'Cancelled due to issues', 'Canceled'),
        ];

        $jobRepository = new InMemoryJobRepository($jobs);

        $notStartedJobs = $jobRepository->findByStatus('Not Started');
        $this->assertEquals([$jobs[1]], $notStartedJobs);

        $startedJobs = $jobRepository->findByStatus('Started');
        $this->assertEquals([$jobs[2]], $startedJobs);

        $finishedJobs = $jobRepository->findByStatus('Finished');
        $this->assertEquals([$jobs[5]], $finishedJobs);
    }
} 