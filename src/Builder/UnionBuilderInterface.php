<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator\Builder;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\UnionTypeDefinitionNode;
use Nette\PhpGenerator\PhpFile;

interface UnionBuilderInterface
{
    public function build(DocumentNode $documentNode, UnionTypeDefinitionNode $definition, string $classNamespace): PhpFile;
}
