<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator\Builder;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\EnumTypeDefinitionNode;
use Nette\PhpGenerator\PhpFile;

interface EnumBuilderInterface extends BuilderInterface
{
    /**
     * @param DocumentNode           $documentNode
     * @param EnumTypeDefinitionNode $definition
     * @param string                 $classNamespace
     *
     * @return PhpFile
     */
    public function build($documentNode, $definition, $classNamespace);
}
