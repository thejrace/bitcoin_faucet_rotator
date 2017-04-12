<?php
    
	class FaucetList {

        private $pdo;
        protected $html = "", $isAdmin = false, $header = '', $countdowns = array(), $unfollowed = array(), $date;
        const TABLE = "faucet";

        public function __construct( ){
            $this->pdo = DB::getInstance();
            $this->date = date("Y-m-d") . " " . date("H:i:s"); 
        }

        public function get( $settings, $type = null ){

            // Admin tools' u eklemek icin
            if( $type == "admin" ) $this->isAdmin = true;

            // SQL cumlesini olusturmaya baslar
            $sql = ' SELECT * FROM ' . self::TABLE . ' WHERE ';
            $key = explode( "sort_", $settings["sortby"] );
            unset($key[0]);
            $orderby = " ORDER BY " . $key[1];

            $unfquery = $this->pdo->query( "SELECT * FROM unfollowed_faucets WHERE user_id = ?", array(Session::get("USER_ID")));
            foreach( $unfquery->results() as $res ){
                $this->unfollowed[] = $res["faucet_id"];
            }


            // Final sql
            $finsql = $sql . $settings["filter"] . " = 1 " . $orderby;

            $query = $this->pdo->query( $finsql );
            $this->generate( $query->results() );

        }

        protected function generate( $data ){

            if( count($data) > 0 ) {

                $adminTools = "";
                if( $this->isAdmin ){

                    $this->header = '


                        <div class="filter-menu admin-menu">
                            <div class="filter-header"><span>Admin Tools</span></div>
                                <div class="filter-menu-item clearfix">
                                <label class="filter-buton" id="add_faucet"> + Add Faucet </label>
                                <div class="search-tab">
                                    <input type="text" name="searchquery" placeholder="address, tag.." />
                                    <input type="submit" value="Search" />
                                </div>
                            </div>
                        </div>

                    ';

                    $adminTools = '
                        <div class="nfo-admin">
                            <b class="admin-tool edit" action="editform"></b></button>
                            <b class="admin-tool delete" action="delete"></b></button>
                        </div>
                    ';

                }

                $usercountdownstart = true;
                // Countdown user kontrolu 
                if( Session::exists("USER_ID") ) {   
                    $cd = $this->pdo->query(" SELECT * FROM countdowns WHERE user_id = ?", array( Session::get("USER_ID") ));
                    ( $cd->count() > 0 ) ? $this->countdowns = $cd->results() : $usercountdownstart = false;
                }
                
                
                // Taglar( details )
                $tags = array();
                $detquery = $this->pdo->query(" SELECT * FROM tag ");
                $tags = $detquery->results();

                $this->html = ' <li class="list-header">
                                    <div class="nfo-web">Website</div>
                                    
                                    <div class="nfo-payout">Payout</div>
                                    <div class="nfo-timer">Available</div>
                                    <div class="nfo-details">Details</div>
                                </li>';

                $defTimer = ( 4000 );
                foreach( $data as $faucet ){
                $followbtn = "Unfollow";

                    if( in_array($faucet["id"], $this->unfollowed ) ) $followbtn = "Follow"; 

                    $countdown = '
                        <div class="ready-button"><a class="faucet-go" href="leave.php?to='.$faucet["address"].'" target="_blank">Now Ready</a></div>
                        <div class="countdowns" start="false" data-time="'.$defTimer.'" data-id="'.$faucet["id"].'">'.$faucet["timer"].'</div>';

                    foreach( $this->countdowns as $item ){
                        if( $item["faucet_id"] == $faucet["id"] ){

                            $remaining = strtotime( $item["start_time"] ) + 18000 + 3600 - time();

                            $countdown = '
                                <div class="ready-button hide"><a class="faucet-go" href="leave.php?to='.$faucet["address"].'" target="_blank">Now Ready</a></div>
                                <div class="countdowns started" start="true" data-time="'.(($remaining)*100).'" data-id="'.$faucet["id"].'"></div>';
                        } 
                    }

                    $details = "";
                    foreach( $tags as $tag ) {
                        if( $faucet[$tag["short"]] ){
                            $details .= '<i class="fdetails '.strtolower($tag["short"]).'">'.$tag["name"].'</i>';
                        }
                    }


                    $this->html .= '

                        <li data="'.$faucet["id"].'">
                            <div class="nfo-web"><a class="faucet-go" href="" target="_blank">'.$faucet["name"].'</a></div>
                                           
                            <div class="nfo-payout">'.$faucet["payout"].'</div>
                            <div class="nfo-timer">
                                '.$countdown.'
                            </div>
                            <div class="nfo-details">
                                '.$followbtn.'
                                '.$details.'
                            </div>
                            '.$adminTools.'
                        </li>
                    ';

                }
            } else {
                $this->html .= '<center>No result. Use filter settings above.</center>';
            }
        }

        public function getAll(){

            $qu = $this->pdo->query(  ' SELECT * FROM ' . self::TABLE );

            $this->isAdmin = true;
            $this->generate( $qu->results() );


        }

        public function adminHeader(){
            return $this->header;
        }

        public function show(){
            return $this->html;
        }

    }


    /*
     V1
    public function get( $settings, $type = null ){

            if( $type == "admin" ) $this->isAdmin = true;

            if( isset($settings["orderby"])){
                $orderby = $settings["orderby"];
                unset($settings["orderby"]);
            } else {
                $orderby = " ORDER BY payout";
            }

            $sql = ' SELECT * FROM ' . self::TABLE . ' WHERE ';
            // user id ve id' yi sil arrayden
            unset($settings["id"]);
            unset($settings["user_id"]);


            $counter = 0;
            $finish  = 0;
            foreach( $settings as $set ){
                if( $set ) $finish++;
            }

            // Eğer en az bir tane seçili ayar varsa, koşullu fetch yap
            if( $finish > 0 ) {
                foreach( $settings as $key => $val ){
                    if( $val ){
                        $counter++;
                        if( $counter < $finish ) {
                            $sql .= ' ' . $key . ' = 1 ||';
                        } else {
                            $sql .= ' ' . $key . ' = 1 ';
                        }

                    }
                }
                $query = $this->pdo->query( $sql . $orderby );
                $_SESSION["hodo"] = $sql . $orderby;
                $this->generate( $query->results() );
            } else {
                $this->generate( array() );
            }



        }
    */
