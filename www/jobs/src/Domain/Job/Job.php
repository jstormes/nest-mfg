<?php

declare(strict_types=1);

namespace App\Domain\Job;

use JsonSerializable;

class Job implements JsonSerializable
{
    private const VALID_STATUSES = [
        'Not Started',
        'Started',
        'Paused',
        'Problem',
        'Finished',
        'Canceled'
    ];

    public function __construct(
        private int $id,
        private string $name,
        private string $description,
        private string $status
    ) {
        $this->validateName($name);
        $this->validateStatus($status);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    private function validateName(string $name): void
    {
        if (empty(trim($name))) {
            throw new \InvalidArgumentException('Job name cannot be empty');
        }
    }

    private function validateStatus(string $status): void
    {
        if (empty(trim($status))) {
            throw new \InvalidArgumentException('Status cannot be empty');
        }

        if (!in_array($status, self::VALID_STATUSES)) {
            throw new \InvalidArgumentException('Invalid status value');
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
        ];
    }
} 