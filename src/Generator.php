<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator;

use Eyeconweb\GraphQL\Generator\Builder\EnumBuilderInterface;
use Eyeconweb\GraphQL\Generator\Builder\InputObjectBuilderInterface;
use Eyeconweb\GraphQL\Generator\Builder\InterfaceBuilderInterface;
use Eyeconweb\GraphQL\Generator\Builder\ObjectBuilderInterface;
use Eyeconweb\GraphQL\Generator\Builder\ScalarBuilderInterface;
use Eyeconweb\GraphQL\Generator\Builder\UnionBuilderInterface;
use GraphQL\Language\AST\EnumTypeDefinitionNode;
use GraphQL\Language\AST\InputObjectTypeDefinitionNode;
use GraphQL\Language\AST\InterfaceTypeDefinitionNode;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\ObjectTypeDefinitionNode;
use GraphQL\Language\AST\ScalarTypeDefinitionNode;
use GraphQL\Language\AST\TypeDefinitionNode;
use GraphQL\Language\AST\UnionTypeDefinitionNode;
use GraphQL\Language\Parser;
use Nette\PhpGenerator\PhpFile;

class Generator
{
    /** @var mixed[] */
    private $builders;

    /** @var string */
    private $classNamespace;

    public function __construct(string $classNamespace, ?EnumBuilderInterface $enumBuilder, ?InterfaceBuilderInterface $interfaceBuilder, ?ObjectBuilderInterface $objectBuilder, ?UnionBuilderInterface $unionBuilder, ?ScalarBuilderInterface $scalarBuilder, ?InputObjectBuilderInterface $inputObjectBuilder)
    {
        $this->classNamespace = $classNamespace;

        $builders = [
            EnumTypeDefinitionNode::class => $enumBuilder,
            InterfaceTypeDefinitionNode::class => $interfaceBuilder,
            ObjectTypeDefinitionNode::class => $objectBuilder,
            UnionTypeDefinitionNode::class => $unionBuilder,
            ScalarTypeDefinitionNode::class => $scalarBuilder,
            InputObjectTypeDefinitionNode::class => $inputObjectBuilder,
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
            $builder = $this->builders[\get_class($definition)] ?? null;
            if ($builder !== null) {
                /** @var TypeDefinitionNode $definition */
                $phpFile = $builder->build($documentNode, $definition, $this->classNamespace);

                $className = $definition->name->value;
                yield "{$this->classNamespace}\\{$className}" => $phpFile;
            }
        }
    }
}
