<?php
namespace Noi\Tests;

use Noi\ParsedownExtraNewline;

class SeparationExtraTest extends \PHPUnit_Framework_TestCase
{
    use SeparationTestTrait;

    protected function initParsedown()
    {
        return new ParsedownExtraNewline();
    }
}
