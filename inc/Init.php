<?php
    
    require_once "Session.php";
    $Session = new Session;
    $Session->start();
    
    require_once "DB.php";
    require_once "Cookie.php";
    require_once "Filters.php";
    require_once "FaucetList.php";
    require_once "FaucetAction.php";
    require_once "User.php";
    require_once "Page.php";
    require_once "UserFilterSettings.php";

    $User = new User;

    // Cookie kontrol
    $User->remember();

    $USER_MAIL = "";
    // Hatirlama basariliysa kullaniciyi login et
    // Degilse guest baslat
    if( $User->isLoggedin() ){
        $USER_MAIL = $User->getEmail();
    }
    // Kullanici izinlerini ayarla
    $User->setPerms();

