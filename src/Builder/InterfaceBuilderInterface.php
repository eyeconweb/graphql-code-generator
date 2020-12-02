<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator\Builder;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\InterfaceTypeDefinitionNode;
use Nette\PhpGenerator\PhpFile;

interface InterfaceBuilderInterface extends BuilderInterface
{
    /**
     * @param DocumentNode                $documentNode
     * @param InterfaceTypeDefinitionNode $definition
     * @param string                      $classNamespace
     *
     * @return PhpFile
     */
    public function build($documentNode, $definition, $classNamespace);
}
