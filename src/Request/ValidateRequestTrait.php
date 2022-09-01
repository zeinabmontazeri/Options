<?php

namespace App\Request;

use App\Factories\Transformer\DataTypeFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ValidateRequestTrait
{
    /**
     * @throws Exception
     */
    public function populate(array $fields): void
    {
        $typeFactory = new DataTypeFactory();
        foreach ($fields as $field => $value) {
            if (property_exists($this, $field)) {
                $refProperty = new \ReflectionProperty($this, $field);
                try {
                    $object = $typeFactory->getObject($refProperty->getType()->getName());
                }catch (Exception $exception){
                    if ($exception instanceof NotFoundHttpException)
                        throw new NotFoundHttpException("$field type not found!");
                }
                $this->{$field} = $object->ConvertToObject($value);
            }
        }
    }
}