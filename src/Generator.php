<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator;

use Eyeconweb\GraphQL\Generator\Builder\BuilderInterface;
use GraphQL\Language\AST\TypeDefinitionNode;
use GraphQL\Language\Parser;
use Nette\PhpGenerator\PhpFile;
use Psr\Container\ContainerInterface;

class Generator
{
    /** @var ContainerInterface */
    private $container;

    /** @var string */
    private $classNamespace;

    public function __construct(ContainerInterface $container, string $classNamespace)
    {
        $this->container = $container;
        $this->classNamespace = $classNamespace;
    }

    /**
     * @phpstan-return \Traversable<string, PhpFile>
     */
    public function generateFromSchema(string $schema): \Traversable
    {
        $documentNode = Parser::parse(
            $schema,
            // Ignore location since it only bloats the AST
            ['noLocation' => true]
        );

        foreach ($documentNode->definitions as $definition) {
            if ($definition instanceof TypeDefinitionNode && $this->container->has($definition->kind)) {
                /** @var BuilderInterface */
                $builder = $this->container->get($definition->kind);
                $phpFile = $builder->build($documentNode, $definition, $this->classNamespace);

                $className = $definition->name->value;
                yield "{$this->classNamespace}\\{$className}" => $phpFile;
            }
        }
    }
}
