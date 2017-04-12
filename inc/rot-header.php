<?php

    require_once "Init.php";

    // class FaucetURL {

    //     public function ref( $address ){
    //         $query = DB::getInstance()->query("SELECT * FROM ref_id WHERE type = ?", array($address));


    //     }

    // }

    class FaucetJSON {

        private $pdo;
        protected $faucets = array(), $unfollowed = array(), $reflist = array();

        public function __construct(){
            $this->pdo = DB::getInstance();
        }

        protected function getfaucets(){
            $user = $_SESSION["USER_ID"];

            // Gosterilmeyecek olanlarin ID lerini array'e al
            $unfquery = $this->pdo->query( " SELECT * FROM unfollowed_faucets WHERE user_id = ?", array( $user ));
            foreach( $unfquery->results() as $res ){
                $this->unfollowed[] = $res["faucet_id"];
            }

            // Tum faucetlerden, unfollow edilenleri ayikla
            $fquery = $this->pdo->query( "SELECT * FROM faucet" ); 
            foreach( $fquery->results() as $faucet ){
                if( !in_array( $faucet["id"], $this->unfollowed ) ) $this->faucets[] = $faucet;
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

    $FList = new FaucetJSON;
    

?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1">

        <link rel="stylesheet" type="text/css" href="../res/css/style.css" />
        <script type="text/javascript" src="http://ahsaphobby.net/resources/js/jquery.js"></script>
        <script type="text/javascript" src="../res/js/main.js"></script>
        <title>Bit - bitcoin_BASE95</title>

        <style>
            


        </style>
            
    </head>
    <body>

        <div class="rotator-wrapper">
            <div class="rotator">
                <ul class="main-cols clearfix">
                    <li class="rotfaucetdata fleft">
                        <div class="faucet-info">
                            <ul class="faucetheader clearfix">
                                <li id="faucetname">Hederoycoin.com</li>
                                <!-- <li><button type="button" class="likebuttons" id="like"></button><span id="likecount">(25)</span></li>
                                <li><button type="button" class="likebuttons" id="dislike"></button><span id="dislikecount">(5)</span></li> -->
                                <li class="viewcomments"><a href="">view comments</a></li>
                            </ul>
                        </div>
                        <div class="faucet-info" id="faucetdataul">
                            <ul class="faucetdata clearfix">
                                <li class="fpayout">1600 Satoshi</li>
                                <li><i class="fdetails fb">Faucet Box</i></li>
                                <li><i class="fdetails fc">FunCaptcha</i></li>
                            </ul>
                        </div>
                    </li>
                    <li class="rotbuttons fright">
                        <ul class="clearfix">
                            <li><button type="button" class="madikbtn" id="prevfaucet" title="Previos Faucet"><span>Previous</span></button></li>
                            <li><button type="button" class="madikbtn" id="follow" title="Follow this faucet."><i class="follow"></i></button></li>
                            <li><button type="button" class="madikbtn" id="addcomment" title="Add a Comment"><i class="comment"></i></button></li>
                            <li><button type="button" class="madikbtn" id="reload" title="Reload"><i class="reload"></i></button></li>
                            <li><button type="button" class="madikbtn" id="sendfeedback" title="Send a feedback for this faucet."><i class="feedback"></i></button></li>
                            <li><button type="button" class="madikbtn" id="nextfaucet" title="Next Faucet"><span>Next</span></button></li>
                        </ul>
                        <div class="maininfo"> Browsing faucet number <span id="currentfaucetno">5</span> out of total <span id="totalfaucetno">122</span> faucets.</div>
                    </li>
                </ul>
            </div>
        </div>

        


        <!-- Nav scriptleri burda yaz -->
        <script type="text/javascript">


        var Header = {
            faucet: function(d) { Com.$A("faucetname").innerHTML = d },
            currentfaucet: function(d) { Com.$A("currentfaucetno").innerHTML = d },
            totalfaucet: function(d) { Com.$A("totalfaucetno").innerHTML = d },
            faucetdata: function(d) { Com.$A("faucetdataul").innerHTML = d },
            follow: function() { return Com.$A("follow") },
            addcomment: function() { return Com.$A("addcomment") },
            reload: function() { return Com.$A("reload")},
            sendfeedback: function() { return Com.$A("sendfeedback")},
            prevfaucet: function() { return Com.$A("prevfaucet") },
            nextfaucet: function() { return Com.$A("nextfaucet") }
        }

        function updateHeader( no ){
            var f = faucets[no];
            Header.faucet(f.name);
            Header.currentfaucet(no);
            Header.totalfaucet(base.totalfaucet);
            Header.faucetdata(f.faucetdata);
            if( f.followed == 1 ) {
                addClass( Header.follow(), "selected" );
                Header.follow().title = "Unfollow this faucet."; 
            } else {
                removeClass( Header.follow(), "selected" );
                Header.follow().title = "Follow this faucet.";
            }

            
            parent.document.getElementById("rot-site").src = f.url;
        }



        var base = {
            'user'        : 2,
            'totalfaucet' : 121
        }

        var faucets = {
            <?php echo $FList->generate(); ?>
        }

            $(document).ready(function(){

                var start = 1;
                updateHeader(1);

                $('#prevfaucet').click(function(){
                    if( start > 1 ) start -= 1;
                    updateHeader(start);
                    console.log( faucets[start] );
                });

                $('#nextfaucet').click(function(){
                    
                    if( start < base.totalfaucet ) start += 1;
                    updateHeader(start);
                    console.log( faucets[start] );
                });

            });
        </script>
    </body>
</html>
