<?php

		require_once "inc/Init.php";

		if( !isset($_SESSION["adm_username"]) ){
			header( "Location: adminlogin.php" );
		}

    $FList = new FaucetList(  );
    $FList->getAll();

    $USER       = $_SESSION["adm_username"];
    $ADMINTOOLS = $FList->adminHeader();
    $FAUCETLIST = $FList->show();

    include "inc/Header.php";

    include "inc/temp.Filters.php";
    include "inc/temp.List.php";

?>


    <script type="text/javascript">

        BitcoinDatabase.reqTo = "inc/AjaxAdmin.php";

        $(document).ready(function(){

            $(document).on("click", ".admin-tool", function(){
                var act = this.getAttribute("action"),
                    send = "data=" + this.parentNode.parentNode.getAttribute("data") + "&req=" + act;
                console.log(send);

                if( act == "delete" ){

                    var c = confirm(" Delete are yu şör beybe ?");
                    if(c){
                        rq( send, function(r){
                            reloadList(r.data);
                        });
                    }

                } else if( act == "editform" ){
                    rq( send, function(r){
                        PopUp.on(r.data);
                    });
                }
            });


            // Add faucet butonu
        	$(document).on("click", "#add_faucet", function(){

                rq( "req=add_faucet_form", function(r){
                    console.log(r);
                    PopUp.on( r.data );
                });
        	});

            // Form
            $(document).on("submit", "#add_faucet_form", function(e){

                if( formCheck("add_faucet_form") ) {

                    rq( $("#add_faucet_form").serialize() + '&req=add_faucet', function(r){
                        console.log(r);
                        PopUp.off();
                        reloadList(r.data);
                    });

                }
                e.preventDefault();
            });

            $(document).on("submit", "#edit_faucet_form", function(e){

                if( formCheck("edit_faucet_form") ) {

                    rq( $("#edit_faucet_form").serialize() + '&req=edit_faucet', function(r){
                        console.log(r);
                        PopUp.off();
                        reloadList(r.data);
                    });

                }
                e.preventDefault();
            });
        });

    </script>

<?php

    include "inc/Footer.php";
