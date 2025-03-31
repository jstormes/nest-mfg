<?php

declare(strict_types=1);

namespace Tests\Domain\Job;

use App\Domain\Job\Job;
use Tests\TestCase;

class JobTest extends TestCase
{
    public function jobProvider(): array
    {
        return [
            [1, 'Assembly Job 1', 'Assemble product components', 'Not Started'],
            [2, 'Quality Check', 'Perform quality inspection', 'Started'],
            [3, 'Packaging', 'Package finished products', 'Paused'],
            [4, 'Maintenance', 'Equipment maintenance', 'Problem'],
            [5, 'Shipping', 'Prepare for shipping', 'Finished'],
            [6, 'Cancelled Job', 'Cancelled due to issues', 'Canceled'],
        ];
    }

    /**
     * @dataProvider jobProvider
     * @param int    $id
     * @param string $name
     * @param string $description
     * @param string $status
     */
    public function testGetters(int $id, string $name, string $description, string $status)
    {
        $job = new Job($id, $name, $description, $status);

        $this->assertEquals($id, $job->getId());
        $this->assertEquals($name, $job->getName());
        $this->assertEquals($description, $job->getDescription());
        $this->assertEquals($status, $job->getStatus());
    }

    /**
     * @dataProvider jobProvider
     * @param int    $id
     * @param string $name
     * @param string $description
     * @param string $status
     */
    public function testJsonSerialize(int $id, string $name, string $description, string $status)
    {
        $job = new Job($id, $name, $description, $status);

        $expectedPayload = json_encode([
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'status' => $status,
        ]);

        $this->assertEquals($expectedPayload, json_encode($job));
    }

    public function testStatusValidation()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Job(1, 'Test Job', 'Test Description', 'Invalid Status');
    }

    public function testEmptyNameValidation()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Job(1, '', 'Test Description', 'Not Started');
    }

    public function testEmptyStatusValidation()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Job(1, 'Test Job', 'Test Description', '');
    }
} 