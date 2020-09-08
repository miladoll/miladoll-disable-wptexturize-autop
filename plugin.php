<?php
/*
    Plugin Name: miladoll-disable-wptexturize-autop
    Plugin URI: https://miladoll.jp/
    Description: `miladoll-disable-wptexturize-autop` disable wptexturize and autop but escape ampasand
    Version: 0.1.2
    Author: MILADOLL Decchi
    Author URI: https://miladoll.jp/
    License: MIT
*/

class miladoll_disable_wptexturize_autop {
    public function miladoll_disable_wptexturize_autop() {
        self::disable_wptexturize_autop();
    }

    /*
        ヘルパメソッド
    */
    // クラス名取得
    public function get_class_name() {
        return( get_class( $this ) );
    }
    // & を &amp;
    public static function replace_amp ( $content ) {
        return( preg_replace( '/&(?![^&;]{1,10};)/', '&amp;', $content ) );
    }

    /*
        メインブロック
    */
    private static function disable_wptexturize_autop() {
        // @see https://on-ze.com/archives/2967
        add_filter( 'run_wptexturize', '__return_false', PHP_INT_MAX );

        /*
            勝手に改行を <p></p> にしたりするのを止める
            @see https://webkikaku.co.jp/blog/wordpress/wordpress-automatic-forming-control/
            @see https://setting-tool.net/wordpress-p-br-auto-format
        */
        add_action('init', function() {
            remove_filter('the_title', 'wpautop');
            remove_filter('the_title', 'wptexturize');
            // ほんとは 'wptexturize' は remove しなくていいはずなのだが念のためだ
            remove_filter('the_content', 'wpautop');
            remove_filter('the_content', 'wptexturize');
            remove_filter('the_excerpt', 'wpautop');
            remove_filter('the_excerpt', 'wptexturize');
            remove_filter('the_editor_content', 'wp_richedit_pre');
        });
        add_filter('tiny_mce_before_init', function($init) {
            // @see http://wpcj.net/727
            $init['wpautop'] = false;
            // $init['apply_source_formatting'] = false;
            return $init;
        });
        /*
            でも&はエスケープしたいねん
        */
        // @see https://wp-setting.info/setting/stop-wptexturize.html
        add_filter(
            'the_content',
            function( $content ) {
                return( self::replace_amp( $content ) );
            }
        );
        add_filter(
            'the_excerpt',
            function( $content ) {
                return( self::replace_amp( $content ) );
            }
        );
        add_filter(
            'the_title',
            function( $content ) {
                return( self::replace_amp( $content ) );
            }
        );
        add_filter(
            'comment_text',
            function( $content ) {
                return( self::replace_amp( $content ) );
            }
        );

    }

}

new miladoll_disable_wptexturize_autop();
