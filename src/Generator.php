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

class Generator
{
    /** @var BuilderInterface[] */
    private $builders;

    /** @var string */
    private $classNamespace;

    public function __construct(string $classNamespace, ?EnumBuilderInterface $enumBuilder, ?InterfaceBuilderInterface $interfaceBuilder, ?ObjectBuilderInterface $objectBuilder, ?UnionBuilderInterface $unionBuilder)
    {
        $this->classNamespace = $classNamespace;

        $builders = [
            NodeKind::ENUM_TYPE_DEFINITION => $enumBuilder,
            NodeKind::INTERFACE_TYPE_DEFINITION => $interfaceBuilder,
            NodeKind::OBJECT_TYPE_DEFINITION => $objectBuilder,
            NodeKind::UNION_TYPE_DEFINITION => $unionBuilder,
        ];

        $this->builders = array_filter($builders, function ($value) {
            return $value !== null;
        });
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
            if ($definition instanceof TypeDefinitionNode && isset($this->builders[$definition->kind])) {
                /** @var BuilderInterface $builder */
                $builder = $this->builders[$definition->kind];
                $phpFile = $builder->build($documentNode, $definition, $this->classNamespace);

                $className = $definition->name->value;
                yield "{$this->classNamespace}\\{$className}" => $phpFile;
            }
        }
    }
}
