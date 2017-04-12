<?php
	
	require_once "Init.php";

	class FaucetAction {
		private $pdo;
		protected $tagdata = array();
		public function __construct(){
			$this->pdo = DB::getInstance();
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
							<input type="text" id="address" name="address" />
						</div>

						<div class="input-grup">
							<label for="payout">Payout</label>
							<input type="text" id="payout" name="payout" />
						</div>

						<div class="input-grup">
							<label for="timer">Timer</label>
							<input type="text" id="timer" name="timer" />
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
							<input type="text" id="address" name="address" value="'.$re["address"].'" />
						</div>

						<div class="input-grup">
							<label for="payout">Payout</label>
							<input type="text" id="payout" name="payout" value="'.$re["payout"].'" />
						</div>

						<div class="input-grup">
							<label for="timer">Timer</label>
							<input type="text" id="timer" name="timer" value="'.$re["timer"].'" />
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



	if( $_POST ) {

		$UserSettings = new UserSettings;
		$FaucetList   = new FaucetList( $Ref->ID );
		$Action = new FaucetAction;

		switch( $_POST["req"] ) {

			case 'editform':

				$DATA = $Action->editForm( $_POST["data"] );

			break;

			case 'delete':

				if( $Action->delete( $_POST["data"] ) ){
	    			$FaucetList->getAll();
					$DATA = $FaucetList->show();
				} else {
					$DATA = "patladı";
				}

			break;

			case 'edit_faucet':
				if( $Action->edit($_POST) ){
	    			$FaucetList->getAll();
					$DATA = $FaucetList->show();
				} else {
					$DATA = "patladı";
				}
			break;

			case 'add_faucet_form':

				$DATA = $Action->generateAddForm();

			break;

			case 'add_faucet':
				if( $Action->add($_POST) ){
	    			$FaucetList->getAll();
					$DATA = $FaucetList->show();
				} else {
					$DATA = "patladı";
				}
				
			break;

			case 'reload_list':

				if( $UserSettings->edit( $UC->getUserData()["id"], $_POST ) ) {
					$FaucetList->get( $UserSettings->newSettings(), "admin" );
				} else {
					$UserFilters = new Filter;
			    	$UserFilters->generate( $UC->getUserData()["id"] );
			    	$FaucetList->get( $UserFilters->userSettings(), "admin" );
				}

				$DATA = $FaucetList->show();
			break;
		}

		$output = json_encode( array(
			'stat' => $_POST,
			'data' => $DATA

		));

		echo $output;
		die;
	}