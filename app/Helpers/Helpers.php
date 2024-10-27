<?php

declare(strict_types=1);

// goes from https://github.com/ricardoper/slim4-twig-skeleton/blob/master/app/Kernel/Helpers/helpers.php

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            $value = $_ENV[$key] ?? $_SERVER[$key] ?? null;
        }
        return is_null($value) ? $default : parseEnvValue($value);
    }

    function parseEnvValue(string $value): mixed
    {
        $specialValues = [
            'true' => true,
            '(true)' => true,
            'false' => false,
            '(false)' => false,
            'empty' => '',
            '(empty)' => '',
            'null' => null,
            '(null)' => null,
        ];

        $lowerValue = strtolower($value);
        if (array_key_exists($lowerValue, $specialValues)) {
            return $specialValues[$lowerValue];
        }

        if (strlen($value) > 1 && $value[0] === '"' && $value[-1] === '"') {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

if (!function_exists('base_path')) {
    /**
     * Get the path to the base folder
     *
     * @param string $path
     * @return string
     */
    function base_path(string $path = ''): string
    {
        if ($path !== '' && $path[0] != '/') {
            $path = '/' . $path;
        }

        return realpath(__DIR__ . '/../..') . $path;
    }
}
