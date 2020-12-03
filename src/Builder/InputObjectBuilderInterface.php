<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator\Builder;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\InputObjectTypeDefinitionNode;
use Nette\PhpGenerator\PhpFile;

interface InputObjectBuilderInterface
{
    public function build(DocumentNode $documentNode, InputObjectTypeDefinitionNode $definition, string $classNamespace): PhpFile;
}
