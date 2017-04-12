<?php
    
    include 'Hash.php';

    class User {

        private $pdo;
        protected $error = "";
        protected $id, $email, $pass, $cookie, $date, $log = "";
        protected $loggedin = false, $guest = false, $perms = array();
        protected $hash;
        const TABLE = " users ";

        public function __construct(){
            $this->pdo = DB::getInstance();
            $this->date = date("Y-m-d") . " " . date("H:i:s"); 
            $this->hash = new Hash;
        }

        public function editAccount( $data ){
            
            if( !empty($data["confirmpass"]) && !empty($data["email"]) && filter_var( $data["email"], FILTER_VALIDATE_EMAIL ) ){

                $user = Session::get("USER_ID");

                $saltquery  = $this->pdo->query( "SELECT salt, password FROM ".self::TABLE." WHERE id = ?", array($user))->results();
                $salt       = $saltquery[0]["salt"];
                $db_oldpass = $saltquery[0]["password"];
                $oldpass    = hash( 'sha512', $salt . $data["confirmpass"] );
                if( $db_oldpass == $oldpass ){

                    $this->pdo->query(" UPDATE user_wallets SET address = ? WHERE user_id = ? ", array( $data["wallet"], $user ));
                    $this->pdo->query(" UPDATE ".self::TABLE." SET email = ? WHERE id = ? ", array( $data["email"], $user ));
                    return true;

                } else {
                    $this->error = "The password you entered is invalid.";
                    return false;
                }

            } else {
                $this->error = "There are missing or wrong fields in the form.";
                return false;
            }

        } 

        public function editPass( $data ){

            $post_newpass   = $data["newpass"];
            $post_newpass_r = $data["newpass_r"];
            $post_oldpass   = $data["oldpass"];

            if( !empty($post_newpass) && !empty($post_newpass_r) && !empty($post_oldpass) ){

                // Salt ve eski password u al
                $user = Session::get("USER_ID");
                $saltquery = $this->pdo->query( "SELECT salt, password FROM ".self::TABLE." WHERE id = ?", array($user))->results();
                $salt = $saltquery[0]["salt"];
                $db_oldpass = $saltquery[0]["password"];

                // Kullanicinin girdigi old password' u hash' le ve
                // Dogrulugunu kontrol et
                $oldpass = hash( 'sha512', $salt . $post_oldpass );
                if( $db_oldpass == $oldpass ){
                    // Eger new passler birbiriyle uyusmuyorsa error logu tut
                    // Uyusuyorlarsa kullaniciyi update et
                    if( $post_newpass == $post_newpass_r ) {
                        $newpass = hash( 'sha512', $salt . $post_newpass );
                        return $this->pdo->query( "UPDATE " . self::TABLE . "SET password = ? WHERE id = ?", array($newpass, $user) );
                    } else {
                        $this->error = "New passwords doesn't match.";
                        return false;
                    }
                }

            } else {
                $this->error = "There are missing fields in the form. Please fill in all inputs.";
                return false;
            }
            
        }

        // Login mi, register mi yapilacak 
        public function action( $data ){

            // post datalarini kontrol et
            if( empty($data["email"]) || empty($data["password"]) ){
                 $this->error = "Password or Email is missing.";
            } else {
                $this->email = $data["email"];
                $this->pass  = $data["password"];

                // girilen email db de var mi, kullanici uye mi kontrol
                $t = $this->pdo->query( "SELECT * FROM users WHERE email = ? ", array( $this->email ) );
                if( $t->count() == 1 ) {
                    // uyusan satir varsa salt ve password u al
                    $r = $t->results();
                    $passfromdb = $r[0]["password"];
                    $salt = $r[0]["salt"];

                    // post pass ile db den aldigin user salt' i hash le ve karislastir
                    // uyusma varsa login et kullaniciyi
                    $postpass = hash( 'sha512', $salt . $this->pass );

                    if( $passfromdb == $postpass ) {
                        $this->id = $r[0]["id"];
                        $this->login();
                    } else {
                        $this->error = " Some credentials are incorrect.";
                    }

                } else {
                    // eger boyle bir mail yoksa, register baslat
                    $this->register();
                }
            }
        }

        private function register(){
            // $this->log .= "Register started || ";

            // password hash sistemi = sha512( salt + pass ) seklinde
            // 32 karakterlik kullaniciya ozel salt uret
            // bu salt password un onune eklenecek, oyle hash edilcek
            // salt' i ayrica user 'in db deki satirina ekle
            $salt = $this->hash->getToken( 32 );
            $this->pass = hash( 'sha512', ($salt . $this->pass) );

            if( $this->pdo->insert("users", array(
                'email'    => $this->email,
                'password' => $this->pass,
                'salt'     => $salt,
                'cookie'   => md5($this->email),
                'register' => $this->date
            ))) {   

                $this->id = $this->pdo->lastInsertedId();

                // Siteye ilk giriste guest olarak cookie ve db kayidi yaptigim icin
                // Uye oldugu zaman guests den kaydini sil
                // Countdown u varsa o kaydin user_id sini yeni user_id ile degistir
                $this->log .= "Register alt basladi || ";
                if( Cookie::exists( "btc_guest" ) ){
                    if( $this->pdo->query( "DELETE FROM guests WHERE rand = ?", array( Cookie::get( "btc_guest" ) ) ) ) {
                        $countdowns = $this->pdo->query( "SELECT * FROM countdowns WHERE user_id = ?", array( Cookie::get( "btc_guest" ) ) );
                        if( $countdowns->count() > 0 ){
                            $this->log .= "DB query tamam, countdownda uyusanlar alindi ||";
                            foreach( $countdowns->results() as $cd ){
                                $this->pdo->query("UPDATE countdowns SET user_id = ? WHERE id = ?", array( $this->id, $cd["id"] ) );
                            }
                        } else {
                            $this->log .= "DB query tamam, countdownda uyusan yok || ";
                        }
                        
                        Cookie::destroy( "btc_guest" );
                        $this->log .= "Cookie silindi || ";
                    }
                }

                $this->log .= "Login basladi || ";

                $this->login();
            } 
        }

        private function login(){
            // $this->log .= "Login started || ";
            if( !Cookie::exists( "btc_gid" ) )  {
                // $this->log .= "Cookie set || ";
                $this->cookie = md5($this->email);

                Cookie::set( "btc_gid", $this->cookie );
                $this->pdo->query("UPDATE users SET cookie = ? WHERE email = ?", array($this->cookie, $this->email));
            }

            if( $this->pdo->query("UPDATE users SET last_login = ? WHERE email = ?", array($this->date, $this->email )) ){
                $this->log .= "Login db update || ";

                Session::set("USER_MAIL", $this->email );
                Session::set("USER_ID", $this->id );

                // Her ilk giriste guest cookie si settigim icin, kullanici eger yeni uye olmayip,
                // giris yaparsa guest cookie' yi ve DB deki datayı sil.
                if( Cookie::exists( "btc_guest" ) ) {
                    Cookie::destroy( "btc_guest" );
                    $this->pdo->query( "DELETE FROM guests WHERE rand = ?", array( Cookie::get( "btc_guest" ) ) );
                }

                $this->loggedin = true; 
            }        
        }

        public function remember(){
            // $this->log .= "Remember started ||";
            if( Cookie::exists( "btc_gid" ) ){
                $cookiecheck = $this->pdo->query( " SELECT * FROM users WHERE cookie = ? ", array( Cookie::get( "btc_gid" ) ) );
                if( $cookiecheck->count() == 1 ){
                    $r = $cookiecheck->results();
                    $this->email = $r[0]["email"];
                    $this->id = $r[0]["id"];
                    if( $this->pdo->query("UPDATE users SET last_login = ? WHERE email = ?", array($this->date, $this->email )) ){
                        // $this->log .= "Login db update || ";

                        Session::set("USER_MAIL", $this->email );
                        Session::set("USER_ID", $this->id );

                        $this->loggedin = true; 
                    } 
                } else {
                    Cookie::destroy( "btc_gid" );
                }
            } else {

                // Guest muamelesi yap ahjahah
                $this->guestAction();
            }
        }

        // Kullanici uye olana kadar gecici id ile guest muamelesi baslat
        // Uye olurken btc_guest cookie varsa, guests tablosundan o guest' e ait kayıtlari sil
        public function guestAction(){


            // Zaten guest olarak bir kere girmisse remember me yap
            if( Cookie::exists( "btc_guest" ) ){
                $check = $this->pdo->query( "SELECT * FROM guests WHERE rand = ? ", array( Cookie::get( "btc_guest" ) ) );
                if( $check->count() ){
                    if( $this->pdo->query("UPDATE users SET last_login = ? WHERE rand = ?", array( $this->date, Cookie::get( "btc_guest" ) ) ) ) {
                        Session::set("USER_ID", Cookie::get( "btc_guest" ) );
                        $this->guest = true;
                    }
                }
            } else {
                // Eger ilk defa giriyorsa guest olarak register et
                $rand = $this->hash->getNumericToken(7);
                if( $this->pdo->query( "INSERT INTO guests SET rand = ?, register_date = ?, last_login = ?", array(  $rand, $this->date, $this->date ) ) ) {
                    Cookie::set( "btc_guest", $rand );
                    Session::set("USER_ID", $rand );
                    $this->guest = true;
                }
            }
        }

        public function logout(){

            Cookie::destroy( "btc_gid" );
            $this->email  = "";
            $this->pass   = "";
            $this->cookie = "";
            $this->loggedin = false;
            Session::destroy("USER_MAIL", $this->email );
            Session::destroy("USER_ID", $this->id );
        }

        public function getWalletAddress(){
            $wallet = "";
            $query = $this->pdo->query("SELECT * FROM user_wallets WHERE user_id = ?", array( $this->id ) )->results();
            if ( count($query) > 0 ) {
                $wallet = $query[0]["address"];
            }
            return $wallet;
        }

        public function setPerms(){

            // guest icin izinler
            $this->perms = array(
                "countdown" => true,
                "PI_SETTINGS"  => false,
                "comment"   => false,
                "follow"    => false,
                "unfollow"  => false,
                "feedback"  => true
            );

            // uyeler icin
            if( $this->loggedin ){
                $this->perms["PI_SETTINGS"] = true;
                $this->perms["comment"]  = true;
                $this->perms["follow"]   = true;
                $this->perms["unfollow"] = true;
            }

            Session::set( "USER_PERMS", $this->perms );
        }

        public function getPerms(){
            return $this->perms;
        }

        public function isGuest(){
            return $this->guest;
        }

        public function isLoggedin(){
            return $this->loggedin;
        }

        public function getEmail(){
            return $this->email;
        }

        public function getID(){
            return $this->id;
        }

        public function getErrors(){
            return $this->error;
        }

        public function getLog(){
            return $this->log;
        }
    }