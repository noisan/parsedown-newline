<?php
namespace Noi;

use ParsedownExtra;
use Noi\Parsedown\JapaneseNewlineTrait;

/**
 * ParsedownExtra 日本語改行支援Extension実装クラス
 *
 * @see \Noi\ParsedownNewline
 * @see \Noi\Parsedown\JapaneseNewlineTrait
 *
 * @copyright Copyright (c) 2015 Akihiro Yamanoi
 * @license MIT
 *
 * For the full license information, view the LICENSE file that was distributed
 * with this source code.
 */

class ParsedownExtraNewline extends ParsedownExtra
{
    use JapaneseNewlineTrait;
}
