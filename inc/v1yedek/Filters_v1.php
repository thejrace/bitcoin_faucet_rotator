<?php

 	class FilterTables {

		const 
			PaymentS = " payment_settings ",
			PaymentF = " user_filter_payment ",
			StatusS  = " status_settings ",
			StatusF  = " user_filter_status ",
			TypeS    = " type_settings ",
			TypeF    = " user_filter_type ";
    }

    abstract class Filter {
        protected $pdo;

        public function __construct() {
            $this->pdo = DB::getInstance();
        }

        protected function get( $type, $userid ){

            switch( $type ) {
                case 'payment':
                    $F_TABLE = FilterTables::PaymentF;
                    $S_TABLE = FilterTables::PaymentS;
                break;

                case 'type':
                    $F_TABLE = FilterTables::TypeF;
                    $S_TABLE = FilterTables::TypeS;
                break;

                case 'status':
                    $F_TABLE = FilterTables::StatusF;
                    $S_TABLE = FilterTables::StatusS;
                break;
            }

            $uset = $this->pdo->query(" SELECT * FROM " . $F_TABLE . " WHERE user_id = ?", array($userid));
            foreach( $uset->results()[0] as $key => $val ){
                $this->status[$key] = $val;
            }

            $res = $this->pdo->query(" SELECT * FROM " . $S_TABLE );
            foreach( $res->results() as $setting ){
                $checked = "";
                if( $this->status[strtolower($setting["short"])] == 1 ) $checked = " checked";
                
                $strlower = strtolower( str_replace( "'", "", str_replace( " ", "_", $setting["name"] ) ) );
                
                $this->html .= '
                    <label class="filter-buton '.$checked.'" id="'.$type.'_lab_'.$strlower.'" for="'.$type.'_'.$strlower.'" title="'.$setting["name"].'">
                        <input type="checkbox" name="'.$type.'_'.$strlower.'" id="'.$type.'_'.$strlower.'" '.$checked.' />
                        '.$setting["short"].'
                    </label>
                ';
            }            

            return $this->html;
        }

        public abstract function generate( $userid );
    }

    class FilterPaymentSettings extends Filter{

        public function __construct(){
            parent::__construct();
        }

        public function generate( $userid ){
            return $this->get( "payment", $userid );
        }

    }

    class FilterTypeSettings extends Filter {

        public function __construct(){
            parent::__construct();
        }

        public function generate( $userid ){
            return $this->get( "type", $userid );
        }

    }

    class FilterStatusSettings extends Filter {

        public function __construct(){
            parent::__construct();
        }

        public function generate( $userid ){
            return $this->get( "status", $userid );
        }

    }