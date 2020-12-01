<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator\Exception;

class UnexpectedDefinitionTypeException extends \LogicException
{
    /**
     * @param mixed $definition
     */
    public function __construct(string $expected, $definition)
    {
        parent::__construct(
            sprintf(
                'Wrong definition type. Instance of "%s" is expected, but "%s" given',
                $expected,
                \is_object($definition) ? \get_class($definition) : \gettype($definition)
            )
        );
    }
}
