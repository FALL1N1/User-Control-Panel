<?php


    include_once('_template/_header.php');

    if(!_getUsername())
        Header('Location: index.php');
?>
    <fieldset>
        <legend><?php echo $L[172]; ?></legend>
        <div>
            <a href = '_donation_methods.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/visa.png'></div>
                <h4><?php echo $L[263]; ?></h4>
                <div class = 'service_desc'><?php echo $L[264]; ?></div>
            </div></a>
            <a href = '_l_select_one_character_l_.php?go=_a_instant_gold_buy_or_sell.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/gold.png'></div>
                <h4><?php echo $L[265]; ?></h4>
               <div class = 'service_desc'><?php echo $L[266]; ?></div>
            </div></a>
        </div>

        <legend><?php echo $L[173]; ?></legend>
        <div>
            <a href = '_acc_unban.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/dalaran.png'></div>
                <h4><?php echo $L[181]; ?></h4>
                <div class = 'service_desc'><?php echo $L[182]; ?></div>
            </div></a>
            <a href = '_acc_change_password.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/reputation.png'></div>
                <h4><?php echo $L[183]; ?></h4>
                <div class = 'service_desc'><?php echo $L[184]; ?></div>
            </div></a>
            <a href = '_acc_history.php?CL'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/reputation.png'></div>
                <h4><?php echo $L[185]; ?></h4>
                <div class = 'service_desc'><?php echo $L[186]; ?></div>
            </div></a>
        </div>

        <legend><?php echo $L[174]; ?></legend>
        <div>
            <a href = '_l_select_one_character_l_.php?go=_a_change_name.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/rename.png'></div>
                <h4><?php echo $L[187]; ?></h4>
                <div class = 'service_desc'><?php echo $L[188]; ?></div>
            </div></a>
            <a href = '_l_select_one_character_l_.php?go=_a_change_customize.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/customize.png'></div>
                <h4><?php echo $L[189]; ?></h4>
                <div class = 'service_desc'><?php echo $L[190]; ?></div>
            </div></a>
            <a href = '_l_select_one_character_l_.php?go=_a_change_faction.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/change_faction.png'></div>
                <h4><?php echo $L[191]; ?></h4>
                <div class = 'service_desc'><?php echo $L[192]; ?></div>
            </div></a>
        </div>

        <div>
            <a href = '_l_select_one_character_l_.php?go=_a_instant_80.php&lvl=80'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/instant80lvl.png'></div>
                <h4><?php echo $L[193]; ?></h4>
                <div class = 'service_desc'><?php echo $L[194]; ?></div>
            </div></a>
            <a href = '_l_select_one_character_l_.php?go=_a_instant_unstruck.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/instant80lvl.png'></div>
                <h4><?php echo $L[195]; ?></h4>
                <div class = 'service_desc'><?php echo $L[196]; ?></div>
            </div></a>
            <a href = '_l_select_one_character_l_.php?go=_a_change_race.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/change_race.png'></div>
                <h4><?php echo $L[197]; ?></h4>
                <div class = 'service_desc'><?php echo $L[198]; ?></div>
            </div></a>
        </div>

        <div>
            <a href = '_l_select_one_character_l_.php?go=_a_instant_remove_deserter.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/deserter.png'></div>
                <h4><?php echo $L[199]; ?></h4>
                <div class = 'service_desc'><?php echo $L[200]; ?></div>
            </div></a>
            <a href = '_l_select_one_character_l_.php?go=_a_instant_teleport_to_dalaran.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/dalaran.png'></div>
                <h4><?php echo $L[201]; ?></h4>
                <div class = 'service_desc'><?php echo $L[202]; ?></div>
            </div></a>
            <a href = '_l_select_one_character_l_.php?go=_a_instant_exalted_reputation.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/reputation.png'></div>
                <h4><?php echo $L[203]; ?></h4>
                <div class = 'service_desc'><?php echo $L[204]; ?></div>
            </div></a>
        </div>

        <div>
            <a href = '_playerside.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/deserter.png'></div>
                <h4><?php echo $L[205]; ?></h4>
                <div class = 'service_desc'><?php echo $L[206]; ?></div>
            </div></a>
            <a href = '_l_select_one_character_l_.php?go=_a_instant_buy_item.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/inv_misc_bag_27.png'></div>
                <h4><?php echo $L[207]; ?></h4>
                <div class = 'service_desc'><?php echo $L[208]; ?></div>
            </div></a>
            <a href = '_l_select_one_character_l_.php?go=_a_instant_quest_complete.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/inv_misc_bag_27.png'></div>
                <h4><?php echo $L[209]; ?></h4>
                <div class = 'service_desc'><?php echo $L[210]; ?></div>
            </div></a>
        </div>
    </fieldset>

<?php include_once('_template/_footer.php');
    ob_end_flush();
?>