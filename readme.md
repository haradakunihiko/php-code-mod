# 最初に
ASTを利用してコードの変換を行う。

ASTの書き換えだとコードのフォーマットがかなり変わっちゃったので、文字列の置換に限定してフォーマットを崩さずに変換できるようにした（参考：https://qiita.com/gong023/items/4c8401e03d843fd15122）
（phpfixerでいい感じに戻してくれるかなーと思ったけど、結構差分でたので。）

（フォーマットに気にせず変えていいなら普通にやればいい気がする。）

超雑に書いてるのですません。

# setup
`composer install` でいけるはず

# 使い方


`src/main.php` を参照。

`src/TraverseFile` が本処理。CSVへの書き出しとかファイルの更新はここでやってる

`src/visitor/MultiByteReplaceTextVisitor.php` がASTの各ノードにアクセスするビジター。 `MutableString` に変換内容を保持させる。

`src/MutableString` 文字列を保持し、置換内容を出力する。

`src/visitor/MultiByteTextVisitor.php` もASTの各ノードにアクセスするビジター。 名前で区別つきづらいんだけど、こっちはNodeを置き換えるよくある方式。
 
# 参考
https://www.tomasvotruba.cz/blog/2017/11/06/how-to-change-php-code-with-abstract-syntax-tree/

https://github.com/nikic/PHP-Parser/blob/master/doc/2_Usage_of_basic_components.markdown

https://github.com/nikic/PHP-Parser/issues/41#issuecomment-269230733

https://qiita.com/gong023/items/4c8401e03d843fd15122

https://github.com/gong023/namae-space