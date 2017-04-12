<?php

	require_once "inc/Init.php";

	$User->logout();
	header( "Location: index.php" );