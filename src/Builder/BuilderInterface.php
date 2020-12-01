<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator\Builder;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\TypeDefinitionNode;
use Nette\PhpGenerator\PhpFile;

interface BuilderInterface
{
    public function build(DocumentNode $documentNode, TypeDefinitionNode $definition, string $classNamespace): PhpFile;

    public static function getTypeDefinition(): string;
}
