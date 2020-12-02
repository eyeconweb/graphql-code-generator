<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator;

use Eyeconweb\GraphQL\Generator\Builder\EnumBuilder;
use Eyeconweb\GraphQL\Generator\Builder\EnumBuilderInterface;
use Eyeconweb\GraphQL\Generator\Builder\InterfaceBuilder;
use Eyeconweb\GraphQL\Generator\Builder\InterfaceBuilderInterface;
use Eyeconweb\GraphQL\Generator\Builder\ObjectBuilder;
use Eyeconweb\GraphQL\Generator\Builder\ObjectBuilderInterface;
use Eyeconweb\GraphQL\Generator\Builder\UnionBuilder;
use Eyeconweb\GraphQL\Generator\Builder\UnionBuilderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @covers \Eyeconweb\GraphQL\Generator\Generator
 */
class GeneratorTest extends TestCase
{
    public function testGenerateFromSchema(): void
    {
        $schema = (string) file_get_contents('tests/schema.graphql');

        $serviceLocator = new ServiceLocator([
            ObjectBuilderInterface::class => function () { return new ObjectBuilder(new DefaultTypes()); },
            EnumBuilderInterface::class => function () { return new EnumBuilder(); },
            InterfaceBuilderInterface::class => function () { return new InterfaceBuilder(new DefaultTypes()); },
            UnionBuilderInterface::class => function () { return new UnionBuilder(); },
        ]);

        $generator = new Generator($serviceLocator, 'TestNamespace');
        $files = $generator->generateFromSchema($schema);

        $count = 0;
        foreach ($files as $phpFile) {
            $this->assertTrue($phpFile->hasStrictTypes());
            ++$count;
        }

        $this->assertSame(7, $count);
    }
}
