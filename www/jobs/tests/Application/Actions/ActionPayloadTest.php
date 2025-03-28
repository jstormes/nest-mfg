<?php

declare(strict_types=1);

namespace Tests\Application\Actions;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use PHPUnit\Framework\TestCase;

class ActionPayloadTest extends TestCase
{
    public function testActionPayloadCreation(): void
    {
        $data = ['test' => 'data'];
        $payload = new ActionPayload(200, $data);

        $this->assertEquals(200, $payload->getStatusCode());
        $this->assertEquals($data, $payload->getData());
    }

    public function testActionPayloadWithError(): void
    {
        $error = new ActionError(
            ActionError::VALIDATION_ERROR,
            'Invalid input'
        );
        $payload = new ActionPayload(400, null, $error);

        $this->assertEquals(400, $payload->getStatusCode());
        $this->assertNull($payload->getData());
        $this->assertEquals($error, $payload->getError());
    }

    public function testActionPayloadSerialization(): void
    {
        $data = ['test' => 'data'];
        $payload = new ActionPayload(200, $data);

        $serialized = json_encode($payload);
        $this->assertIsString($serialized);

        $decoded = json_decode($serialized, true);
        $this->assertEquals(200, $decoded['statusCode']);
        $this->assertEquals($data, $decoded['data']);
    }

    public function testActionPayloadWithErrorSerialization(): void
    {
        $error = new ActionError(
            ActionError::VALIDATION_ERROR,
            'Invalid input'
        );
        $payload = new ActionPayload(400, null, $error);

        $serialized = json_encode($payload);
        $this->assertIsString($serialized);

        $decoded = json_decode($serialized, true);
        $this->assertEquals(400, $decoded['statusCode']);
        $this->assertArrayNotHasKey('data', $decoded);
        $this->assertEquals(ActionError::VALIDATION_ERROR, $decoded['error']['type']);
        $this->assertEquals('Invalid input', $decoded['error']['description']);
    }
} 