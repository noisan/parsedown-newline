<?php
namespace Noi\Tests;

use Noi\ParsedownExtraNewline;
use ParsedownExtraTest;

class ParsedownExtraNewlineTest extends ParsedownExtraTest
{
    use JapaneseNewlineTestTrait;

    protected function initParsedown()
    {
        $Parsedown = new ParsedownExtraNewline();

        return $Parsedown;
    }
}
