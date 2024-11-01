<?php
/**
 * WordPress plugin.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @version 1.3.2
 * @package ycwp-qr-me
 */
 
/**
 * This class handles various shotcodes for YCWP QR Me plugin.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @since 1.0
 * @package ycwp-qr-me
 */
class YCWP_QR_Me_shortcodes {
    /**
     * QR Codes id
     *
     * @var int
     * @access private
     * @since 1.0
     */
    private $_id;
    
    /**
     * Define if the Javascript code is already active
     *
     * @var bool
     * @access private
     * @since 1.3
     */
    private $_js;
    
    /**
     * Initializes the properties.
     *
     * @param string $type
     * @since 1.2
     */
    public function __construct() {
        $this->_id = 0;
        $this->_js = false;
        
        //Adds the shortcodes
        add_shortcode( 'qrme'              , array( &$this, 'ycwp_qr_me_shortcode'                )   );
        add_shortcode( 'qrme_androidmarket', array( &$this, 'ycwp_qr_me_shortcode_android_market' )   );
        add_shortcode( 'qrme_contact'      , array( &$this, 'ycwp_qr_me_shortcode_contact'        )   );
        add_shortcode( 'qrme_email'        , array( &$this, 'ycwp_qr_me_shortcode_email'          )   );
        add_shortcode( 'qrme_geoloc'       , array( &$this, 'ycwp_qr_me_shortcode_geoloc'         )   );
        add_shortcode( 'qrme_github'       , array( &$this, 'ycwp_qr_me_shortcode_github'         )   );
        add_shortcode( 'qrme_sms'          , array( &$this, 'ycwp_qr_me_shortcode_sms'            )   );
        add_shortcode( 'qrme_steam'        , array( &$this, 'ycwp_qr_me_shortcode_steam'          )   );
        add_shortcode( 'qrme_tel'          , array( &$this, 'ycwp_qr_me_shortcode_tel'            )   );
        add_shortcode( 'qrme_twitter'      , array( &$this, 'ycwp_qr_me_shortcode_twitter'        )   );
        add_shortcode( 'qrme_url'          , array( &$this, 'ycwp_qr_me_shortcode_url'            )   );
        add_shortcode( 'qrme_viewsource'   , array( &$this, 'ycwp_qr_me_shortcode_viewsource'     )   );
        add_shortcode( 'qrme_wifi'         , array( &$this, 'ycwp_qr_me_shortcode_wifi'           )   );
    }
    
    /**
     * Handles URL shortcodes
     *
     * @since 1.2
     */
    public function ycwp_qr_me_shortcode_url( $atts, $content = null ) {
        $this->_id++;
        
        $attrs = shortcode_atts( array(
            'size' => get_option( 'ycwp_qr_me_size' ),
            'error' => get_option( 'ycwp_qr_me_error' ),
            'url' => '',
            'class' => '',
            'title' => '',
            'alt' => ''
        ), $atts );
        
        $qr = new QRCode_URL();
        
        $params = preg_replace( '/[^\w\-]/', ' ', $attrs['class'] );
        $params = array(
            'class' => $params . ' ycwp-qr-me-image ycwp-qr-me-url',
            'title' => $attrs['title'],
            'alt' => $attrs['alt'],
            'id' => $this->_id . '-ycwp-qr-me-shortcode'
        );
        
        if( empty( $content ) ) {
            $content = $attrs['url'];
        }
        
        $qr->set_size( $attrs['size'], $attrs['size'] );
        $qr->set_error_level( $attrs['error'] );
        
        if( !$qr->set_content( $content ) ) {
            echo $qr->error;
        } else {
            $image = $qr->QR( $params );
            return $this->_make_img( $image );
        }
    }
    
    /**
     * Handles SMS shortcodes
     *
     * @since 1.2
     */
    public function ycwp_qr_me_shortcode_sms( $atts, $content = null ) {
        $this->_id++;
        
        $attrs = shortcode_atts( array(
            'size' => get_option( 'ycwp_qr_me_size' ),
            'error' => get_option( 'ycwp_qr_me_error' ),
            'tel' => '',
            'message' => '',
            'class' => '',
            'title' => '',
            'alt' => ''
        ), $atts );
        
        $qr = new QRCode_SMS();
        
        $params = preg_replace( '/[^\w\-]/', ' ', $attrs['class'] );
        $params = array(
            'class' => $params . ' ycwp-qr-me-image ycwp-qr-me-sms',
            'title' => $attrs['title'],
            'alt' => $attrs['alt'],
            'id' => $this->_id . '-ycwp-qr-me-shortcode'
        );
        
        $sms = array();
        
        if( empty( $content ) ) {
            $sms['message'] = $attrs['message'];
        } else {
            $sms['message'] = $content;
        }
        
        $sms['number'] = $attrs['tel'];
        
        $qr->set_size( $attrs['size'], $attrs['size'] );
        $qr->set_error_level( $attrs['error'] );
        
        if( !$qr->set_content( $sms['number'], $sms['message'] ) ) {
            echo $qr->error;
        } else {
            $image = $qr->QR( $params );
            return $this->_make_img( $image );
        }
    }
    
    /**
     * Handles GeoLocation shortcodes
     *
     * @since 1.2
     */
    public function ycwp_qr_me_shortcode_geoloc( $atts, $content = null ) {
        $this->_id++;
        
        $attrs = shortcode_atts( array(
            'size' => get_option( 'ycwp_qr_me_size' ),
            'error' => get_option( 'ycwp_qr_me_error' ),
            'lat' => 0.0,
            'lon' => 0.0,
            'altitude' => 0,
            'u' => 0,
            'class' => '',
            'title' => '',
            'alt' => ''
        ), $atts );
        
        $qr = new QRCode_GeoLoc();
        
        $params = preg_replace( '/[^\w\-]/', ' ', $attrs['class'] );
        $params = array(
            'class' => $params . ' ycwp-qr-me-image ycwp-qr-me-geoloc',
            'title' => $attrs['title'],
            'alt' => $attrs['alt'],
            'id' => $this->_id . '-ycwp-qr-me-shortcode'
        );
        
        $geoloc = array();
        
        $geoloc['lat'] = $attrs['lat'];
        $geoloc['lon'] = $attrs['lon'];
        $geoloc['alt'] = $attrs['altitude'];
        $geoloc['u'] = $attrs['u'];
        
        $qr->set_size( $attrs['size'], $attrs['size'] );
        $qr->set_error_level( $attrs['error'] );
        
        if( !$qr->set_content( $geoloc['lat'], $geoloc['lon'],  $geoloc['alt'],  $geoloc['u'] ) ) {
            echo $qr->error;
        } else {
            $image = $qr->QR( $params );
            return $this->_make_img( $image );
        }
    }
    
    /**
     * Handles email shortcodes
     *
     * @since 1.2
     */
    public function ycwp_qr_me_shortcode_email( $atts, $content = null ) {
        $this->_id++;
        
        $attrs = shortcode_atts( array(
            'size' => get_option( 'ycwp_qr_me_size' ),
            'error' => get_option( 'ycwp_qr_me_error' ),
            'email' => '',
            'subject' => '',
            'body' => '',
            'class' => '',
            'title' => '',
            'alt' => ''
        ), $atts );
        
        $qr = new QRCode_Email();
        
        $params = preg_replace( '/[^\w\-]/', ' ', $attrs['class'] );
        $params = array(
            'class' => $params . ' ycwp-qr-me-image ycwp-qr-me-email',
            'title' => $attrs['title'],
            'alt' => $attrs['alt'],
            'id' => $this->_id . '-ycwp-qr-me-shortcode'
        );
        
        $email = array();
        
        if( empty( $content ) ) {
            $email['body'] = $attrs['body'];
        } else {
            $email['body'] = $content;
        }
        
        $email['subject'] = $attrs['subject'];
        $email['address'] = $attrs['email'];
        
        $qr->set_size( $attrs['size'], $attrs['size'] );
        $qr->set_error_level( $attrs['error'] );
        
        if( !$qr->set_content( $email['address'], $email['subject'], $email['body'] ) ) {
            echo $qr->error;
        } else {
            $image = $qr->QR( $params );
            return $this->_make_img( $image );
        }
    }
    
    /**
     * Handles telephone shortcodes
     *
     * @since 1.2
     */
    public function ycwp_qr_me_shortcode_tel( $atts, $content = null ) {
        $this->_id++;
        
        $attrs = shortcode_atts( array(
            'size' => get_option( 'ycwp_qr_me_size' ),
            'error' => get_option( 'ycwp_qr_me_error' ),
            'tel' => '',
            'class' => '',
            'title' => '',
            'alt' => ''
        ), $atts );
        
        $qr = new QRCode_Tel();
        
        $params = preg_replace( '/[^\w\-]/', ' ', $attrs['class'] );
        $params = array(
            'class' => $params . ' ycwp-qr-me-image ycwp-qr-me-tel',
            'title' => $attrs['title'],
            'alt' => $attrs['alt'],
            'id' => $this->_id . '-ycwp-qr-me-shortcode'
        );
        
        $tel = '';
        
        if( empty( $content ) ) {
            $tel = $attrs['tel'];
        } else {
            $tel = $content;
        }        
        
        $qr->set_size( $attrs['size'], $attrs['size'] );
        $qr->set_error_level( $attrs['error'] );
        
        if( !$qr->set_content( $tel ) ) {
            echo $qr->error;
        } else {
            $image = $qr->QR( $params );
            return $this->_make_img( $image );
        }
    }
    
    /**
     * Handles contact shortcodes
     *
     * @since 1.2
     */
    public function ycwp_qr_me_shortcode_contact( $atts, $content = null ) {
        $this->_id++;
        
        $attrs = shortcode_atts( array(
            'size' => get_option( 'ycwp_qr_me_size' ),
            'error' => get_option( 'ycwp_qr_me_error' ),
            'name' => '',
            'tel' => '',
            'email' => '',
            'memo' => '',
            'address' => '',
            'url' => '',
            'class' => '',
            'title' => '',
            'alt' => ''
        ), $atts );
        
        $qr = new QRCode_Contact();
        
        $params = preg_replace( '/[^\w\-]/', ' ', $attrs['class'] );
        $params = array(
            'class' => $params . ' ycwp-qr-me-image ycwp-qr-me-contact',
            'title' => $attrs['title'],
            'alt' => $attrs['alt'],
            'id' => $this->_id . '-ycwp-qr-me-shortcode'
        );
        
        $contact = array();
        $contact['name'] = $attrs['name'];
        $contact['tel'] = $attrs['tel'];
        $contact['email'] = $attrs['email'];
        $contact['memo'] = $attrs['memo'];
        $contact['address'] = $attrs['address'];
        $contact['url'] = $attrs['url'];
        
        $qr->set_size( $attrs['size'], $attrs['size'] );
        $qr->set_error_level( $attrs['error'] );
        
        if( !$qr->set_content( $contact ) ) {
            echo $qr->error;
        } else {
            $image = $qr->QR( $params );
            return $this->_make_img( $image );
        }
    }
    
    /**
     * Handles android market shortcodes
     *
     * @since 1.2
     */
    public function ycwp_qr_me_shortcode_android_market( $atts, $content = null ) {
        $this->_id++;
        
        $attrs = shortcode_atts( array(
            'size' => get_option( 'ycwp_qr_me_size' ),
            'error' => get_option( 'ycwp_qr_me_error' ),
            'package' => '',
            'class' => '',
            'title' => '',
            'alt' => ''
        ), $atts );
        
        $qr = new QRCode_Android_Market();
        
        $params = preg_replace( '/[^\w\-]/', ' ', $attrs['class'] );
        $params = array(
            'class' => $params . ' ycwp-qr-me-image ycwp-qr-me-androidmarket',
            'title' => $attrs['title'],
            'alt' => $attrs['alt'],
            'id' => $this->_id . '-ycwp-qr-me-shortcode'
        );
        
        $package = '';
        
        if( empty( $content ) ) {
            $package = $attrs['package'];
        } else {
            $package = $content;
        }
        
        $qr->set_size( $attrs['size'], $attrs['size'] );
        $qr->set_error_level( $attrs['error'] );
        
        if( !$qr->set_content( $package ) ) {
            echo $qr->error;
        } else {
            $image = $qr->QR( $params );
            return $this->_make_img( $image );
        }
    }
    
    /**
     * Handles github shortcodes
     *
     * @since 1.2
     */
    public function ycwp_qr_me_shortcode_github( $atts, $content = null ) {
        $this->_id++;
        
        $attrs = shortcode_atts( array(
            'size' => get_option( 'ycwp_qr_me_size' ),
            'error' => get_option( 'ycwp_qr_me_error' ),
            'path' => '',
            'class' => '',
            'title' => '',
            'alt' => ''
        ), $atts );
        
        $qr = new QRCode_GitHub();
        
        $params = preg_replace( '/[^\w\-]/', ' ', $attrs['class'] );
        $params = array(
            'class' => $params . ' ycwp-qr-me-image ycwp-qr-me-androidmarket',
            'title' => $attrs['title'],
            'alt' => $attrs['alt'],
            'id' => $this->_id . '-ycwp-qr-me-shortcode'
        );
        
        $path = '';
        
        if( empty( $content ) ) {
            $path = $attrs['path'];
        } else {
            $path = $content;
        }
        
        $qr->set_size( $attrs['size'], $attrs['size'] );
        $qr->set_error_level( $attrs['error'] );
        
        if( !$qr->set_content( $path ) ) {
            echo $qr->error;
        } else {
            $image = $qr->QR( $params );
            return $this->_make_img( $image );
        }
    }
    
    /**
     * Handles view-source shortcodes
     *
     * @since 1.2
     */
    public function ycwp_qr_me_shortcode_viewsource( $atts, $content = null ) {
        $this->_id++;
        
        $attrs = shortcode_atts( array(
            'size' => get_option( 'ycwp_qr_me_size' ),
            'error' => get_option( 'ycwp_qr_me_error' ),
            'uri' => '',
            'class' => '',
            'title' => '',
            'alt' => ''
        ), $atts );
        
        $qr = new QRCode_ViewSource();
        
        $params = preg_replace( '/[^\w\-]/', ' ', $attrs['class'] );
        $params = array(
            'class' => $params . ' ycwp-qr-me-image ycwp-qr-me-url',
            'title' => $attrs['title'],
            'alt' => $attrs['alt'],
            'id' => $this->_id . '-ycwp-qr-me-shortcode'
        );
        
        if( empty( $content ) ) {
            $content = $attrs['uri'];
        }
        
        $qr->set_size( $attrs['size'], $attrs['size'] );
        $qr->set_error_level( $attrs['error'] );
        
        if( !$qr->set_content( $content ) ) {
            echo $qr->error;
        } else {
            $image = $qr->QR( $params );
            return $this->_make_img( $image );
        }
    }
    
    /**
     * Handles wifi shortcodes
     *
     * @since 1.2
     */
    public function ycwp_qr_me_shortcode_wifi( $atts, $content = null ) {
        $this->_id++;
        
        $attrs = shortcode_atts( array(
            'size' => get_option( 'ycwp_qr_me_size' ),
            'error' => get_option( 'ycwp_qr_me_error' ),
            'authtype' => '',
            'ssid' => '',
            'passw' => '',
            'class' => '',
            'title' => '',
            'alt' => ''
        ), $atts );
        
        $qr = new QRCode_WiFi();
        
        $params = preg_replace( '/[^\w\-]/', ' ', $attrs['class'] );
        $params = array(
            'class' => $params . ' ycwp-qr-me-image ycwp-qr-me-wifi',
            'title' => $attrs['title'],
            'alt' => $attrs['alt'],
            'id' => $this->_id . '-ycwp-qr-me-shortcode'
        );
        
        $wifi = array();        
        $wifi['auth_type'] = $attrs['authtype'];
        $wifi['ssid'] = $attrs['ssid'];
        $wifi['passw'] = $attrs['passw'];
        
        $qr->set_size( $attrs['size'], $attrs['size'] );
        $qr->set_error_level( $attrs['error'] );
        
        if( !$qr->set_content( $wifi['auth_type'], $wifi['ssid'], $wifi['passw'] ) ) {
            echo $qr->error;
        } else {
            $image = $qr->QR( $params );
            return $this->_make_img( $image );
        }
    }
    
    /**
     * Handles steam shortcodes
     *
     * @since 1.3
     */
    public function ycwp_qr_me_shortcode_steam( $atts, $content = null ) {
        $this->_id++;
        
        $attrs = shortcode_atts( array(
            'size' => get_option( 'ycwp_qr_me_size' ),
            'error' => get_option( 'ycwp_qr_me_error' ),
            'command' => '',
            'subcommand' => '',
            'value' => '',
            'class' => '',
            'title' => '',
            'alt' => ''
        ), $atts );
        
        $qr = new QRCode_Steam();
        
        $params = preg_replace( '/[^\w\-]/', ' ', $attrs['class'] );
        $params = array(
            'class' => $params . ' ycwp-qr-me-image ycwp-qr-me-steam',
            'title' => $attrs['title'],
            'alt' => $attrs['alt'],
            'id' => $this->_id . '-ycwp-qr-me-shortcode'
        );
        
        if( empty( $params['class'] ) ) {
            unset( $params['class'] );
        }
        
        $steam = array();
        $steam['command'] = $attrs['command'];
        $steam['value'] = $attrs['value'];
        
        $qr->set_size( $attrs['size'], $attrs['size'] );
        $qr->set_error_level( $attrs['error'] );
        
        if( !empty( $attrs['subcommand'] ) ) {
            $steam['subcommand'] = $attrs['subcommand'];
            
            if( !$qr->set_content( 'steam-advanced-command', $steam ) ) {
                echo $qr->error;
            } else {
                $image = $qr->QR( $params );
                return $this->_make_img( $image );
            }
        } else {
            if( !$qr->set_content( 'steam-command', $steam ) ) {
                echo $qr->error;
            } else {
                $image = $qr->QR( $params );
                return $this->_make_img( $image );
            }
        }
    }
    
    /**
     * Handles twitter shortcodes
     *
     * @since 1.3
     */
    public function ycwp_qr_me_shortcode_twitter( $atts, $content = null ) {
        $this->_id++;
        
        $attrs = shortcode_atts( array(
            'size' => get_option( 'ycwp_qr_me_size' ),
            'error' => get_option( 'ycwp_qr_me_error' ),
            'type' => '',
            'id' => 0,
            'screen_name' => '',
            'slug' => '',
            'replyto' => 0,
            'message' => '',
            'class' => '',
            'title' => '',
            'alt' => ''
        ), $atts );
        
        $qr = new QRCode_Twitter();
        
        $params = preg_replace( '/[^\w\-]/', ' ', $attrs['class'] );
        $params = array(
            'class' => $params . ' ycwp-qr-me-image ycwp-qr-me-twitter',
            'title' => $attrs['title'],
            'alt' => $attrs['alt'],
            'id' => $this->_id . '-ycwp-qr-me-shortcode'
        );
        
        $twitter = array();
        $twitter['id'] = abs( $attrs['id'] );
        $twitter['screen_name'] = $attrs['screen_name'];
        $twitter['slug'] = $attrs['slug'];
        
        if( !empty( $content ) ) {
            if( $attrs['type'] == 'post' ) {
                $twitter['message'] = $content;
                $twitter['in_reply_to_status_id'] = abs( $attrs['replyto'] );
            }
            
            if( $attrs['type'] == 'urlshortener' ) {
                $twitter = $content;
            }
        } else {
            if( $attrs['type'] == 'post' ) {
                $twitter['message'] = $attrs['message'];
                $twitter['in_reply_to_status_id'] = abs( $attrs['replyto'] );
            }
        }
        
        $qr->set_size( $attrs['size'], $attrs['size'] );
        $qr->set_error_level( $attrs['error'] );
        
        if( empty( $params['class'] ) ) {
            unset( $params['class'] );
        }
        
        if( $attrs['type'] == 'timeline' OR $attrs['type'] == 'messages' OR $attrs['type'] == 'mentions' ) {
            if( !$qr->set_content( $attrs['type'] ) ) {
               echo $qr->error;
            } else {
                $image = $qr->QR( $params );
                return $this->_make_img( $image );
            }
        } else {
            if( !$qr->set_content( $attrs['type'], $twitter ) ) {
               echo $qr->error;
            } else {
                $image = $qr->QR( $params );
                return $this->_make_img( $image );
            }
        }
    }
    
    /**
     * Handles generic shortcodes
     *
     * @since 1.2
     */
    public function ycwp_qr_me_shortcode( $atts, $content = null ) {
        $this->_id++;
        
        $attrs = shortcode_atts( array(
            'size' => get_option( 'ycwp_qr_me_size' ),
            'error' => get_option( 'ycwp_qr_me_error' ),
            'content' => '',
            'class' => '',
            'title' => '',
            'alt' => ''
        ), $atts );
        
        $qr = new QRCode();
        
        $params = preg_replace( '/[^\w\-]/', ' ', $attrs['class'] );
        $params = array(
            'class' => $params . ' ycwp-qr-me-image ycwp-qr-me-simple',
            'title' => $attrs['title'],
            'alt' => $attrs['alt'],
            'id' => $this->_id . '-ycwp-qr-me-shortcode'
        );
        
        if( empty( $params['class'] ) ) {
            unset( $params['class'] );
        }
        
        if( empty( $content ) ) {
            $content = $attrs['content'];
        }
        
        $qr->set_size( $attrs['size'], $attrs['size'] );
        $qr->set_error_level( $attrs['error'] );
        
        if( !$qr->set_content( $content ) ) {
           echo $qr->error;
        } else {
            $image = $qr->QR( $params );
            return $this->_make_img( $image );
        }
    }
    
    /**
     * Create divs and toggle button and returns them.
     *
     * @param string $data Data to insert in the <img> tag
     * @return string
     * @access private
     * @since 1.0
     */
    private function _make_img( $data ) {
        $return  = $this->_ycwp_qr_me_make_style_js();
        $return .= '<div class="ycwp-qr-me-box">';
        $return .= '<a href="javascript:void(0)" title="Toggle QR Code" alt="Toggle QR Code" class="ycwp-qr-me-toggle"></a><br />';
        $return .= $data;
        $return .= '</div>';
        
        return $return;
    }
    
    ///// DEPRECATED METHODS /////
    
    /**
     * Print jQuery code for show/hide effect
     *
     * @return string
     * @since 1.0
     * @deprecated 1.3
     */
    private function _ycwp_qr_me_make_style_js() {
        if( get_option( 'ycwp_qr_me_add_to_the_content' ) != 'true' AND !$this->_js ) {
            $this->_js = true;
            
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
            var ycwp_qr_me_hide = true;
            
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
    }
}
 ?>