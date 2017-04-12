 <div class="filter-row clearfix">
            <form id="filters" action="" method="post">
                
                <div class="filter-menu">
                    <div class="filter-header"><span>Filters</span></div>   
                    
                        <div class="filter-menu-item filter-radios clearfix">

                            <label class="filter-buton checked" id="lab_fb" for="fb" title="">
                            <input type="radio" name="filter" value="fb" id="fb" checked />
                                FaucetBox
                            </label>

                            <label class="filter-buton" id="lab_ep" for="ep" title="">
                            <input type="radio" name="filter" value="ep" id="ep" />
                                ePay
                            </label>

                            <label class="filter-buton" id="lab_direct" for="direct" title="">
                            <input type="radio" name="filter" value="direct" id="direct" '.$checked.' />
                                Direct
                            </label>

                            <label class="filter-buton" id="lab_xp" for="xp" title="">
                            <input type="radio" name="filter" value="xp" id="xp" />
                                Xapo
                            </label>

                            <label class="filter-buton" id="lab_pt" for="pt" title="">
                            <input type="radio" name="filter" value="pt" id="pt" />
                                Paytoshi
                            </label>

                        </div>
                </div>
                
                <div class="filter-menu">
                    <div class="filter-header"><span>Sort By</span></div>
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
                    
                </div>

                <?php echo $ADMINTOOLS; ?>

            </form>
        </div>