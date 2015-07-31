<?php


    if(!file_exists('_template/_header.php') || !isset($_SESSION['AccountUN']))
        Header('Location: ../index.php');

    include_once('_template/_header.php');
    include_once('_core/f_switch.php');

    $AuthConnection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);

if(isset($_POST['Account']) && !empty($_POST['Account']) && isset($_POST['Password']) && !empty($_POST['Password'])
 && isset($_POST['ServerUrl']) && !empty($_POST['ServerUrl']) && isset($_POST['RealmlistList']) && !empty($_POST['RealmlistList'])) {

    $o_Account  = trim($_POST['Account']);
    $o_Password = base64_encode(trim($_POST['Password']));
    $o_URL      = trim($_POST['ServerUrl']);

    if($_FILES['file']['name'] != "SaveMe.lua") {
        $realson = $L[223];
        _Migration_STEP1FORM($AuthConnection, _RDiv($realson));
    } else {
        $_FILE  = md5(time() . $_FILES['file']['name'] . rand(1, 100));
        $_FILE  = "./_!_STORAGE_!_/" . $_FILE;
        move_uploaded_file($_FILES['file']['tmp_name'], $_FILE);
        $fileopen   = fopen($_FILE,'r');
        $buffer     = null;
        $realson    = null;

        while(!feof($fileopen)) {
            $buffer2 = fgets($fileopen);
            $buffer .= $buffer2;
        }

        fclose($fileopen);
        unlink($_FILE);
        $part = explode('"', $buffer);
        if(!isset($part[1]) || !isset($part[3]) || _CHECK_KEY($part[3]) != "v335.699")
            return _Migration_STEP1FORM($AuthConnection, _RDiv($L[36]));

        $DUMP               = $part[1];
        $REALM_NAME         = $_POST['RealmlistList'];
        $DECODED_DUMP       = _DECRYPT($DUMP);
        $CHAR_REALM         = _getRealmID($AuthConnection, $REALM_NAME);
        $CHAR_ACCOUNT_ID    = _getAccountID();
        mysql_close($AuthConnection);

        $GM_ACCOUNT_ID      = _CanOrNoTransferServer($DBUser, $DBPassword, $CHAR_REALM, $GMLevel);
        $AuthConnection     = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);

        $json               = json_decode(stripslashes($DECODED_DUMP), true);
        $CHAR_NAME          = mb_convert_case(mb_strtolower($json['uinf']['name'],'UTF-8'), MB_CASE_TITLE,'UTF-8');
        $O_REALMLIST        = $json['ginf']['realmlist'];
        $O_REALM            = $json['ginf']['realm'];
        $RaceID             = _GetRaceID(strtoupper($json['uinf']['race']));
        $ClassID            = _GetClassID(strtoupper($json['uinf']['class']));
        $CharLevel          = _MaxValue($json['uinf']['level'], $MaxCL);

        $result = mysql_query("SELECT `address`,`port` FROM `realmlist` WHERE `id` = ". $CHAR_REALM .";", $AuthConnection) or die(mysql_error());
        $row    = mysql_fetch_array($result);
        $SPT    = $row['port'];
        $SIP    = $row['address'];

        $AchievementsCount  = 0;
        $ACHMINTime         = 0;
        $ACHMAXTime         = 0;
        foreach($json['achiev'] as $key => $value) {
            if($ACHMINTime == 0)
                $ACHMINTime = $value['D'];
            if($ACHMINTime > $value['D'])
                $ACHMINTime = $value['D'];
            if($ACHMAXTime < $value['D'])
                $ACHMAXTime = $value['D'];
            ++$AchievementsCount;
        }

        if(((10 + $CharLevel > $AchievementsCount) || ($AchievementsCount > $AchievementsMinCount)) && $AchievementsCheck == 1) {
            $realson = $L[221];
        } else if(_CHECK_PLAYTIME($ACHMAXTime, $ACHMINTime) < $PLAYTIME) {
            $realson = $L[98];
        } else if(_checkInBlackList($AuthConnection, $o_URL)      ||
                  _checkInBlackList($AuthConnection, $O_REALM)    ||
                  _checkInBlackList($AuthConnection, $O_REALMLIST)) {
            $realson = $L[54];
        } else if(_CanOrNoTransferPlayer($DBUser, $DBPassword, $CHAR_REALM, $CHAR_ACCOUNT_ID)) {
            $realson = $L[30] ." '". $REALM_NAME ." '". $L[31];
        } else if($GM_ACCOUNT_ID < 0) {
            $realson = $L[32] . $REALM_NAME ." ". $L[33];
        } else if(strlen($o_Account) > 32) {
            $realson = $L[59];
        } else if(!_ServerOn($SIP, $SPT))
            $realson = $L[119] ." '". $REALM_NAME ."' <u>". $L[119] ."!</u>";

        $GUID   = _getCharacterGUID($DBUser, $DBPassword, $CHAR_REALM);

        $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
        if(empty($realson)) {
            $ID =
            _DUMP_WriteDumpInDB($connection,
            $DUMP, $CHAR_NAME, $CHAR_ACCOUNT_ID, $CHAR_REALM,
                                $o_Account, $o_Password, $O_REALMLIST, $O_REALM, $o_URL, $GUID, $GM_ACCOUNT_ID, $L[35]);
        } else return _Migration_STEP1FORM($connection, _RDiv($realson));

        $connection = _MySQLConnect(_HostDBSwitch($CHAR_REALM), $DBUser, $DBPassword, _CharacterDBSwitch($CHAR_REALM));

        unset($_SESSION['STEP2']);
        $char_money         = _MaxValue($json['uinf']['money'], $MaxMoney);
        $char_speccount     = $json['uinf']['specs'];
        $char_gender        = $json['uinf']['gender'] - 2 == 1 ? 1 : 0;
        $char_totalkills    = $json['uinf']['kills'];
        $char_arenapoints   = _MaxValue($json['uinf']['arenapoints'], $MaxAP);
        $char_honorpoints   = _MaxValue($json['uinf']['honor'], $MaxHP);
        $INVrow             = "";
        $GEMrow             = "";
        $CURrow             = "";
        $row                = "";
        $QUERYFOREXECUTE    = "";
        mysql_query("
        INSERT INTO `characters`(`guid`,`name`,`level`,`gender`,`totalHonorPoints`,`arenaPoints`,`totalKills`,`money`,`class`,`race`,`at_login`,`account`,`taximask`,`speccount`,`online`) VALUES (
        ". $GUID .",'". _X($CHAR_NAME) ."',". (int)$CharLevel .",". (int)$char_gender .",". (int)$char_honorpoints .",". (int)$char_arenapoints .",
        ". (int)$char_totalkills .",".(int)$char_money .",". $ClassID .",". $RaceID .", 0x180, 1, '0 0 0 0 0 0 0 0 0 0 0 0 0 0',". (int)$char_speccount .", 0);", $connection) or die(mysql_error());
        $QUERYFOREXECUTE    = $QUERYFOREXECUTE. "
        INSERT INTO `character_transfer` VALUES (". $GUID .",". $CHAR_ACCOUNT_ID .",". $GM_ACCOUNT_ID .",". $ID .");

        UPDATE `characters` SET
        `position_x`    = 5741.36,
        `position_y`    = 626.982,
        `position_z`    = 648.354,
        `map`           = 571,
        `health`        = 100,
        `zone`          = 4395,
        `cinematic`     = 1
            WHERE `guid` = ". $GUID .";";

        if($char_speccount == 2) {
            _LearnSeparateSpell(63644, $GUID, $connection);
            _LearnSeparateSpell(63645, $GUID, $connection);
        }

        if($ClassID == 6)
            $QUERYFOREXECUTE = $QUERYFOREXECUTE. "\n ". _DKMigration($GUID);

        foreach($json['glyphs'] as $key => $value) {
            $GlyphID1 = _GetGlyphID($value[0][0]);
            $GlyphID2 = _GetGlyphID($value[0][1]);
            $GlyphID3 = _GetGlyphID($value[0][2]);
            $GlyphID4 = _GetGlyphID($value[1][0]);
            $GlyphID5 = _GetGlyphID($value[1][1]);
            $GlyphID6 = _GetGlyphID($value[1][2]);

            $QUERYFOREXECUTE    = $QUERYFOREXECUTE. "\n INSERT IGNORE /* GLYPHS */ INTO `character_glyphs` VALUES (". $GUID .",". (int)$key .",
            ". (int)$GlyphID1 .",". (int)$GlyphID4 .",". (int)$GlyphID5 .",". (int)$GlyphID2 .",". (int)$GlyphID6 .",". (int)$GlyphID3 .");";
        }

        foreach($json['achiev'] as $key => $value) {
            $AchievementID  = $value['I'];
            $date           = $value['D'];
            if(_CheckWrongOrNoAchievement($AchievementID))
                $QUERYFOREXECUTE = $QUERYFOREXECUTE. "\n INSERT IGNORE /* ACHIEVEMENT */ INTO `character_achievement` VALUES (". $GUID .", ". (int)$AchievementID .", ". (int)$date .");";
        }

        $locale         = trim(strtoupper($json['ginf']['locale']));
        foreach($json['rep'] as $key => $value) {
            $reputation = $value['V'];
            $faction    = GetFactionID(mb_strtoupper($value['N'],'UTF-8'), $locale);
            if($faction < 1 || $reputation < 1)
                continue;
            $flag       = $value['F'] + 1;
            if($faction == 1119 && $reputation > 1)
                $QUERYFOREXECUTE = $QUERYFOREXECUTE. "\n ". _SOHMigration($GUID);
            $QUERYFOREXECUTE    = $QUERYFOREXECUTE. "\n INSERT IGNORE /* REPUTATION */ INTO `character_reputation` VALUES (". $GUID .", ". $faction .", ". (int)$reputation .",". (int)$flag .");";
        }

        foreach($json['skills'] as $key => $value) {
            $SkillName = mb_strtoupper($value['N'],'UTF-8');

            if(_checkRiding($SkillName, $value['C'], $connection, $GUID, $CharLevel))
                continue;

            $SkillID = _GetSkillID($SkillName, $locale);
            if($SkillID < 1)
                continue;
            $max = _MaxValue(_RemoveRaceBonus($RaceID, $SkillID, $value['M']), 450);
            $cur = _MaxValue(_RemoveRaceBonus($RaceID, $SkillID, $value['C']), 450);

            $SpellID = GetSpellIDForSkill($SkillID, $max);

            if(_CheckExtraSpell($SkillID))
                _LearnSeparateSpell(_GetExtraSpellForSkill($SkillID, $cur, $GUID, $connection), $GUID, $connection);

            $QUERYFOREXECUTE = $QUERYFOREXECUTE. "\n INSERT IGNORE /* SKILL */ INTO `character_skills` VALUES (". $GUID .", ". (int)$SkillID .",". (int)$cur .",". (int)$max .");";
            if($SpellID < 3)
                continue;

            $QUERYFOREXECUTE = $QUERYFOREXECUTE. "\n INSERT IGNORE /* SPELL FOR SKILL */ INTO `character_spell` VALUES (". $GUID .", ". (int)$SpellID .", 1, 0);";
        }

        mysql_close($connection);
        foreach($json['spells'] as $SpellID => $value) {
            if(_isSpellValid($SpellID, $ClassID))
                $QUERYFOREXECUTE    = $QUERYFOREXECUTE. "\n INSERT IGNORE /* NOT MOUNT OR CRITTER */ INTO `character_spell` VALUES (". $GUID .", ". (int)$SpellID .", 1, 0);";
        }

        foreach($json['creature'] as $key => $SpellID) {
            $QUERYFOREXECUTE        = $QUERYFOREXECUTE. "\n INSERT IGNORE /* MOUNT OR CRITTER */ INTO `character_spell` VALUES (". $GUID .", ". (int)$SpellID .", 1, 0);";
        }

        foreach($json['currency'] as $key => $value) {
            $CurrencyID = $value['I'];
            $COUNT      = $value['C'];

            if($COUNT < 1 || $CurrencyID < 1)
                continue;
            else if(_CheckCurrency($CurrencyID))
                $CURrow .= $CurrencyID.":".$COUNT." ";
        }

        foreach($json['inventory'] as $key => $value) {
            $item   = _GetChangedItem($CHAR_REALM, $value['I']);
            $count  = _checkItemCount($value['C']);
            if($item < 1 || $count < 0)
                continue;

            $INVrow .= $item .":". $count ." ";
            $GEM1   = _GetGemID($value['G1']);
            $GEM2   = _GetGemID($value['G2']);
            $GEM3   = _GetGemID($value['G3']);
            if($GEM1 > 1)
                $GEMrow .= $GEM1 .":1 ";
            if($GEM2 > 1)
                $GEMrow .= $GEM2 .":1 ";
            if($GEM3 > 1)
                $GEMrow .= $GEM3 .":1 ";
        }

        $QUERYFOREXECUTE_CON = new mysqli(_HostDBSwitch($CHAR_REALM), $DBUser, $DBPassword, _CharacterDBSwitch($CHAR_REALM));
        mysqli_multi_query($QUERYFOREXECUTE_CON, $QUERYFOREXECUTE) or die(mysqli_error($QUERYFOREXECUTE_CON));

        $row = trim($INVrow . $GEMrow . $CURrow);
        _DUMP_UpdateItemRow($DBUser, $DBPassword, $ID, $row);

        if(_CheckCharacterName($DBUser, $DBPassword, $CHAR_REALM, $CHAR_NAME) > 1) {
            $_SESSION['guid']   = $GUID;
            $_SESSION['realm']  = $CHAR_REALM;
            $_SESSION['dumpID'] = $ID;
            $_SESSION['STEP2']  = 1;
            include("step2.php");
        } else {
            $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
            _updateMigrationStatusAndCharacterName($connection, $ID, $CHAR_NAME, 0);
            mysql_close($connection);

            _PreparateMails($row, $CHAR_NAME, $SOAPUser, $SOAPPassword, $CHAR_REALM);
            $connection = _MySQLConnect(_HostDBSwitch($CHAR_REALM), $DBUser, $DBPassword, _CharacterDBSwitch($CHAR_REALM));
            _TalentsReset($connection, $GUID);
            _MoveToGMAccount($connection, $GUID);
            mysql_close($connection);

            echo _GDiv($L[51]);
        }
    }
} else _Migration_STEP1FORM($AuthConnection);

    function _CHECK_PLAYTIME($TIME1, $TIME2) { return floor(($TIME1 - $TIME2) / 86400); }

    function _Migration_STEP1FORM($connection, $REALSON = "") {
        global $L;
        echo "<fieldset>". $REALSON ."
        <div class = 'alert text-center'>". $L[42] ."</div>
        <form action = '". $_SERVER['PHP_SELF'] ."' method = 'post' enctype = 'multipart/form-data'>
            <div class = 'text-left'>
                <label for = 'Account'>". $L[43] ."</label>
                <input name = 'Account' type = 'text' size = '32' placeholder = '". $L[151] ."'>
                <label for = 'Password'>". $L[44] ."</label>
                <input name = 'Password' type = 'password' size = '32' placeholder = '". $L[152]."'>
                <div class = 'text-center'>". $L[47] ."</div>
                <label for = 'RealmDropDown'>". $L[40] ."</label>
                <ul class = 'nav nav-pills'>
                    <li name = 'RealmDropDown' class = 'dropdown'>
                        <a id = 'RealmlistListNameNeedChange' class = 'dropdown-toggle' role = 'button' data-toggle = 'dropdown' href = '#'>". $L[100] ."<b class = 'caret'></b></a>
                        <ul id = 'RealmlistListNameNeedChangeList' class = 'dropdown-menu' role = 'menu'>";
                $result = mysql_query("SELECT `id`,`name` FROM `realmlist` WHERE `TransferAvailable` = 1;", $connection) or die(mysql_error());
                mysql_close($connection) or die(mysql_error());
                while($row = mysql_fetch_array($result))
                    echo "<li><a tabindex = '-1' href= '# ' onclick = \"RealmlistJS($(this).html());\">". $row['name'] ."</a></li>";
                    echo "</ul>
                    </li>
                </ul>
                <input type = 'hidden' name = 'RealmlistList' value = '' id = 'RealmlistList' />

                <div class = 'text-center'>". $L[45] ."</div>
                <label for = 'ServerUrl'>". $L[99] .":</label><input name = 'ServerUrl' type = 'text' size = '60' placeholder = '". $L[99] ."'>
                <div class = 'text-center'>". $L[46] ."</div>
                <div>
                    <label for = 'file'>". $L[41] ."</label>
                    <input type = 'file' name = 'file' id = 'file' value = 'Attach'/>
                    <input class = 'btn btn-info' type = 'submit' name = 'load' style = 'float: right;' value = '". $L[11] ."' />
                </div>
            </div>
        </form>
        </fieldset>
        <script>
            $('#RealmlistListNameNeedChange').click(function() {
                $('#RealmlistListNameNeedChangeList').toggle();
            })

            function RealmlistJS(html) {
                $('#RealmlistListNameNeedChangeList').hide();
                $('#RealmlistListNameNeedChange').html(html);
                $('#RealmlistList').val($('#RealmlistListNameNeedChange').text());
            }
        </script>";
    }
?>