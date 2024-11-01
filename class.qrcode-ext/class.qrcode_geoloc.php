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
 * QRCode_GeoLoc can handle geographical location and generate QR Code with them.
 * When a device scans these QR Codes, it should asks to start a GPS or an application.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @since 1.3
 * @package ycwp-qr-me
 * @subpackage qrcode
 */
class QRCode_GeoLoc extends QRCode {
    /**
     * Invoke parent constructor.
     *
     * @since 1.3
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Sets an geographical location as content.
     *
     * @param float $lon Longitude in WGS-84
     * @param float $lat Latitude in WGS-84
     * @param int $alt Altitude in WGS-84
     * @param int $u Uncertainty
     * @return bool
     * @access private
     * @since 1.3
     */
    public function set_content( $lon, $lat, $alt = 0, $u = 0 ) {
        $lat = urlencode( ( float ) $lat );
        $lon = urlencode( ( float ) $lon );
        $alt = urlencode( ( int ) $alt );
        $u = urlencode( ( int ) $u );
        
        $content = 'geo:' . $lat . ',' . $lon . ',' . $alt . ';crs=wgs84;u=' . $u;
        
        if( !$this->_set_property( 'chl', $content ) ) {
            $this->error = sprintf( __( 'Unable to set the geographical location %s as content.', 'ycwp-qr-me' ), $content );
            return false;
        }
        
        return true;
    }
}
?>