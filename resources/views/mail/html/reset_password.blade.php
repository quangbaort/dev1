@component('mail::message')

いつも{{ config('app.name') }}をご利用いただきありがとうございます。

お客様よりパスワードリセットのご依頼をいただいたためパスワードリセット用の本メールを送信しました。

以下のボタンをクリックし、パスワードの再設定を行ってください。

@component('mail::button', ['url' => $data['action_url']])
パスワード再設定
@endcomponent

本メールに心当たりがない場合は、本メールを削除してください。

{{ config('app.name') }}より
@endcomponent