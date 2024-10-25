<?php

namespace App\Validators;

use App\Helpers\UrlNameNormalizer;
use GuzzleHttp\Psr7\Exception\MalformedUriException;
use Valitron\Validator;

class UrlValidator
{
    public function validate(string $url): array
    {
        $validator = new Validator(['url' => $url]);
        $validator->rule('required', 'url')->message('URL не должен быть пустым');
        $validator->rule('lengthMax', 'url', 255)->message('Некорректный URL');
        $validator->rule('url', 'url')->message('Некорректный URL');
        $validator->validate();

        return (array)$validator->errors();
    }
}
