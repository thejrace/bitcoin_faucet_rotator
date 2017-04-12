<?php

class Cookie {

	public static function exists($name){
		return isset($_COOKIE[$name]) ? true : false;
	}

	public static function set($name, $value){
		setCookie($name, $value, time()+86400*365, "/");
	}

	public static function setwithtime($name, $value, $time){
		setCookie($name, $value, $time, "/");
	}

	public static function get($name){
		return $_COOKIE[$name];
	}

	public static function destroy( $name ){
		setcookie($name, "", time()-86400*365, "/");
	}
}

