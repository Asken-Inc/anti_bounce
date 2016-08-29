# AntiBounce plugin for CakePHP

## インストール

[composer](http://getcomposer.org)を用いてプラグインのインストールが可能です。

以下のコマンドを実行し、パッケージをCakePHPに導入してください。

```
composer require Uluru/AntiBounce
```

## 初期設定

#### テーブル作成

バウンスになったメールアドレスのログを`default`データベースに読み書きを行います。
マイグレーションファイルを用意してありますので、以下の通りマイグレーションを実行してください。

```
$ bin/cake migrations migrate -p AntiBounce
```

`bounce_logs`テーブルが生成されます。

#### config/antibounce.php

プラグインの各種設定を上書きできます。特に、以下に示すtopicと、mailアドレスの変更を忘れずに行ってください。

また、照準設定では`Users`モデル（`users`テーブル）の`mail`フィールドと、バウンスメールアドレスの比較を行う設定になっています。
必要に応じ、プラグインディレクトリの`config/setting.php`の内容を、このファイルで書き換えてください。

```php
return [
    'AntiBounce.topic' => 'arn:aws:sns:us-east-1:****************:***********',
    'AntiBounce.mail' => 'your@domain.com',
];
```

#### config/bootstrap.php

CakePHPに本プラグインをロードさせる前に、`AntiBounce.config`キーのコンフィグ設定によって、読み込ませる外部設定ファイルを指定できます。

```php
Configure::write('AntiBounce.config', ['antibounce']);
Plugin::load('AntiBounce', ['routes' => true, 'bootstrap' => true]);
```

