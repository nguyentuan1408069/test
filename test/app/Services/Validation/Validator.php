<?php

namespace App\Services\Validation;

use App\Exceptions\ValidationException;
use App\Services\Request\RequestInterface;
use App\Services\Validation\Interfaces\RuleInterface;
use App\Services\Validation\Interfaces\Validator as ValidatorInterface;

class Validator implements ValidatorInterface
{
    protected $rules;

    protected static $errorBag = [];

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function validate(RequestInterface $request): bool
    {
        collect($this->rules)->each(function ($rules, $attribute) use ($request) {
            collect($rules)->each(function (RuleInterface $rule) use ($request, $attribute) {
                $value = $request->getInput($attribute);

                if (! $rule->passes($attribute, $value)) {
                    self::$errorBag[$attribute][] = $rule->message($attribute, $value);
                }
            });
        });

        return count(self::$errorBag) === 0;
    }

    /**
     * @return mixed
     * @throws ValidationException
     */
    public function failedValidation()
    {
        throw new ValidationException(self::$errorBag);
    }
}