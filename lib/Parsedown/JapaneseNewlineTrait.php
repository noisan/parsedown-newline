<?php
namespace Noi\Parsedown;

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

        return false;
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
}
