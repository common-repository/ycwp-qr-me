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
 * QRCode_GitHub can handle GitHub URLs and generate QR Code with them.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @since 1.3
 * @package ycwp-qr-me
 * @subpackage qrcode
 */
class QRCode_GitHub extends QRCode {
    /**
     * Invoke parent constructor.
     *
     * @since 1.3
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Sets a GitHub URL as content.
     *
     * @param string $path
     * @return bool
     * @since 1.3
     */
    public function set_content( $path ) {
        $content = 'git://github.com/' . $path;
        
        if( !$this->_set_property( 'chl', urlencode( $content ) ) ) {
            $this->error = sprintf( __( 'Unable to set the GitHub\'s path %s as content.', 'ycwp-qr-me' ), $content );
            return false;
        }
        
        return true;
    }
}
?>