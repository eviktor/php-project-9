<?php

namespace App\Validators;

use App\Helpers\UrlNameNormalizer;
use App\Repositories\UrlRepository;
use Valitron\Validator;

class UrlValidator
{
    public function __construct(protected UrlRepository $urlRepository)
    {
    }

    public function validate(array $urlData): array
    {
        $urlData['url']['name'] = UrlNameNormalizer::normalize($urlData['url']['name']);

        $validator = new Validator($urlData);

        $validator->mapFieldRules('url.name', [
            'required',
            ['lengthMax', 255],
            'url'
        ]);

        $validator->rule(function ($field, $value, $params, $fields) {
            $url = $this->urlRepository->findByName($value);
            return $url === null;
        }, "url.name")->message("{field} already exists...");

        $validator->validate();

        return (array)$validator->errors();
    }
}
