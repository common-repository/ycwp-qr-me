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
 * QRCode_Contact can handle contacts information throught MeCards and generate QR Code with them.
 * When a device scans these QR Codes, it should asks to save the contact.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @since 1.3
 * @package ycwp-qr-me
 * @subpackage qrcode
 */
class QRCode_Contact extends QRCode {
    /**
     * Invoke parent constructor.
     *
     * @since 1.3
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Sets a contact informations as content.
     *
     * @param array $infos
     * @return bool
     * @since 1.3
     */
    public function set_content( $infos ) {
        $name = ( isset( $infos['name'] ) ) ? $infos['name'] : false;
        $tel = ( isset( $infos['tel'] ) ) ? $infos['tel'] : false;
        $email = ( isset( $infos['email'] ) ) ? $infos['email'] : '';
        $memo = ( isset( $infos['memo'] ) ) ? $infos['memo'] : '';
        $address = ( isset( $infos['address'] ) ) ? $infos['address'] : '';
        $url = ( isset( $infos['url'] ) ) ? $infos['url'] : '';
        
        
        if( !$name OR !$tel ) {
            $this->error = __( 'Insert the name and the telephone number of the contact.', 'ycwp-qr-me' );
            return false;
        }
        
        
        $name = strip_tags( trim( $name ) );
        $tel = trim( $tel );
        $email = ( !empty( $email ) ) ? $this->_is_valid_email ( trim( $email ) ) : false;
        $memo = ( !empty( $memo ) ) ? strip_tags( trim( $memo ) ) : false;
        $address = ( !empty( $address ) ) ? strip_tags( trim( $address ) ) : false;
        $url = ( !empty( $url ) ) ? $this->_is_valid_url( $url ) : false;
        
        if( $this->_is_valid_tel( trim( $tel ) ) === false ) {
            $this->error = sprintf( __( '%s is not a valid telephone number.', 'ycwp-qr-me' ), $tel );
            return false;
        }
        
        $content  = 'MECARD:';
        $content .= 'N:' . urlencode( $name ) . ';';
        $content .= 'TEL:' . urlencode( $tel ) . ';';
        
        $content .= ( $email ) ? 'EMAIL:' . urlencode( $email ) . ';' : '';
        $content .= ( $memo ) ? 'NOTE:' . urlencode( $memo ) . ';' : '';
        $content .= ( $address ) ? 'ADR:' . urlencode( $address ) . ';' : '';
        $content .= ( $url ) ? 'URL:' . urlencode( $url ) . ';' : '';
        
        $content .= ';';
        
        if( !$this->_set_property( 'chl', $content ) ) {
            $this->error = sprintf( __( 'Unable to set the contact %s as content.', 'ycwp-qr-me' ), $content );
            return false;
        }
        
        return true;
    }
}
?>