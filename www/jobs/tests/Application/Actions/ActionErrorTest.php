<?php

declare(strict_types=1);

namespace Tests\Application\Actions;

use App\Application\Actions\ActionError;
use PHPUnit\Framework\TestCase;

class ActionErrorTest extends TestCase
{
    public function testActionErrorCreation(): void
    {
        $error = new ActionError(
            ActionError::VALIDATION_ERROR,
            'Invalid input'
        );

        $this->assertEquals(ActionError::VALIDATION_ERROR, $error->getType());
        $this->assertEquals('Invalid input', $error->getDescription());
    }

    public function testActionErrorTypes(): void
    {
        $this->assertEquals('VALIDATION_ERROR', ActionError::VALIDATION_ERROR);
        $this->assertEquals('RESOURCE_NOT_FOUND', ActionError::RESOURCE_NOT_FOUND);
        $this->assertEquals('SERVER_ERROR', ActionError::SERVER_ERROR);
        $this->assertEquals('UNAUTHORIZED', ActionError::UNAUTHORIZED);
        $this->assertEquals('FORBIDDEN', ActionError::FORBIDDEN);
        $this->assertEquals('CONFLICT', ActionError::CONFLICT);
    }

    public function testActionErrorSerialization(): void
    {
        $error = new ActionError(
            ActionError::VALIDATION_ERROR,
            'Invalid input'
        );

        $serialized = json_encode($error);
        $this->assertIsString($serialized);

        $decoded = json_decode($serialized, true);
        $this->assertEquals(ActionError::VALIDATION_ERROR, $decoded['type']);
        $this->assertEquals('Invalid input', $decoded['description']);
    }
} 