<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator\Builder;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\EnumTypeDefinitionNode;
use GraphQL\Language\AST\EnumValueDefinitionNode;
use GraphQL\Language\AST\NodeList;
use MyCLabs\Enum\Enum;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;

class EnumBuilder implements EnumBuilderInterface
{
    public function build(DocumentNode $documentNode, EnumTypeDefinitionNode $definition, string $classNamespace): PhpFile
    {
        $file = new PhpFile();
        $file->setStrictTypes();

        $namespace = $file->addNamespace($classNamespace)
            ->addUse(Enum::class)
        ;

        $classType = $namespace->addClass($definition->name->value)
            ->setFinal()
            ->addExtend('Enum')
        ;

        if ($definition->values !== null) {
            $this->buildDocBlock($definition->values, $classType);
            $this->buildConstants($definition->values, $classType);
        }

        return $file;
    }

    /**
     * @param NodeList<EnumValueDefinitionNode> $valueNodes
     */
    private function buildDocBlock(NodeList $valueNodes, ClassType $classType): void
    {
        $classType->addComment("@phpstan-extends Enum<string>\n");
        foreach ($valueNodes as $node) {
            $classType->addComment("@method static self {$node->name->value}()");
        }
    }

    /**
     * @param NodeList<EnumValueDefinitionNode> $valueNodes
     */
    private function buildConstants(NodeList $valueNodes, ClassType $class): void
    {
        foreach ($valueNodes as $node) {
            $class->addConstant($node->name->value, $node->name->value)->setPublic();
        }
    }
}
