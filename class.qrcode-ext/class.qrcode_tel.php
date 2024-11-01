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
 * QRCode_Email can handle telephone numbers and generate QR Code with them.
 * When a device scans these QR Codes, it should asks to call the telephone number.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @since 1.3
 * @package ycwp-qr-me
 * @subpackage qrcode
 */
class QRCode_Tel extends QRCode {
    /**
     * Invoke parent constructor.
     *
     * @since 1.3
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Sets a telephone number as content.
     *
     * @param string $tel Telephone number, including country code
     * @return bool
     * @since 1.3
     */
    public function set_content( $tel ) {        
        if( $this->_is_valid_tel( $tel ) !== false ) {
            $content = 'tel:' . urlencode( $tel );
            
            if( !$this->_set_property( 'chl', $content ) ) {
                $this->error = sprintf( __( 'Unable to set the telephone number %s as content.', 'ycwp-qr-me' ), $content );
                return false;
            }
            
            return true;
        }
        
        $this->error = sprintf( __( '%s is not a valid telephone number.', 'ycwp-qr-me' ), ( string )$tel );
        return false;
    }
}
?>