<?php

  require_once "inc/Init.php";
    class FaucetJSON {

        private $pdo;
        protected $faucets = array(), $unfollowed = array(), $reflist = array(), $visited = array();

        public function __construct(){
            $this->pdo = DB::getInstance();
        }

        protected function getfaucets(){
            $user = Session::get("USER_ID");

            // Gosterilmeyecek olanlarin ID lerini array'e al
            $unfquery = $this->pdo->query( " SELECT * FROM unfollowed_faucets WHERE user_id = ?", array( $user ));
            foreach( $unfquery->results() as $res ){
                $this->unfollowed[] = $res["faucet_id"];
            }

            // $cdquery = $this->pdo->query( "SELECT * FROM countdowns WHERE user_id = ?", array($user));
            // foreach( $cdquery->results() as $cd ){
            //     $this->visited[] = $cd["faucet_id"];
            // }

            // Tum faucetlerden, unfollow edilenleri ve countdown da olanlari ayikla
            $fquery = $this->pdo->query( "SELECT * FROM faucet" ); 
            foreach( $fquery->results() as $faucet ){
                if( !in_array( $faucet["id"], $this->unfollowed ) /* && ( !in_array( $faucet["id"], $this->visited ) )*/ ) $this->faucets[] = $faucet;
            }

            $refquery = $this->pdo->query( "SELECT * FROM ref_id" );
            foreach( $refquery->results() as $ref ){
                $this->reflist[$ref["id"]] = $ref["no"];
            }

            $walletquery = $this->pdo->query( "SELECT * FROM wallet ");
            foreach( $walletquery->results() as $wallet ){
                $this->reflist[$wallet["name"]] = $wallet["code"];
            }
        }

        public function generate(){
            $this->getfaucets();
            $json = "";
            
            $key = 1;
            $limit = count($this->faucets);
            $comma = ",";

            $detquery = $this->pdo->query(" SELECT * FROM tag ");
            $tags = $detquery->results();

            foreach( $this->faucets as $faucet ){

                // if( $key == 10 ) continue;
                
                $faucetdata = "<li class='fpayout'>".$faucet["payout"]." Satoshi</li>";
                foreach( $tags as $tag ) {
                    if( $faucet[$tag["short"]] ){
                        $faucetdata .= "<li><i class='fdetails ".strtolower($tag["short"])."'>".$tag["name"]."</i></li>";
                    }
                }

                if( $key == $limit ) $comma = "";
                $json .= 
                $key . ': {
                    "id"         : '.$faucet["id"].',
                    "name"       : "'.$faucet["name"].'",
                    "url"        : "'.$faucet["address"].$this->reflist[$faucet["ref_type"]].'",
                    "payout"     : '.$faucet["payout"].',
                    "liked"      : 1,
                    "followed"   : 1,
                    "likes"      : 192,
                    "dislikes"   : 6,
                    "faucetdata" : "<ul class=\'faucetdata clearfix\'>'.$faucetdata.'</ul>"
                }'.$comma.' ';

                $key++;

            }

            return $json;
        }

    }

    $StartFaucetNo = 1;
    if( isset( $_GET["fno"] ) ){
        $StartFaucetNo = $_GET["fno"];
    }


    $FList = new FaucetJSON;
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1">

        <link rel="stylesheet" type="text/css" href="res/css/style.css" />
        <script type="text/javascript" src="http://ahsaphobby.net/resources/js/jquery.js"></script>
        <script type="text/javascript" src="res/js/main.js"></script>
        <title>Bit - bitcoin_BASE95</title>
        <style>
          
        </style>
    </head>
    <body style="overflow-y:hidden">
    <div id="popup-overlay"></div>
    <div id="popup">


    </div>

        <div class="rotator-wrapper">
            <div class="rotator">
                <ul class="main-cols clearfix">
                    <li class="rotfaucetdata fleft">
                        <div class="faucet-info">
                            <ul class="faucetheader clearfix">
                                <li id="faucetname"></li>
                                <!-- <li><button type="button" class="likebuttons" id="like"></button><span id="likecount">(25)</span></li>
                                <li><button type="button" class="likebuttons" id="dislike"></button><span id="dislikecount">(5)</span></li> -->
                                <li class="viewcomments"><a href="">view comments</a></li>
                            </ul>
                        </div>
                        <div class="faucet-info" id="faucetdataul">
                            <ul class="faucetdata clearfix">
                            <!-- <li class="fpayout">1600 Satoshi</li>
                                <li><i class="fdetails fb">Faucet Box</i></li>
                                <li><i class="fdetails fc">FunCaptcha</i></li> -->
                            </ul>
                        </div>
                    </li>
                    <li class="rotbuttons fright">
                        <ul class="clearfix">
                            <li><button type="button" class="madikbtn" id="prevfaucet" title="Previos Faucet"><span>Previous</span></button></li>
                            
                            <?php 
                            if( $User->isLoggedin() ) { // Uye olmayanlar bunlari yapamaz ?>
                                
                                <li><button type="button" class="madikbtn selected" id="follow" title="Follow this faucet."><i class="follow"></i></button></li>
                                <li><button type="button" class="madikbtn" id="addcomment" title="Add a Comment"><i class="comment"></i></button></li>
                                <li><button type="button" class="madikbtn" id="copywalletadress" title="Copy your wallet address to the clipboard."><i class="wallet"></i></button></li>
                                
                            <?php } else { ?>
                                <li><button type="button" class="madikbtn" title="Login"><span><a href="index.php">Login</a></span></button></li>

                            <?php  } ?>
                            <li><button type="button" class="madikbtn" id="reload" title="Reload"><i class="reload"></i></button></li>
                            <li><button type="button" class="madikbtn" id="sendfeedback" title="Send a feedback for this faucet."><i class="feedback"></i></button></li>
                            <li><button type="button" class="madikbtn" id="nextfaucet" title="Next Faucet"><span>Next</span></button></li>
                        </ul>
                        <div class="maininfo"> Browsing faucet number <span id="currentfaucetno">5</span> out of total <span id="totalfaucetno">122</span> faucets.</div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="rotator-border-bot"></div>
       
        <iframe id="rot-site" src="" ></iframe>

         <!-- Nav scriptleri burda yaz -->
        <script type="text/javascript">

            var MSG = {
                "TR" : {
                    "FAUCET_COMMENT_ADDED" : "Yorumunuz eklendi. Teşekkürler.",
                    "FAUCET_FEEDBACK_SENT" : "Bildiriminiz için teşekkürler.",
                    "FAUCET_FEEDBACK_ERROR": "Lütfen bildirim tipini seçiniz.",
                    "FAUCET_WRONG_ERROR" : "Birşeyler ters gitti. Lütfen tekrar deneyin"
                },
                "EN" : {
                    "FAUCET_COMMENT_ADDED" : "Your comment has been added. Thank you.",
                    "FAUCET_FEEDBACK_SENT" : "Your feedback has been sent. Thank you.",
                    "FAUCET_FEEDBACK_ERROR": "Choose an option.",
                    "FAUCET_WRONG_ERROR" : "Something went wrong. Try again."
                }
            };

            var LANG = "TR";


            function rot(){
                this.iframe           = Com.$A("rot-site");
                this.currentfaucetno  = parseInt(Com.$A("currentfaucetno").innerHTML);
                this.faucetname       = Com.$A("faucetname");
                this.currentfaucet    = Com.$A("currentfaucetno");
                this.totalfaucet      = Com.$A("totalfaucetno");
                this.faucetdata       = Com.$A("faucetdataul");
                this.btnfollow        = Com.$A("follow");
                this.btnaddcomment    = Com.$A("addcomment");
                this.btnreload        = Com.$A("reload");
                this.btnsendfeedback  = Com.$A("sendfeedback");
                this.btnprevfaucet    = Com.$A("prevfaucet");
                this.btnnextfaucet    = Com.$A("nextfaucet");
            }
            rot.prototype.update = function( no, t ){
                var /*fstart,*/ f;
                    switch(t){
                        // prev
                        case 'PREVIOUS':
                            ( no > 1 && faucets[no-1] != undefined ) ? start = no-1 : start = no;
                        break;
                        // next
                        case 'NEXT':
                            ( no < base.totalfaucet && faucets[no+1] != undefined ) ? start = no+1 : start = no;
                        break;
                        // delete ve ilk acilista update
                        default:
                            start = no;
                        break;
                    }

                    f = faucets[start];
                    // console.log( f );
                    base.totalfaucet = Object.size(faucets);

                    this.setfaucetname(f.name);
                    this.setcurrentfaucet(start);
                    this.settotalfaucet(base.totalfaucet);
                    this.setfaucetdata(f.faucetdata);
                    // this.iframe().src = f.url;
            };
            rot.prototype.unfollow = function(){
                 // databaseden unfollow a ekle
                 rq( "fid="+faucets[this.getcurrentfaucet()].id+"&action=unfollow&req=rotatoractions", function(r){
                    if( r.data ){
                        // listeden sil
                        Rotator.deletefaucet( Rotator.getcurrentfaucet() );         
                        // siradaki faucet e gec
                        Rotator.update( Rotator.getcurrentfaucet(), false );
                    }
                });
            };
            rot.prototype.addcomment = function(c){
                rq( c + "&req=rotatoractions", function(r){
                    if( r.data ){
                        PopUp.on(MSG[LANG]["FAUCET_COMMENT_ADDED"]);
                    } else {
                        Com.$A("form-notf").innerHTML = MSG[LANG]["FAUCET_WRONG_ERROR"];
                    }
                });
            };
            rot.prototype.addfeedback = function(c){
                if( Com.$A("ftype").getAttribute("value") != 0 ){
                    rq( c + "&req=rotatoractions", function(r){
                        if( r.data ){
                            PopUp.on(MSG[LANG]["FAUCET_FEEDBACK_SENT"]);
                        } else {
                            Com.$A("form-notf").innerHTML = MSG[LANG]["FAUCET_WRONG_ERROR"];
                        }     
                    });
                } else {
                    Com.$A("form-notf").innerHTML = MSG[LANG]["FAUCET_FEEDBACK_ERROR"];
                }
            };
            rot.prototype.deletefaucet = function(no){
                delete faucets[ no ];
                // silinen faucetten sonra gelenlerin keylerini bir azalt
                // bosluk kalmasin diye
                // örn 1a 2a 3a(sil) 4a 5a ----> 1a 2a 3b(4a) 4b(5a)
                for( var i = no; i < Object.size(faucets); i++ ){
                    faucets[i] = faucets[i+1];
                }
                // en sonda kalan fazla faucet'i de sil
                delete faucets[ Object.size(faucets) ];
                // Sildikten sonra next-prev aksiyonlarinda hesaplama icin
                base.totalfaucet = Object.size(faucets);
            };
            rot.prototype.getcurrentfaucet = function(){
                return this.currentfaucetno;
            };
            rot.prototype.setcurrentfaucet = function(d){
                this.currentfaucet.innerHTML = d;
            };
            rot.prototype.settotalfaucet = function(d){
                this.totalfaucet.innerHTML = d;
            };
            rot.prototype.setfaucetdata = function(d){
                this.faucetdata.innerHTML = d;
            };
            rot.prototype.setfaucetname = function(d){
                this.faucetname.innerHTML = d;
            };
            rot.prototype.getfaucet = function(){
                for ( var i = 1; i < Object.size(faucets); i++ ){
                    if( faucets[i].id == start ) {
                        start = i;
                        break;
                    }
                }
            };

            var Rotator = new rot();

            BitcoinDatabase.reqTo = "inc/Ajax.php";
           
            var createForm = {
                comment: function(){
                    return '<div class="popup-header"><span>+ Add a comment for “'+faucets[Rotator.getcurrentfaucet()].name+'”</span></div><div class="form-container"> <span id="form-notf"></span> <form action="" method="post" id="f_addcomment"> <input type="hidden" name="action" value="add_comment" /><input type="hidden" name="fid" value="'+faucets[Rotator.getcurrentfaucet()].id+'" /> <div class="input-grup"> <label for="usercomment">Your comment ( max 200 char )</label> <textarea name="usercomment" id="usercomment" placeholder="write it down..."></textarea></div><div class="input-grup"><input type="submit" class="btn-submit" value="Submit" /></div></form></div> ';
                },
                feedback: function(){
                    return '<div class="popup-header"><span>+ Send a feedback for “'+faucets[Rotator.getcurrentfaucet()].name+'”</span></div><div class="form-container"><span id="form-notf"></span><form action="" method="post" id="f_addfeedback"><input type="hidden" name="action" value="add_feedback" /><input type="hidden" id="ftype" name="ftype" value="0" /><input type="hidden" name="fid" value="'+faucets[Rotator.getcurrentfaucet()].id+'" /><div class="input-grup"><label>Choose your feedback type.</label></div><ul id="feedback-type-cont"><li><span class="feedbackoption" id="infochange">Payout / Timer information needs to change.</span></li><li><span class="feedbackoption" id="notworking">Not working / not paying.</span></li></ul><div class="input-grup"><input type="submit" class="btn-submit" value="Submit" /></div></form></div>';
                }
            };

            var base = {
                'user'        : 2,
                'totalfaucet' : Object.size(faucets)
            };

            var faucets = {
                <?php echo $FList->generate(); ?>
            };

            console.log( " Toplam faucet sayisi : " + Object.size(faucets) );

            var start = <?php echo $StartFaucetNo ?>;
    

            (function(){
                console.log("init");
                
                Rotator.getfaucet();
                console.log(start);

                Rotator.update( start, false );

                $('#follow').click(function(){
                    Rotator.unfollow();
                });

                $('#addcomment').click(function(){
                    PopUp.on( createForm.comment() );
                });

                $('#sendfeedback').click(function(){
                    PopUp.on( createForm.feedback() );
                });

                $('#prevfaucet').click(function(){
                    Rotator.update(start, "PREVIOUS")
                });

                $('#nextfaucet').click(function(){
                    Rotator.update(start, "NEXT");
                });

                $(document).on("click", ".feedbackoption", function(){
                    var el = $(this);
                    Com.$A("ftype").setAttribute("value", el.attr("id") );
                    $('.feedbackoption').removeClass("selected");
                    el.addClass("selected");
                });

                $(document).on("submit", "#f_addfeedback", function(e){
                    Rotator.addfeedback($(this).serialize());
                    e.preventDefault();
                });

                $(document).on("submit", "#f_addcomment", function(e){
                    Rotator.addcomment($(this).serialize());
                    //console.log($(this).serialize());
                    e.preventDefault();
                });

            })();
        </script>

    

    

    
    </body>
</html>
