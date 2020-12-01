<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator\Builder;

use Eyeconweb\GraphQL\Generator\ASTHelper;
use Eyeconweb\GraphQL\Generator\DefaultTypes;
use Eyeconweb\GraphQL\Generator\Exception\UnexpectedDefinitionTypeException;
use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\FieldDefinitionNode;
use GraphQL\Language\AST\InterfaceTypeDefinitionNode;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Language\AST\NodeList;
use GraphQL\Language\AST\ObjectTypeDefinitionNode;
use GraphQL\Language\AST\TypeDefinitionNode;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;

class InterfaceBuilder implements BuilderInterface
{
    /** @var DefaultTypes */
    private $defaultTypes;

    public function __construct(DefaultTypes $defaultTypes)
    {
        $this->defaultTypes = $defaultTypes;
    }

    public static function getTypeDefinition(): string
    {
        return NodeKind::INTERFACE_TYPE_DEFINITION;
    }

    public function build(DocumentNode $documentNode, TypeDefinitionNode $definition, string $classNamespace): PhpFile
    {
        if (!$definition instanceof InterfaceTypeDefinitionNode) {
            throw new UnexpectedDefinitionTypeException(InterfaceTypeDefinitionNode::class, $definition);
        }

        $file = new PhpFile();
        $file->setStrictTypes();

        $namespace = $file->addNamespace($classNamespace)
            ->addUse('Symfony\Component\Serializer\Annotation\DiscriminatorMap')
        ;

        $classType = $namespace->addClass($definition->name->value)
            ->setInterface()
        ;

        if ($definition->fields !== null) {
            $this->buildGetters($definition->fields, $classType);
            $this->buildDiscriminatorMap(ASTHelper::findNodesByInterfaceName($documentNode->definitions, $definition->name->value), $classType);
        }

        return $file;
    }

    /**
     * @param NodeList<FieldDefinitionNode> $fields
     */
    private function buildGetters(NodeList $fields, ClassType $classType): void
    {
        foreach ($fields as $field) {
            if ($field->arguments->count() !== 0) {
                continue;
            }
            $class = $this->defaultTypes->typeToClass(ASTHelper::getTypeName($field->type));
            $isArray = ASTHelper::isList($field->type);
            $nullable = !ASTHelper::isNonNullable($field->type);
            $method = $classType->addMethod(sprintf('get%s', ucfirst($field->name->value)))
                ->setReturnType($isArray ? 'array' : $class)
                ->setReturnNullable($nullable)
                ->setPublic()
            ;

            if ($isArray) {
                $method->addComment(sprintf('@return %s[]', $class));
            }
        }
    }

    /**
     * @param ObjectTypeDefinitionNode[] $nodes
     */
    private function buildDiscriminatorMap(array $nodes, ClassType $classType): void
    {
        $classType->addComment('@DiscriminatorMap(typeProperty = "type", mapping = {');
        foreach ($nodes as $node) {
            $classType->addComment("    \"{$node->name->value}\" : {$node->name->value}::class,");
        }
        $classType->addComment('})');
    }
}
