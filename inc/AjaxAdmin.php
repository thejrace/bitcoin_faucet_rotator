<?php
	
	require_once "Init.php";

	if( $_POST ) {

		$FaucetList   = new FaucetList;
		$Action       = new FaucetAction;

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

				$FaucetList->get( $_POST, "admin" );

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