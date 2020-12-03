<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator\Builder;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\InterfaceTypeDefinitionNode;
use Nette\PhpGenerator\PhpFile;

interface InterfaceBuilderInterface
{
    public function build(DocumentNode $documentNode, InterfaceTypeDefinitionNode $definition, string $classNamespace): PhpFile;
}
