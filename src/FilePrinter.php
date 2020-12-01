<?php

declare(strict_types=1);

namespace Eyeconweb\GraphQL\Generator;

use Nette\PhpGenerator\Printer;

class FilePrinter extends Printer
{
    protected $indentation = '    ';

    protected $linesBetweenProperties = 1;

    protected $linesBetweenMethods = 1;

    protected $returnTypeColon = ': ';

    public function __construct()
    {
        $this->setTypeResolving(false);
    }
}
