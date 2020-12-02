<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator\Builder;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\TypeDefinitionNode;
use Nette\PhpGenerator\PhpFile;

interface BuilderInterface
{
    /**
     * @param DocumentNode       $documentNode
     * @param TypeDefinitionNode $definition
     * @param string             $classNamespace
     *
     * @return PhpFile
     */
    public function build($documentNode, $definition, $classNamespace);
}
