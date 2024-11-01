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
 * QRCode_WiFi can handle WiFi Networks connection data and generate QR Code with them.
 * When a device scan these QR Codes, it should asks to connect to the network.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @since 1.3
 * @package ycwp-qr-me
 * @subpackage qrcode
 */
class QRCode_WiFi extends QRCode {
    /**
     * Invoke parent constructor.
     *
     * @since 1.3
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Sets wifi data as content.
     *
     * @param string $auth_type WEP or WPA
     * @param string $ssid
     * @param string $passw
     * @return bool
     * @since 1.3
     */
    public function set_content( $auth_type, $ssid, $passw ) {
        /**
         * I'm not sure that this can be a valid URI...
         */
        $content  = 'WIFI:';
        $auth_type = strtoupper( $auth_type );
        
        if( $auth_type != 'WPA' AND $auth_type != 'WEP' ) {
            $this->error = sprintf( __( '%s is not a valid authentication type. Choose WPA or WEP.', 'ycwp-qr-me' ), $auth_type );
            return false;
        }
        
        $content .= 'T:' . $auth_type . ';';
        $content .= 'S:' . urlencode( $ssid ) . ';';
        $content .= 'P:' . urlencode( $passw ) . ';';
        $content .= ';';
        
        /**
         * @todo Probable bug. This URI doesn't work on iPhone 4 with iOS 5.0.1
         */
        if( !$this->_set_property( 'chl', $content ) ) {
            $this->error = sprintf( __( 'Unable to set the wifi %s as content.', 'ycwp-qr-me' ), $content );
            return false;
        }
        
        return true;
    }
}
?>