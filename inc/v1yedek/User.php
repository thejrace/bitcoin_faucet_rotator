<?php
    
    // Deprecated hajiii

    class Register {

        private $pdo, $date;
        protected $userid;
        const TABLE = " users";

        public function __construct(){
            $this->pdo = DB::getInstance();
            $this->date = date("Y-m-d") . " " . date("H:i:s");
        }

        public function action(){
            $firstCookie = md5( time() . rand(0, 150 ) );
            $register = $this->pdo->insert(self::TABLE, array(
                "ip"         => $_SERVER["REMOTE_ADDR"],
                "cookie"     => $firstCookie,
                "last_login" => $this->date
            ));

            $newID = $this->pdo->lastInsertedId();

            if( $register ) {
                setcookie("btc_gid", $firstCookie, time()+86400*365, "/");

                $this->userid = $newID;

                if( 
                	$this->pdo->insert("user_filter", array(
                        "user_id"   => $this->userid,
                        "XP"        => 1,
                        "EP"        => 1, 
                        "FB"        => 1,
                        "DIRECT"    => 1, 
                        "PT"        => 1, 
                        "SM"        => 1, 
                        "RC"        => 1,
                        "FC"        => 1, 
                        "RK"        => 1, 
                        "AH"        => 1,
                        "OTHER"     => 1, 
                        "VISITED"   => 0, 
                        "JUNK"      => 0, 
                        "NOT_RECOM" => 0, 
                        "DRY"       => 0 
                    ))
                ) {
                	return true;
                } else {
                	return false;
                }

            }
        }

        public function getUserID(){
            return $this->userid;
        }

    }

    class Login {

        private $pdo;
        protected $user = array(), $date, $confirm = false;
        const TABLE = " users";
        public function __construct(){
            $this->pdo = DB::getInstance();
            $this->date = date("Y-m-d") . " " . date("H:i:s");
        }

        public function useIP(){
            $q = $this->pdo->query(" SELECT * FROM " .self::TABLE. " WHERE ip = ?", array( $_SERVER["REMOTE_ADDR"] ));
            if( $q->count() > 0 ){
                foreach( $q->results()[0] as $key => $val ){
                    $this->user[$key] = $val;
                }
                $newCookie = md5( $this->user["id"] . rand(0, 10 ) );
                $update = $this->pdo->update( self::TABLE, "id", $this->user["id"], array(
                    "last_login" => $this->date,
                    "cookie" => $newCookie
                ));
                if( $update ) {
                    setcookie("btc_gid", $newCookie, time()+86400*365, "/");
                    $this->confirm = true;
                }
            }
        }

        public function useCookie(){

            $q = $this->pdo->query(" SELECT * FROM " .self::TABLE. " WHERE cookie = ?", array( $_COOKIE["btc_gid"] ));
            if( $q->count() > 0 ){
                foreach( $q->results()[0] as $key => $val ){
                    $this->user[$key] = $val;
                }

                $update = $this->pdo->update( self::TABLE, "id", $this->user["id"], array(
                    "last_login" => $this->date,
                    "ip" => $_SERVER["REMOTE_ADDR"]
                ));

                if( $update ) $this->confirm = true;
            }
        }

        public function getUserID(){
            return $this->user["id"];
        }

        public function success(){
            return $this->confirm;
        }

    }


    class UserCheck {

        private $pdo;
        protected $user = array(), $confirm = false;
        public $log = " No prob" ;

        public function __construct(){
            $this->pdo = DB::getInstance();
        }

        // Kodla ugras az
        public function checkCookie(){
 
            if( isset( $_COOKIE["btc_gid"] ) ){   
                
                // Cookie varsa DB'den kontrol et 
                $Login = new Login;
                $Login->useCookie();

                // Eğer DB'de yoksa, IP adresini kontrol et
                if( !$Login->success() ){
                    $this->log = " Cookie DB'de yok ";
                    $Reg = new Register;
                    if( $Reg->action() ) {
                        
                        $this->user["id"] = $Reg->getUserID();
                        $this->confirm = true;
                    }  
                } else {
                    $this->user["id"] = $Login->getUserID();
                    $this->confirm = true;
                }

            } else {

                $Reg = new Register;
                if( $Reg->action() ) {
                    $this->user["id"] = $Reg->getUserID();
                    $this->confirm = true;
                }
            } 
        }

        public function success(){
        	return $this->confirm;
        }

        public function getUserData(){
            return $this->user;
        }


    }