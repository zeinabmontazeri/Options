<?php
namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends \Exception
{
    private $violations;

    public function __construct(ConstraintViolationListInterface $violations)
    {
        $this->violations = $violations;
        parent::__construct('Validation failed.');
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