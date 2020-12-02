<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator\Builder;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\ObjectTypeDefinitionNode;
use Nette\PhpGenerator\PhpFile;

interface ObjectBuilderInterface extends BuilderInterface
{
    /**
     * @param DocumentNode             $documentNode
     * @param ObjectTypeDefinitionNode $definition
     * @param string                   $classNamespace
     *
     * @return PhpFile
     */
    public function build($documentNode, $definition, $classNamespace);
}
