<?php

namespace App\Validators;

use App\Helpers\UrlNameNormalizer;
use App\Repositories\UrlRepository;
use GuzzleHttp\Psr7\Exception\MalformedUriException;
use Valitron\Validator;

class UrlValidator
{
    public function validate(array $urlData): array
    {
        $urlName = $urlData['url']['name'];
        try {
            $urlName = UrlNameNormalizer::normalize($urlName);
        } catch (MalformedUriException $e) {
            return ['url.name' => 'Некорректный URL']; //$e->getMessage()];
        }
        $urlData['url']['name'] = $urlName;

        $validator = new Validator($urlData);
        $validator->rule('required', 'url.name')->message('URL не должен быть пустым');
        $validator->rule('lengthMax', 'url.name', 255)->message('Некорректный URL');
        $validator->rule('url', 'url.name')->message('Некорректный URL');
        $validator->validate();

        return (array)$validator->errors();
    }
}
