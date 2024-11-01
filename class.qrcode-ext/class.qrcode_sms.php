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
 * QRCode_SMS can handle SMSs and generate QR Code with them.
 * When a device scans these QR Codes, it should asks to send the SMS.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @since 1.3
 * @package ycwp-qr-me
 * @subpackage qrcode
 */
class QRCode_SMS extends QRCode {
    /**
     * Invoke parent constructor.
     *
     * @since 1.3
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Sets a telephone number and a text message as content.
     *
     * @param string $number Telephone number including country code.
     * @param string $message
     * @return bool
     * @access private
     * @since 1.3
     */
    public function set_content( $number, $message = '' ) {
        if( $this->_is_valid_tel( $number ) !== false ) {
            /**
             * @todo Probable bug. Message body not recognized on iPhone 4 width iOS 5.0.1
             */
            $message = urlencode( $message );
            $content = 'sms:' . urlencode( $number ) . '?body=' . urlencode( $message );
            
            if( !$this->_set_property( 'chl', $content ) ) {
                $this->error = sprintf( __( 'Unable to set the sms %s as content.', 'ycwp-qr-me' ), $content );
                return false;
            }
            
            return true;
        }
        
        $this->error = sprintf( __( '%s is not a valid telephone number.', 'ycwp-qr-me' ), ( string ) $number );
        return false;
    }
}
?>