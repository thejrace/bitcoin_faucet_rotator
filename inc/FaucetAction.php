<?php

	class FaucetAction {
		private $pdo;
		protected $tagdata = array(), $user, $date;

		const DB_CD = " countdowns ",
			  DB_UNFOLLOWED = " unfollowed_faucets ",
			  DB_COMMENTS = " faucet_comments ",
			  DB_FEEDBACKS = " faucet_feedback ",
			  DB_FAUCET = " faucet ";

		public function __construct(){
			$this->pdo = DB::getInstance();
			$this->user = $_SESSION["USER_ID"];
			$this->date = date("Y-m-d") . " " . date("H:i:s");
		}

		public function startCountdown( $fid ){

			if( $this->pdo->query("SELECT * FROM ".self::DB_CD." WHERE user_id = ? && faucet_id = ?", array( $this->user, $fid ) )->count() > 0 ) {
				// Varolanı update
				return $this->pdo->query("UPDATE ".self::DB_CD." SET start_time = ? WHERE user_id = ? && faucet_id = ?", array(
					$this->date, $this->user, $fid
				));
			} else {
				// Yeni ekle
				return $this->pdo->query("INSERT INTO ".self::DB_CD." SET faucet_id = ?, user_id = ?, start_time = ?", array(
					$fid, $this->user, $this->date
				));
			}
			
		}	
			
		public function follow( $fid ){
			return $this->pdo->query( "DELETE FROM ".self::DB_UNFOLLOWED." WHERE faucet_id = ? && user_id = ?", array( $fid, $this->user ) );
		}
		public function unfollow( $fid ){
			return $this->pdo->query( "INSERT INTO ".self::DB_UNFOLLOWED." SET faucet_id = ?, user_id = ?", array( $fid, $this->user ) );
		}

		public function addcomment( $data ){
			if( !empty($data["usercomment"]) ){
				return $this->pdo->query("INSERT INTO ".self::DB_COMMENTS." SET faucet_id = ?, user_id = ?, comment = ?, date = ?", array(
					$data["fid"], $this->user, $data["usercomment"], $this->date
				));
			} else {
				return false;
			}
		}

		public function addfeedback( $data ){
			return $this->pdo->query("INSERT INTO ".self::DB_FEEDBACKS." SET faucet_id = ?, user_id = ?, type = ?, date = ?", array(
				$data["fid"], $this->user, $data["ftype"], $this->date
			));
		}

		public function add( $data ){

			$res = $this->pdo->query(" SELECT * FROM tag " );
			foreach( $res->results() as $setting ){
					// Aktif edilmisleri 1 
				if( isset( $data[strtolower( $setting["short"] )] ) ) {
					$this->tagdata[ $setting["short"] ] = 1;
				} else {
					$this->tagdata[ $setting["short"] ] = 0;
				}
			}
			return 
				$this->pdo->insert("faucet", array(
					"address"   => $data["address"],
					"timer"     => $data["timer"],
					"payout"    => $data["payout"],
					"XP" 		=> $this->tagdata["XP"],
					"EP"		=> $this->tagdata["EP"], 
					"FB" 		=> $this->tagdata["FB"],
					"DIRECT"	=> $this->tagdata["DIRECT"], 
					"PT"		=> $this->tagdata["PT"], 
					"SM" 		=> $this->tagdata["SM"], 
					"RC"		=> $this->tagdata["RC"],
					"FC" 		=> $this->tagdata["FC"], 
					"RK" 		=> $this->tagdata["RK"], 
					"AH" 		=> $this->tagdata["AH"],
					"OTHER"     => $this->tagdata["OTHER"], 
					"VISITED"   => $this->tagdata["VISITED"], 
					"JUNK"      => $this->tagdata["JUNK"], 
					"NOT_RECOM" => $this->tagdata["NOT_RECOM"], 
					"DRY"       => $this->tagdata["DRY"] 
				));
		}

		public function edit( $data ){

			$res = $this->pdo->query(" SELECT * FROM tag " );
			foreach( $res->results() as $setting ){
					// Aktif edilmisleri 1 
				if( isset( $data[strtolower( $setting["short"] )] ) ) {
					$this->tagdata[ $setting["short"] ] = 1;
				} else {
					$this->tagdata[ $setting["short"] ] = 0;
				}
			}

			return $this->pdo->update("faucet", "id", $data["fid"], array(
				"address"   => $data["address"],
				"timer"     => $data["timer"],
				"payout"    => $data["payout"],
				"XP" 		=> $this->tagdata["XP"],
				"EP"		=> $this->tagdata["EP"], 
				"FB" 		=> $this->tagdata["FB"],
				"DIRECT"	=> $this->tagdata["DIRECT"], 
				"PT"		=> $this->tagdata["PT"], 
				"SM" 		=> $this->tagdata["SM"], 
				"RC"		=> $this->tagdata["RC"],
				"FC" 		=> $this->tagdata["FC"], 
				"RK" 		=> $this->tagdata["RK"], 
				"AH" 		=> $this->tagdata["AH"],
				"OTHER"     => $this->tagdata["OTHER"], 
				"VISITED"   => $this->tagdata["VISITED"], 
				"JUNK"      => $this->tagdata["JUNK"], 
				"NOT_RECOM" => $this->tagdata["NOT_RECOM"], 
				"DRY"       => $this->tagdata["DRY"] 
			));

		}

		public function delete( $id ){
			return $this->pdo->delete("faucet", array('id', '=', $id) );
		}

		public function generateAddForm(){

			$UserFilters = new Filter;
			$UserFilters->formType( array() );

			return '
				<div class="popup-header"><span>Add Faucet</span></div>
				<div class="form-container">
					<span id="form-notf"></span>
					<form action="" method="post" id="add_faucet_form">
		                <input type="hidden" name="req" value="add_faucet" />
						<div class="input-grup">
							<label for="address">Address</label>
							<input type="text" id="address" name="address" class="b-bot" />
						</div>

						<div class="input-grup">
							<label for="payout">Payout</label>
							<input type="text" id="payout" name="payout" class="b-bot"  />
						</div>

						<div class="input-grup">
							<label for="timer">Timer</label>
							<input type="text" id="timer" name="timer" class="b-bot"  />
						</div>

						<div class="tag-grup">
		                   '.$UserFilters->show().'
						</div>

		                <div class="input-grup">
		                    <input type="submit" class="btn-submit" value="Save" />
		                </div>
					</form>
				</div>';
		}

		public function editform( $id ){
			$qu = $this->pdo->query("SELECT * FROM faucet WHERE id = ?", array($id));
			$re = $qu->results()[0];

			$Det = new Filter;
			$Det->formType( $re );

			return '
				<div class="popup-header"><span>Edit Faucet</span></div>
				<div class="form-container">
					<span id="form-notf"></span>
					<form action="" method="post" id="edit_faucet_form">
		                <input type="hidden" name="req" value="edit_faucet" />
		                <input type="hidden" name="fid" value="'.$re["id"].'" />
						<div class="input-grup">
							<label for="address">Address</label>
							<input type="text" id="address" name="address" value="'.$re["address"].'" class="b-bot"  />
						</div>

						<div class="input-grup">
							<label for="payout">Payout</label>
							<input type="text" id="payout" name="payout" value="'.$re["payout"].'"  class="b-bot" />
						</div>

						<div class="input-grup">
							<label for="timer">Timer</label>
							<input type="text" id="timer" name="timer" value="'.$re["timer"].'" class="b-bot"  />
						</div>

						<div class="tag-grup">
		                   '.$Det->show().'
						</div>

		                <div class="input-grup">
		                    <input type="submit" class="btn-submit" value="Save" />
		                </div>
					</form>
				</div>';

		}

	}