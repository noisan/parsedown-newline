<?php
namespace Noi\Tests;

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

class ParsedownNewline extends \Parsedown
{
    use \Noi\Parsedown\JapaneseNewlineTrait;
}
