<?php

	require_once "inc/Init.php";

	if( !Page::userHasPermission( "PI_SETTINGS", $User->getPerms() ) ) die;

    include "inc/Header.php";


?>

	<div class="filter-menu full-width">
	    <div class="filter-header"><span>Customize your settings.</span></div>
	    <div class="header-user-nav">
	    <ul class="tab-btn-cont clearfix" tabdiv=".settings-tab">
	    	<li class="tab-btn selected">Account</li>
	    	<li class="tab-btn">Security</li>
	    	<li class="tab-btn">Browsing</li>
	    	<li class="tab-btn">My Stats</li>
	    </ul>
	    <div class="settings-tab">
	    	<div class="form-container">
	    		<div id="form-notf"></div>
	    		<form action="" method="post" id="f_account">
	    		<input type="hidden" name="action" value="account" />
	    		<div class="form-seperator">Email and wallet settings</div>
	    		<div class="input-grup">
	    			<label for="email">Your E-Mail</label>
	    			<input type="text" id="email" name="email" value="<?php echo Session::get("USER_MAIL") ?>" />
	    		</div>

	    		<div class="input-grup">
	    			<label for="wallet">Wallet address</label>
	    			<input type="text" id="wallet" name="wallet" value="<?php echo $User->getWalletAddress() ?>" />
	    		</div>

	    		<div class="input-grup">
		    			<label for="confirmpass">Enter your password to confirm the changes</label>
		    			<input type="password" id="confirmpass" name="confirmpass" placeholder="Your password"/>
		    		</div>

	    		<div class="input-grup">
	    			<input type="submit" value="Save" class="btn-submit" />
	    		</div>

	    		</form>
	    	</div>
	    </div>
	    <div class="settings-tab">
	    	<div class="form-container">
	    		<div id="form-notf"></div>
	    		<form action="" method="post" id="f_security">
	    			<input type="hidden" name="action" value="password" />
	    			<div class="form-seperator">Password settings</div>
	    			<div class="input-grup">
		    			<label for="oldpass">Old password</label>
		    			<input type="password" id="oldpass" name="oldpass" />
		    		</div>
		    		<div class="input-grup">
		    			<label for="newpass">New password</label>
		    			<input type="password" id="newpass" name="newpass" />
		    		</div>
		    		<div class="input-grup">
		    			<label for="newpass_r">New password repeat</label>
		    			<input type="password" id="newpass_r" name="newpass_r" />
		    		</div>

		    		<div class="input-grup">
		    			<input type="submit" value="Save" class="btn-submit" />
		    		</div>
	    		</form>
	    	</div>

	    </div>
	    <div class="settings-tab">
	    	<div class="form-container">
	    		<div id="form-notf"></div>
	    		<form action="" method="post" id="f_browsing">
	    			<input type="hidden" name="action" value="browsing" />
	    			<div class="form-seperator">Rotator and additional settings</div>
	    			

		    		<div class="input-grup">
		    			<input type="submit" value="Save" class="btn-submit" />
		    		</div>
	    		</form>
	    	</div>

	    </div>
	    <div class="settings-tab">
	    	<div class="form-seperator">Here is your stats since your first visit</div>
	    </div>

	    </div>
	</div>

	<script type="text/javascript">

		BitcoinDatabase.reqTo = "inc/Ajax.php";
		(function(){

			$(document).on("submit", '#f_account', function(e){

				rq( $(this).serialize() + "&req=usersettings", function(r){
					ajaxNotf(r.data, r.notf);
					Com.$A('confirmpass').value = "";
				});

				e.preventDefault();
			});


			$(document).on("submit", '#f_security', function(e){

				rq( $(this).serialize() + "&req=usersettings", function(r){
					ajaxNotf(r.data, r.notf);
					Com.$A('f_security').reset();
				});

				e.preventDefault();
			});


		})();

	</script>