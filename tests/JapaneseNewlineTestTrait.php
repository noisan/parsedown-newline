<?php
namespace Noi\Tests;

trait JapaneseNewlineTestTrait
{
    protected function initDirs()
    {
        $dirs = parent::initDirs();

        $dirs []= dirname(__FILE__).'/data/';

        return $dirs;
    }

    abstract protected function initParsedown();
}
