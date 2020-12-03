<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator\Builder;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\ScalarTypeDefinitionNode;
use Nette\PhpGenerator\PhpFile;

interface ScalarBuilderInterface
{
    public function build(DocumentNode $documentNode, ScalarTypeDefinitionNode $definition, string $classNamespace): PhpFile;
}
