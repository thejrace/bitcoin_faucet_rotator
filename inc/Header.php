<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1">

        <link rel="stylesheet" type="text/css" href="res/css/style.css" />
        <script type="text/javascript" src="http://ahsaphobby.net/resources/js/jquery.js"></script>
        <script type="text/javascript" src="res/js/jquery.timer.js"></script>
        <script type="text/javascript" src="res/js/main.js"></script>
        <title>Bit - bitcoin_BASE95</title>
    </head>
    <body>

    <div id="popup-overlay"></div>
    <div id="popup">


    </div>
    <div id="ajax-notf"></div>

    <div class="header clearfix">
        <div class="logo">
            <a href="">Faucet List</a>
            <div class="date"></div>
            <div class="social"></div>
        </div>

    </div>

    <div class="wrapper">

        <div class="filter-menu full-width">
            <div class="filter-header"><span>User</span></div>
            <div class="header-user-nav">

            <?php if( !$User->isLoggedin() ) { ?>
                

                <div class="not-loggedin">
                    <div class="login-information">Quickly login or create an account to start earning. An account is required for full experience.</div>
                    <div class="form-container">
                        <div class="form-notf"></div>

                        <form action="" method="post" id="loginform">
                            <div class="input-grup">
                                <input type="email" id="email" name="email" placeholder="email address.." />
                            </div>

                            <div class="input-grup">
                                <input type="password" id="password" name="password" placeholder="password.." />
                            </div>

                            <div class="input-grup">
                                <input type="submit" class="btn-submit" value="Start" />
                            </div>
                        </form>
                    </div>
                </div>

           <?php } else { ?>
                
                <div class="user-start">welcome <?php echo $USER_MAIL ?> </div>
                <div class="user-navi">
                    <ul class="clearfix">
                        <li><a href="settings.php">[ SETTINGS ]</a></li>
                        <li><a href="">[ START BROWSING ]</a></li>
                        <li><a href="">[ FEEDBACK ]</a></li>
                        <li><a href="logout.php">[ LOGOUT ]</a></li>   
                    </ul>
                </div>
            

            <?php  } ?>
            </div>
        </div>