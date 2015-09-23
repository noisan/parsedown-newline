<?php
namespace Noi\Tests;

use Noi\ParsedownNewline;

class SeparationTest extends \PHPUnit_Framework_TestCase
{
    use SeparationTestTrait;

    protected function initParsedown()
    {
        return new ParsedownNewline();
    }
}
