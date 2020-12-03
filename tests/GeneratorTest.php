<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator;

use Eyeconweb\GraphQL\Generator\Builder\EnumBuilder;
use Eyeconweb\GraphQL\Generator\Builder\InterfaceBuilder;
use Eyeconweb\GraphQL\Generator\Builder\ObjectBuilder;
use Eyeconweb\GraphQL\Generator\Builder\UnionBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Eyeconweb\GraphQL\Generator\Generator
 */
class GeneratorTest extends TestCase
{
    public function testGenerateFromSchema(): void
    {
        $schema = (string) file_get_contents('tests/schema.graphql');

        $generator = new Generator(
            'TestNamespace',
            new EnumBuilder(),
            new InterfaceBuilder(new DefaultTypes()),
            new ObjectBuilder(new DefaultTypes()),
            new UnionBuilder(),
            null,
            null
        );
        $files = $generator->generateFromSchema($schema);

        $count = 0;
        foreach ($files as $phpFile) {
            $this->assertTrue($phpFile->hasStrictTypes());
            ++$count;
        }

        $this->assertSame(7, $count);
    }
}
