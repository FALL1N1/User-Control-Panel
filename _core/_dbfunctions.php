<?php


    function _doesCharacterNotOnlineATM($DBUser, $DBPassword, $RealmID, $GUID) {
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        $query  = mysql_query("SELECT `online` FROM `characters` WHERE `guid` = ". (int)$GUID ." AND `account` = ". (int)_getAccountID() .";", $connection) or die(mysql_error());
        $result = mysql_fetch_array($query);
        mysql_close($connection) or die(mysql_error());
        return $result[0] == 0 ? true : false;
    }

    function _getMigrationStatus($connection, $ID) {
        $query  = mysql_query("SELECT `cStatus` FROM `account_transfer` WHERE `id` = ". (int)$ID .";", $connection) or die(mysql_error());
        $row    = mysql_fetch_array($query);
        return $row[0];
    }

    function _CanOrNoTransferPlayer($DBUser, $DBPassword, $RealmID, $AccountID) {
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        $result     = _CanOrNoTransferServer_CountQueue($connection, $AccountID);
        mysql_close($connection) or die(mysql_error());
        return $result < 9 ? false : true;
    }

    function _CanOrNoTransferServer($DBUser, $DBPassword, $RealmID, $GMLevel) {
        global $AccountDBHost, $AccountDB;
        $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);

        $query = mysql_query("SELECT `id` FROM `account_access` WHERE `gmlevel` IN ". $GMLevel .";", $connection) or die(mysql_error());
        mysql_close($connection) or die(mysql_error());
        $A = array();
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        while($result = mysql_fetch_array($query))
            $A[$result['id']] = _CanOrNoTransferServer_CountQueue($connection, $result['id']);
        mysql_close($connection) or die(mysql_error());
        $REVIEWER_ID = -1;
        $MIN         = 8;

        foreach($A as $i) {
            if($A[$i] < $MIN) {
                $MIN         = $A[$i];
                $REVIEWER_ID = $i;
            }
        }

        return $REVIEWER_ID;
    }

    function _CanOrNoTransferServer_CountQueue($connection, $ACC_ID) {
        $query  = mysql_query("SELECT COUNT(*) FROM `characters` WHERE `account` = ". (int)$ACC_ID .";", $connection) or die(mysql_error());
        $result = mysql_fetch_array($query);
        return $result[0];
    }

    function _checkInBlackList($connection, $VALUE) {
        $query  = mysql_query("SELECT `b_address` FROM `account_transfer_blacklist` WHERE `b_address` LIKE '%". _X(trim($VALUE)) ."%';", $connection) or die(mysql_error());
        $row    = mysql_fetch_array($query);
        return empty($row[0]) ? false : true;
    }

    function _getRealmArray($connection) {
        if(isset($_SESSION['R']['W']))
            return;
        $query   = mysql_query("SELECT `id`,`name` FROM `realmlist` ORDER BY `id` ASC;", $connection) or die(mysql_error());
        while($result = mysql_fetch_array($query))
            $_SESSION['R']['W'][$result['id']]['N'] = $result['name'];
    }

    function _getRealmID($connection, $RName) {
        $query  = mysql_query("SELECT `id` FROM `realmlist` WHERE `name` = '". _X($RName) ."';", $connection) or die(mysql_error());
        $row    = mysql_fetch_array($query);
        return $row[0];
    }

    function _getCharacterGUID($DBUser, $DBPassword, $RealmID) {
        global $AccountDBHost, $AccountDB;
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        $CQuery = mysql_query("SELECT MAX(`guid`) FROM `characters` WHERE `guid` BETWEEN 1000000 AND 1999999;", $connection) or die(mysql_error());
        $CRow   = mysql_fetch_array($CQuery);
        mysql_close($connection) or die(mysql_error());
        $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
        $AQuery = mysql_query("SELECT `GUID` FROM `account_transfer_guid` WHERE `RealmID` = ". (int)$RealmID .";", $connection) or die(mysql_error());
        $ARow   = mysql_fetch_array($AQuery);

        $GUID   = 0;
        if($CRow[0] > $ARow[0])
            $GUID = $CRow[0] + 1;
        else
            $GUID = $ARow[0] + 1;

        mysql_query("DELETE FROM `account_transfer_guid` WHERE `RealmID` = ". $RealmID .";", $connection) or die(mysql_error());
        mysql_query("INSERT INTO `account_transfer_guid`(`RealmID`,`GUID`) VALUES (". (int)$RealmID .", ". (int)$GUID .");", $connection) or die(mysql_error());
        mysql_close($connection) or die(mysql_error());
        return $GUID;
    }

    function _CancelORDenyCharacterTransfer($DBUser, $DBPassword, $RealmID, $GUID, $STORAGE) {
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        mysql_query("UPDATE `characters` SET `name` = (SELECT `dump_id` FROM `character_transfer` WHERE `guid` = ". (int)$GUID ."),`account` = ". $STORAGE ." WHERE `guid` = ". $GUID .";", $connection) or die(mysql_error());
        mysql_close($connection) or die(mysql_error());
    }

    function _X($A) { return get_magic_quotes_gpc() ? stripslashes(mysql_real_escape_string($A)) : mysql_real_escape_string($A); }
    function _Z($A) { return strtoupper(addslashes($A)); }
    function _ApproveCharacterTransfer($connection, $GUID) { mysql_query("UPDATE `characters` SET `account` = (SELECT `player_account` FROM `character_transfer` WHERE guid = ". (int)$GUID .") WHERE `guid` = ". (int)$GUID .";", $connection) or die(mysql_error()); }
    function _MoveToGMAccount($connection, $GUID) { mysql_query("UPDATE `characters` SET `account` = (SELECT `gm_account` FROM `character_transfer` WHERE `guid` = ". (int)$GUID .") WHERE `guid` = ". $GUID .";", $connection) or die(mysql_error()); }
    function _updateCharacterName($connection, $NAME, $GUID) { mysql_query("UPDATE `characters` SET `name` = '". _X($NAME) ."' WHERE `guid` = ". (int)$GUID .";", $connection) or die(mysql_error()); }
    function _updateMigrationStatus($connection, $ID, $STATUS) { mysql_query("UPDATE `account_transfer` SET `cStatus` = ".(int)$STATUS ." WHERE `id` = ". (int)$ID .";", $connection) or die(mysql_error()); }
    function _updateMigrationStatusAndCharacterName($connection, $ID, $NAME, $STATUS) { mysql_query("UPDATE `account_transfer` SET `cNameNew` = '". _X($NAME) ."', `cStatus` = ". (int)$STATUS ." WHERE `id` = ". (int)$ID .";", $connection) or die(mysql_error()); }
    function _TalentsReset($connection, $GUID) { mysql_query("UPDATE `characters` SET `at_login` = `at_login`|4|16 WHERE `guid` = ". (int)$GUID .";", $connection) or die(mysql_error()); }
    function _UnbanAccount($connection) { mysql_query("UPDATE `account_banned` SET `active` = 0 WHERE `id` = ". (int)_getAccountID() .";", $connection) or die(mysql_error()); }
    function _isEnoughMythCoins($HowMuch, $connection) { if($HowMuch < 1) return true; return _getMythCoinsAmount($connection) > $HowMuch ? true : false; }
    function _isEnoughGoldCoins($HowMuch, $GUID, $connection) { if($HowMuch < 1) return true; return _getGoldCoinsAmount($connection, $GUID) > $HowMuch ? true : false; }
    function _SpendGoldCoins($HowMuch, $GUID, $connection) { mysql_query("UPDATE `characters` SET `money` = `money` - ". $HowMuch * 10000 ." WHERE `guid` = ". $GUID ." ;", $connection) or die(mysql_error()); }
    function _GiveGoldCoins($HowMuch, $GUID, $connection) { mysql_query("UPDATE `characters` SET `money` = `money` + ". $HowMuch * 10000 ." WHERE `guid` = ". $GUID ." ;", $connection) or die(mysql_error()); }

    function _MySQLConnect($DBHost, $DBUser, $DBPassword, $DBName) {
        $connection = mysql_connect($DBHost, $DBUser, $DBPassword) or die(mysql_error());
        mysql_select_db($DBName, $connection);
        mysql_set_charset('utf8', $connection);
        return $connection;
    }

    function _modifyFirePoints($HowMuch, $TEXT, $connection, $ACC) {
        if($HowMuch < 0)
            _SpendMythCoins(abs($HowMuch), 16, "", 0, "", 0, $TEXT, $connection, $ACC);
        else if($HowMuch > 0)
            _GiveMythCoins(abs($HowMuch), $TEXT, $connection, $ACC);
    }

    function _SpendMythCoins($HowMuch, $ActionID, $CharName, $CharGUID, $RealmName, $RealmID, $TEXT, $connection, $ACC = null) {
        if(!isset($ACC))
            $ACC = _getAccountID();
        mysql_query("UPDATE `account_details` SET `myth_coins` = `myth_coins` - ". (int)$HowMuch ." WHERE `id` = ". $ACC ." ;", $connection) or die(mysql_error());
        $BALANCE = _getMythCoinsAmount($connection, $ACC);
        mysql_query("INSERT INTO `account_billing_history`(`id`,`action`,`charName`,`charGUID`,`realmName`,`realmID`,`myth_coins_spend`,`text`,`IP`,`myth_coins_balance`) VALUES
        (". $ACC .",". (int)$ActionID .",'". $CharName ."',". (int)$CharGUID .",'". $RealmName ."',". (int)$RealmID .",". -(int)$HowMuch .",'". trim($TEXT) ."',
        '". $_SERVER['REMOTE_ADDR'] ."', ". $BALANCE .");", $connection) or die(mysql_error());
    }

    function _GiveMythCoins($HowMuch, $TEXT, $connection, $ACC = null) {
        if(!isset($ACC))
            $ACC = _getAccountID();
        mysql_query("UPDATE `account_details` SET `myth_coins` = `myth_coins` + ". (int)$HowMuch ." WHERE `id` = ". $ACC ." ;", $connection) or die(mysql_error());
        $BALANCE = _getMythCoinsAmount($connection, $ACC);
        mysql_query("INSERT INTO `account_billing_history`(`id`,`action`,`charName`,`charGUID`,`realmName`,`realmID`,`myth_coins_spend`,`text`,`IP`,`myth_coins_balance`) VALUES
        (". $ACC .", 11, '', 0, '', 0,". (int)$HowMuch .",'". trim($TEXT) ."','". $_SERVER['REMOTE_ADDR'] ."', ". $BALANCE .");", $connection) or die(mysql_error());
    }

    function _returnStatusSTR($VALUE, $COMMENT = "") {
        global $L;
        switch($VALUE) {
            case 0:     return $L[20];
            case 1:     return $L[21];
            case 2:     return $L[22] ." | Realson: ". $COMMENT;
            case 3:     return $L[23];
            default:    return $L[24];
        }
    }

    function _returnTABLEStatusSTR($X) {
        switch($X) {
            case 0:     return "info";
            case 1:     return "success";
            case 3:     return "warning";
            default:    return "error";
        }
    }

    function _getRealmNameFromID($connection, $ID) {
        $query  = mysql_query("SELECT `name` FROM `realmlist` WHERE `id` = ". (int)$ID .";", $connection) or die(mysql_error());
        $row    = mysql_fetch_array($query);
        return $row[0];
    }

    function _isGMAllowed($connection) {
        global $GMLevel;
        $query  = mysql_query("SELECT `id` FROM `account_access` WHERE `id` = ". (int)_getAccountID() ." AND `gmlevel` IN ". $GMLevel .";", $connection) or die(mysql_error());
        return mysql_num_rows($query) > 0;
    }

    function _CheckCharacterName($DBUser, $DBPassword, $RealmID, $NAME) {
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        $query      = mysql_query("SELECT `name` FROM `characters` WHERE `name` = '". _X($NAME) ."';", $connection) or die(mysql_error());
        mysql_close($connection) or die(mysql_error());
        return mysql_num_rows($query);
    }

    function _GetCharacterNameAndDeleteMails($DBUser, $DBPassword, $RealmID, $GUID) {
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        mysql_query("DELETE FROM `mail` WHERE `receiver` = ". (int)$GUID .";", $connection) or die(mysql_error());
        $query      = mysql_query("SELECT `name` FROM `characters` WHERE `guid` = ". (int)$GUID .";", $connection) or die(mysql_error());
        $row        = mysql_fetch_array($query);
        mysql_close($connection) or die(mysql_error());
        return $row['name'];
    }

    function _LearnSeparateSpell($SpellID, $GUID, $connection) {
        if($SpellID < 1)
            return;
        mysql_query("/* LEARN_SEPARATE_SPELL */ INSERT IGNORE INTO `character_spell` VALUES (". (int)$GUID .", ". (int)$SpellID .", 1, 0 );", $connection) or die(mysql_error());
    }

    function _LoadItemRoW($DBUser, $DBPassword, $ID) {
        global $AccountDBHost, $AccountDB;
        $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
        $query      = mysql_query("SELECT `cItemRow` FROM `account_transfer` WHERE `id` = '". (int)$ID ."';", $connection) or die(mysql_error());
        $row        = mysql_fetch_array($query);
        mysql_close($connection) or die(mysql_error());
        return $row[0];
    }

    function _DUMP_UpdateItemRow($DBUser, $DBPassword, $ID, $ROW) {
        global $AccountDBHost, $AccountDB;
        $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
        mysql_query("UPDATE `account_transfer` SET `cItemRow` = '". _Y($ROW) ."' WHERE `id` = ". (int)$ID .";", $connection) or die(mysql_error());
        mysql_close($connection) or die(mysql_error());
    }

    function _DUMP_WriteDumpInDB($connection, $DUMP, $CHAR_NAME, $CHAR_ACCOUNT_ID, $RealmID,
        $o_Account, $o_Password, $O_REALMLIST, $O_REALM, $o_URL, $GUID, $GM_ACCOUNT, $ERROR) {
        $query = mysql_query("INSERT INTO `account_transfer`(
        `cStatus`,`cRealm`,`oAccount`,`oPassword`,`oRealmlist`,`oRealm`,`oServer`,`cDump`,`cNameOLD`,`cNameNEW`,`cAccount`,`GUID`,`gmAccount`) VALUES (
        5,'". _Y($RealmID) ."','". _Y($o_Account) ."','". _Y($o_Password) ."','". _Y($O_REALMLIST) ."','". _Y($O_REALM) ."','". _Y($o_URL) ."'
        ,'". _Y($DUMP) ."','". _Y($CHAR_NAME) ."','". _Y($CHAR_NAME) ."',". (int)$CHAR_ACCOUNT_ID .",". (int)$GUID .",". (int)$GM_ACCOUNT .");", $connection) or die(mysql_error($ERROR));
        $ID = mysql_insert_id($connection);
        mysql_close($connection) or die(mysql_error());
        return $ID;
    }

    function _checkRiding($SKILL, $CUR, $connection, $GUID, $LEVEL) {
        $SpellID    = -1;
        switch($SKILL) {
            case "RIDING":          // enGB
            case "MONTE":           // frFR
            case "REITEN":          // deDE
            case "EQUITACIÓN":      // esES
            case "ВЕРХОВАЯ ЕЗДА":   // ruRU
                switch($CUR) {
                    case 75:    $SpellID = 33388;   break;
                    case 150:   $SpellID = 33391;   break;
                    case 225:   $SpellID = 34090;   break;
                    case 300:   $SpellID = 34091;
                        if($LEVEL == 80)
                            _LearnSeparateSpell(54197, $GUID, $connection);
                        break;
                    default: return false;
                }
                _LearnSeparateSpell($SpellID, $GUID, $connection);
                return true;
            default: return false;
       }
    }

    function _DKMigration($GUID) {
        return "INSERT INTO `character_queststatus_rewarded`(`guid`,`quest`) VALUES
            (". (int)$GUID .", 12593),  (". (int)$GUID .", 12619),  (". (int)$GUID .", 12641),  (". (int)$GUID .", 12657),
            (". (int)$GUID .", 12670),  (". (int)$GUID .", 12678),  (". (int)$GUID .", 12679),  (". (int)$GUID .", 12680),
            (". (int)$GUID .", 12687),  (". (int)$GUID .", 12697),  (". (int)$GUID .", 12698),  (". (int)$GUID .", 12700),
            (". (int)$GUID .", 12701),  (". (int)$GUID .", 12706),  (". (int)$GUID .", 12711),  (". (int)$GUID .", 12714),
            (". (int)$GUID .", 12715),  (". (int)$GUID .", 12716),  (". (int)$GUID .", 12717),  (". (int)$GUID .", 12719),
            (". (int)$GUID .", 12720),  (". (int)$GUID .", 12722),  (". (int)$GUID .", 12723),  (". (int)$GUID .", 12724),
            (". (int)$GUID .", 12725),  (". (int)$GUID .", 12727),  (". (int)$GUID .", 12733),  (". (int)$GUID .", 12738),
            (". (int)$GUID .", 12747), /* RACE       */
            (". (int)$GUID .", 13189), /* HORDE      */
            (". (int)$GUID .", 13188), /* ALLIANCE   */
            (". (int)$GUID .", 12751),  (". (int)$GUID .", 12754),  (". (int)$GUID .", 12755),  (". (int)$GUID .", 12756),
            (". (int)$GUID .", 12757),  (". (int)$GUID .", 12778),  (". (int)$GUID .", 12779),  (". (int)$GUID .", 12800),
            (". (int)$GUID .", 12801),  (". (int)$GUID .", 12842),  (". (int)$GUID .", 12848),  (". (int)$GUID .", 12849),
            (". (int)$GUID .", 12850),  (". (int)$GUID .", 13165),  (". (int)$GUID .", 13166);";
    }

    function _SOHMigration($GUID) {
        return "INSERT INTO `character_queststatus_rewarded`(`guid`,`quest`) VALUES
            (". (int)$GUID .", 12841),  (". (int)$GUID .", 12843),  (". (int)$GUID .", 12846),  (". (int)$GUID .", 12851),
            (". (int)$GUID .", 12856),  (". (int)$GUID .", 12886),  (". (int)$GUID .", 12900),  (". (int)$GUID .", 12905),
            (". (int)$GUID .", 12906),  (". (int)$GUID .", 12907),  (". (int)$GUID .", 12908),  (". (int)$GUID .", 12915),
            (". (int)$GUID .", 12921),  (". (int)$GUID .", 12924),  (". (int)$GUID .", 12969),  (". (int)$GUID .", 12970),
            (". (int)$GUID .", 12971),  (". (int)$GUID .", 12972),  (". (int)$GUID .", 12983),  (". (int)$GUID .", 12996),
            (". (int)$GUID .", 12997),  (". (int)$GUID .", 13061),  (". (int)$GUID .", 13062),  (". (int)$GUID .", 13063),
            (". (int)$GUID .", 13064);";
    }

    function _doesCharacterExistsOnAccount($DBUser, $DBPassword, $RealmID, $GUID, $SESSION_ARRAY_EXISTS = false) {
        if($SESSION_ARRAY_EXISTS)
            return true;
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        $query  = mysql_query("SELECT `guid` FROM `characters` WHERE `guid` = ". (int)$GUID ." AND `account` = '". (int)_getAccountID() ."';", $connection) or die(mysql_error());
        mysql_close($connection) or die(mysql_error());
        return mysql_num_rows($query) > 0;
    }

    function _doesRealmExists($RealmID, $DBUser, $DBPassword, $SESSION_ARRAY_EXISTS = false) {
        if($SESSION_ARRAY_EXISTS)
            return true;
        global $AccountDBHost, $AccountDB;
        $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
        $query      = mysql_query("SELECT `id` FROM `realmlist` WHERE `id` = ". (int)$RealmID ." ;", $connection) or die(mysql_error());
        mysql_close($connection) or die(mysql_error());
        return mysql_num_rows($query) > 0;
    }

    function _instant80LevelForCharacter($GUID, $RealmID, $DBUser, $DBPassword) {
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        mysql_query("UPDATE `characters` SET `level` = 80 WHERE `guid` = ". (int)$GUID .";", $connection) or die(mysql_error());
        mysql_close($connection) or die(mysql_error());
    }

    function _addFlag_Character($GUID, $RealmID, $DBUser, $DBPassword, $FLAG) {
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        mysql_query("UPDATE `characters` SET `at_login` = `at_login`|". $FLAG ." WHERE `guid` = ". (int)$GUID .";", $connection) or die(mysql_error());
        mysql_close($connection) or die(mysql_error());
    }

    function _teleportCharacterToDalaran($GUID, $RealmID, $DBUser, $DBPassword) {
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        mysql_query("UPDATE `characters` SET
        `position_x` = 5741.36,
        `position_y` = 626.982,
        `position_z` = 648.354,
        `map`        = 571,
        `zone`       = 4395
            WHERE `guid` = ". (int)$GUID .";", $connection) or die(mysql_error());
        mysql_close($connection) or die(mysql_error());
    }

    function _instantExaltedReputationWithSelectedFaction($GUID, $FACTION, $RealmID, $DBUser, $DBPassword) {
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        mysql_query("UPDATE `character_reputation` SET `standing` = 42999, `flags` = 17 WHERE `guid` = ". (int)$GUID ." AND `faction` = ". (int)$FACTION .";", $connection) or die(mysql_error());
        mysql_close($connection) or die(mysql_error());
    }

    function _teleportCharacterUnstruck($DBUser, $DBPassword, $SOAPUser, $SOAPPassword, $RealmID, $GUID, $NAME) {
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        $query      = mysql_query("SELECT `race` FROM `characters` WHERE `guid` = ". (int)$GUID .";", $connection) or die(mysql_error());
        $result     = mysql_fetch_array($query);
        if(!_isHorde($result['race']))
            /* Stormwind */
            _SOAP_SentRemoteCommand($SOAPUser, $SOAPPassword, $RealmID, trim(".tele name ". $NAME ." stormwind"));
        else
            /* Orgrimmar */
            _SOAP_SentRemoteCommand($SOAPUser, $SOAPPassword, $RealmID, trim(".tele name ". $NAME ." orgrimmar"));
        mysql_close($connection) or die(mysql_error());
    }

    function _removeDeserterDebuffFromCharacter($GUID, $RealmID, $DBUser, $DBPassword) {
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        mysql_query("DELETE FROM `character_aura` WHERE `guid` = ". (int)$GUID ." AND `spell` IN (26013, 71041);", $connection) or die(mysql_error());
        mysql_close($connection) or die(mysql_error());
    }

    function _doesCharacterHasDeserterDebuff($GUID, $RealmID, $DBUser, $DBPassword) {
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        $query      = mysql_query("SELECT `guid` FROM `character_aura` WHERE `guid` = ". (int)$GUID ." AND `spell` IN (26013, 71041);", $connection) or die(mysql_error());
        mysql_close($connection) or die(mysql_error());
        return mysql_num_rows($query) > 0;
    }

    function _doesCharacterHaveActiveQuests($GUID, $RealmID, $DBUser, $DBPassword) {
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        $query      = mysql_query("SELECT COUNT(*) FROM `character_queststatus` WHERE `guid` = ". (int)$GUID ." AND `status` = 3;", $connection) or die(mysql_error());
        $row        = mysql_fetch_array($query);
        mysql_close($connection) or die(mysql_error());
        return $row[0] > 0;
    }

    function _doesCharacterHaveAFlag($GUID, $RealmID, $DBUser, $DBPassword, $FLAG) {
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        $query      = mysql_query("SELECT `guid` FROM `characters` WHERE `at_login` & ". $FLAG ." AND `guid` = ". (int)$GUID ." AND `account` = ". (int)_getAccountID() .";", $connection) or die(mysql_error());
        mysql_close($connection) or die(mysql_error());
        return mysql_num_rows($query) > 0;
    }

    function _getMythCoinsAmount($connection, $ACC = null) {
        if(!isset($ACC))
            $ACC = _getAccountID();
        $query  = mysql_query("SELECT `myth_coins` FROM `account_details` WHERE `id` = ". $ACC ." ;", $connection) or die(mysql_error());
        $row    = mysql_fetch_array($query);
        return $row[0];
    }

    function _getGoldCoinsAmount($connection, $GUID) {
        $query  = mysql_query("SELECT `money` FROM `characters` WHERE `guid` = ". $GUID ." ;", $connection) or die(mysql_error());
        $row    = mysql_fetch_array($query);
        return intval($row[0] / 10000);
    }

    function _lookForAccount($connection, $U) {
        $query  = mysql_query("SELECT `id` FROM `account` WHERE `username` = '". _Y($U) ."';", $connection) or die(mysql_error());
        $row    = mysql_fetch_array($query);
        return isset($row[0]) ? $row[0] : -1;
    }

    function _FORM_CHAR_ARRAY($AccountDBHost, $AccountDB, $DBUser, $DBPassword, $RealmID, $GUID) {
        $SA = null;
        $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
        $query  = mysql_query("SELECT `name` FROM `realmlist` WHERE `id` = ". (int)$RealmID .";", $connection) or die(mysql_error());
        while($result = mysql_fetch_array($query))
            $RealmName = $result['name'];

        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        $query  = mysql_query("SELECT `guid`,`name`,`level`,`race`,`class`,`gender`,`money`,`totalKills` FROM `characters` WHERE `account` = ". (int)_getAccountID() ." AND `guid` = ". $GUID .";", $connection) or die(mysql_error());
        while($result = mysql_fetch_array($query)) {
            $SA['CharName']     = $result['name'];
            $SA['CharGUID']     = $result['guid'];
            $SA['CharClass']    = $result['class'];
            $SA['CharRace']     = $result['race'];
            $SA['CharLevel']    = $result['level'];
            $SA['CharGender']   = $result['gender'];
            $SA['CharMoney']    = $result['money'];
            $SA['CharHKills']   = $result['totalKills'];
            $SA['RealmName']    = $RealmName;
            $SA['RealmID']      = $RealmID;
        }
        mysql_close($connection) or die(mysql_error());
        $_SESSION['TCA'] = $SA;
        return $SA;
    }
?>