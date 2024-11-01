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
 * QRCode generator. Uses Google Charts API to generate QR Codes.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @since 1.0
 * @package ycwp-qr-me
 */
class QRCode {
    /**
     * Last error occurred.
     *
     * @var string
     * @since 1.2
     */
    public $error;
    
	/**
	 * Base path for QR Code Google request.
     *
     * @access protected
     * @var string
     * @since 1.0
	 */
	protected $_GOOGLE_CHART_URL;
	
    /**
     * QR Code image width.
     *
     * @access protected
     * @var int
     * @since 1.0
     */
	protected $_width;
    
    /**
     * QR Code image height.
     *
     * @access protected
     * @var int
     * @since 1.0
     */
	protected $_height;
	
    /**
     * QR COde image settings.
     *
     * @access protected
     * @var array
     * @since 1.0
     */
	protected $_settings;
	
    /**
     * Initialize properties.
     *
     * @since 1.0
     */
	public function __construct() {
		$this->_width = 200;
		$this->_height = 200;
		$this->_settings = array(
			'cht' => 'qr',
			'chs' => $this->_width . 'x' . $this->_height,
			'choe' => 'UTF-8',
			'chld' => 'L',
			'chl' => ' '
		);
		
        if( in_array( 'https', stream_get_wrappers() ) ) {
            $this->_GOOGLE_CHART_URL = 'https://chart.googleapis.com/chart?';
        } else {
            $this->_GOOGLE_CHART_URL = 'http://chart.googleapis.com/chart?';
        }
	}
    
    /**
     * Sets QR Code image size.
     *
     * @param int $w
     * @param int $h
     * @return void
     * @since 1.2
     */
    public function set_size( $w, $h ) {
        $this->set_width( $w );
		$this->set_height( $h );
		
		$size = $this->_width . 'x' . $this->_height;
		if( !$this->_set_property( 'chs', $size ) ) {
            $this->error = sprintf( __( 'Unable to set the size %s.', 'ycwp-qr-me' ), $w . 'x' . $h );
        }
    }
    
    /**
     * Sets QR Code image width.
     *
     * @param int $width
     * @return void
     * @since 1.2
     */
    public function set_width( $w ) {
        $this->_width = (int) $w;
    }
    
     /**
     * Sets QR Code image height.
     *
     * @param int $height
     * @return void
     * @since 1.2
     */
    public function set_height( $h ) {
        $this->_height = (int) $h;
    }
    
    /**
     * Sets QR Code image content charset.
     *
     * @param string $charset
     * @return void
     * @since 1.2
     */
    public function set_charset( $charset ) {
        if( !$this->_set_property( 'choe', $charset ) ) {
            $this->error = sprintf( __( 'Unable to set the charset %s.', 'ycwp-qr-me' ), $charset );
        }
    }
    
    /**
     * Sets QR Code image error correction level and margin.
     *
     * @param string $level
     * @param int $margin
     * @return void
     * @since 1.0
     */
	public function set_error_level( $level, $margin = 0 ) {
		if( !$this->_set_property( 'chld', $level ) ) {
            $this->error = spritnf( __( 'Unable to set level of error correction %s.', 'ycwp-qr-me' ), $level );
        }
        
		if( !$this->_set_property( 'chld', $margin, true ) ) {
            $this->error = sprintf( __( 'Unable to set the margin %d.', 'ycwp-qr-me' ), $margin );
        }
	}
    
    /**
     * Sets QR Code content.
     *
     * @param mixed $content
     * @return bool
     * @since 1.2
     */
	public function set_content( $content ) {
		return $this->_set_property( 'chl', $content );
	}
    
    /**
     * Magic method to get properties value.
     *
     * @param string $name Property name
     * @return mixed|bool False on failure
     * @since 1.0
     */
    public function __get( $name ) {
        if( property_exists( $this, $name ) ) {            
            return $this->{$name};
        }
        
        $this->error = sprintf( __( '%s property doesn\'t exist', 'ycwp-qr-me' ), $name );
        return false;
    }
    
    /**
     * Chooses the request type to submit to Google and return the qr code.
     *
     * @param array $params Additional params that will be added to the <img> tag.
     * @return string
     * @since 1.2
     */
    public function QR( $params = array() ) {
        $data = $this->_make_URL();
        
        return ( ( mb_strlen( $data ) > 2000 ) ? $this->QR_POST( $data, $params ) : $this->QR_GET( $data, $params ) );
    }
    
    /**
     * Send a GET request to Google for a QR Code.
     *
     * @param string $data
     * @param array $params Additional params that will be added to the <img> tag.
     * @return string
     * @since 1.0
     */
	public function QR_GET( $data, $params = array() ) {
		$qr = '<img src="' . $data . '"';
			
		if(!empty($params)) {
			foreach($params as $key => $value) {
				$qr .= ' ' . $key . '="' . $value .'"';	
			}
		}
		
		$qr .= ' />';
		
		return $qr;
	}
    
    /**
     * Send a POST request to Google for a QR Code. Required if the content is larger than 2 KB.
     *
     * @param string $url
     * @param array $params Additional params that will be added to the <img> tag.
     * @return string
     * @since 1.0
     */
    public function QR_POST( $url, $params = array() ) {
        $context = stream_context_create( 
            array( 'http' => 
                array(
                    'method' => 'POST',
                    'header' => 'Content-Type: image/png\r\n',
                    'content' => http_build_query( $this->_settings )
                )
            )
        );
          
        $data = null;
        $fp = fopen( $url, 'r', false, $context );
        
        while( !feof( $fp ) ) {
            $data .= fread( $fp, 1024 );
        }
          
        $qr = '<img src="data:image/png;base64,' . base64_encode( $data ) . '" ';
        
        if( !empty( $params ) ) {
			foreach( $params as $key => $value ) {
				$qr .= ' ' . $key . '="' . $value .'"';	
			}
		}
        
        $qr .= ' />';
        
        return $qr;
    }
	
    /**
     * Re-initialize the properties.
     *
     * @return void
     * @since 1.0
     */
	public function reset() {
		$this->_width = 200;
		$this->_height = 200;
		$this->_settings = array(
			'cht' => 'qr',
			'chs' => $this->_width . 'x' . $this->_height,
			'choe' => 'UTF-8',
			'chld' => 'L',
			'chl' => ''
		);	
	}
    
    /**
     * Set the specified value for a property.
     *
     * @param string $key Property name
     * @param mixed $value
     * @param bool $append Append the value, otherwise it will override
     * @param string $separator
     * @return bool
     * @access protected
     * @since 1.2
     */
	protected function _set_property( $key, $value, $append = false, $separator = '|' ) {
		$params = array_keys( $this->_settings );
		
		if( $key != 'cht' ) {
			if( in_array( $key, $params ) ) {
				if( !$append ) {
					$this->_settings[$key] = $value;	
				} else {
					$this->_settings[$key] = $this->_settings[$key] . $separator . $value;
				}
				
				return true;
			}
		}
		
		return false;
	}
	
    /**
     * Make the URL for the request.
     *
     * @return string
     * @access protected
     * @since 1.2
     */
	protected function _make_URL() {
		$query  = '&amp;cht='  . $this->_settings['cht'] ;
		$query .= '&amp;chs='  . $this->_settings['chs'] ;
		$query .= '&amp;chld=' . $this->_settings['chld'];
		$query .= '&amp;chl='  . $this->_settings['chl'] ;
		$query .= '&amp;choe=' . $this->_settings['choe'];
		
		return $this->_GOOGLE_CHART_URL .  $query;
	}
    
    /**
     * Checks for a valid URL
     *
     * @param string $url
     * @return string|bool False on failure
     * @access protected
     * @since 1.2
     */
    protected function _is_valid_url( $url ) {
        if( filter_var( $url, FILTER_VALIDATE_URL ) ) {
            return $url;
        }
        
        return false;
    }
    
    /**
     * Checks for a valid email address.
     *
     * @param string $email
     * @return string|bool False on failure
     * @access protected
     * @since 1.2
     */
    protected function _is_valid_email( $email ) {
        if( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            return $email;
        }
        
        return false;
    }
    
    /**
     * Checks for a valid telephone number. It must contain a country code.
     * Example of valid number: +15105550101
     * Example of invalid number: (152)-800-800
     *
     * @param string $tel
     * @return string|bool False on failure
     * @access protected
     * @since 1.2
     */
    protected function _is_valid_tel( $tel ) {
        //Replace all chars except + and numbers.
        $reg_exp = '/[^\+\d]/';
        $tel = preg_replace( $reg_exp, '', $tel );
        
        //Check for correct number format.
        $reg_exp = '/^\+[\d]{1,24}/';
        return preg_match( $reg_exp, $tel );
    }
	
    /* ===== DEPRECATED ===== */
    
    /**
     * Sets an URL as content.
     *
     * @param string $url
     * @return bool
     * @access private
     * @since 1.2
     * @deprecated 1.3
     */
    private function _set_content_url( $url ) {
        $valid_url = $this->_is_valid_url( $url );
        
        if( $valid_url !== false ) {
            if( !$this->_set_property( 'chl', urlencode( $valid_url ) ) ) {
                $this->error = __( 'Unable to set URL as content: ', 'ycwp-qr-me' ) . $valid_url;
                return false;
            }
            
            return true;
        }
        
        $this->error = __( 'Invalid URL: ', 'ycwp-qr-me' ) . $url;
        return false;
    }
    
    /**
     * Sets a telephone number and a message for an sms as content.
     *
     * @param string $number Telephone number including country code.
     * @param string $message
     * @return bool
     * @access private
     * @since 1.2
     * @deprecated 1.3
     */
    private function _set_content_sms( $number, $message = 'New message' ) {
        $number = $this->_is_valid_tel( $number );
        
        if( $number != false ) {
            /**
             * @todo Fix this bug. Message body not recognized on iPhone 4 width iOS 5.0.1
             */
            $message = urlencode( $message );
            $content = 'sms:' . urlencode( $number ) . '?body=' . urlencode( $message );
            
            if( !$this->_set_property( 'chl', $content ) ) {
                $this->error = __( 'Unable to set sms content: ', 'ycwp-qr-me' ) . $content;
                return false;
            }
            
            return true;
        }
        
        $this->error = $number . __( ' is not a valid telephone number.', 'ycwp-qr-me' );
        return false;
    }
    
    /**
     * Sets a position as content.
     *
     * @param float $lat Latitude in WGS-84
     * @param float $lon Longitude in WGS-84
     * @param int $alt Altitude in WGS-84
     * @param int $u Uncertainty
     * @return bool
     * @access private
     * @since 1.2
     * @deprecated 1.3
     */
    private function _set_content_geoloc( $lat, $lon, $alt = 0, $u = 0 ) {
        $lat = urlencode( ( float ) $lat );
        $lon = urlencode( ( float ) $lon );
        $alt = urlencode( ( int ) $alt );
        $u = urlencode( ( int ) $u );
        
        $content = 'geo:' . $lat . ',' . $lon . ',' . $alt . ';crs=wgs84;u=' . $u;
        
        if( !$this->_set_property( 'chl', $content ) ) {
            $this->error = __( 'Unable to set geolocation as content: ', 'ycwp-qr-me' ) . $content;
        }
    }
    
    /**
     * Sets an email address as content.
     *
     * @param string $address
     * @param string $subject
     * @param string $body
     * @return bool
     * @access private
     * @since 1.2
     * @deprecated 1.3
     */
    private function _set_content_email( $address, $subject = '', $body = '' ) {
        $address = $this->_is_valid_email( $address );
        
        if( $address === false ) {
            $this->error = $address . __( ' is not a valid email address.', 'ycwp-qr-me' );
            return false;
        }
        
        $content = 'mailto:' . $address;
        
        if( !empty( $subject ) OR !empty( $body ) ) {
            $content .= '?';
            
            if( !empty( $subject ) AND !empty( $body ) ) {
                $content .= 'subject=' . urlencode( $subject ) . '&amp;body=' . urlencode( $body );
            } else if( !empty( $subject ) AND empty( $body ) ) {
                $content .= 'subject=' . urlencode( $subject );
            } else if( empty( $subject ) AND !empty( $body ) ) {
                $content .= 'body=' . urlencode( $body );
            }
        }
        
        if( !$this->_set_property( 'chl', $content ) ) {
            $this->error = __( 'Unable to set email as content: ', 'ycwp-qr-me' ) . $content;
            return false;
        }
        
        return true;
    }
    
    /**
     * Sets a telephone number as content.
     *
     * @param string $tel Telephone number, including country code
     * @return bool
     * @access private
     * @since 1.2
     * @deprecated 1.3
     */
    private function _set_content_tel( $tel ) {
        $tel = $this->_is_valid_tel( $tel );
        
        if( $tel !== false ) {
            $content = 'tel:' . urlencode( $tel );
            
            if( !$this->_set_property( 'chl', $content ) ) {
                $this->error = __( 'Unable to set tel as content: ', 'ycwp-qr-me' ) . $content;
                return false;
            }
            
            return true;
        }
        
        $this->error = $tel . __( ' is not a valid telephone number.', 'ycwp-qr-me' );
        return false;
    }
    
    /**
     * Sets a contact informations as content.
     *
     * @param array $infos
     * @return bool
     * @access private
     * @since 1.2
     * @deprecated 1.3
     */
    private function _set_content_contact( $infos ) {
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
        $tel = $this->_is_valid_tel( trim( $tel ) );
        $email = ( !empty( $email ) ) ? $this->_is_valid_email ( trim( $email ) ) : false;
        $memo = ( !empty( $memo ) ) ? strip_tags( trim( $memo ) ) : false;
        $address = ( !empty( $address ) ) ? strip_tags( trim( $address ) ) : false;
        $url = ( !empty( $url ) ) ? $this->_is_valid_url( $url ) : false;
        
        if( $tel === false ) {
            $this->error = $tel . __( ' is not a valid telephone number.', 'ycwp-qr-me' );
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
            $this->error = __( 'Unable to set contact as content: ', 'ycwp-qr-me' ) . $content;
            return false;
        }
        
        return true;
    }
    
    /**
     * Sets an Android Market URL as content. It will redirect properly only on Android devices.
     *
     * @param string $package
     * @return bool
     * @access private
     * @since 1.2
     * @deprecated 1.3
     */
    private function _set_content_android_market( $package ) {
        $content = 'maket://details?id=' . $package;
        
        if( !$this->_set_property( 'chl', urlencode( $content ) ) ) {
            $this->error = __( 'Unable to set package as content: ', 'ycwp-qr-me' ) . $content;
            return false;
        }
        
        return true;
    }
    
    /**
     * Sets a GitHub URL as content.
     *
     * @param string $path
     * @return bool
     * @access private
     * @since 1.2
     */
    private function _set_content_github( $path ) {
        $content = 'git://github.com/' . $path;
        
        if( !$this->_set_property( 'chl', urlencode( $content ) ) ) {
            $this->error = __( 'Unable to set GitHub path as content: ', 'ycwp-qr-me' ) . $content;
            return false;
        }
        
        return true;
    }
    
    /**
     * Sets an view-source URL as content.
     *
     * @param string $uri
     * @return bool
     * @access private
     * @since 1.2
     * @deprecated 1.3
     */
    private function _set_content_viewsource( $uri ) {
        $valid_uri = $this->_is_valid_url( $uri );
        
        if( $valid_uri !== false ) {
            if( !$this->_set_property( 'chl', 'view-source:' . urlencode( $valid_uri ) ) ) {
                $this->error = __( 'Unable to set view-source URL as content: ', 'ycwp-qr-me' ) . $valid_uri;
                return false;
            }
            
            return true;
        }
        
        $this->error = __( 'Invalid URL: ', 'ycwp-qr-me' ) . $uri;
        return false;
    }
    
    /**
     * Sets wifi data as content.
     *
     * @param string $auth_type WEP or WPA
     * @param string $ssid
     * @param string $passw
     * @return bool
     * @access private
     * @since 1.2
     * @deprecated 1.3
     */
    private function _set_content_wifi( $auth_type, $ssid, $passw ) {
        /**
         * I'm not sure that this can be a valid URI...
         */
        $content  = 'WIFI:';
        $auth_type = strtoupper( $auth_type );
        
        if( $auth_type != 'WPA' AND $auth_type != 'WEP' ) {
            $this->error = $auth_type . __( ' is not a valid authentication type. Choose WPA or WEP.', 'ycwp-qr-me' );
            return false;
        }
        
        $content .= 'T:' . $auth_type . ';';
        $content .= 'S:' . urlencode( $ssid ) . ';';
        $content .= 'P:' . urlencode( $passw ) . ';';
        $content .= ';';
        
        /**
         * @todo Fix this bug. This URI doesn't work on iPhone 4 with iOS 5.0.1
         */
        if( !$this->_set_property( 'chl', $content ) ) {
            $this->error = __( 'Unable to set wifi as content: ', 'ycwp-qr-me' ) . $content;
            return false;
        }
        
        return true;
    }
}
?>