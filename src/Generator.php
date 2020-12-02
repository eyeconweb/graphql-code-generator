<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator;

use Eyeconweb\GraphQL\Generator\Builder\BuilderInterface;
use Eyeconweb\GraphQL\Generator\Builder\EnumBuilderInterface;
use Eyeconweb\GraphQL\Generator\Builder\InterfaceBuilderInterface;
use Eyeconweb\GraphQL\Generator\Builder\ObjectBuilderInterface;
use Eyeconweb\GraphQL\Generator\Builder\UnionBuilderInterface;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\NodeKind;
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
     * @return string[]
     * @phpstan-return array<string,class-string<BuilderInterface>>
     */
    public static function getSubscribedServices(): array
    {
        return [
            NodeKind::ENUM_TYPE_DEFINITION => EnumBuilderInterface::class,
            NodeKind::INTERFACE_TYPE_DEFINITION => InterfaceBuilderInterface::class,
            NodeKind::OBJECT_TYPE_DEFINITION => ObjectBuilderInterface::class,
            NodeKind::UNION_TYPE_DEFINITION => UnionBuilderInterface::class,
        ];
    }

    /**
     * @return \Traversable<string, PhpFile>
     */
    public function generateFromSchema(string $schema): \Traversable
    {
        $documentNode = Parser::parse(
            $schema,
            // Ignore location since it only bloats the AST
            ['noLocation' => true]
        );

        /** @var Node $definition */
        foreach ($documentNode->definitions as $definition) {
            $builderInterface = self::getSubscribedServices()[$definition->kind] ?? null;
            if ($builderInterface !== null && $definition instanceof TypeDefinitionNode && $this->container->has($builderInterface)) {
                /** @var BuilderInterface $builder */
                $builder = $this->container->get($builderInterface);

                $phpFile = $builder->build($documentNode, $definition, $this->classNamespace);

                $className = $definition->name->value;
                yield "{$this->classNamespace}\\{$className}" => $phpFile;
            }
        }
    }
}
