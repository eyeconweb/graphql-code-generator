<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator\Builder;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\ObjectTypeDefinitionNode;
use Nette\PhpGenerator\PhpFile;

interface ObjectBuilderInterface
{
    public function build(DocumentNode $documentNode, ObjectTypeDefinitionNode $definition, string $classNamespace): PhpFile;
}
