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
 * QRCode_URL can handle web URLs and generate QR Code with them.
 * When a device scans these QR Codes, it should asks to open the URL in a browser.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @since 1.3
 * @package ycwp-qr-me
 * @subpackage qrcode
 */
class QRCode_URL extends QRCode {
    /**
     * Invoke parent constructor.
     *
     * @since 1.3
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Sets an URL as content.
     *
     * @param string $url
     * @return bool
     * @since 1.3
     */
    public function set_content( $url ) {
        $valid_url = $this->_is_valid_url( $url );
        
        if( $valid_url !== false ) {
            if( !$this->_set_property( 'chl', urlencode( $valid_url ) ) ) {
                $this->error = sprintf( __( 'Unable to set the URL %s as content.', 'ycwp-qr-me' ), $valid_url );
                return false;
            }
            
            return true;
        }
        
        $this->error = sprintf( __( 'Invalid URL: %s', 'ycwp-qr-me' ), $url );
        return false;
    }
}
?>