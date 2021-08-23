# acms-form2-field-group
a-blog cmsでフォームの送信内容をエントリーのカスタムフィールドグループに追加するプラグインです。アンケートをその場で受け付けページに掲載する、Form2Entryプラグインと組み合わせて掲示板のシステムを実装することなどが可能です。

## 使い方

1. ダウンロード後、**extension/plugins/Form2FieldGroup** に設置してください。（フォルダ名は１文字目が大文字になります）
2. 管理ページ > 拡張アプリのページに移動し、Form2FieldGroup をインストールします。
3. 管理ページ > フォームの「変更」ページに移動すると、「Form2FieldGroup設定」が追加されていますので、「有効にする」にチェックを入れて、追加したいフィールドグループ（「board_comment」など）を指定します。
4. フォームのHTMLにカスタムフィールドグループのタグと、追加する先のエントリーIDを指定します。

```

<!-- BEGIN_MODULE Form -->
<form action="" method="post">
  <!-- フォームの入力項目 -->
  <div class="row">
    <label for="comment-name">お名前</label>
    <input type="text" id="comment-name" name="board_comment_name" value="" placeholder="例）山田 太郎" class="text-field">
    <input type="hidden" name="field[]" value="board_comment_name">
  </div>
  <div class="row">
    <label id="comment-division">部署名</label>
    <input type="text" id="comment-division" name="board_comment_division" value="" placeholder="例）○○○○○部" class="text-field">
    <input type="hidden" name="field[]" value="board_comment_division">
  </div>
  <div class="row">
    <label for="comment-body">本文</label>
    <textarea id="comment-body" name="board_comment_body" cols="50" rows="4" placeholder="" class="text-area"></textarea>
    <input type="hidden" name="field[]" value="board_comment_body">
  </div>
  <button type="submit" name="ACMS_POST_Form_Submit" class="comment-button"><span>投稿する</span></button>

  <!-- エントリーIDを指定（カスタマイズ箇所） -->
  <input type="hidden" name="entry_id" value="\{entry:loop.eid\}">
  <input type="hidden" name="field[]" value="entry_id">
  
  <!-- フォームの内容をカスタムフィールドグループに格納（カスタマイズ箇所） -->
  <input type="hidden" name="@board_comment[]" value="board_comment_name" />
  <input type="hidden" name="@board_comment[]" value="board_comment_division" />
  <input type="hidden" name="@board_comment[]" value="board_comment_body" />
  <input type="hidden" name="field[]" value="@board_comment" />
  
  <!-- フォームのパラメータ設定 -->
  <input type="hidden" name="step" value="result" />
  <input type="hidden" name="error" value="reapply" />
  <input type="hidden" name="id" value="board_comment" />
</form>
<!-- END_MODULE Form -->
```

## 注意点

config.server.phpでHOOKを有効にしておく必要があります。

```
define('HOOK_ENABLE', 1);
```

 
