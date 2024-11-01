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
 * QRCode_Steam can handle Steam URI and generate QR Code with them.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @since 1.3
 * @package ycwp-qr-me
 * @subpackage qrcode
 */
class QRCode_Steam extends QRCode {
    /**
     * Steam URI scheme.
     *
     * @var string
     * @access private
     * @since 1.3
     */
    private $_scheme;
    
    /**
     * Steam URI commands.
     *
     * @var array
     * @access private
     * @since 1.3
     */
    private $_commands;
    
    /**
     * Invoke parent constructor.
     *
     * @since 1.3
     */
    public function __construct() {
        parent::__construct();
        $this->_scheme = 'steam://';
        $this->_commands = array(
            'AddNonSteamGame' => '',
            'advertise' => '<id>', //Opens up the store for an application.
            'appnews' => '<id>', //Opens up the news page for an app.
            'browsemedia' => '',
            'friends' => array(), //Opens Friends.
            'hardwarepromo' => array(), //Tests whether the user has hardware that matches a promotional offer.
            'publisher' => '<name>', //Loads the specified publisher catalogue in the Store. Type the publisher's name in lowercase, e.g. activision or valve. Bug: Somehow broken.
            'purchase' => '<id>', //Opens a dialog box to buy an application from Steam.
            'store' => '<id>', //Opens up the store for an app, if no app is specified then the default one is opened
            'updatenews' => '<id>', //Opens the news about the latest updates for an app
            'url' => array() //Opens a special, named web pages.  
        );
        
        
        //Subcommands
        $this->_commands['friends'] = array(
            'add' => '<id>', //Adds user with specified id number
            'friends' => '<id>', //Shows list of users with whom you recently played
            'players' => '' //Shows table of recent players you've played with
        );
        $this->_commands['hardwarepromo'] = array(
            'ATi' => '<id>',
            'nVidia' => '<id>'
        );
        $this->_commands['url'] = array(
            'CommunitySearch' => '',
            'CommunityGroupSearch' => '',
            'GroupEventsPage' => '<id>',
            'LegalInformation' => '',
            'PrivacyPolicy' => '',
            'Store' => '',
            'StoreFrontPage' => ''
        );
    }
    
    /**
     * Sets QR Code content.
     *
     * @param string $type
     * @param mixed $content
     * @return bool
     * @since 1.3
     */
	public function set_content( $type, $content ) {
		switch( strtolower( $type ) ) {
            case 'steam-command'         : return $this->_set_content_command( $content['command'], $content['value'] );                                  break;
            case 'steam-advanced-command': return $this->_set_content_command_advanced( $content['command'], $content['subcommand'], $content['value'] ); break;
        }
	}
    
    /**
     * Sets a command without subcommand as content.
     *
     * @param string $command
     * @param int|string $value
     * @return bool
     * @access private
     * @since 1.3
     */
    private function _set_content_command( $command, $value = null ) {
        $forbidden = array(
            'friends',
            'hardwarepromo',
            'url'
        );
        
        if( in_array( $command, $forbidden ) ) {
            $this->error = $command . __( ' is an advanced command.', 'ycwp-qr-me' );
            return false;
        }
        
        $commands = array_keys( $this->_commands );
        
        if( in_array( $command, $commands ) OR !$this->_command_exists( $command ) ) {
            $command_value = $this->_commands[ $command ];
            
            if( empty( $command_value ) ) {
            
                $content = $this->_scheme . urlencode( $command );
                if( !$this->_set_property( 'chl', $content ) ) {
                    $this->error = sprintf( __( 'Unable to set the steam command %s as content.', 'ycwp-qr-me' ), $content );
                    return false;
                }
                
                return true;
            }
            
            if( empty( $value ) ) {
                $this->error = sprintf( __( '%s command need a value.', 'ycwp-qr-me' ), $command );
                return false;
            }
            
            switch( $command_value ) {
                case '<id>'  : $value = ( int ) $value;                  break;
                case '<name>': $value = strtolower( ( string ) $value ); break;
                default      : 
                    $this->error = __( 'Unknown command value type.', 'ycwp-qr-me' );
                    return false;
            }
            
            $content = $this->_scheme . urlencode( $command ) . '/' . urlencode( $value );
            if( !$this->_set_property( 'chl', $content ) ) {
                $this->error = sprintf( __( 'Unable to set the steam command %s as content.', 'ycwp-qr-me' ), $content );
                return false;
            }
            
            return true;
        }
        
        $this->error = sprintf( __( 'Unknown command %s', 'ycwp-qr-me' ), $command );
        return false;
    }
    
    /**
     * Sets a command and subcommand as content.
     *
     * @param string $command
     * @param string $subcommand
     * @param int|string $value
     * @return bool
     * @access private
     * @since 1.3
     */
    private function _set_content_command_advanced( $command, $subcommand, $value = null ) {
        $allowed = array(
            'friends',
            'hardwarepromo',
            'url'
        );
        
        if( !in_array( $command, $allowed ) OR !$this->_subcommand_exists( $command, $subcommand ) ) {
            $this->error = sprintf( __( ' %s/%s is not a valid command.', 'ycwp-qr-me' ), $command, $subcommand );
            return false;
        }
        
        $subcommands = array_keys( $this->_commands[ $command ] );
        
        if( in_array( $subcommand, $subcommands ) ) {
            $subcommand_value = $this->_commands[ $command ][ $subcommand ];
            
            if( empty( $subcommand_value ) ) {
            
                $content = $this->_scheme . urlencode( $command . '/' . $subcommand );
                if( !$this->_set_property( 'chl', $content ) ) {
                    $this->error = sprintf( __( 'Unable to set the steam command %s as content.', 'ycwp-qr-me' ), $content );
                    return false;
                }
                
                return true;
            }
            
            if( empty( $value ) ) {
                $this->error = sprintf( __( ' %s/%s command needs a value.', 'ycwp-qr-me' ), $command, $subcommand );
                return false;
            }
            
            switch( $subcommand_value ) {
                case '<id>'  : $value = ( int ) $value;                  break;
                default      : 
                    $this->error = __( 'Unknown command value type.', 'ycwp-qr-me' );
                    return false;
            }
            
            $content = $this->_scheme . urlencode( $command ) . '/' . urlencode( $subcommand ) . '/' . urlencode( $value );
            if( !$this->_set_property( 'chl', $content ) ) {
                $this->error = sprintf( __( 'Unable to set the steam command  %s as content.', 'ycwp-qr-me' ), $content );
                return false;
            }
            
            return true;
        }
        
        $this->error = sprintf( __( 'Unknown command %s/%s', 'ycwp-qr-me' ), $command, $subcommand );
        return false;
    }
    
    /**
     * Checks if a specific command exists.
     *
     * @param string $command
     * @return bool
     * @access private
     * @since 1.3
     */
    private function _command_exists( $command ) {
        return in_array( $command, $this->_command );
    }
    
    /**
     * Checks if a specific subcommand exists.
     *
     * @param string $command
     * @param string $subcommand
     * @return bool
     * @access private
     * @since 1.3
     */
    private function _subcommand_exists( $command, $subcommand ) {
        return in_array( $subcommand, $this->_command[ $command ] );
    }
}
?>