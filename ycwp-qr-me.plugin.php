<?php
/*
Plugin Name: YCWP QR Me
Description: YCWP QR Me is a simple plugin that creates and displays qr code images in your blog pages.
Version: 1.3.2
License: GPL v2 or later
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Author: Nicola Mustone <mail@nicolamustone.it>
Author URI: http://www.nicolamustone.it

YCWP QR Me is a simple plugin that create and display qr code images in your blog pages. It provides also a configurable and useful widget and shortcodes.
You can add your own QR Code in a widget ready sidebar, or in a post using shortcodes.
You can also automatically add your preconfigured QR Code at the end of each post and choose if you want to display it only on single post pages.
*/

/*
Copyright 2012 Nicola Mustone (mail@nicolamustone.it)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

/**
 * WordPress plugin.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @version 1.3.2
 * @package ycwp-qr-me
 */

/**
 * @see QRCode
 * @see QRCode_Android_Market
 * @see QRCode_Contact
 * @see QRCode_Email
 * @see QRCode_GeoLoc
 * @see QRCode_GitHub
 * @see QRCode_SMS
 * @see QRCode_Steam
 * @see QRCode_Tel
 * @see QRCode_Twitter
 * @see QRCode_URL
 * @see QRCode_ViewSource
 * @see QRCode_WiFi
 */
require_once( 'class.qrcode.php' );
require_once( 'class.qrcode-ext/class.qrcode_androidmarket.php' );
require_once( 'class.qrcode-ext/class.qrcode_contact.php' );
require_once( 'class.qrcode-ext/class.qrcode_email.php' );
require_once( 'class.qrcode-ext/class.qrcode_geoloc.php' );
require_once( 'class.qrcode-ext/class.qrcode_github.php' );
require_once( 'class.qrcode-ext/class.qrcode_sms.php' );
require_once( 'class.qrcode-ext/class.qrcode_steam.php' );
require_once( 'class.qrcode-ext/class.qrcode_tel.php' );
require_once( 'class.qrcode-ext/class.qrcode_twitter.php' );
require_once( 'class.qrcode-ext/class.qrcode_url.php' );
require_once( 'class.qrcode-ext/class.qrcode_viewsource.php' );
require_once( 'class.qrcode-ext/class.qrcode_wifi.php' );

/**
 * @see YCWP_QR_Me_Widget
 */
require_once( 'ycwp-qr-me.widget.php' );

/**
 * @see YCWP_QR_Me_shortcodes
 */
require_once( 'ycwp-qr-me.shortcodes.php' );

/**
 * This class can handle qr code images through QRCode class (class.qrcode.php).
 * It uses Google Charts APIs to generate QR Code image used in blog posts.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @since 1.0
 * @package ycwp-qr-me
 */
class YCWP_QR_Me {
    /**
     * YCWP QR Me version
     *
     * @var string
     */
    const VERSION = '1.3.2';
    
    /**
     * QRCode class instance
     *
     * @var object
     * @access private
     * @see QRCode
     * @since 1.0
     */
    protected $_qr;
    
    /**
     * Post permalink
     *
     * @var string
     * @access private
     * @since 1.0
     */
    private $_the_permalink;
    
    /**
     * Post title
     *
     * @var string
     * @access private
     * @since 1.0
     */
    private $_the_title;
    
    /**
     * QR Code id
     *
     * @var int
     * @access private
     * @since 1.0
     */
    private $_id;
    
    /**
     * Initializes properties, sets filters, actions and shortcodes
     *
     * @since 1.0
     */
    public function __construct() {        
        //Properties initialization
        $this->_qr = null;
        $this->_the_permalink = '';
        $this->_the_title = '';
        $this->_id = 0;
        
        //Localization initialization
        add_action( 'init', array( &$this, 'ycwp_qr_me_localize' ) );
        
        //Filter functions
        add_filter( 'the_content', array( &$this, 'ycwp_qr_me_print_image' ) );
        
        //Initialize option group in backend.
        add_action( 'admin_init', array( &$this, 'ycwp_qr_me_register_opt_group' ) );
        //Set activation hook.
        register_activation_hook( __FILE__, array( &$this, 'ycwp_qr_me_set_defaults_on_init' ) );
        //Add the option page link.
        add_action( 'admin_menu', array( &$this, 'ycwp_qr_me_option_page' ) );
        //Add custom scripts.
        add_action( 'wp_enqueue_scripts', array( &$this, 'ycwp_qr_me_enqueue_stuff' ) );
        //Add tinyMCE Buttons
        add_action( 'init', array( &$this, 'ycwp_qr_me_mce' ) );
        
        
        //Initialize the class QRCode
        $this->_qr = new QRCode();
    }
    
    /**
     * Enqueues jQuery scripts and styles
     *
     * @return void
     * @since 1.0
     */
    public function ycwp_qr_me_enqueue_stuff() {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-easing', WP_PLUGIN_URL . '/ycwp-qr-me/js/jquery-easing1.3.js', array( 'jquery' ), '1.3' );
        
        //Looks first for user's style
        if( file_exists( TEMPLATEPATH . '/ycwp-qr-me.css' ) ) {
            $css = get_template_directory_uri() . '/ycwp-qr-me.css';
        } else {
            $css = WP_PLUGIN_URL . '/ycwp-qr-me/css/ycwp-qr-me.css';
        }
        
        wp_enqueue_style( 'ycwp-qr-me-style', $css, false, self::VERSION, 'screen' );
    }
    
    /**
     * Adds custom TinyMCE plugin and button
     *
     * @since 1.3
     */
    public function ycwp_qr_me_mce() {
        if( !current_user_can( 'edit_posts' ) AND !current_user_can( 'edit_pages' ) ) {
            return;
        }
        
        if( get_user_option( 'rich_editing' ) == 'true' ) {
            add_filter( 'mce_external_plugins', array( &$this, 'ycwp_qr_me_register_mce' ) );
            add_filter( 'mce_buttons', array( &$this, 'ycwp_qr_me_register_mce_button' ) );
        }
    }
    
    /**
     * Registers custom TinyMCE button
     *
     * @since 1.3
     */
    public function ycwp_qr_me_register_mce_button( $buttons ) {
        array_push( $buttons, '|', 'YCWP_QR_Me', 'YCWP_QR_Me_Twitter', 'YCWP_QR_Me_Steam' );
        
        return $buttons;
    }
    
    /**
     * Registers TinyMCE plugin
     *
     * @since 1.3
     */
    public function ycwp_qr_me_register_mce( $plugin_array ) {
        $plugin_array['YCWP_QR_Me'] = WP_PLUGIN_URL . '/ycwp-qr-me/js/ycwp-qr-me-tinymce.js';
        
        return $plugin_array;
    }
    
    /**
     * Adds an option page
     *
     * @since 1.0
     */
    public function ycwp_qr_me_option_page() {
        add_options_page( 'YCWP QR Me options', 'YCWP QR Me', 'administrator', 'ycwp-qr-me-options', array( &$this, 'ycwp_qr_me_option_form' ) );
        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ) , array( &$this, 'ycwp_qr_me_settings_link' ), 10, 2 );
    }
    
    /**
     * Adds the Settings link to the plugin activation page
     *
     * @since 1.0
     */
    public function ycwp_qr_me_settings_link( $links, $file ) {
        $link = '<a href="options-general.php?page=ycwp-qr-me-options">' . __( 'Settings', 'ycwp-qr-me' ) .'</a>';
        array_unshift( $links, $link );
        
        return $links;
    }
    
    /**
     * Defines the defaults values for the plugin.
     *
     * @since 1.0
     */
    public function ycwp_qr_me_set_defaults_on_init() {
        update_option( 'ycwp_qr_me_content', 'permalink' );
        update_option( 'ycwp_qr_me_size', $this->_qr->_width );
        update_option( 'ycwp_qr_me_error', $this->_qr->_settings['chld'] );
        update_option( 'ycwp_qr_me_classes', ' ' );
        update_option( 'ycwp_qr_me_add_to_the_content', 'true' );
        update_option( 'ycwp_qr_me_only_posts', 'false' );
        update_option( 'ycwp_qr_me_hide', 'true' );
        update_option( 'ycwp_qr_me_effect', 'fade' );
    }
    
    /**
     * Registers plugin options
     *
     * @since 1.0
     */
    public function ycwp_qr_me_register_opt_group() {
        register_setting( 'ycwp_qr_me_opt_group', 'ycwp_qr_me_content' );
        register_setting( 'ycwp_qr_me_opt_group', 'ycwp_qr_me_size' );
        register_setting( 'ycwp_qr_me_opt_group', 'ycwp_qr_me_error' );
        register_setting( 'ycwp_qr_me_opt_group', 'ycwp_qr_me_classes' );
        register_setting( 'ycwp_qr_me_opt_group', 'ycwp_qr_me_add_to_the_content' );
        register_setting( 'ycwp_qr_me_opt_group', 'ycwp_qr_me_only_posts' );
        register_setting( 'ycwp_qr_me_opt_group', 'ycwp_qr_me_hide' );
        register_setting( 'ycwp_qr_me_opt_group', 'ycwp_qr_me_effect' );
    }
    
    /**
     * Localizes the plugin
     *
     * @since 1.0
     */
    public function ycwp_qr_me_localize() {
        load_plugin_textdomain( 'ycwp-qr-me', false, basename( dirname( __FILE__ ) ) . '/i18n' );
    }
    
    /**
     * Print a QR Code image
     *
     * @param string $content
     * @return string
     * @version 1.1
     * @since 1.0
     */
    public function ycwp_qr_me_print_image( $content ) {
        if( ( !is_single() AND !is_home() AND !is_category() AND !is_archive() AND !is_tag() ) OR ( is_search() AND is_page() ) ) {
            return $content;
        }
        
        $return = $content;
        $add_to_content = get_option( 'ycwp_qr_me_add_to_the_content' );
        $only_posts = get_option( 'ycwp_qr_me_only_posts' );
        
        if( $add_to_content === 'true' ) {
            if( $only_posts === 'true' AND !is_single() ) {
                return $return;
            } else {
                return $this->ycwp_qr_me_add_qr_code( $return );
            }
        }
        
        return $content;
    }
    
    /**
     * Add a QR Code image to the post content.
     *
     * @param string $content
     * @return $string
     * @since 1.0
     */
    public function ycwp_qr_me_add_qr_code( $content ) {
        $qr = $this->_qr;
        $size = get_option( 'ycwp_qr_me_size' );
        $this->_id++;
        $this->_the_permalink = get_permalink();
        $this->_the_title = get_the_title();
        
        $params = preg_replace( '/[^\w\-,]/', '', get_option( 'ycwp_qr_me_classes' ) );
        $params = array(
            'class' => str_replace( ',', ' ', $params ) . ' ycwp-qr-me-image',
            'alt' => 'QR Code',
            'title' => $this->_the_title,
            'id' => $this->_id . '-ycwp-qr-me-plugin'
        );
        
        $qr->set_size( $size, $size );
        $qr->set_error_level( get_option( 'ycwp_qr_me_error' ) );
        
        
        
        switch( get_option( 'ycwp_qr_me_content' ) ) {
            case 'permalink':
                $qr_content = urlencode( $this->_the_permalink );
                break;
            case 'fb':
                $qr_content = 'http://www.facebook.com/sharer.php?u=' . urlencode( $this->_the_permalink ) . '&amp;t=' . urlencode( $this->_the_title );
                break;
            case 'twitter':
                $qr_content = 'http://twitter.com/intent/tweet?source=sharethiscom&amp;text=' . urlencode( $this->_the_title ) . '&amp;url=' . urlencode( $this->_the_permalink );
                break;
            case 'gplus':
                $qr_content = 'https://plusone.google.com/_/+1/confirm?hl=' . get_locale() . '&amp;url=' . urlencode( $this->_the_permalink );
        }
        
        if( !$qr->set_content( $qr_content ) ) {
            echo $qr->error;
        } else {        
            $image = $qr->QR( $params );
            
            $return  = ( $this->_id < 2 ) ? $this->_ycwp_qr_me_make_style_js() : '';
            $return .= '<div class="ycwp-qr-me-box">';
            $return .= '<a href="javascript:void(0)" title="Toggle QR Code" alt="Toggle QR Code" class="ycwp-qr-me-toggle"></a><br />';
            $return .= $image;
            $return .= '</div>';
            return $content . $return;
        }
    }
    
    /**
     * Prints a options form
     *
     * @since 1.0
     */
    public function ycwp_qr_me_option_form() {
        ?>
        <script type="text/javascript">
        jQuery( document ).ready( function( $ ) {
            if( !$( '#ycwp_qr_me_add_to_the_content' ).is( ':checked' ) ) {
                $( '#ycwp_qr_me_only_posts' ).attr( 'disabled', 'disabled' );
                $( '#ycwp_qr_me_hide' ).attr( 'disabled', 'disabled' );
            }
                
            $( '#ycwp_qr_me_add_to_the_content' ).click( function() {
            
                if( !$( '#ycwp_qr_me_add_to_the_content' ).is( ':checked' ) ) {
                    $( '#ycwp_qr_me_only_posts' ).attr( 'disabled', 'disabled' );
                    $( '#ycwp_qr_me_hide' ).attr( 'disabled', 'disabled' );
                } else {
                    $( '#ycwp_qr_me_only_posts' ).removeAttr( 'disabled' );
                    $( '#ycwp_qr_me_hide' ).removeAttr( 'disabled' );
                }
                
            } );
        });
        </script>
        <div class="wrap">
            <div class="icon32" id="icon-options-general"></div>
            <h2><?php _e( 'YCWP QR Me Configuration', 'ycwp-qr-me' ) ?></h2>
            
            <form method="post" action="options.php">
                <?php settings_fields( 'ycwp_qr_me_opt_group' ) ?>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row">
                                <label for="ycwp_qr_me_content"><?php _e( 'Content', 'ycwp-qr-me' ); ?>:</label>
                            </th>
                            <td>
                                <select name="ycwp_qr_me_content" id="ycwp_qr_me_content">
                                    <option value="permalink" <?php selected( get_option( 'ycwp_qr_me_content' ), 'permalink' ); ?>><?php _e( 'Post permalink', 'ycwp-qr-me' ); ?></option>
                                    <option value="fb" <?php selected( get_option( 'ycwp_qr_me_content' ), 'fb' ); ?>><?php _e( 'Share on Facebook link', 'ycwp-qr-me' ); ?></option>
                                    <option value="twitter" <?php selected( get_option( 'ycwp_qr_me_content' ), 'twitter' ); ?>><?php _e( 'Share on Twitter link', 'ycwp-qr-me' ); ?></option>
                                    <option value="gplus" <?php selected( get_option( 'ycwp_qr_me_content' ), 'gplus' ); ?>><?php _e( 'Google +1 link', 'ycwp-qr-me' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="ycwp_qr_me_size"><?php _e( 'Image size', 'ycwp-qr-me' ); ?>:</label>
                            </th>
                            <td>
                                <select name="ycwp_qr_me_size" id="ycwp_qr_me_size">
                                    <option value="100" <?php selected( get_option( 'ycwp_qr_me_size'), '100' ); ?>>100x100 px</option>
                                    <option value="150" <?php selected( get_option( 'ycwp_qr_me_size'), '150' ); ?>>150x150 px</option>
                                    <option value="200" <?php selected( get_option( 'ycwp_qr_me_size'), '200' ); ?>>200x200 px</option>
                                    <option value="250" <?php selected( get_option( 'ycwp_qr_me_size'), '250' ); ?>>250x250 px</option>
                                    <option value="300" <?php selected( get_option( 'ycwp_qr_me_size'), '300' ); ?>>300x300 px</option>
                                </select>
                                <span class="description"><?php _e( 'QR Code image size ( 200x200px raccomanded )', 'ycwp-qr-me' ); ?>.</span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="ycwp_qr_me_error"><?php _e( 'Error correction level', 'ycwp-qr-me' ); ?>:</label>
                            </th>
                            <td>
                                <select name="ycwp_qr_me_error" id="ycwp_qr_me_error">
                                    <option value="L" <?php selected( get_option( 'ycwp_qr_me_error' ), 'L' ); ?>>L</option>
                                    <option value="M" <?php selected( get_option( 'ycwp_qr_me_error' ), 'M' ); ?>>M</option>
                                    <option value="Q" <?php selected( get_option( 'ycwp_qr_me_error' ), 'Q' ); ?>>Q</option>
                                    <option value="H" <?php selected( get_option( 'ycwp_qr_me_error' ), 'H' ); ?>>H</option>
                                </select>
                                <br /><span class="description"><?php _e( 'L = 7% of codewords can be restored', 'ycwp-qr-me' ); ?>.</span>
                                <br /><span class="description"><?php _e( 'M = 15% of codewords can be restored', 'ycwp-qr-me' ); ?>.</span>
                                <br /><span class="description"><?php _e( 'Q = 25% of codewords can be restored', 'ycwp-qr-me' ); ?>.</span>
                                <br /><span class="description"><?php _e( 'H = 30% of codewords can be restored', 'ycwp-qr-me' ); ?>.</span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="ycwp_qr_me_classes"><?php _e( 'Additional image classes', 'ycwp-qr-me' ); ?>:</label>
                            </th>
                            <td>
                                <input type="text" name="ycwp_qr_me_classes" id="ycwp_qr_me_classes" value="<?php echo get_option( 'ycwp_qr_me_classes' ); ?>" />
                                <span class="description"><?php _e( 'Comma separated classes and only <strong>alphanumeric chars</strong>, <strong>-</strong> and <strong>_</strong>', 'ycwp-qr-me' ); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="ycwp_qr_me_add_to_the_content"><?php _e( 'Add QR Code to posts content', 'ycwp-qr-me' ); ?>:</label>
                            </th>
                            <td>
                                <input type="checkbox" name="ycwp_qr_me_add_to_the_content" id="ycwp_qr_me_add_to_the_content" value="true" <?php checked ( get_option( 'ycwp_qr_me_add_to_the_content' ), 'true' ); ?> />
                                <span class="description"><?php _e( 'If not checked, all QR Codes generated with a shortcode, will be not displayed on startup.', 'ycwp-qr-me' ); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="ycwp_qr_me_only_posts"><?php _e( 'Display QR Code only on single posts page', 'ycwp-qr-me' ); ?>:</label>
                            </th>
                            <td>
                                <input type="checkbox" name="ycwp_qr_me_only_posts" id="ycwp_qr_me_only_posts" value="true" <?php checked ( get_option( 'ycwp_qr_me_only_posts' ), 'true' ); ?> />
                                <span class="description"><?php _e( 'If checked, only shows QR Code on posts page', 'ycwp-qr-me' ); ?>.</span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="ycwp_qr_me_hide"><?php _e( 'Hide on startup', 'ycwp-qr-me' ); ?>:</label>
                            </th>
                            <td>
                                <input type="checkbox" name="ycwp_qr_me_hide" id="ycwp_qr_me_hide" value="true" <?php checked ( get_option( 'ycwp_qr_me_hide' ), 'true' ); ?> />
                                <span class="description"><?php _e( 'If checked, only display a link to show the QR Code, also for shortcodes', 'ycwp-qr-me' ); ?>.</span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="ycwp_qr_me_effect"><?php _e( 'Show/Hide effect', 'ycwp-qr-me' ); ?>:</label>
                            </th>
                            <td>
                                <select name="ycwp_qr_me_effect" id="ycwp_qr_me_effect">
                                    <option value="fade" <?php selected( get_option( 'ycwp_qr_me_effect' ), 'fade' ); ?>>fade</option>
                                    <option value="slide" <?php selected( get_option( 'ycwp_qr_me_effect' ), 'slide' ); ?>>slide</option>
                                    <option value="no_effect" <?php selected( get_option( 'ycwp_qr_me_effect' ), 'no_effect' ); ?>>no effect</option>
                                </select>
                                <span class="description"><?php _e( 'jQuery effect', 'ycwp-qr-me' ); ?>.</span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"></th>
                            <td>
                                <?php submit_button() ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <?php
    }
    
    /**
     * Print jQuery code for show/hide effect
     *
     * @return string
     * @since 1.0
     */
    private function _ycwp_qr_me_make_style_js() {
        switch( get_option( 'ycwp_qr_me_effect' ) ) {
            case 'fade':       $effect = 'fade';               break;
            case 'slide':      $effect = 'slide';              break;
            case 'no_effect':  $effect = 'no_effect';          break;
            default:           $effect = 'fade';
        }
        
        if( $effect == 'fade' ) {
            $toggle_function = '
            $( ".ycwp-qr-me-toggle" ).click( function() {
                $(this).parent().find( ".ycwp-qr-me-image" ).fadeToggle( "slow" );
                $(this).text( $(this).text() == "' . __( 'Show QR Code', 'ycwp-qr-me' ) . '" ? "' . __( 'Hide QR Code', 'ycwp-qr-me' ) . '" : "' . __( 'Show QR Code', 'ycwp-qr-me' ) . '" );
            });';
        } else if( $effect == 'slide' ) {
            $toggle_function = '
            $( ".ycwp-qr-me-toggle" ).click( function() {
                $(this).parent().find( ".ycwp-qr-me-image" ).slideToggle( "slow" );
                $(this).text( $(this).text() == "' . __( 'Show QR Code', 'ycwp-qr-me' ) . '" ? "' . __( 'Hide QR Code', 'ycwp-qr-me' ) . '" : "' . __( 'Show QR Code', 'ycwp-qr-me' ) . '" );
            });';
        } else if( $effect == 'no_effect' ) {
            $toggle_function = '
            $( ".ycwp-qr-me-toggle" ).click( function() {
                $(this).parent().find( ".ycwp-qr-me-image" ).toggle();
                $(this).text( $(this).text() == "' . __( 'Show QR Code', 'ycwp-qr-me' ) . '" ? "' . __( 'Hide QR Code', 'ycwp-qr-me' ) . '" : "' . __( 'Show QR Code', 'ycwp-qr-me' ) . '" );
            });';
        }
        
        $style_js = '
        <script type="text/javascript">
        var ycwp_qr_me_hide = ' . ( get_option( 'ycwp_qr_me_hide' ) ? 'true' : 'false' ) .';
        
        jQuery( document ).ready( function( $ ) {            
            if( ycwp_qr_me_hide ) {
                $( ".ycwp-qr-me-image" ).hide();
            }
            
            if( ycwp_qr_me_hide ) {
                $( ".ycwp-qr-me-toggle" ).text( "' . __( 'Show QR Code', 'ycwp-qr-me' ) . '" );
            } else {
                $( ".ycwp-qr-me-toggle" ).text( "' . __( 'Hide QR Code', 'ycwp-qr-me' ) . '" );
            }
            
            ' . $toggle_function  . '
        });
        </script>';
        
        $style_js .= '
        <style>
        .ycwp-qr-me-box {
            width: ' . get_option( 'ycwp_qr_me_size' ) . 'px;
        }
        </style>';
        
        return $style_js;
    }
    
    /* ===== DEPRECATED ===== */
    /**
     * Retrives the permalink of the current post
     *
     * @since 1.0
     * @deprecated 1.3.2
     */
    public function ycwp_qr_me_retrive_post_permalink( $the_permalink ) {
        $this->_the_permalink = $the_permalink;
        return $the_permalink;
    }
    
    /**
     * Retrives the title of the current post
     *
     * @since 1.0
     * @deprecated 1.3.2
     */
    public function ycwp_qr_me_retrive_post_title( $the_title ) {
        $this->_the_title = $the_title;
        return $the_title;
    }
}

/**
 * Starts the plugin!
 */
if( class_exists( 'QRCode' )                AND
    class_exists( 'QRCode_Android_Market' ) AND
    class_exists( 'QRCode_Contact' )        AND
    class_exists( 'QRCode_Email' )          AND
    class_exists( 'QRCode_GeoLoc' )         AND
    class_exists( 'QRCode_GitHub' )         AND
    class_exists( 'QRCode_SMS' )            AND
    class_exists( 'QRCode_Steam' )          AND
    class_exists( 'QRCode_Tel' )            AND
    class_exists( 'QRCode_Twitter' )        AND
    class_exists( 'QRCode_URL' )            AND
    class_exists( 'QRCode_ViewSource' )     AND
    class_exists( 'QRCode_WiFi' )           AND
    class_exists( 'YCWP_QR_Me' ) ) {
    
    $YCWP_QR_Me = new YCWP_QR_Me();
    
    if( class_exists( 'YCWP_QR_Me_shortcodes' ) ) {
        $YCWP_QR_Me_shortcodes = new YCWP_QR_Me_shortcodes();
    }
}
?>