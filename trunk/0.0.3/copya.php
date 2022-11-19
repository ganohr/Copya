<?php
/*
Plugin Name: Copya! - Copyable area
Plugin URI: https://ganohr.net/blog/copya/
Description: WordPress Plugin of append Copyable area with text-click or button-click!
Version: 0.0.3
Author: Ganohr<ganohr@gmail.com>
Author URI: https://ganohr.net/
License: GPL2
*/
?>
<?php
/*
  Copyright 2021 Ganohr (email : ganohr@gmail.com)

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
if ( ! function_exists( 'gnr_copya_func' ) ) {
	// プラグイン本体
	function gnr_copya_func( $atts, $content ) {
		// プラグイン情報を定義
		$handle = 'ganohrs-copya';
		$src	= plugins_url( 'style.css', __FILE__ );
		$ver	= '0.0.3';

		// AMPページか否か判定しておく
		$is_amp = function_exists( 'gnr_is_amp' ) && gnr_is_amp() ? gnr_is_amp() : false;

		// AMPページ以外ならスタイルシートをエンキューする
		if ( ! wp_style_is( $handle ) && ! $is_amp ) {
			wp_enqueue_style(
				$handle,
				$src,
				false,
				$ver,
				'all'
			);
		}

		// オプションを展開
		extract(
			shortcode_atts(
				array(
					'label' 		 => 'Copyable:',
					'button'		 => 'Copy',
					'alert' 		 => 'Copied!',
					'cssprefix' 	 => 'copya-',
					'id'			 => 'auto',
					'textclick' 	 => 'yes',
					'readonly'		 => 'yes',
					'newline_escape' => 'yes',
					'alert_escape'	 => 'yes',
					'text'			 => 'Copyable Shortcode',
				),
				$atts
			)
		);

		// 基本はコンテンツの内容を優先するが、空なら「text」指定を取得する
		if ( ! empty( $content ) ) {
			$text = $content;
		}

		// “”,‘’に勝手に修正されるのを是正
		$text = str_replace(
			array( '“', '”', '″', '"', '&#8220;', '&#8221;', '&#8243;', '&quot;' ),
			'"',
			$text
		);
		$text = str_replace(
			array( '‘', '’', "'", '&#039;' ),
			"'",
			$text
		);

		// 「&nbsp;」,「 」で指定された空白を通常の半角スペース「 」へ置換する
		$text = str_replace(
			array( '&nbsp;', ' ', '&ensp;', ' ', '&emsp;', ' ' ),
			' ',
			$text
		);

		// テキストに含まれる改行をエスケープする
		$text = strip_tags(
			$newline_escape === 'yes' ? (
				str_replace(
					array( '<br>', '<br >', '<br />', "\r\n", "\r", "\n\n" ),
					"\n",
					$text
				)
			) : (
				$text
			)
		);

		// alertの内容が空以外なら、アラートを出す
		if ( ! empty( $alert ) ) {
			$alert = strip_tags(
				$alert_escape === 'yes' ? (
				str_replace(
					array( "\r\n", "\r", "\n" ),
					array( '\r\n', '\r', '\n' ),
					addslashes( $alert )
				)
				) : (
				$alert
				)
			);
			$alert = "alert('$alert');";
		}

		// 「id」未指定や「auto」が指定されている場合、IDはテキストのハッシュ値を採用
		if ( $id === 'auto' || empty( $id ) ) {
			$id = bin2hex( hash( 'crc32b', $text ) );
		}

		// 「id」を元に、onclickを構築
		$js_onclick = " onclick=\"document.getElementById('$id').select();document.execCommand('copy');$alert\"";

		// テキストクリックイベントが必要（'yes'）でAMPじゃないならクリックイベントを登録する
		if ( $textclick === 'yes' && ! $is_amp ) {
			$textclick = $js_onclick;
		} else {
			$textclick = '';
		}

		// ボタン用のタグを生成する。なおbuttonの内容が空文字だったりAMPページならボタンを無くす
		if ( empty( $button ) || $is_amp ) {
			$button = '';
		} else {
			$button = "<input type='button'$js_onclick value='$button' class='${cssprefix}button'/>";
		}

		// 「readonly」が「yes」以外なら読み込み専用にしない
		if ( $readonly !== 'yes' ) {
			$readonly = '';
		} else {
			$readonly = ' readonly';
		}

		// コピーする内容に改行が入っているならtextarea、入ってないならinput
		if ( strpos( $text, "\r" ) !== false
			|| strpos( $text, "\n" ) !== false
		) {
			$tag = <<<EOF
<br class="${cssprefix}br1" id="br1-$id">
<textarea id="$id"$textclick class="${cssprefix}text"$readonly>$text</textarea>
<br class="${cssprefix}br2" id="br2-$id">
EOF;
		} else {
			$text = str_replace( '"', '&quot;', $text );
			$tag  = <<<EOF
<input id="$id"$textclick type="text" value="$text" class="${cssprefix}text"$readonly/>
EOF;
		}

		// コード全体を返却する
		return <<<EOF
<div class="${cssprefix}outer" id="outer$id"><label class="${cssprefix}label" id="label$id">$label</label>$tag$button</div>
EOF;
	}
	add_shortcode( 'copya', 'gnr_copya_func', 9999 );
}

