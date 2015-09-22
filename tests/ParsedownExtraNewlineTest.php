<?php
namespace Noi\Tests;

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

class ParsedownExtraNewline extends \ParsedownExtra
{
    use \Noi\Parsedown\JapaneseNewlineTrait;
}
