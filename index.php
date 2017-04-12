<?php
    
    require_once "inc/Init.php";

    $defSettings = array( "filter" => "fb", "sortby" => "sort_payout" );
    $FList = new FaucetList ( );
    $FList->get( $defSettings );

    $USER = "guest";

    $ADMINTOOLS = "";
    $FAUCETLIST = $FList->show();

    if( $User->isLoggedin() ){
        
    } else if ( $User->isGuest() ){
        
    }

    include "inc/Header.php";
    include "inc/temp.Filters.php";
    include "inc/temp.List.php";

    
?>

    
    <script type="text/javascript">

        BitcoinDatabase.reqTo = "inc/Ajax.php";
        function countdownCheck(){
            $('.countdowns').each(function(){
                var id = $(this).data("id"),
                    t  = $(this).data("time");

                var Cdown = {
                    'fid'      : id,
                    'main'     : $("[data="+id+"]"),
                    'maincls'  : "visited",
                    'cdelem'   : $(this),
                    'cdelemcls': 'started',
                    'endtime'  : t
                };

                
                if( $(this).attr("start") == "true" ) {
                    Cdown.main.addClass(Cdown.maincls);
                    initCountdown( Cdown );
                }
                

            });
        }

        $(document).ready(function(){

            $(document).on("submit", "#loginform", function(e){
                // console.log($(this).serialize());
                console.log( $(this).serialize() + "&req=login" );
                rq( $(this).serialize() + "&req=login", function(r){
                    console.log(r);
                    if( r.data == true ) {
                        window.location.reload(true);
                    } else {
                        $('.form-notf').html(r.data);
                    }
                    
                });
                
                e.preventDefault();
            });

            $(document).on("click", ".faucet-go", function(e){
                var id = $(this).parent().parent().attr("data") || $(this).parent().parent().parent().attr("data"),
                    cdelem = $("[data-id=" + id + "]");

                //if( cdelem.attr("start") == "false" ){
                
                    var Cdown = {
                        'fid'      : id,
                        'main'     : $("[data="+id+"]"),
                        'maincls'  : "visited",
                        'cdelem'   : cdelem,
                        'cdelemcls': 'started',
                        'endtime'  : cdelem.data("time")
                    };
                    Cdown.cdelem.attr("start", true);
                    Cdown.main.addClass(Cdown.maincls);

                    rq( "fid=" + id + "&req=leave", function(r){
                        console.log(r);
                    });

                    initCountdown( Cdown );
                    window.location.href = "rotator.php?fno=" + id;

                //}
                e.preventDefault();
            });

            
            countdownCheck();

        });

        function calcRemaining( endtime ){
            var hrs = parseInt( endtime / 360000 ),
                min = parseInt(( endtime / 6000 ) - ( hrs * 60 )),
                sec = parseInt(( endtime / 100 ) - ( hrs * 60 * 60 ) - ( min * 60 ));
            return {
                'total' : endtime,
                'sec'   : sec,
                'min'   : min,
                'hrs'   : hrs
            };
        }

        function initCountdown( d ){
            
            function updateCountdown(){
                d.endtime -= 100;
                var t = calcRemaining( d.endtime ),
                    btn = d.cdelem.parent().find(".ready-button");
                btn.hide();
                if( !d.cdelem.hasClass(d.cdelemcls)) d.cdelem.addClass(d.cdelemcls);
                d.cdelem.html( ( '0' + t.hrs ).slice(-2) + ":" + ( '0' + t.min ).slice(-2) + ":" + ( '0' + t.sec ).slice(-2) );
                if( d.endtime <= 0 ) {
                    rq( "fid=" + d.fid + "&req=cdfinish", function(r){
                        console.log(r);
                    });
                    btn.show();
                    d.cdelem.removeClass(d.cdelemcls);
                    d.cdelem.attr("start", false);
                    d.main.removeClass(d.maincls);
                    clearInterval(timeInterval);
                }
            }

            updateCountdown();
            var timeInterval = setInterval(updateCountdown,1000);
        
        }

        







       
    </script>

<?php

    include "inc/Footer.php";