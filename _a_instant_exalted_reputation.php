<?php


    include_once('_template/_header.php');

    if(!_getUsername())
        Header('Location: index.php');

    $SA         = null;
    $RealmID    = null;
    $GUID       = null;
    $REALSON    = null;
    $faction    = null;
    $level      = null;

    if(isset($_SESSION['TCA']) && isset($_GET['faction']) && isset($_GET['level'])) {
        $faction    = (int)$_GET['faction'];
        $level      = (int)$_GET['level'];
        $_SESSION['TCA']['selectedFaction']         = $faction;
        $_SESSION['TCA']['selectedFactionStanding'] = $level;
        $SA         = $_SESSION['TCA'];
        $RealmID    = $_SESSION['TCA']['RealmID'];
        $RealmName  = $_SESSION['TCA']['RealmName'];
        $GUID       = $_SESSION['TCA']['CharGUID'];
        $CharName   = $_SESSION['TCA']['CharName'];
    } else if(((isset($_GET['realmid']) && isset($_GET['guid'])) ||
                isset($_SESSION['TCA']['CharGUID']))
            && !isset($_SESSION['TCA']['selectedFaction'])) {
        $RealmID    = isset($_GET['realmid']) ? (int)$_GET['realmid'] : $_SESSION['TCA']['RealmID'];
        $GUID       = isset($_GET['guid']) ? (int)$_GET['guid'] :  $_SESSION['TCA']['CharGUID'];
        if(!is_numeric($RealmID) || !is_numeric($GUID))
            Header('Location: _userside.php');
    } else if(isset($_SESSION['TCA']['selectedFaction']) && isset($_POST['2nd_checker'])) {
        $faction    = $_SESSION['TCA']['selectedFaction'];
        $level      = $_SESSION['TCA']['selectedFactionStanding'];
        $SA         = $_SESSION['TCA'];
        $RealmID    = $_SESSION['TCA']['RealmID'];
        $RealmName  = $_SESSION['TCA']['RealmName'];
        $GUID       = $_SESSION['TCA']['CharGUID'];
        $CharName   = $_SESSION['TCA']['CharName'];

        $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
        if(_isEnoughMythCoins($PriceForExaltedReputation, $connection)) {
            _SpendMythCoins($PriceForExaltedReputation, 10, $CharName, $GUID, $RealmName, $RealmID, "", $connection);
            mysql_close($connection) or die(mysql_error());
            $REALSON = _GDiv($L[17]);
            unset($_SESSION['TCA']['selectedFaction']);
            _instantExaltedReputationWithSelectedFaction($GUID, $faction, $RealmID, $DBUser, $DBPassword);
            $faction = null;
            $level   = null;
        } else {
            mysql_close($connection) or die(mysql_error());
            $faction = null;
            $level   = null;
            $REALSON = _getNotEnoughtFireSTR();
        }
        unset($_SESSION['TCA']['selectedFaction']);
        // unset($_SESSION['TCA']);
    } else Header('Location: _userside.php'); // die("EXEPTION");

    if(_doesRealmExists($RealmID, $DBUser, $DBPassword, isset($SA))) {
        if(_doesCharacterExistsOnAccount($DBUser, $DBPassword, $RealmID, $GUID, isset($SA))) {
            if(_doesCharacterNotOnlineATM($DBUser, $DBPassword, $RealmID, $GUID)) {
                _FORM_INSTANT_EXT_REPUTATION($SA ? $SA : _FORM_CHAR_ARRAY($AccountDBHost, $AccountDB, $DBUser, $DBPassword, $RealmID, $GUID) /* CHECK FOR SESSION ARRAY */,
                                            $RealmID, $DBUser, $DBPassword, $faction, $level, $PriceForExaltedReputation, $REALSON);
            } else echo _RDiv($L[60]);
        } else echo _RDiv($L[9]);
    } else echo _RDiv($L[9]);

    include_once('_template/_footer.php');
    ob_end_flush();

    function _FORM_INSTANT_EXT_REPUTATION($SA, $RealmID, $DBUser, $DBPassword,
                                            $faction, $level, $PRICE, $REALSON = "") {
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        $query = mysql_query("SELECT `faction`,`standing`,`flags` FROM `character_reputation` WHERE `guid` = ". $SA['CharGUID'] ." AND `flags` & 1;", $connection) or die(mysql_error());
        while($result = mysql_fetch_array($query)) {
            $SA['ReputationList'][$result['faction']]['Level'] = $result['standing'];
            $SA['ReputationList'][$result['faction']]['Flag'] = $result['flags'];
        }

        global $L;
        echo "
        <form action = ". $_SERVER['PHP_SELF'] ." method = 'POST'>
        <fieldset>
            <div class = 'text-center'>". $REALSON ."
                <h2>". $L[88] ."</h2>
        <fieldset>";
        _FORM_CHAR_BLOCK($SA, null, true);

        include_once('_core/f_switch.php');
        if(isset($faction) && isset($level)) {
            $faction = mb_convert_case(_getFactionNameFromID($faction), MB_CASE_TITLE, 'UTF-8');
            echo "<div class = 'alert service ". _getColorOfReputationBlock($level)." reputationBlock'>
                <div class = 'service_icon'><img src = '_template/img/reputation.png'></div>
                <h4>". $faction ."  (". $L[114] .")</h4>
                <div class = 'service_desc'>".
                    _getReputationRank($level)
                ."</div>
            </div>
            <div class = 'alert service alert-success reputationBlock'>
                <div class = 'service_icon'><img src = '_template/img/reputation.png'></div>
                <h4>". $faction ." (". $L[115] .")</h4>
                <div class = 'service_desc'>".
                    _getReputationRank(42001)
                ."</div>
            </div>";
        }
        echo "
        </fieldset>";
        if(isset($faction) && isset($level)) {
            echo "
            <fieldset>
            ". _BDiv(_PRICE_STR($PRICE)) ."
                <form action = ". $_SERVER['PHP_SELF'] ." method = 'POST'>
                    <p><button class = 'btn btn-primary' type = 'submit'>". _getPriceButtonSTR($PRICE) ."</button></p>
                    <input type = 'hidden' name = '2nd_checker' value = '". $faction ."' id = '2nd_checker' />
               </form>
            </div>
            </fieldset>";
        } else {
            echo "
            <legend>". $L[81] ."</legend>
            <fieldset>";
            if(isset($SA['ReputationList'])) {
                foreach($SA['ReputationList'] as $ID => $Array) {
                    $faction = mb_convert_case(_getFactionNameFromID($ID), MB_CASE_TITLE, 'UTF-8');
                    if($faction < 0)
                        continue;
                    echo "<a href = '_a_instant_exalted_reputation.php?faction=". $ID ."&level=". $Array['Level'] ."'><div class = 'alert service ". _getColorOfReputationBlock($Array['Level'])."'>
                        <div class = 'service_icon'><img src = '_template/img/reputation.png'></div>
                        <h4>". $faction ."</h4>
                        <div class = 'service_desc'>".
                            _getReputationRank($Array['Level'])
                        ."</div>
                    </div></a>";
                }
            } else echo _getAlreadyEffectSTR("<h2>". $L[270] ."</h2>");
            echo "
            </fieldset>";
        }
        echo "</fieldset>
            </form>";
    }
?>