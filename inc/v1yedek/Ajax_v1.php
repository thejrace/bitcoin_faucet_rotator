<?php
	
	require_once "Init.php";


	// Tum taglari cekip karislastirmayi birden fazla yapiyorum BAK BUNA
	// INPUT POST ESCAPE

	if( $_POST ){

		$UserSettings = new UserSettings;
		$FaucetList   = new FaucetList( $Ref->ID );

		switch( $_POST["req"] ) {

			case 'reload_list':

				if( $UserSettings->edit( $UC->getUserData()["id"], $_POST ) ) {
					$FaucetList->get( $UserSettings->newSettings() );
				} else {
					$UserFilters = new Filter;
			    	$UserFilters->generate( $UC->getUserData()["id"] );
			    	$FaucetList->get( $UserFilters->userSettings() );
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