<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator\Builder;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\UnionTypeDefinitionNode;
use Nette\PhpGenerator\PhpFile;

interface UnionBuilderInterface extends BuilderInterface
{
    /**
     * @param DocumentNode            $documentNode
     * @param UnionTypeDefinitionNode $definition
     * @param string                  $classNamespace
     *
     * @return PhpFile
     */
    public function build($documentNode, $definition, $classNamespace);
}
