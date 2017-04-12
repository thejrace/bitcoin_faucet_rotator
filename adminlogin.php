<?php
	
	if( !isset($_SESSION ) ){
    	session_start();
    }
	require_once "inc/DB.php";

	class Admin {

		private $pdo;
		protected $flag = true, $admin = array();

		public function __construct(){
			$this->pdo = DB::getInstance();
		}

		public function login( $data ){

			$query =  $this->pdo->query("SELECT * FROM admin WHERE username = ? && password = ?", array( sha1($data["username"]), sha1($data["password"]) ));
			$results = $query->results();
			if( !empty( $results ) > 0 ){
				if( isset($data["remember_me"]) ){
					// Beni hatırla derse 10 günlük cookie yaz
					$cookie = md5( time() . rand(0, 150 ) );
					setcookie("btc_adm", $cookie, time()+86400*10, "/");
					if( !$this->pdo->update("admin", "id", $results[0]["id"], array(
						"cookie" => $cookie,
						"ip"     => $_SERVER["REMOTE_ADDR"]
					)) ){
						$this->flag = false;
					}
				} 
				$_SESSION["adm_username"] = $data["username"];

			} else {
				$this->flag = false;
			}
		}

		public function check(){

			if( isset( $_COOKIE["btc_adm"]) ){
				$q = $this->pdo->query( " SELECT * FROM admin WHERE cookie = ? ", array($_COOKIE["btc_adm"]));
				$res = $q->results();
				if( !empty($res) ){
					$_SESSION["adm_username"] = $res[0]["username"];
				} else {
					setcookie("btc_adm", "", time()-86400*10, "/");
					$this->flag = false;
				}
			} else {
				$this->flag = false;
			}

		}

		public function success(){
			return $this->flag;
		}

	}

	$Admin = new Admin;
	$output = " Giriş yap ";
	if( $_POST ){

		
		$Admin->login( $_POST );
		if( $Admin->success() ){

			header( "Location: adminfaust.php" );
		} else {
			$output = "Başarısız giriş.";
		}

	} else {
		if( isset($_SESSION["adm_username"]) ){
			header( "Location: adminfaust.php" );
		}
	}

	

?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1">

        <link rel="stylesheet" type="text/css" href="res/css/style.css" />
        <script type="text/javascript" src="http://ahsaphobby.net/resources/js/jquery.js"></script>
        <script type="text/javascript" src="res/js/main.js"></script>

        <title>Bit - bitcoin_BASE95</title>
    </head>
    <body>
    <div style="padding:20px; width:180px; margin:0 auto;">
    	<?php echo $output; ?>
	    <form action="" method="post" />

	    	<div><input type="text" name="username" placeholder="Username.." /></div>
	    	<div><input type="password" name="password" placeholder="Password" /></div>
	    	<div><label for="remember_me">Remember me</label>
	    		 <input type="checkbox" name="remember_me" id="remember_me"/></div>
	    	<div><input type="submit" value="Login" /></div>
	    </form>
	</div>

    </body>
</html>