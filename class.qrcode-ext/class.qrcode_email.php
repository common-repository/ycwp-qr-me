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
 * QRCode_Email can handle emails and generate QR Code with them.
 * When a device scans these QR Codes, it should asks to send an email.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @since 1.3
 * @package ycwp-qr-me
 * @subpackage qrcode
 */
class QRCode_Email extends QRCode {
    /**
     * Invoke parent constructor.
     *
     * @since 1.3
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Sets an email address as content.
     *
     * @param string $address
     * @param string $subject
     * @param string $body
     * @return bool
     * @since 1.3
     */
    public function set_content( $address, $subject = '', $body = '' ) {
        $address = $this->_is_valid_email( $address );
        
        if( $address === false ) {
            $this->error = sprintf( __( '%s is not a valid email address.', 'ycwp-qr-me' ), $address );
            return false;
        }
        
        $content = 'mailto:' . $address;
        
        if( !empty( $subject ) OR !empty( $body ) ) {
            $content .= '?';
            
            if( !empty( $subject ) AND !empty( $body ) ) {
                $content .= 'subject=' . urlencode( $subject ) . '&amp;body=' . urlencode( $body );
            } else if( !empty( $subject ) AND empty( $body ) ) {
                $content .= 'subject=' . urlencode( $subject );
            } else if( empty( $subject ) AND !empty( $body ) ) {
                $content .= 'body=' . urlencode( $body );
            }
        }
        
        if( !$this->_set_property( 'chl', $content ) ) {
            $this->error = sprintf( __( 'Unable to set the email %s as content.', 'ycwp-qr-me' ), $content );
            return false;
        }
        
        return true;
    }
}
?>