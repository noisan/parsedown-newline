<?php
namespace Noi\Tests;

use Noi\ParsedownNewline;
use ParsedownTest;

class ParsedownNewlineTest extends ParsedownTest
{
    use JapaneseNewlineTestTrait;

    protected function initParsedown()
    {
        $Parsedown = new ParsedownNewline();

        return $Parsedown;
    }
}
