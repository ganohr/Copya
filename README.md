# What's this?
WordPress Plugin of append Copy-Able area with text-click or button-click!

クリックやボタンクリックでコピー可能な領域を追加するワードプレスのプラグインです!

# Usage
```
[copya]Copy able text[/copya]
```
* Append copay-able area, when click this area or below button, then copy to clipboard the text "Copy able text".
* コピー可能な領域を追加し、この領域や後ろに付くボタンをクリックすると、クリップボードに「Copy able text」がコピーされます。

```
[copya label="コピー可能" button="コピー" alert="コピーしました!" textclick="yes" readonly="yes" text="コピー可能なテキスト"/]
```
* ```label="コピー可能"``` change label / ラベルを変更する
* ```button="コピー"```  change button text / ボタンのテキストを変更する
* ```alert="コピーしました!"``` change alert text / コピーした際に表示される警告文を変更する
* ```readonly="yes"``` change to text area is not edit-able / テキスト領域を編集不可能にする
* ```text="コピー可能なテキスト"``` set copying text / コピーするテキストを指定する

# other options

* ```cssprefix="copya-"``` change CSS prefix to others / CSSクラスの接頭辞を指定する
* ```id="auto / or set manual"``` set id manualy(direct set) or "auto" / IDの指定を自動（"auto"）にするか指定する
* ```textclick="yes"``` set copy-able when text click / テキスト領域をクリックしてコピー可能にする
* ```newline_escape="yes"``` change newline code or &gt;br&lt; to escaped / 改行コードや「&gt;br&lt;」タグをエスケープする
* ```alert_escape="yes"``` change newline code to escaping for alert message / 警告文の改行コードをエスケープする
