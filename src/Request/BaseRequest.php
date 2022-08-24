<?php

namespace App\Request;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRequest

{
    public $errors = "";
    /**
     * @throws Exception
     */
    public function __construct(protected ValidatorInterface $validator)
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
            $this->makeError((string)$errors);
        }
    }

    public function makeError(string $msg): void
    {
        $res = explode('.', $msg);
        foreach ($res as $key => $value){
            if ($key % 2 == 1){
                $this->errors .= $value.PHP_EOL;
            }
        }
    }

    public function getRequest(): array
    {
        $request = Request::createFromGlobals();
        if ($request->getMethod() === 'GET') {
            return $request->query->all();
        } else {
            return json_decode($request->getContent(), true);
        }
    }

    abstract public function populate(array $fields): void;

    protected abstract function autoValidateRequest(): bool;

}