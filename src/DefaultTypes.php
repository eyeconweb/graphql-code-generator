<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator;

class DefaultTypes
{
    /** @var string[] */
    private $defaultTypes = [
        'String' => 'string',
        'ID' => 'string',
        'Float' => 'float',
        'Boolean' => 'bool',
        'Cursor' => 'string',
    ];

    /**
     * @param string[]|null $defaultTypes
     */
    public function __construct(array $defaultTypes = null)
    {
        if ($defaultTypes !== null) {
            $this->defaultTypes = $defaultTypes;
        }
    }

    public function typeToClass(string $type): string
    {
        return $this->defaultTypes[$type] ?? $type;
    }
}
