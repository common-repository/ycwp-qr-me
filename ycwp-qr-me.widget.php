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
 * Register the widget
 *
 * @since 1.0
 */ 
function ycwp_qr_me_register_widget() {
    register_widget( 'YCWP_QR_Me_Widget' );
}
add_action( 'widgets_init', 'ycwp_qr_me_register_widget' );

/**
 * This class define the widget.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @version 1.2
 * @package ycwp-qr-me
 *
 * @link http://core.trac.wordpress.org/browser/tags/3.3.1/wp-includes/widgets.php
 */
class YCWP_QR_Me_Widget extends WP_Widget {
    /**
     * QRCode class instance.
     *
     * @var object
     * @access private
     * @see QRCode
     * @since 1.0
     */
    private $_qr;
    
    /**
     * Invoke WP_Widget::__construct() and QRCode::__construct().
     *
     * @see QRCode
     * @link http://core.trac.wordpress.org/browser/tags/3.3.1/wp-includes/widgets.php#L76
     * @since 1.0
     */
    public function __construct() {
        parent::WP_Widget( 'ycwp-qr-me', 'YCWP QR Me', array( 'description' => 'Generate a QR Code of the given content using Google Charts API' ) );
        $this->_qr = new QRCode();
    }
    
    /**
     * Echo the widget content.
     *
     * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
     * @param array $instance The settings for the particular instance of the widget
     *
     * @link http://core.trac.wordpress.org/browser/tags/3.3.1/wp-includes/widgets.php#L44
     * @since 1.0
     */
    public function widget( $args, $instance ) {
        $qr = $this->_qr;
        $width = ( int) $instance['size'];
        $params = preg_replace( '/[^\w\-,]/', '', $instance['classes'] );
        $params = array(
            'class' => str_replace( ',', ' ', $params ),
            'title' => $instance['title'],
            'alt' => __( 'QR Code', 'ycwp-qr-me' ) . ': ' . $instance['title']
        );
        
        extract( $args );
        $title = empty( $instance['title'] ) ? 'YCWP QR Me Code' : apply_filters( 'widget_title', $instance['title'] );
        
        $instance['content'] = $instance['encodeurl'] ? urlencode( $instance['content'] ) : $instance['content']; 
        
        $qr->set_size( $width, $width );
        $qr->set_error_level( $instance['error'] );
        $qr->set_content( $instance['content'] );

        $image = $qr->QR( $params );
        
        echo $before_widget;
        echo $before_title . $title . $after_title;
        ?>
            <div class="ycwp-qr-me-widget">
            <?php echo $image; ?>
            </div>
        <?php
        echo $after_widget;
    }
    
    /**
     * Echo the settings update form
     *
     * @param array $instance Current settings
     * @link http://core.trac.wordpress.org/browser/tags/3.3.1/wp-includes/widgets.php#L66
     * @since 1.0
     */
    public function form( $instance ) {
        $defaults = array(
            'title' => 'YCWP QR Me Code',
            'size' => $this->_qr->_settings['chs'],
            'error' => $this->_qr->_settings['chld'],
            'content' => $this->_qr->_settings['chl'],
            'encodeurl' => false,
            'classes' => ''
        );
        
        $instance = wp_parse_args( ( array ) $instance, $defaults );
        
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'ycwp-qr-me' ); ?>:</label>
            <input type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" value="<?php echo $instance['title']; ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"><?php _e( 'Size', 'ycwp-qr-me' ); ?>:</label>
            <select name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>">
                <option value="100" <?php selected( $instance['size'], '100' ); ?>>100x100 px</option>
                <option value="150" <?php selected( $instance['size'], '150' ); ?>>150x150 px</option>
                <option value="200" <?php selected( $instance['size'], '200' ); ?>>200x200 px</option>
                <option value="250" <?php selected( $instance['size'], '250' ); ?>>250x250 px</option>
                <option value="300" <?php selected( $instance['size'], '300' ); ?>>300x300 px</option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'error' ) ); ?>"><?php _e( 'Error correction level', 'ycwp-qr-me' ); ?>:</label>
            <select name="<?php echo esc_attr( $this->get_field_name( 'error' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'error' ) ); ?>">
                <option value="L" <?php selected( $instance['error'], 'L' ); ?>>L</option>
                <option value="M" <?php selected( $instance['error'], 'M' ); ?>>M</option>
                <option value="Q" <?php selected( $instance['error'], 'Q' ); ?>>Q</option>
                <option value="H" <?php selected( $instance['error'], 'H' ); ?>>H</option>
            </select>
            <br /><span class="description"><?php _e( 'L = 7% of codewords can be restored', 'ycwp-qr-me' ); ?>.</span>
            <br /><span class="description"><?php _e( 'M = 15% of codewords can be restored', 'ycwp-qr-me' ); ?>.</span>
            <br /><span class="description"><?php _e( 'Q = 25% of codewords can be restored', 'ycwp-qr-me' ); ?>.</span>
            <br /><span class="description"><?php _e( 'H = 30% of codewords can be restored', 'ycwp-qr-me' ); ?>.</span>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>"><?php _e( 'Content', 'ycwp-qr-me' ); ?>: </label>
            <input type="text" name="<?php echo esc_attr( $this->get_field_name( 'content' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>" value="<?php echo $instance['content']; ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'encodeurl' ) ); ?>"><?php _e( 'Encode URL', 'ycwp-qr-me' ); ?>: </label>
            <input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'encodeurl' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'encodeurl' ) ); ?>" value="1" <?php checked( $instance['encodeurl'], 1 ) ?> />
            <span class="description"><?php _e( 'If you use an URL as content, you should encode it.', 'ycwp-qr-me' ) ?></span>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'classes' ) ); ?>"><?php _e( 'Image classes', 'ycwp-qr-me' ); ?>:</label>
            <input type="text" name="<?php echo esc_attr( $this->get_field_name( 'classes' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'classes' ) ); ?>" value="<?php echo $instance['classes']; ?>" />
            <span class="description"><?php _e( 'Comma separated classes and only <strong>alphanumeric chars</strong>, <strong>-</strong> and <strong>_</strong>', 'ycwp-qr-me' ); ?></span>
        </p>
        <?php
    }
    
    /**
     * Update a particular instance.
     *
     * This function should check that $new_instance is set correctly.
     * The newly calculated value of $instance should be returned.
     * If "false" is returned, the instance won't be saved/updated.
     *
     * @param array $new_instance New settings for this instance as input by the user via form()
     * @param array $old_instance Old settings for this instance
     * @return array Settings to save or bool false to cancel saving
     * @link http://core.trac.wordpress.org/browser/tags/3.3.1/wp-includes/widgets.php#L58
     * @since 1.0
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['size'] = $new_instance['size'];
        $instance['error'] = $new_instance['error'];
        $instance['content'] = strip_tags( $new_instance['content'] );
        $instance['encodeurl'] = $new_instance['encodeurl'];
        $instance['classes'] = strip_tags( $new_instance['classes'] );
        
        return $instance;
    }
}
?>