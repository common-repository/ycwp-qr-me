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
 * QRCode_Android_Market can handle Android market URLs and generate QR Code with them.
 * Only Android devices correctly handle these QR Codes.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @since 1.3
 * @package ycwp-qr-me
 * @subpackage qrcode
 */
class QRCode_Android_Market extends QRCode {
    /**
     * Invoke parent constructor.
     *
     * @since 1.3
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Sets an Android Market URL as content.
     *
     * @param string $package
     * @return bool
     * @since 1.3
     */
    public function set_content( $package ) {
        $content = 'maket://details?id=' . $package;
        
        if( !$this->_set_property( 'chl', urlencode( $content ) ) ) {
            $this->error = sprintf( __( 'Unable to set the package %s as content.', 'ycwp-qr-me' ), $content );
            return false;
        }
        
        return true;
    }
}
?>