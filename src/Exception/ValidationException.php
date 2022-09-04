<?php
namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends HttpException
{
    private $violations;

    public function __construct(ConstraintViolationListInterface $violations)
    {
        $this->violations = $violations;
        parent::__construct(Response::HTTP_BAD_REQUEST, 'Validation failed.');
    }

    public function getMessages()
    {
        $messages = [];
        foreach ($this->violations as $violation) {
            $messages[$violation->getPropertyPath()][] = $violation->getMessage();
        }
        return $messages;
    }

    public function getJoinedMessages()
    {
        $messages = [];
        foreach ($this->violations as $paramName => $violationList) {
            foreach ($violationList as $violation) {
                $messages[$paramName][] = $violation->getMessage();
            }
            $messages[$paramName] = implode(' ', $messages[$paramName]);
        }
        return $messages;
    }
}