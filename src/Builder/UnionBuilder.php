<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator\Builder;

use Eyeconweb\GraphQL\Generator\Exception\UnexpectedDefinitionTypeException;
use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\NamedTypeNode;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Language\AST\NodeList;
use GraphQL\Language\AST\TypeDefinitionNode;
use GraphQL\Language\AST\UnionTypeDefinitionNode;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;

class UnionBuilder implements BuilderInterface
{
    public static function getTypeDefinition(): string
    {
        return NodeKind::UNION_TYPE_DEFINITION;
    }

    public function build(DocumentNode $documentNode, TypeDefinitionNode $definition, string $classNamespace): PhpFile
    {
        if (!$definition instanceof UnionTypeDefinitionNode) {
            throw new UnexpectedDefinitionTypeException(UnionTypeDefinitionNode::class, $definition);
        }

        $file = new PhpFile();
        $file->setStrictTypes();

        $namespace = $file->addNamespace($classNamespace)
            ->addUse('Symfony\Component\Serializer\Annotation\DiscriminatorMap')
        ;

        $classType = $namespace->addClass($definition->name->value)
            ->setFinal()
        ;

        if ($definition->types !== null) {
            $this->buildGetClassesMethod($definition->types, $classType);
            $this->buildDiscriminatorMap($definition->types, $classType);
        }

        return $file;
    }

    /**
     * @param NodeList<NamedTypeNode> $types
     */
    private function buildGetClassesMethod(NodeList $types, ClassType $classType): void
    {
        $classes = array_map(function ($type): string {
            return "{$type->name->value}::class";
        }, iterator_to_array($types));
        $classType->addMethod('getClasses')
            ->setReturnType('array')
            ->addBody(sprintf('return [%s];', implode(', ', $classes)))
            ->setStatic()
            ->setPublic()
            ->addComment("@phpstan-return array<int, class-string>\n")
            ->addComment('@return string[]')
        ;
    }

    /**
     * @param NodeList<NamedTypeNode> $types
     */
    private function buildDiscriminatorMap(NodeList $types, ClassType $classType): void
    {
        $classType->addComment('@DiscriminatorMap(typeProperty = "type", mapping = {');
        foreach ($types as $type) {
            $classType->addComment("    \"{$type->name->value}\" : {$type->name->value}::class,");
        }
        $classType->addComment('})');
    }
}
