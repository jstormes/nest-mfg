<?php

declare(strict_types=1);

namespace App\Domain\Job;

interface JobRepository
{
    public function findAll(): array;
    public function findJobOfId(int $id): Job;
    public function save(Job $job): void;
    public function delete(int $id): void;
    public function findByStatus(string $status): array;
} 