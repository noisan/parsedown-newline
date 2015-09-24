<?php
namespace Noi;

use Parsedown;
use Noi\Parsedown\JapaneseNewlineTrait;

/**
 * Parsedown 日本語改行支援Extension実装クラス
 *
 * このクラスは、ParsedownのHTML変換処理に
 * 以下の振る舞いを追加したクラスです:
 *
 *   * 全角文字に隣接した改行("\n")を一定のルールで取り除く
 *
 * この処理によって、日本語(全角文字)で書かれたMarkdownの
 * 改行位置に「意図しない空白」ができなくなります。
 *
 * Usage:
 *
 *     $p = new Noi\ParsedownNewline();
 *     echo $p->text('Parsedownは' . "\n" . 'とても' . "\n" . '便利');
 *
 *     // Output:
 *     <p>Parsedownはとても便利</p>
 *
 * 実際の処理は以下のtraitに依存しています。
 * 詳細な改行の削除ルールなどは以下のクラスを確認してください。
 * @see \Noi\Parsedown\JapaneseNewlineTrait
 *
 * ParsedownExtraから派生した実装クラスもあります。
 * @see \Noi\ParsedownExtraNewline
 *
 * @copyright Copyright (c) 2015 Akihiro Yamanoi
 * @license MIT
 *
 * For the full license information, view the LICENSE file that was distributed
 * with this source code.
 */
class ParsedownNewline extends Parsedown
{
    use JapaneseNewlineTrait;
}
