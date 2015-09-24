Parsedown 日本語改行支援extension
====
このエクステンションは、[Parsedown](https://github.com/erusev/parsedown)のHTML変換処理に以下の振る舞いを追加します:

  * 全角文字に隣接した改行(`\n`)を一定のルールで取り除く

この処理により、日本語(全角文字)で書いたMarkdownの

  * 改行位置に「意図しない空白」ができる問題

を解消します。

例えば、以下のようなMarkdownの可読性をだいなしにする苦肉の策は必要なくなります:

  * 空白を避けるため、仕方なく段落を1行の横長で書いてしまう
  * 空白をごまかすため、各行の長さが極端に変わっても英数字や記号の後で改行する

以降の動作例を確認してください。


動作例
----

**Markdown**

    このように日本語で
    複数行入力するとき、
    改行は空白を意図していません。

**Parsedownで変換したHTMLの表示**

    このように日本語で 複数行入力するとき、 改行は空白を意図していません。
    　　　　　　　　 ^^^(ここに空白)　　 ^^^(ここにも空白)

「日本語で」と「複数行」の間、「するとき、」と「改行は」の間に空白が入ります。

**このエクステンションを組み込んで変換したHTMLの表示**

    このように日本語で複数行入力するとき、改行は空白を意図していません。

空白は入りません。

ParsedownはHTML変換時、Markdown上の改行を`\n`(newline)としてそのまま残します。
`\n`はHTMLで空白として表示されます。

この空白は、元々単語を分かち書きする英語など多くの言語では問題になりませんが、
基本的に分かち書きをしない日本語では上記の例のようにちょっと気になるスキマになってしまいます。

このエクステンションは、HTML変換時に、意図しない空白の原因になる`\n`を削除します。

これにより、Markdownを日本語で書く場合にも改行位置を気にせず好きなように入力できるようになる利点が得られます。


改行処理ルール
----
HTML変換時の改行処理条件は以下のとおりです:

  1. 改行の前後両方が半角文字なら、`\n`を残す。
  2. 改行の前後両方が英数字(全角・半角問わず)なら、`\n`を残す。
  3. その他、改行の前後どちらか一方でも、特別に指定した文字種なら、`\n`を残す。
  4. 上記以外の場合、改行は全角文字に隣接しているので、`\n`を**削除**。

基本的には全角文字と隣接する改行は、空白を意図したものではないとみなして、削除します。
例外として、単語間の分かち書きを維持すべき文字種の間では、改行は空白を意図しているので、`\n`をそのまま残します。

この改行処理はコードblockには適用しません。

なお、Markdown上の改行をHTML上でも改行(`<br>`)と解釈させたいのであれば、
このエクステンションを使わずにParsedown標準の`Parsedown::setBreaksEnabled()`を使用してください。


改行処理ルールの微調整
----
「全角文字と半角文字の間は必ず空白を開ける」
のような組版ルールに従ってMarkdownを書いている場合は、
改行位置でも一貫性を保つ必要があります。

しかし、このExtensionを組み込むと、デフォルト動作では全角文字に隣接する改行を削除するため、

    全角 ABC
    全角

のようなMarkdown("`全角 ABC\n全角`")の`\n`も変換後のHTMLでは消えてしまいます。
このHTMLの表示は

    全角 ABC全角

になり("ABC"の後ろに空白がないため)組版ルールに違反したHTMLになってしまいます。

このような特定文字種にスペースを入れる規則を守る必要がある状況では、
以下のメソッドで改行処理ルールを調整することができます:

* `setSingleByteCharsSeparated($bool)` -- 半角文字用
* `setSingleByteAlphaSeparated($bool)` -- 半角アルファベット用
* `setSingleByteNumericSeparated($bool)` -- 半角数字用
* `setSingleByteSymbolSeparated($bool)` -- 半角記号用
* `setMultiByteAlphaSeparated($bool)` -- 全角アルファベット用
* `setMultiByteNumericSeparated($bool)` -- 全角数字用

これらのメソッドに`true`を指定することで、各文字種に隣接する改行を削除しなくなります。

例えば、`setSingleByteCharsSeparated(true)`を設定すると、
改行の前後どちらか一方が半角文字であれば、
もう一方が全角文字だとしても`\n`を残します。
前述の例では改行が削除されなくなり、求めていた表示結果

    全角 ABC 全角

が得られます。

あなたの文書が従う表記規則に一致するように上記メソッドで調整してください。


Installation - インストール方法
----
[Composer](http://getcomposer.org/) で以下を実行してください。

```sh
$ composer require noi/parsedown-newline "*"
$ composer require erusev/parsedown-extra "*"
```

または、`composer.json` に以下の行を含めてください。

```json
{
    "require": {
        "noi/parsedown-newline": "*",
        "erusev/parsedown-extra": "*"
    }
}
```

Markdown Extraの拡張記法を使わない場合は、
[`erusev/parsedown-extra`](https://github.com/erusev/parsedown-extra "Parsedown Extra")の行は必要ありません。

Composerによるパッケージ管理をしていない場合は、`include_path` のいずれかに
`Noi/` ディレクトリを作り、そこへ `lib/` 以下のファイルを置いてください。

Usage - エクステンションの組み込み方法
----

### 使い方1: `Noi\ParsedownNewline` / `Noi\ParsedownExtraNewline` を使う

これらのクラスは、それぞれ`Parsedown`と`ParsedownExtra`に本エクステンションを組み込んだ実装クラスです。

そのまま`new`して使うか、さらに派生クラスを定義して他のエクステンションを組み込んで使用してください。

```php
<?php
$pd = new \Noi\ParsedownNewline(); // or new \Noi\ParsedownExtraNewline();
echo $pd->text("Parsedownは\nとても\n便利");

// Output:
<p>Parsedownはとても便利</p>
```

### 使い方2: 独自の`Parsedown`派生クラスに組み込む

あなた独自の`Parsedown`派生クラスがある場合は、本エクステンションのtraitを組み込んでください。

以下のように`Noi\Parsedown\JapaneseNewlineTrait`を`use`します:

```php
<?php
class YourParsedown extends \Parsedown /* or \ParsedownExtra or etc. */ {
  use \Noi\Parsedown\JapaneseNewlineTrait;
  //...
}
```

**注意**

以下の条件を片方でも満たす場合は`use`だけで組み込むことができません:

  1. 組み込むクラス(上記の場合`YourParsedown`)で`unmarkedText()`が再定義されている
  2. 同時に`use`する他のtraitに`unmarkedText()`が定義されている

1の場合、本Extensionの`unmarkedText()`が上書きされるため有効になりません。
2の場合、メソッド名の衝突エラーが発生します。

これらを解決するためには競合回避コードが必要です。
使い方3を確認してください。


### 使い方3: 競合回避コードを書く

本Extensionのtraitは`unmarkedText()`をoverrideします。

同時に`use`する他のtraitでも`unmarkedText()`が定義されている場合、
メソッド名の衝突エラーが発生してしまうため、そのままでは組み込むことができません。
これを解決するための競合回避コードを書く必要があります。

組み込み先クラスを以下のように定義してください:

```php
<?php
use Noi\Parsedown\JapaneseNewlineTrait;
use AnotherExtensionTrait;

class YourSuperDuperParsedown extends ... {
  // 1. 競合Extension側のunmarkedText()に別名を付ける
  use JapaneseNewlineTrait, AnotherExtensionTrait {
    AnotherExtensionTrait::unmarkedText as unmarkedText_Another;
  }

  // 2. 実装クラスにunmarkedText()を定義
  protected function unmarkedText($text) {
    // 3. unmarkedText()の中で、競合Extension側のunmarkedText()を別名で呼ぶ
    $text = $this->unmarkedText_Another($text);

    // 4. 3の結果をunmarkedJapaneseNewline()の引数に渡す
    return $this->unmarkedJapaneseNewline($text);
  }
}
```

要点は、実装クラスの`unmarkedText()`内で

 * 競合Extensionの`unmarkedText()`を別名で実行すること
 * `JapaneseNewlineTrait::unmarkedJapaneseNewline()`を実行すること

です。


License
----
MITライセンスです。ライセンスの制限の範囲内であれば商用・非商用を問わず自由にお使いください。

Code released under the MIT License - see the `LICENSE` file for details.
