<?php

    class Filter {
        protected $pdo, $html;

        public function __construct() {
            $this->pdo = DB::getInstance();
        }

        public function generate( $userid ){

            $uset = $this->pdo->query(" SELECT * FROM user_filter WHERE user_id = ?", array($userid));
            foreach( $uset->results()[0] as $key => $val ){
                $this->status[$key] = $val;
            }

            $res = $this->pdo->query(" SELECT * FROM tag " );
            foreach( $res->results() as $setting ){
                $checked = "";
                if( $this->status[$setting["short"]] == 1 ) $checked = " checked";
                
                $strlower = strtolower( $setting["short"] );
                
                $this->html .= '
                    <label class="filter-buton '.$checked.'" id="lab_'.$strlower.'" for="'.$strlower.'" title="'.$setting["name"].'">
                        <input type="checkbox" name="'.$strlower.'" id="'.$strlower.'" '.$checked.' />
                        '.$setting["short"].'
                    </label>
                ';
            }            

        }

        public function defaultType(){

        	$res = $this->pdo->query(" SELECT * FROM tag " );
            foreach( $res->results() as $setting ){
                    
                $strlower = strtolower( $setting["short"] );
                
                $this->html .= '
                    <label class="filter-buton" id="lab_'.$strlower.'" for="'.$strlower.'" title="'.$setting["name"].'">
                        <input type="checkbox" name="'.$strlower.'" id="'.$strlower.'"/>
                        '.$setting["short"].'
                    </label>
                ';
            }   
        }

        public function formType( $values ){


            $res = $this->pdo->query(" SELECT * FROM tag " );
            foreach( $res->results() as $setting ){
                $checked = "";            
                    
                // Duzenleme de secili olanlari checked yap
                if( isset( $values[ $setting["short"] ] ) && $values[ $setting["short"] ] ) $checked = " checked";

                if( $setting["short"] != "VISITED" ) {
                    $strlower = strtolower( $setting["short"] );
                    
                    $this->html .= '
                        <div class="tag">
                            <input type="checkbox" name="'.$strlower.'" id="'.$strlower.'" '.$checked.'/>
                            <label id="lab_'.$strlower.'" for="'.$strlower.'" title="'.$setting["name"].'">'.$setting["name"].'</label>
                        </div>
                    ';
                }
            }   
        }

        public function userSettings(){
        	return $this->status;
        }

        public function show(){
        	return $this->html;
        }

    }






