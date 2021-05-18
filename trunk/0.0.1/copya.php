<?php
/*
Plugin Name: Copya! - Copyable area
Plugin URI: https://ganohr.net/blog/copya/
Description: WordPress Plugin of append Copyable area with text-click or button-click!
Version: 0.0.1
Author: Ganohr
Author URI: https://ganohr.net/
License: GPL2
*/
?>
<?php
/*  Copyright 2021 Ganohr (email : ganohr@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
     published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php
// プラグイン関数が未定義なら定義する
if ( !function_exists('gnr_copya_func') ) {
  // プラグイン本体
  function gnr_copya_func($atts, $content) {
    // AMP判定処理 / AMPなら通常記事への遷移用リンクを掲載
  	if(function_exists('gnr_is_amp')
      && gnr_is_amp()
      && function_exists('gnr_replace_amp_to_normal')
      && function_exists('amp_to_normal')
    ) {
  		return gnr_replace_amp_to_normal("[amp_to_normal]");
  	}
  	extract(
  		shortcode_atts(
  			array(
  				'label' => 'Copyable:',
  				'button' => 'Copy',
  				'alert' => 'Copied!',
  				'cssprefix' => 'copya-',
  				'id' => 'auto',
  				'textclick' => 'yes',
  				'readonly' => 'yes',
  				'newline_escape' => 'yes',
  				'alert_escape' => 'yes',
  				'text' => 'Copyable Shortcode'
  			), $atts
  		)
  	);
  	// 基本はコンテンツの内容を優先するが、空なら「text」指定を取得する
  	if(!empty($content)) {
  		$text = $content;
  	}
  	// “”,‘’に勝手に修正されるのを是正
  	$text = str_replace(
  		array("“", "”", "″", '"', "&#8220;", "&#8221;", "&#8243;", "&quot;"),
  		'"',
  		$text
  	);
  	$text = str_replace(
  		array("‘", "’", "'", "&#039;"),
  		"'",
  		$text
  	);
    // テキストに含まれる改行をエスケープする
  	$text = strip_tags(
  		$newline_escape === 'yes' ? (
  			str_replace(
  				array("<br>", "<br >", "<br />", "\r\n", "\r", "\n\n"),
  				"\n",
  				$text
  			)
  		) : (
  			$text
  		)
  	);
  	// alertの内容が空以外なら、アラートを出す
  	if(!empty($alert)) {
  		$alert = strip_tags(
  			$alert_escape === 'yes' ? (
  				str_replace(
  					array("\r\n", "\r", "\n"),
  					array('\r\n', '\r', '\n'),
  					addslashes($alert)
  				)
  			) : (
  				$alert
  			)
  		);
  		$alert = "alert('$alert');";
  	}
  	// 「id」未指定や「auto」が指定されている場合、IDはテキストのハッシュ値を採用
  	if($id === 'auto' || empty($id)) {
  		$id = bin2hex(hash('crc32b', $text));
  	}
  	// 「id」を元に、onclickを構築
  	$js_onclick = " onclick=\"document.getElementById('$id').select();document.execCommand('copy');$alert\"";
  	// テキストクリックイベントが必要（'yes'）ならクリックイベントを登録する
  	if($textclick === 'yes') {
  		$textclick = $js_onclick;
  	}
  	// ボタン用のタグを生成する。なおbuttonの内容が空文字ならボタンを無くす
  	if(empty($button)) {
  		$button = "";
  	} else {
  		$button = "<input type='button'$js_onclick value='$button' class='${cssprefix}button'/>";
  	}
  	// 「readonly」が「yes」以外なら読み込み専用にしない
  	if($readonly !== 'yes') {
  		$readonly = '';
  	} else {
  		$readonly = ' readonly';
  	}
  	// コピーする内容に改行が入っているならtextarea、入ってないならinput
  	if(strpos($text, "\r") !== false
      || strpos($text, "\n") !== false
  	) {
  		$tag = <<<EOF
  <br class="${cssprefix}br1" id="br1-$id"><textarea id="$id"$textclick class="${cssprefix}text"$readonly>
  $text
  </textarea><br class="${cssprefix}br2" id="br2-$id">
  EOF;
  	} else {
  		$text = str_replace('"', '&quot;',$text);
  		$tag = <<<EOF
  <input id="$id"$textclick type="text" value="$text" class="${cssprefix}text"$readonly/>
  EOF;
  	}
  	// コード全体を返却する
  	return <<<EOF
  <div class="${cssprefix}outer" id="outer$id"><label class="${cssprefix}label" id="label$id">$label</label>$tag$button</div>
  EOF;
  }
  add_shortcode('copya', 'gnr_copya_func', 9999);
}
?>
