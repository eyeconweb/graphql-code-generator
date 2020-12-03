<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator\Builder;

use Eyeconweb\GraphQL\Generator\ASTHelper;
use Eyeconweb\GraphQL\Generator\DefaultTypes;
use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\FieldDefinitionNode;
use GraphQL\Language\AST\NodeList;
use GraphQL\Language\AST\ObjectTypeDefinitionNode;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;

class ObjectBuilder implements ObjectBuilderInterface
{
    /** @var DefaultTypes */
    private $defaultTypes;

    public function __construct(DefaultTypes $defaultTypes)
    {
        $this->defaultTypes = $defaultTypes;
    }

    public function build(DocumentNode $documentNode, ObjectTypeDefinitionNode $definition, string $classNamespace): PhpFile
    {
        $file = new PhpFile();
        $file->setStrictTypes();

        $namespace = $file->addNamespace($classNamespace);

        $classType = $namespace->addClass($definition->name->value)
            ->setFinal()
        ;

        foreach ($definition->interfaces as $interface) {
            $classType->addImplement($interface->name->value);
        }

        if ($definition->fields !== null) {
            $this->buildProperties($definition->fields, $classType);
            $this->buildConstructor($definition->fields, $classType);
            $this->buildGetters($definition->fields, $classType);
        }

        return $file;
    }

    /**
     * @param NodeList<FieldDefinitionNode> $fields
     */
    private function buildProperties(NodeList $fields, ClassType $classType): void
    {
        foreach ($fields as $field) {
            $class = $this->defaultTypes->typeToClass(ASTHelper::getTypeName($field->type));
            $isArray = ASTHelper::isList($field->type);
            $nullable = !ASTHelper::isNonNullable($field->type);

            if ($field->arguments->count() !== 0) {
                $classType->addComment(sprintf('@graphql-resolver "%s"', $field->name->value));
                continue;
            }

            $property = $classType->addProperty($field->name->value)
                ->setPrivate()
                ->setType($isArray ? 'array' : $class)
                ->setNullable($nullable)
            ;

            if ($isArray) {
                $property->addComment(sprintf('@var %s[]', $class));
            }
        }
    }

    /**
     * @param NodeList<FieldDefinitionNode> $fields
     */
    private function buildConstructor(NodeList $fields, ClassType $classType): void
    {
        $constructor = $classType->addMethod('__construct');

        foreach ($fields as $field) {
            if ($field->arguments->count() !== 0) {
                continue;
            }
            $class = $this->defaultTypes->typeToClass(ASTHelper::getTypeName($field->type));
            $isArray = ASTHelper::isList($field->type);
            $nullable = !ASTHelper::isNonNullable($field->type);

            $constructor->addParameter($field->name->value)
                ->setType($isArray ? 'array' : $class)
                ->setNullable($nullable)
            ;

            $constructor->addBody('$this->? = $?;', [$field->name->value, $field->name->value]);

            if ($isArray) {
                $constructor->addComment(sprintf('@param %s[] $%s', $class, $field->name->value));
            }
        }

        if (\count($constructor->getParameters()) === 0) {
            $classType->removeMethod($constructor->getName());
        }
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
                ->addBody(sprintf('return $this->%s;', $field->name->value))
                ->setPublic()
            ;

            if ($isArray) {
                $method->addComment(sprintf('@return %s[]', $class));
            }
        }
    }
}
