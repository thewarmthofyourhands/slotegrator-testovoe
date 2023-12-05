<?php

declare(strict_types=1);

namespace App\Validation;

use App\Exceptions\Validation\ValidatorException;
use JsonException;
use JsonSchema\Validator as JsonValidator;

use stdClass;

class Validator
{
    private readonly JsonValidator $validator;

    public function __construct()
    {
        $this->validator = new JsonValidator();
    }

    /**
     * @throws ValidatorException
     * @throws JsonException
     */
    public function validate(null|stdClass|array $data, array $validationData): void
    {
        if (is_array($data)) {
            $data = json_decode(json_encode($data), false);
        }

        $this->validator->validate($data, $validationData);

        if (false === $this->validator->isValid()) {
            $errorList = $this->validator->getErrors();
            $this->validator->reset();
            throw new ValidatorException($errorList);
        }

        $this->validator->reset();
    }
}
