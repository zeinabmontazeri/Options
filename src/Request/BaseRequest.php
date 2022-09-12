<?php

namespace App\Request;

use App\Exception\ValidationException;
use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRequest

{

    /**
     * @throws Exception
     */
    public function __construct(protected ValidatorInterface $validator,protected RequestStack $requestStack)
    {
        $this->populate($this->getRequest());
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

    public function getRequest(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request->getMethod() === 'GET') {
            return $request->query->all();
        } else {
            # request is form-data with uploaded files
            $files = $request->files->all();
            if ($files)
                return $files;
            # request is json
            else {
                $payload = json_decode($request->getContent(), true);
                if ($payload === null) {
                    throw new BadRequestException('Invalid JSON payload.');
                } else {
                    return $payload;
                }
            }
        }
    }

    abstract public function populate(array $fields): void;

    protected abstract function autoValidateRequest(): bool;
}