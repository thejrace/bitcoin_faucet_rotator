<?php
	
	require_once "Init.php";

	// Tum taglari cekip karislastirmayi birden fazla yapiyorum BAK BUNA
	// INPUT POST ESCAPE

	if( $_POST ){

		$UserSettings = new UserSettings;
		$FaucetList   = new FaucetList;
		$Action 	  = new FaucetAction;
		$NOTF = "";
		switch( $_POST["req"] ) {

			case 'reload_list':

				$FaucetList->get( $_POST );
				$DATA = $FaucetList->show();
				
			break;

			case 'leave':
			
				$DATA  = $Action->startCountdown( $_POST["fid"] );

			break;

			case 'cdfinish':
				$DATA = DB::getInstance()->query("DELETE FROM countdowns WHERE user_id = ? && faucet_id = ?", array(Session::get("USER_ID"), $_POST["fid"]));
			break;

			case 'login':

				$User->action( $_POST );
				if( $User->isLoggedin() ){
					$USER_MAIL = $User->getEmail();
					$DATA = true;
				} else {
					$DATA = $User->getErrors();
				}

			break;

			case 'rotatoractions':

				switch( $_POST["action"] ){
					case 'unfollow':
						$Action->unfollow( $_POST["fid"] ) ?
							$DATA = true:
							$DATA = false;		
					break;

					case 'add_comment':
						$DATA = $Action->addcomment( $_POST );
					break;

					case 'add_feedback':
						$DATA = $Action->addfeedback( $_POST );
					break;
				}

				
			break;

			case 'usersettings':
				switch( $_POST["action"] ){

					case 'account':
						$DATA = $User->editAccount($_POST);
						( $DATA ) ? $NOTF = "Your changes has been saved successfully" : $NOTF = $User->getErrors();
					break;

					case 'password':
						$DATA = $User->editPass($_POST);
						( $DATA ) ? $NOTF = "Your password has changed successfully" : $NOTF = $User->getErrors();
					break;

					case 'browsing':
						$DATA = true;
					break;
				}

			break;

		}




		$output = json_encode( array(
			'stat' => $_POST,
			'data' => $DATA,
			'notf' => $NOTF

		));

		echo $output;
		die;

	}