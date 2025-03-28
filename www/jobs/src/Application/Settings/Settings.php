<?php

declare(strict_types=1);

namespace App\Application\Settings;

class Settings implements SettingsInterface
{
    private array $settings;

    public function __construct(array $settings)
    {
        $this->settings = array_merge([
            'displayErrorDetails' => false,
            'logErrors' => false,
            'logErrorDetails' => false,
        ], $settings);
    }

    public function get(string $key = ''): mixed
    {
        if (empty($key)) {
            return $this->settings;
        }

        if (str_contains($key, '.')) {
            $keys = explode('.', $key);
            $value = $this->settings;
            foreach ($keys as $k) {
                if (!isset($value[$k])) {
                    return null;
                }
                $value = $value[$k];
            }
            return $value;
        }

        return $this->settings[$key] ?? null;
    }

    public function getAll(): array
    {
        return $this->settings;
    }
}
