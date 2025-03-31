<?php

declare(strict_types=1);

namespace App\Domain\Job;

use App\Domain\DomainException\DomainRecordNotFoundException;

class JobNotFoundException extends DomainRecordNotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("Job not found with ID: {$id}");
    }
} 