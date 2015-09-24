<?php
namespace Noi\Parsedown;

/**
 * Parsedown 日本語改行支援Extension
 *
 * このエクステンションは、Parsedown派生クラスの
 * HTML変換処理に以下の振る舞いを追加します:
 *
 *   * 全角文字に隣接した改行("\n")を一定のルールで取り除く
 *
 * この処理により、日本語(全角文字)で書いたMarkdownの
 *
 *   * 改行が「意図しない空白」になる問題
 *
 * を解決します。
 *
 * 例えば、以下のようなHTMLの見栄えを気にした苦肉の策は
 * 必要なくなります:
 *
 *   - 空白を避けるため、仕方なく段落を1行の横長で書いてしまう
 *   - 空白をごまかすため、各行の長さを無視して英数字や記号の後で改行する
 *
 * trait形式のExtensionなので、組み込み先のクラスは自由に選択できます。
 * \Noi\ParsedownNewline, \Noi\ParsedownExtraNewlineクラスは
 * このtraitを使った実装クラスです。
 * @see \Noi\ParsedownNewline
 * @see \Noi\ParsedownExtraNewline
 *
 * Markdown:
 *
 *     段落内でこのように
 *     改行をした場合、
 *     意図しない空白になる。
 *
 *     // Parsedown::text()が出力する通常のHTML
 *     <p>段落内でこのように
 *     改行をした場合、
 *     意図しない空白になる。</p>
 *
 *     // このExtensionを組み込んで変換したHTML
 *     <p>段落内でこのように改行をした場合、意図しない空白になる。</p>
 *
 * ほとんどのブラウザは改行("\n")を空白として表示します。
 * 上記の例の場合、Parsedown::text()が出力する通常のHTMLは
 * ブラウザで表示すると次のように見えます:
 *
 *     段落内でこのように 改行をした場合、 意図しない空白になる。
 *                      ^^^(ここに空白)  ^^^(ここにも空白)
 *
 * このExtensionは、上記のような「意図しない空白」の削除を
 * 目的としています。
 *
 * HTML変換時の改行処理ルール:
 *
 *   1. 改行の前後両方が半角文字なら、"\n" を残す。
 *   2. 改行の前後両方が英数字(全角・半角問わず)なら、"\n" を残す。
 *   3. その他、改行がset***Separated()で指定した文字種に隣接するなら、"\n" を残す。
 *   4. 上記以外の場合、改行は全角文字に隣接しているので、"\n" を削除。
 *
 * 改行処理の実装は以下のメソッドを確認してください。
 * @see unmarkedJapaneseNewline()
 *
 * 改行処理ルールの微調整:
 *
 * 「全角文字と半角文字の間は必ず空白を開ける」のような組版ルールに従って
 * Markdownを書いている場合は、改行位置でも一貫性を保つ必要があります。
 * しかし、このExtensionを組み込むと、全角文字に隣接する改行は削除するため
 * "全角 ABC\n全角" のような箇所の "\n" も消えてしまいます。
 *
 * これは気付きにくいですが、HTMLの表示を見ると "全角 ABC全角" になり
 * ("ABC"の後ろに空白がなく)組版ルールに違反したHTMLになってしまいます。
 *
 * このような特定文字種にスペースを入れる規則を守る必要がある場合は、
 * 目的の表記規則に一致するようsetSingleByteCharsSeparated()などで
 * 改行処理ルールを調整することができます。
 * @see setSingleByte***Separated()
 * @see setMultiByte***Separated()
 *
 * 他のExtensionと競合した場合:
 *
 * このtraitはParsedown::unmarkedText()をoverrideします。
 * そのため、unmarkedText()を置き換える他のtraitと同時に`use`すると
 * メソッド名の衝突エラーが発生します。
 *
 * これを解決するためには、以下のどちらかを選択してください:
 *
 *   1. \Noi\ParsedownNewline または \Noi\ParsedownExtraNewline の
 *      派生クラスを定義し、そこに他のExtension用traitを組み込む
 *   2. 独自のParsedown派生クラス内で競合回避コードを書く
 *
 * 通常は1の方法が簡単です。
 * もしも、競合するExtensionが複数あるようなら2の方法で利用してください。
 *
 * 次のUsageは2の競合回避コードを書く場合の例です。
 *
 * Usage:
 *
 *     use Noi\Parsedown\JapaneseNewlineTrait;
 *     use AnotherExtensionTrait;
 *
 *     class YourParsedown extends \Parsedown [ or \ParsedownExtra or etc. ] {
 *       // 1. 競合Extension側のunmarkedText()に別名を付ける
 *       use JapaneseNewlineTrait, AnotherExtensionTrait {
 *         AnotherExtensionTrait::unmarkedText as unmarkedText_Another;
 *       }
 *
 *       // 2. 実装クラスにunmarkedText()を定義
 *       protected function unmarkedText($text) {
 *         // 3. unmarkedText()の中で、競合Extension側のunmarkedText()を別名で呼ぶ
 *         $text = $this->unmarkedText_Another($text);
 *
 *         // 4. 3の結果をunmarkedJapaneseNewline()の引数に渡す
 *         return $this->unmarkedJapaneseNewline($text);
 *       }
 *     }
 *
 *     $p = new YourParsedown();
 *     echo $p->text('Parsedownは' . "\n" . 'とても' . "\n" . '便利');
 *     // Output:
 *     <p>Parsedownはとても便利</p>
 *
 * @copyright Copyright (c) 2015 Akihiro Yamanoi
 * @license MIT
 *
 * For the full license information, view the LICENSE file that was distributed
 * with this source code.
 */
trait JapaneseNewlineTrait
{
    // override
    protected function unmarkedText($text)
    {
        return $this->unmarkedJapaneseNewline(parent::unmarkedText($text));
    }

    /*
     * 全角文字に隣接する改行を削除する。
     *
     * 以下を満たすときは改行を残す:
     *
     *   1. 改行の前後両方が半角文字の場合
     *   2. 改行の前後両方が英数字の場合(全角・半角を問わない)
     *   3. その他、指定した文字種に隣接する場合
     *
     * 数字や単語が繋がると意味が変わってしまう可能性もあるので
     * 分かち書きが必要な箇所では改行を削除しない。
     */
    protected function unmarkedJapaneseNewline($text)
    {
        $lines = explode("\n", $text);
        $count = count($lines);

        for ($i = 0;  $i < $count - 1; $i++) {
            $prev = mb_substr($lines[$i], -1);
            $next = mb_substr($lines[$i + 1], 0, 1);

            if ($this->isJapaneseNewlineSeparated($prev, $next)) {
                // 分かち書きを維持すべき箇所では改行を残す。
                $lines[$i] .= "\n";
            }
        }

        return join('', $lines);
    }

    /*
     * 分かち書きすべき箇所かどうか調べる。
     *
     * $prevには前の単語の末尾の文字を指定する。
     * $nextには次の単語の先頭の文字を指定する。
     *
     * 指定した文字が両方とも分かち書きすべき文字種であればtrueを返す。
     * それ以外の場合はfalseを返す。
     */
    protected function isJapaneseNewlineSeparated($prev, $next)
    {
        if ((strlen($prev) <= 1) and (strlen($next) <= 1)) {
            // 前後両方が半角文字(もしくは空)
            return true;
        }

        if ($this->isJapaneseNewlineAlphaNumeric($prev) and $this->isJapaneseNewlineAlphaNumeric($next)) {
            // 前後両方が英数字(半角・全角を問わない)
            return true;
        }

        if ($this->isJapaneseNewlineSepapator($prev) or $this->isJapaneseNewlineSepapator($next)) {
            // 前後どちらか一方でも、分かち書き指定の文字種
            return true;
        }

        return false;
    }

    protected function isJapaneseNewlineSepapator($char)
    {
        foreach ($this->japanese_newline_Separators as $separator) {
            if ($separator($char)) {
                return true;
            }
        }

        return false;
    }

    public function setJapaneseNewlineSeparator($key, $callback)
    {
        if ($callback) {
            $this->japanese_newline_Separators[$key] = $callback;
        } else {
            unset($this->japanese_newline_Separators[$key]);
        }
    }

    /**
     * 半角文字分かち書きモードを設定する。
     *
     * `true`を指定すると半角文字分かち書きモードになり、
     * 半角文字に隣接した "\n" を削除せず残す。
     *
     * 「全角文字と半角文字の間は常に空白を開ける」
     * のような表記ルールでMarkdownを書いている場合に使う。
     */
    public function setSingleByteCharsSeparated($bool)
    {
        $this->setJapaneseNewlineSeparator('single_all', ($bool) ? function ($char) { return (strlen($char) <= 1); } : null);
        return $this;
    }

    /**
     * 半角英字分かち書きモードを設定する。
     *
     * `true`を指定すると半角英字分かち書きモードになり、
     * 半角のアルファベットに隣接した "\n" を削除せず残す。
     */
    public function setSingleByteAlphaSeparated($bool)
    {
        $this->setJapaneseNewlineSeparator('single_alpha', ($bool) ? 'ctype_alpha' : null);
        return $this;
    }

    /**
     * 半角数字分かち書きモードを設定する。
     *
     * `true`を指定すると半角数字分かち書きモードになり、
     * 半角の数字に隣接した "\n" を削除せず残す。
     */
    public function setSingleByteNumericSeparated($bool)
    {
        $this->setJapaneseNewlineSeparator('single_numeric', ($bool) ? 'ctype_digit' : null);
        return $this;
    }

    /**
     * 半角記号分かち書きモードを設定する。
     *
     * `true`を指定すると半角記号分かち書きモードになり、
     * 半角の記号に隣接した "\n" を削除せず残す。
     */
    public function setSingleByteSymbolSeparated($bool)
    {
        $this->setJapaneseNewlineSeparator('single_symbol', ($bool) ? 'ctype_punct' : null);
        return $this;
    }

    /**
     * 全角英字分かち書きモードを設定する。
     *
     * `true`を指定すると全角英字分かち書きモードになり、
     * 全角のアルファベットに隣接した "\n" を削除せず残す。
     */
    public function setMultiByteAlphaSeparated($bool)
    {
        $this->setJapaneseNewlineSeparator('multi_alpha', ($bool) ? function ($char) {
            return ((strlen($char) > 1) and preg_match('/[\p{Latin}\p{Greek}\p{Cyrillic}]\z/Au', $char));
        } : null);
        return $this;
    }

    /**
     * 全角数字分かち書きモードを設定する。
     *
     * `true`を指定すると全角数字分かち書きモードになり、
     * 全角の数字に隣接した "\n" を削除せず残す。
     */
    public function setMultiByteNumericSeparated($bool)
    {
        $this->setJapaneseNewlineSeparator('multi_numeric', ($bool) ? function ($char) {
            return preg_match('/[０-９]\z/Au', $char);
        } : null);
        return $this;
    }

    protected function isJapaneseNewlineAlphaNumeric($char)
    {
        return preg_match($this->getJapaneseNewlineAlphaNumericPattern(), $char);
    }

    public function getJapaneseNewlineAlphaNumericPattern()
    {
        return $this->japanese_newline_AlphaNumericPattern;
    }

    public function setJapaneseNewlineAlphaNumericPattern($pattern)
    {
        $this->japanese_newline_AlphaNumericPattern = $pattern;
        return $this;
    }

    /*
     * 英数字判定用正規表現
     *
     * 改行("\n")の前後がこのパターンに一致する文字の場合、
     * その箇所では分かち書きを維持する必要がある。
     */
    protected $japanese_newline_AlphaNumericPattern = '/[0-9０-９\p{Latin}\p{Greek}\p{Cyrillic}]\z/Au';

    protected $japanese_newline_Separators = array();
}
