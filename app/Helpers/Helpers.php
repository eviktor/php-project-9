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
        $parsedValue = $value;

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                $parsedValue = true;
                break;
            case 'false':
            case '(false)':
                $parsedValue = false;
                break;
            case 'empty':
            case '(empty)':
                $parsedValue = '';
                break;
            case 'null':
            case '(null)':
                $parsedValue = null;
                break;
            default:
                if (strlen($value) > 1 && $value[0] === '"' && $value[-1] === '"') {
                    $parsedValue = substr($value, 1, -1);
                }
                break;
        }

        return $parsedValue;
    }
}
