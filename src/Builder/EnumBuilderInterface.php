<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator\Builder;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\EnumTypeDefinitionNode;
use Nette\PhpGenerator\PhpFile;

interface EnumBuilderInterface
{
    public function build(DocumentNode $documentNode, EnumTypeDefinitionNode $definition, string $classNamespace): PhpFile;
}
