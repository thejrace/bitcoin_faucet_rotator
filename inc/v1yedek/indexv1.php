<?php
    
    require_once "inc/Init.php";

    $UserFilters = new Filter;
    $UserFilters->generate( $UC->getUserData()["id"] );

    $FaucetList = new FaucetList ( $Ref->ID );
    $FaucetList->get( $UserFilters->userSettings() );


    $USER = "guest";

    include "inc/Header.php";
?>

        <div class="filter-row">
            <div class="filter-header"><span>Filters</span></div>
            <div class="filter-menu">
                <form id="filters" action="" method="post">
                
                    <div class="filter-menu-item clearfix">
                        
                        <?php  
                            echo $UserFilters->show();
                        ?>
                    </div>
            </div>
            <div class="filter-header"><span>Sort By</span></div>
            <div class="filter-menu">
                    
                    <div class="filter-menu-item sort-radios clearfix">

                        
                        <label class="filter-buton checked" for="sort_payout" title="Sort By Payout">
                            <input type="radio" name="sortby" value="sort_payout" for="sort_payout" id="sort_payout" checked />
                            Payout
                        </label>
                        <label class="filter-buton" for="sort_timer" title="Sort By Timer">
                            <input type="radio" name="sortby" value="sort_timer" id="sort_timer"  />
                            Timer
                        </label>
                        <label class="filter-buton" for="sort_recommended" title="Sort By Recommended">
                            <input type="radio" name="sortby" value="sort_recommended" id="sort_recommended"  />
                            Recommended
                        </label>
                        <label class="filter-buton" for="sort_new" title="Newest">
                            <input type="radio" name="sortby" value="sort_new" for="sort_new" id="sort_new"  />
                            New
                        </label>
                    </div>
                </form>
            </div>
        </div>

        <div class="kapsul">
            <ul id="web-list">
                <?php echo $FaucetList->show(); ?>
            </ul>
        </div>

    </div>

    <script type="text/javascript">

        BitcoinDatabase.reqTo = "inc/Ajax.php";

        $(document).ready(function(){


            $(document).on("click", ".filter-buton input[type='checkbox']", function(e){
                c_status( $(this).parent() );
                rq( $("#filters").serialize()  + '&req=reload_list', function(r){ reloadList(r.data) } );
                e.stopPropagation();
            });

            $(document).on("click", ".filter-buton input[type='radio']", function(e){
                var p = $(this).parent();
                if( $(this).is(':checked') ){
                    $(".sort-radios .filter-buton").removeClass("checked");
                    p.addClass("checked");
                }   
                rq( $("#filters").serialize()  + '&req=reload_list', function(r){ reloadList(r.data) } );
                e.stopPropagation();
            });   

        });
       
    </script>

<?php

    include "inc/Footer.php";