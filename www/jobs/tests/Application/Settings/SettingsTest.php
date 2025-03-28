<?php

declare(strict_types=1);

namespace Tests\Application\Settings;

use App\Application\Settings\Settings;
use PDO;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{
    public function testSettingsCreation(): void
    {
        $settings = [
            'displayErrorDetails' => true,
            'logErrors' => true,
            'logErrorDetails' => true,
            'db' => [
                'driver' => 'mysql',
                'host' => 'localhost',
                'database' => 'test',
                'username' => 'test',
                'password' => 'test',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'flags' => [
                    PDO::ATTR_PERSISTENT => false,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => true,
                ],
            ],
        ];

        $settingsObject = new Settings($settings);

        $this->assertTrue($settingsObject->get('displayErrorDetails'));
        $this->assertTrue($settingsObject->get('logErrors'));
        $this->assertTrue($settingsObject->get('logErrorDetails'));
        $this->assertEquals('mysql', $settingsObject->get('db.driver'));
        $this->assertEquals('localhost', $settingsObject->get('db.host'));
        $this->assertEquals('test', $settingsObject->get('db.database'));
        $this->assertEquals('test', $settingsObject->get('db.username'));
        $this->assertEquals('test', $settingsObject->get('db.password'));
        $this->assertEquals('utf8mb4', $settingsObject->get('db.charset'));
        $this->assertEquals('utf8mb4_unicode_ci', $settingsObject->get('db.collation'));
        $this->assertIsArray($settingsObject->get('db.flags'));
    }

    public function testSettingsWithDefaultValues(): void
    {
        $settings = new Settings([]);

        $this->assertFalse($settings->get('displayErrorDetails'));
        $this->assertFalse($settings->get('logErrors'));
        $this->assertFalse($settings->get('logErrorDetails'));
    }

    public function testSettingsGetAll(): void
    {
        $settings = [
            'displayErrorDetails' => true,
            'logErrors' => true,
            'logErrorDetails' => true,
        ];

        $settingsObject = new Settings($settings);
        $allSettings = $settingsObject->getAll();

        $this->assertEquals($settings, $allSettings);
    }

    public function testSettingsGetNonExistentKey(): void
    {
        $settings = new Settings([]);

        $this->assertNull($settings->get('non_existent_key'));
    }

    public function testSettingsGetNestedKey(): void
    {
        $settings = [
            'db' => [
                'host' => 'localhost',
                'port' => 3306,
            ],
        ];

        $settingsObject = new Settings($settings);

        $this->assertEquals('localhost', $settingsObject->get('db.host'));
        $this->assertEquals(3306, $settingsObject->get('db.port'));
        $this->assertNull($settingsObject->get('db.non_existent'));
    }
} 