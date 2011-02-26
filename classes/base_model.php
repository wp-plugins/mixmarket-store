<?php
if ( !class_exists( 'Base_Model' ) ) {
    /**
     * Base model class.
     * It contains methods for setting fields, getting their values and validating
     * data.
     */
    class Base_Model {
        public $id = null;

        //fields array
        protected $fields = array();
        //array with rules for fields (keys of elements in this array should
        // correspond to elements in $fields array)
        protected $rules = array();
        //this array will contain validation results
        protected $errors = array();
        //types of the fields (you can use '%s' or '%d' type)
        //order of elements in this array should correspond to elements in $fields array
        protected $fields_types = array();

        public function  __construct() {
        }

        /**
         * Setting elements in $fields array
         *
         * @param string $name key in $fields array
         * @param mixed $value value on an element
         */
        public function __set( $name, $value ) {
            if ( array_key_exists( $name, $this->fields ) ) {
                $this->fields[ $name ] =  $value;
            }
        }

        /**
         * Retrieving value of element in $fields array
         *
         * @param string $name key of element in $fields array
         * @return mixed value of element in $fields array or NULL in element is not found
         */
        public function __get( $name ) {
            if (array_key_exists( $name, $this->fields ) ) {
                return $this->fields[ $name ];
            }
            else {
                return NULL;
            }
        }

        /**
         * Massive fields assigning. Sets only elements, that has corresponding keys in $fields array.
         *
         * @param array $data fields data array
         */
        public function set_fields( $data ) {
            $s_data = stripslashes_deep( $data );
            foreach ( $s_data as $key => $value ) {
                $this->__set( $key, $value );
            }
        }

        /**
         * Validating fields values. You should set rules in $rules array.
         * Only 'required' and 'numeric' are supported.
         *
         * @return boolean TRUE if validation passed, FALSE - otherwise.
         */
        public function validate() {
            $res = true;
            foreach ( $this->rules as $key => $rule ) {
                $val = $this->fields[ $key ];
                switch ( $rule[ 'name' ] ) {
                    case 'required':
                        if ( !isset( $val ) || trim( $val ) == '' ) {
                            $res = false;
                            $this->errors[ $key ] = __( 'This field is required', MM_TEXTDOMAIN );
                        }
                        break;
                    case 'numeric':
                        if ( !isset( $val ) || !is_numeric( $val ) ) {
                            $res = false;
                            $this->errors[ $key ] = __( 'This field must be numeric', MM_TEXTDOMAIN );
                        }
                        break;
                }
            }
            return $res;
        }

        /**
         * Retrieves errors message for the field. Should be called after validate method.
         *
         * @param string $field name of a field
         * @param string $before string, that will be added before error messge
         * @param string $after string, that will be added after error messge
         * @return string error message, or empty string if there is no error
         */
        public function get_error( $field, $before = '', $after = '' ) {
            if ( isset( $this->errors[ $field ] ) ) {
                return $before . $this->errors[ $field ] . $after;
            }
            return '';
        }

        /**
         * Checks if this is a new model.
         * Model consided to be new if $id property is not set.
         *
         * @return boolean TRUE if the model is new, FALSE - otherwise.
         */
        public function is_new() {
            return ( $this->id === null ) ? true : false;
        }

        /**
         * Purifies all fields data.
         * This method uses HTMLPurifier library and $purifier global should be created before calling this method.
         *
         * @global HTMLPurifier $purifier instance of HTMLPurifier object
         */
		public function purify_fields() {
			global $purifier;
			foreach ( $this->fields as $key => $field ) {
				$this->fields[ $key ] = $purifier->purify( $field );
			}
		}

        /**
         * Returns $fields array. For use with $wpdb class.
         *
         * @return array fields data
         */
        public function get_fields() {
            return $this->fields;
        }

        /**
         * Returns $fields_types array. For use with $wpdb class.
         *
         * @return array fields types
         */
        public function get_types() {
            return $this->fields_types;
        }
    }
}