<?php
namespace Noi\Tests;

trait SeparationTestTrait
{
    /** @test */
    public function 半角文字分かち書きモード_改行前後のどちらか一方でも半角文字なら常に改行を残す()
    {
        $markdown = '半角ABC' . "\n" . '全角' . "\n" . 'ABC' . "\n" . '全角' . "\n" . '123';
        $expected = $markdown;

        $this->parsedown->setSingleByteCharsSeparated(true);

        $this->assertEquals($expected, $this->parsedown->line($markdown));
    }

    /** @test */
    public function 半角英字分かち書きモード_改行前後のどちらか一方でも半角英字なら常に改行を残す()
    {
        $markdown = '半角ABC' . "\n" . '全角' . "\n" . 'ABC' . "\n" . '全角' . "\n" . '123';
        $expected = '半角ABC' . "\n" . '全角' . "\n" . 'ABC' . "\n" . '全角' . ""   . '123';

        $this->parsedown->setSingleByteAlphaSeparated(true);

        $this->assertEquals($expected, $this->parsedown->line($markdown));
    }

    public function setUp()
    {
        $this->parsedown = $this->initParsedown();
    }

    abstract protected function initParsedown();

    protected $parsedown;
}
