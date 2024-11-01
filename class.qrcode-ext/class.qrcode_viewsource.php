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
 * QRCode_ViewSource can handle web URLs and generate QR Code with them.
 * These URLs shows page source code.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @since 1.3
 * @package ycwp-qr-me
 * @subpackage qrcode
 */
class QRCode_ViewSource extends QRCode {
    /**
     * Invoke parent constructor.
     *
     * @since 1.3
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Sets an view-source URI as content.
     *
     * @param string $uri
     * @return bool
     * @since 1.3
     */
    public function set_content( $uri ) {
        $valid_uri = $this->_is_valid_url( $uri );
        
        if( $valid_uri !== false ) {
            if( !$this->_set_property( 'chl', 'view-source:' . urlencode( $valid_uri ) ) ) {
                $this->error = sprintf( __( 'Unable to set the URL %s as content.', 'ycwp-qr-me' ), $valid_uri );
                return false;
            }
            
            return true;
        }
        
        $this->error = sprintf( __( 'Invalid URL: %s', 'ycwp-qr-me' ), $uri );
        return false;
    }
}
?>