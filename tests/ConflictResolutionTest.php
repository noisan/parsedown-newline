<?php
namespace Noi\Tests;

use Noi\Parsedown\JapaneseNewlineTrait;

use Parsedown;
use ParsedownTest;

use ParsedownExtra;
use ParsedownExtraTest;

/*
 * unmarkedText()をoverrideする他のExtension
 *
 * これを競合回避コードなしでJapaneseNewlineTraitと同時にuseすると、
 * unmarkedText()が競合しているため、名前の衝突エラーが発生する。
 *
 *   > Trait method unmarkedText has not been applied,
 *   > because there are collisions with other trait methods...
 */
trait ConflictExtensionTrait
{
    // override
    protected function unmarkedText($text)
    {
        return parent::unmarkedText($text);
    }
}

/*
 * Extensionを組み込む実装クラス
 *
 * 名前の衝突を回避する手順:
 *   1. 競合するExtension側のunmarkedText()に別名を付ける。
 *   2. 実装クラスにunmarkedText()を定義する。
 *   3. unmarkedText()の中で、競合Extension側のunmarkedText()を別名で呼ぶ。
 *   4. 3の結果をunmarkedJapaneseNewline()の引数に渡す。
 */
class ConflictResolutionTest_TestImpl extends Parsedown
{
    // 1. 競合するExtension側のunmarkedText()に別名を付ける
    use JapaneseNewlineTrait, ConflictExtensionTrait {
        ConflictExtensionTrait::unmarkedText as unmarkedText_Conflict;
    }

    // 2. 実装クラスにunmarkedText()を定義
    protected function unmarkedText($text)
    {
        // 3. unmarkedText()の中で、競合Extension側のunmarkedText()を別名で呼ぶ
        $text = $this->unmarkedText_Conflict($text);

        // 4. 3の結果をunmarkedJapaneseNewline()の引数に渡す
        return $this->unmarkedJapaneseNewline($text);
    }
}

class ConflictResolutionTest_TestExtraImpl extends ParsedownExtra
{
    use JapaneseNewlineTrait, ConflictExtensionTrait {
        ConflictExtensionTrait::unmarkedText as unmarkedText_Conflict;
    }

    protected function unmarkedText($text)
    {
        return $this->unmarkedJapaneseNewline($this->unmarkedText_Conflict($text));
    }
}

class ConflictResolutionTest_ParsedownTest extends ParsedownTest
{
    use JapaneseNewlineTestTrait;

    protected function initParsedown()
    {
        return new ConflictResolutionTest_TestImpl();
    }
}

class ConflictResolutionTest_ParsedownExtraTest extends ParsedownExtraTest
{
    use JapaneseNewlineTestTrait;

    protected function initParsedown()
    {
        return new ConflictResolutionTest_TestExtraImpl();
    }
}
