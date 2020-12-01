<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator;

use Eyeconweb\GraphQL\Generator\Exception\DefinitionException;
use GraphQL\Language\AST\DefinitionNode;
use GraphQL\Language\AST\ListTypeNode;
use GraphQL\Language\AST\NamedTypeNode;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\NodeList;
use GraphQL\Language\AST\NonNullTypeNode;
use GraphQL\Language\AST\ObjectTypeDefinitionNode;
use GraphQL\Language\AST\TypeNode;

class ASTHelper
{
    public static function getTypeName(TypeNode $node): string
    {
        $namedTypeNode = self::getNamedTypeNode($node);

        return $namedTypeNode->name->value;
    }

    public static function isNonNullable(TypeNode $node): bool
    {
        return self::hasWrappingTypeNode($node, NonNullTypeNode::class);
    }

    public static function isList(TypeNode $node): bool
    {
        return self::hasWrappingTypeNode($node, ListTypeNode::class);
    }

    public static function getNamedTypeNode(TypeNode $node): NamedTypeNode
    {
        if ($node instanceof NamedTypeNode) {
            return $node;
        }

        return self::getNamedTypeNode(self::getTypeNode($node));
    }

    public static function hasWrappingTypeNode(TypeNode $node, string $wrappingType): bool
    {
        if ($node instanceof $wrappingType) {
            return true;
        }

        if (!property_exists($node, 'type')) {
            return false;
        }

        return self::hasWrappingTypeNode(self::getTypeNode($node), $wrappingType);
    }

    /**
     * @param NodeList<DefinitionNode&Node> $definitions
     *
     * @return ObjectTypeDefinitionNode[]
     */
    public static function findNodesByInterfaceName(NodeList $definitions, string $interfaceName): array
    {
        $nodes = [];
        foreach ($definitions as $definition) {
            if ($definition instanceof ObjectTypeDefinitionNode) {
                foreach ($definition->interfaces as $interface) {
                    if ($interface->name->value === $interfaceName) {
                        $nodes[] = $definition;
                    }
                }
            }
        }

        return $nodes;
    }

    protected static function getTypeNode(TypeNode $node): TypeNode
    {
        if (!property_exists($node, 'type')) {
            throw new DefinitionException(sprintf('The node "%s" does not have a type associated with it.', $node->kind));
        }

        return $node->type;
    }
}
