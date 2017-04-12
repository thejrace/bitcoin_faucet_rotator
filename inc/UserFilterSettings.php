<?php
	// depracated hajiiii
	class UserSettings {
		
		private $pdo;
		protected $usersettings = array();
		public function __construct(){

			$this->pdo = DB::getInstance();
		
		}

		public function edit( $userid, $data ){


			if( count($data) > 0 ){
				// Kullanicinin ayarlari
				$us = $this->pdo->query( " SELECT * FROM user_filter WHERE user_id = ?", array( $userid ) );
				$this->usersettings = $us->results();

				// Tum filter ayarlari icin, kullanicinin degisiklik yaptiklarini db ye kaydet
				$res = $this->pdo->query(" SELECT * FROM tag " );
				foreach( $res->results() as $setting ){
					// Aktif edilmisleri 1 
					if( isset( $data[strtolower( $setting["short"] )] ) ) {
						$this->usersettings[ $setting["short"] ] = 1;
					// Aktif olmayanlari 1 yap
					} else {
						$this->usersettings[ $setting["short"] ] = 0;
					}
				}

				if( isset($data["sortby"]) ){
					$key = explode( "sort_", $data["sortby"] );
					unset($key[0]);
					$this->usersettings["orderby"] = " ORDER BY " . $key[1];
				}

				// Admin degilse ayarlari kullaniciya kaydet
				if( !isset($data["type"]) ){
					return $this->pdo->update("user_filter", "user_id", $userid, array(
						"XP" 		=> $this->usersettings["XP"],
						"EP"		=> $this->usersettings["EP"], 
						"FB" 		=> $this->usersettings["FB"],
						"DIRECT"	=> $this->usersettings["DIRECT"], 
						"PT"		=> $this->usersettings["PT"], 
						"SM" 		=> $this->usersettings["SM"], 
						"RC"		=> $this->usersettings["RC"],
						"FC" 		=> $this->usersettings["FC"], 
						"RK" 		=> $this->usersettings["RK"], 
						"AH" 		=> $this->usersettings["AH"],
						"OTHER"     => $this->usersettings["OTHER"], 
						"VISITED"   => $this->usersettings["VISITED"], 
						"JUNK"      => $this->usersettings["JUNK"], 
						"NOT_RECOM" => $this->usersettings["NOT_RECOM"], 
						"DRY"       => $this->usersettings["DRY"] 
					));
				}

				return true;
			} 
		}

		public function newSettings(){
			return $this->usersettings;
		}

	}