******************************************
　商品一覧レイアウト拡張プラグイン
　WCEX Item List Layout for Welcart
******************************************


WCEX Item List Layout は、カテゴリーページ及び複合検索結果ページのレイアウトを
管理画面から変更できるWelcart専用の拡張プラグインです。

グリッド型とリスト型のレイアウトを選べます。

グリッド型では、管理画面から全体の幅やサムネイルの列数を変更できます。その
際の画像の大きさなどは自動調整されるのでCSSを編集する必要がありません。

カテゴリーごとに別のレイアウトを指定できます。

カテゴリーページでは、価格順や売れ筋順などに並び替えが可能。ページングもOKです。
詳しくは、《カスタムフィールドを更新》をご覧ください。
(複合検索結果ページでは並び替えはできません)



==============
動作環境
==============
WordPress3.2 + Welcart1.3 以上



==============
ご利用方法
==============

【インストール】
管理パネルのプラグインの新規追加画面（プラグインのインストール）から
”アップロード”リンクをクリックして、ダウンロードしたItem List Layout の
圧縮ファイル（[例]wcex_item _list_layout.1.2.zip）をそのままアップロードして
有効化します。
Welcart を最新版（Welcart1.3 以上）にアップグレードしてください。


【準備】
Item List Layout のインストール＆有効化が済んだら、現在使用中のテーマのフォルダに
category.php を設置します。プラグインフォルダにサンプルのcategory.php が入ってい
ますので、れをそのまま使っていただいても構いませんが、サンプルにはサイドバーなどが
入っておりませんので、適宜調整が必要となります。


【商品一覧レイアウトの設定】
管理パネルの”Welcart Shop”メニューの中の”商品一覧レイアウト”という
メニューをクリックして「WCEX 商品一覧レイアウト」画面を見ます。
各項目の意味は次の通りです。

《レイアウト追加》
カテゴリーごとにレイアウトを変更したい場合にレイアウトを追加します。

《カテゴリー》
適用するカテゴリーです。「その他のカテゴリー」や「商品複合検索結果一覧」などが有ります。

《スタイル》
グリッド型かリスト型かを選びます。

《全体の横幅》
並べるコンテンツの横幅を設定します。

《サムネイルの列数》
グリッド型の場合の列数を指定します。

《枠の間隔》
枠と枠の間隔（マージン）を指定します。
初期値は10（ピクセル）となっています。

《枠の内側余白》
一商品の枠のパディングを指定します。

《枠線の太さ》
枠のボーダーの太さを指定します。0でボーダーはなくなります。
2以上に指定した場合はスタイルシートもあわせて変更する必要が有ります。
（.item_list_layout_li）

《枠の高さ》
グリッド型の場合の枠の高さを指定します。

《テキストエリアの高さ》
グリッド型の場合のテキストエリアの高さを指定します。

《ヘッダー》
カテゴリーページの上部にhtml を埋め込む事ができます。

《フッター》
カテゴリーページの下部にhtml を埋め込む事ができます。

《レイアウトを更新》
設定を変更したら「レイアウトを更新」を押します。各レイアウトに更新ボタンが有ります

《レイアウトを削除する》
必要がなくなったレイアウトを削除します。
該当しないカテゴリーにはデフォルトの設定が割り振られます
※最初のレイアウトには削除ブタンは表示されません。



《ソート項目》
ソートナビゲーション（並び替えメニュー）に表示する項目を選んで「ソート項目を更新」を
押します。


《カスタムフィールドを更新》
ここでは、価格順や売れ筋順に並び替える為に必要なカスタムフィールドを自動生成します。
この更新処理を行なわないと商品が表示されませんのでご注意ください。

・ 価格フィールド更新
初めて利用する場合、また、商品価格を変更したなど商品情報が変った場合は、必ずこの
「価格フィールド更新」を押してカスタムフィールドを更新してください。

・ 人気順（売れ筋順）フィールド更新
このフィールド更新は、売上記録を集計してソート用のカスタムフィールドを生成します。
カスタムフィールドは自動で更新されませんので、定期的にこの「人気順（売れ筋順）
フィールド更新」を押して更新処理を実行してください。




======================================
カテゴリーテンプレート（category.php）
======================================
付属のサンプル・カテゴリーテンプレートで使用しているテンプレートタグは次の通りです。

☆ usces_is_cat_of_item( $wp_query->query_vars['cat'] )
　指定したカテゴリーID が商品のカテゴリーかどうか（true or false）

☆ wcex_ill_sort_navigation()
　ソート用のナビゲーション出力タグ

☆ wcex_item_list_layout()
　商品一覧を出力するタグ

☆ wcex_ill_header()
　管理画面で設定したヘッダーhtmlを出力するタグ

☆ wcex_ill_footer()
　管理画面で設定したフッターhtmlを出力するタグ



==============
ライセンス
==============
このプラグインのライセンスはGPLとなっております。


==============
著作権
==============
著作権は、同梱のスクリプトに記載してあるAuthor（作者）が保有しています。



==============
サポート
==============
この商品は、ご購入後30日間はスタンダード・サポートがご利用いただけます。
このサービスは主に環境の違いによる不具合の原因究明が主な目的であり、
修正等の実作業はお客様ご自身が行うことになります。作業をご依頼される場合は
改めてお見積りさせて頂きます。
また、お客様自身がプラグインのカスタマイズ行っている場合は、簡単には
動作不良の原因が特定できませんので、別途調査費が必要になる場合がございます。

30日の有効期限が過ぎた場合は、新たにスタンダード・サポートをご購入いただくか
「開発フォーラム」でご質問ください。

なお、このサポートでの連絡手段はメールのみとなっており、電話でのサポートが
必要な場合は別途プレミアム・サポートのご購入が必要となります。
またこのサポートは、100パーセント原因を解明できるものでは有りませんので
予めご了承ください。

このプラグインの最新版は無償でダウンロードする事ができます。
「Welcart Home」にて、WCEX Item List Layout の詳細ページを確認して、お手持ちの
バージョン情報と比較してください。




==============
更新履歴
==============
WCEX Item List Layout 1.3  　2013/3/15
・Welcart1.3に対応
・価格順の基準金額の仕様変更
・商品カテゴリを適用すると、商品以外の記事が表示されてしまう不具合を修正

WCEX Item List Layout 1.2.7  　2012/5/15
・フィルターフック「item_list_layout_filter_list_price」を追加

WCEX Item List Layout 1.2.6  　2012/2/20
・ドキュメントを添付
・カテゴリ選択から「全商品」を削除
・「ショーケース」を「グリッド」に変更

WCEX Item List Layout 1.2.5  　2012/2/3
・ヘッダおよびフッタテキストエリアにてバックスラッシュが入ってしまう不具合を修正

WCEX Item List Layout 1.2.4  　2012/1/25
・Welcart1.1 に対応

WCEX Item List Layout 1.2.3  　2011/5/7
・Welcart1.0 にて、通貨シンボルが切り替わらない不具合を修正

WCEX Item List Layout 1.2.2  　2011/4/30
・「非公開」ステータスの商品を表示しないよう修正
・リスト型表示で「1ページの商品数」の設定が効かない不具合を修正

WCEX Item List Layout 1.2.1  　2011/3/23
・WP3.1でソートができなくなる不具合を修正

WCEX Item List Layout 1.2  　2011/2/12
・ローカライズに対応
・レイアウトの名前を選択したカテゴリー名になるよう仕様を変更
・カテゴリーページの上部及び下部にhtml を埋め込めるよう機能を拡張
・開発協力金を4,000円に変更

WCEX Item List Layout 1.1.4  　2010/12/7
・商品検索結果一覧でレイアウトが適用されていなかった不具合を修正

WCEX Item List Layout 1.1.3  　2010/10/21
・管理画面でエラーになる不具合を修正

WCEX Item List Layout 1.1.2  　2010/10/15
・出力文字のメタ処理を修正

WCEX Item List Layout 1.1.1  　2010/9/28
・Welcart 0.7 に伴いリンクの修正を行いました
・表示価格にカンマが入るよう修正

WCEX Item List Layout 1.1  　2010/9/6
・フィルターフック'item_list_layout_filter_list' に引数を追加
・価格順の並び替えがうまく行っていなかった不具合を修正

WCEX Item List Layout 1.0  　2010/8/2
・公開開始

