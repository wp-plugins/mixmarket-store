<?php
if ( !class_exists( 'Base_Controller' ) ) {
    /**
     * Base class for all controllers of the plugin.
     * It contains methods for views rendering handling messages.
     */
    class Base_Controller {
        private $message = null;

        public function  __construct() {
        }

        /**
         * Renders a view.
         *
         * @param string $view file name of a view with absolute path
         * @param array $data variables that should be passed to a view
         * @param boolean $return if is true view content will be returned
         * @return string view content (if $return == true) otherwise send it to a browser
         */
        public function render( $view, $data = null, $return = false ) {
            if ( is_array( $data ) ) {
                //extracting variables
                extract( $data, EXTR_PREFIX_SAME, 'data' );
            }
            if ( $return ) {
                //buffering view content
                ob_start();
                ob_implicit_flush( false );
                require( $view );
                return ob_get_clean();
            }
            else {
                require( $view );
            }
        }

        /**
         * Setting info message. A message will be save in class property and in session.
         *
         * @param string $message message text
         */
        public function set_message( $message ) {
            $_SESSION[ 'mm_message' ] = $message;
            $this->message = $message;
        }

        /**
         * This method returns message text.
         * The method searches for message in class property and session.
         * Each message will be automatically unset by this method.
         *
         * @return string message if it exists or FALSE
         */
        public function get_message() {
            if ( $this->message === null ) {
                if ( isset( $_SESSION[ 'mm_message' ] ) ) {
                    $mes = $_SESSION[ 'mm_message' ];
                    unset( $_SESSION[ 'mm_message' ] );
                    return $mes;
                }
                else {
                    return FALSE;
                }
            }
            else {
                return $this->message;
            }
        }
    }
}