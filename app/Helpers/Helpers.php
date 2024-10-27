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
            return $default;
        }
        return parseEnvValue($value);
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
