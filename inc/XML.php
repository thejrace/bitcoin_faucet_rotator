<?php
	require_once "Init.php";
	$xml = simplexml_load_file("schema.xml");

	$ref_type_wallet = false;
	$ok = true;

	$wquery = DB::getInstance()->query(" SELECT * FROM wallet ");
	$wallets = $wquery->results();
	foreach($xml->children() as $array  ){
		$ref_type_wallet = false;
		$ok = true;
		$f_address = "";
		$f_name = "";
		$url = (string)$array->URL;

		echo $url .  " Eklenmeye baslandi...   ";

		if( strpos($url, '?ref=') !== false  ){
			$xp = explode( '?ref=', $url);
			foreach( $wallets as $wallet ){
				if( $xp[1] == $wallet["code"] ){
					$wallet_type = $wallet["name"];
					$ref_type_wallet = true;
				}
			}

			if( !$ref_type_wallet ){
				DB::getInstance()->insert("ref_id", array(
					"no"   => $xp[1],
					"type" => $xp[0]
				));
				$wallet_type = DB::getInstance()->lastInsertedId();
			}

			$f_name = substr($xp[0], 7);
			$f_address = $xp[0] . "?ref=";

		} else if( strpos($url, "?r=") !== false ) {
			$xp = explode( '?r=', $url);
			foreach( $wallets as $wallet ){
				if( $xp[1] == $wallet["code"] ){
					$wallet_type = $wallet["name"];
					$ref_type_wallet = true;
				}
			}

			if( !$ref_type_wallet ){
				DB::getInstance()->insert("ref_id", array(
					"no"   => $xp[1],
					"type" => $xp[0]
				));
				$wallet_type = DB::getInstance()->lastInsertedId();
			}

			$f_name = substr($xp[0], 7);
			$f_address = $xp[0] . "?r=";

		} else if( strpos($url, "?id=")  !== false )  {
			$xp = explode( '?id=', $url);
			foreach( $wallets as $wallet ){
				if( $xp[1] == $wallet["code"] ){
					$ref_type_wallet = true;
					$wallet_type = $wallet["name"];
				}
			}

			if( !$ref_type_wallet ){
				DB::getInstance()->insert("ref_id", array(
					"no"   => $xp[1],
					"type" => $xp[0]
				));
				$wallet_type = DB::getInstance()->lastInsertedId();
			}

			$f_name = substr($xp[0], 7);
			$f_address = $xp[0] . "?id=";

		} else {
			// Sitenin ref ozelligi wallet ile degil uyelikleyse ref_id tablosuna ekleme
			$ok = false;
			echo $url . " eklenemedi <br>";
		}

		$tags = array();

		$d = 0;
		$fb = 0;
		switch( (string)$array->Payment ){
			case 'FaucetBOX':
				$fb = 1;
			break;

			case 'Direct':
				$d = 1;
			break;
		}

		if( $ok ){

			// echo "ok";
			DB::getInstance()->insert("faucet", array(
					"address"   => $f_address,
					"name"      => $f_name,
					"timer"     => (string)$array->Timer,
					"payout"    => (int)$array->Payout,
					"ref_type"  => $wallet_type,
					"XP" 				=> 0,
					"EP"				=> 0,
					"FB" 				=> $fb,
					"DIRECT"		=> $d,
					"PT"				=> 0,
					"SM" 				=> 0,
					"RC"				=> 0,
					"FC" 				=> 0,
					"RK" 				=> 0,
					"AH" 				=> 0,
					"OTHER"     => 0,
					"VISITED"   => 0,
					"JUNK"      => 0,
					"NOT_RECOM" => 0,
					"DRY"       => 0
			));
			echo $f_address . " eklendi <br>";
		}



		// $vals[$c]["FaucetName"] = (string)$array->FaucetName;
		// $vals[$c]["MinPay"]     = (string)$array->MinPay;
		// $vals[$c]["MaxPay"]     = (string)$array->MaxPay;
		// $vals[$c]["Payout"]     = (string)$array->Payout;
		// $vals[$c]["Payment"]    = (string)$array->Payment;
		// $vals[$c]["Timer"]      = (string)$array->Timer;


	}
	// echo '<pre>';
	// print_r($vals);
