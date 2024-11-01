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
 * QRCode_Twitter can handle Twitter URIs and generate QR Code with them.
 * When a device scans these QR Codes, it should open the application of Twitter.
 * Only devices that have Twitter app can correctly handle these QR Codes.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @since 1.3
 * @package ycwp-qr-me
 * @subpackage qrcode
 */
class QRCode_Twitter extends QRCode {
    /**
     * Twitter URIs scheme
     * @var string
     * @access private
     * @since 1.3
     */
    private $_scheme;
    
    /**
     * Invoke parent constructor.
     *
     * @since 1.3
     */
    public function __construct() {
        parent::__construct();
        $this->_scheme = 'twitter://';
    }
    
    /**
     * Sets Twitter URI as content.
     *
     * @param string $type
     * @param string|array $value
     * @return bool
     * @since 1.3
     */
    public function set_content( $type, $value = null ) {
        switch( strtolower( $type ) ) {
            case 'user'        : return $this->_set_content_user( $value )                                         ; break;
            case 'status'      : return $this->_set_content_status( $value )                                       ; break;
            case 'timeline'    : return $this->_set_content_timeline()                                             ; break;
            case 'mentions'    : return $this->_set_content_mentions()                                             ; break;
            case 'messages'    : return $this->_set_content_messages()                                             ; break;
            case 'list'        : return $this->_set_content_list( $value['screen_name'], $value['slug'] )          ; break;
            case 'post'        : return $this->_set_content_post( $value['message'], $value['in_reply_to_status_id'] ); break;
            case 'urlshortener': return $this->_set_content_url_shortener( $value )                                ; break;
            default:
                $this->error = sprintf( __( 'Unknown type: %s', 'ycwp-qr-me' ), $type );
                return false;
        }
    }
    
    /**
     * Sets Twitter user URI as content.
     *
     * @param string|int $value User screen name or ID
     * @return bool
     * @access private
     * @since 1.3
     */
    private function _set_content_user( $value ) {
        $base = $this->_scheme . 'user?';
        
        if( !is_array( $value ) AND ( !isset( $value['screen_name'] ) OR !isset( $value['id'] ) ) ) {
            $this->error = __( 'Twitter user needs a user id or a screen name.', 'ycwp-qr-me' );
            return false;
        }
        
        if( ( int ) $value['id'] ) {
            if( !$this->_set_property( 'chl', $base . 'id=' . $value['id'] ) ) {
                $this->error = sprintf( __( 'Unable to set Twitter user ID %s as content.', 'ycwp-qr-me' ), $value['id'] );
                return false;
            }
            
            return true;
        } else if( ( string ) $value['screen_name'] ) {
            if( !$this->_set_property( 'chl', $base . 'screen_name=' . urlencode( $value['screen_name'] ) ) ) {
                $this->error = sprintf( __( 'Unable to set Twitter user screen name %s as content.', 'ycwp-qr-me' ), $value['screen_name'] );
                return false;
            }
            
            return true;
        }
        
        $this->error = __( 'Invalid user ID or screen name.', 'ycwp-qr-me' );
        return false;
    }
    
    /**
     * Sets Twitter status URI as content.
     *
     * @param int $id
     * @return bool
     * @access private
     * @since 1.3
     */
    private function _set_content_status( $id ) {
        $base = $this->_scheme . 'status?id=';
        
        if( !is_array( $id ) AND !isset( $id['id'] ) ) {
            $this->error = __( 'Twitter status needs a user id.', 'ycwp-qr-me' );
            return false;
        }
        
        if( ( int ) $id['id'] ) {
            if( !$this->_set_property( 'chl', $base . $id['id'] ) ) {
                $this->error = sprintf( __( 'Unable to set Twitter status ID %s as content.', 'ycwp-qr-me' ), $id['id'] );
                return false;
            }
            
            return true;
        }
        
        $this->error = __( 'Invalid status ID.', 'ycwp-qr-me' );
        return false;
    }
    
    /**
     * Sets Twitter timeline URI as content.
     *
     * @return bool
     * @access private
     * @since 1.3
     */
    private function _set_content_timeline() {
        if( !$this->_set_property( 'chl', $this->_scheme . 'timeline' ) ) {
            $this->error = __( 'Unable to set Twitter timeline as content.', 'ycwp-qr-me' );
            return false;
        }
        
        return true;
    }
    
    /**
     * Sets Twitter mentions URI as content.
     *
     * @return bool
     * @access private
     * @since 1.3
     */
    private function _set_content_mentions() {
        if( !$this->_set_property( 'chl', $this->_scheme . 'mentions' ) ) {
            $this->error = __( 'Unable to set Twitter mentions as content.', 'ycwp-qr-me' );
            return false;
        }
        
        return true;
    }
    
    /**
     * Sets Twitter messages URI as content.
     *
     * @return bool
     * @access private
     * @since 1.3
     */
    private function _set_content_messages() {
        if( !$this->_set_property( 'chl', $this->_scheme . 'messages' ) ) {
            $this->error = __( 'Unable to set Twitter messages as content.', 'ycwp-qr-me' );
            return false;
        }
        
        return true;
    }
    
    /**
     * Sets Twitter list URI as content.
     *
     * @param string $screen_name
     * @param string $slug
     * @return bool
     * @access private
     * @since 1.3
     */
    private function _set_content_list( $screen_name, $slug = '' ) {
        $base = $this->_scheme . 'list?screen_name=';
        
        if( is_string( $screen_name ) ) {
            $base .= urlencode( $screen_name );
            
            if( is_numeric( $slug ) AND $slug != '' ) {
                $base .= '&amp;slug=' . urlencode( $slug );
            }
            
            if( !$this->_set_property( 'chl', $base ) ) {
                $this->error = sprintf( __( 'Unable to set Twitter list %s as content.', 'ycwp-qr-me' ), $base );
                return false;
            }
            
            return true;
        }
        
        $this->error = sprintf( __( '%s is not a valid user screen name', 'ycwp-qr-me' ), $screen_name );
        return false;
    }
    
    /**
     * Sets Twitter post URI as content.
     *
     * @param string $message
     * @param string $in_reply_to_status_id
     * @return bool
     * @access private
     * @since 1.3
     */
    private function _set_content_post( $message, $in_reply_to_status_id = 0 ) {
        $base = $this->_scheme . 'post?message=';
        
        if( is_string( $message ) ) {
            $base .= urlencode( $message );
            
            if( is_numeric( $in_reply_to_status_id ) AND $in_reply_to_status_id != 0 ) {
                $base .= '&amp;in_reply_to_status_id=' . $in_reply_to_status_id;
            }
            
            if( !$this->_set_property( 'chl', $base ) ) {
                $this->error = sprintf( __( 'Unable to set Twitter post %s as content.', 'ycwp-qr-me' ), $base );
                return false;
            }
            
            return true;
        }
        
        $this->error = sprintf( __( '%s is not a valid message', 'ycwp-qr-me' ), $message );
        return false;
    }
    
    /**
     * Sets Twitter shortener URL as content.
     *
     * @param string $url
     * @return bool
     * @access private
     * @since 1.3
     */
    private function _set_content_url_shortener( $url ) {
        $base = $this->_scheme . 'install_url_shortener?url=';
        
        if( !$this->_is_valid_url( $url ) ) {
            $this->error = sprintf( __( 'Invalid URL: %s', 'ycwp-qr-me' ), $url );
            return false;
        }
        
        if( !$this->_set_property( 'chl', $base . urlencode( $url ) ) ) {
            $this->error = sprintf( __( 'Unable to set Twitter shortener URL %s as content.', 'ycwp-qr-me' ), urlencode( $url ) );
            return false;
        }
        
        return true;
    }
}
?>