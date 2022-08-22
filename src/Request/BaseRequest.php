<?php

namespace App\Request;

use App\Exception\ValidationException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRequest

{

    /**
     * @throws Exception
     */
    public function __construct(protected ValidatorInterface $validator)
    {
        $this->populate($this->getRequest()->toArray());
        if ($this->autoValidateRequest()) {
            $this->validate();
        }
    }

    /**
     * @throws Exception
     */
    public function validate(): void
    {
        $errors = $this->validator->validate($this);
        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }
    }

    public function getRequest(): Request
    {
        return Request::createFromGlobals();
    }

    abstract public function populate(array $fields): void;

    protected abstract function autoValidateRequest(): bool;

}