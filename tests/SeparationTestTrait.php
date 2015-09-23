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

    /** @test */
    public function 半角数字分かち書きモード_改行前後のどちらか一方でも半角数字なら常に改行を残す()
    {
        $markdown = '半角123' . "\n" . '全角' . "\n" . '123' . "\n" . '全角' . "\n" . 'ABC';
        $expected = '半角123' . "\n" . '全角' . "\n" . '123' . "\n" . '全角' . ""   . 'ABC';

        $this->parsedown->setSingleByteNumericSeparated(true);

        $this->assertEquals($expected, $this->parsedown->line($markdown));
    }

    /** @test */
    public function 半角記号分かち書きモード_改行前後のどちらか一方でも半角記号なら常に改行を残す()
    {
        $markdown = '半角!?' . "\n" . '全角' . "\n" . '#$%' . "\n" . '全角' . "\n" . 'ABC';
        $expected = '半角!?' . "\n" . '全角' . "\n" . '#$%' . "\n" . '全角' . ""   . 'ABC';

        $this->parsedown->setSingleByteSymbolSeparated(true);

        $this->assertEquals($expected, $this->parsedown->line($markdown));
    }

    /** @test */
    public function 全角英字分かち書きモード_改行前後のどちらか一方でも全角英字なら常に改行を残す()
    {
        $markdown = '全角ＡＢＣ' . "\n" . '全角' . "\n" . 'ＡＢＣ' . "\n" . '全角' . "\n" . '１２３！' . "\n" . '半角ABC' . "\n" . "全角";
        $expected = '全角ＡＢＣ' . "\n" . '全角' . "\n" . 'ＡＢＣ' . "\n" . '全角' . ""   . '１２３！' . ""   . '半角ABC' . ""   . "全角";

        $this->parsedown->setMultiByteAlphaSeparated(true);

        $this->assertEquals($expected, $this->parsedown->line($markdown));
    }

    /** @test */
    public function 全角数字分かち書きモード_改行前後のどちらか一方でも全角数字なら常に改行を残す()
    {
        $markdown = '全角１２３' . "\n" . '全角' . "\n" . '１２３' . "\n" . '全角' . "\n" . 'ＡＢＣ！' . "\n" . '半角123' . "\n" . "全角";
        $expected = '全角１２３' . "\n" . '全角' . "\n" . '１２３' . "\n" . '全角' . ""   . 'ＡＢＣ！' . ""   . '半角123' . ""   . "全角";

        $this->parsedown->setMultiByteNumericSeparated(true);

        $this->assertEquals($expected, $this->parsedown->line($markdown));
    }

    public function setUp()
    {
        $this->parsedown = $this->initParsedown();
    }

    abstract protected function initParsedown();

    protected $parsedown;
}
