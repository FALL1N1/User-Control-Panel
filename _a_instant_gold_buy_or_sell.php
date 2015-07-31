<?php

    include_once('_template/_header.php');

    if(!_getUsername())
        Header('Location: index.php');

    $SA         = null;
    $RealmID    = null;
    $GUID       = null;
    $REALSON    = null;
    $ExchangeArray = array(
        "sell" => array(
            25000 => 1,
            50000 => 2,
            100000 => 4,
            200000 => 10
        ),
        "buy" => array(
            1 => 10000,
            2 => 22500,
            3 => 55000,
            4 => 80000
        )
    );

    if(isset($_GET['realmid']) && isset($_GET['guid'])) {
        unset($_SESSION['TCA']);
        $RealmID    = (int)$_GET['realmid'];
        $GUID       = (int)$_GET['guid'];
        if(!is_numeric($RealmID) || !is_numeric($GUID))
            Header('Location: _userside.php');
    } else if(isset($_SESSION['TCA']) && (isset($_GET['sell']) || isset($_GET['buy']))) {
        $SA         = $_SESSION['TCA'];
        $RealmID    = $_SESSION['TCA']['RealmID'];
        $RealmName  = $_SESSION['TCA']['RealmName'];
        $GUID       = $_SESSION['TCA']['CharGUID'];
        $CharName   = $_SESSION['TCA']['CharName'];
        $action     = isset($_GET['sell']) ? "sell" : "buy";
        $index      = isset($_GET['sell']) ? (int)$_GET['sell'] : (int)$_GET['buy'];
        $amount     = null;
        if(isset($ExchangeArray[$action][$index])) {
            $amount = $ExchangeArray[$action][$index];
            if($action == "sell") {
                $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
                if(_isEnoughGoldCoins($index, $GUID, $connection)) {
                    _SpendGoldCoins($index, $GUID, $connection);
                    mysql_close($connection) or die(mysql_error());
                    $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
                    _GiveMythCoins($amount, "Gold exchange G:". $index ." for F: ". $amount, $connection);
                    mysql_close($connection) or die(mysql_error());
                    $REALSON = _GDiv($L[17]);
                } else {
                    $REALSON = _getNotEnoughtGoldSTR();
                    mysql_close($connection) or die(mysql_error());
                }
            } else {
                $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
                if(_isEnoughMythCoins($index, $connection)) {
                    _SpendMythCoins($index, 17, $CharName, $GUID, $RealmName, $RealmID, "Gold buy amount: ". $amount, $connection);
                    mysql_close($connection) or die(mysql_error());
                    $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
                    _GiveGoldCoins($amount, $GUID, $connection);
                    mysql_close($connection) or die(mysql_error());
                    $REALSON = _GDiv($L[17]);
                } else {
                    mysql_close($connection) or die(mysql_error());
                    $REALSON = _getNotEnoughtFireSTR();
                }
            }
        }
        //unset($_SESSION['TCA']);
    } else Header('Location: _userside.php'); // die("EXEPTION");

    if(_doesRealmExists($RealmID, $DBUser, $DBPassword, isset($SA))) {
        if(_doesCharacterExistsOnAccount($DBUser, $DBPassword, $RealmID, $GUID, isset($SA))) {
            if(_doesCharacterNotOnlineATM($DBUser, $DBPassword, $RealmID, $GUID)) {
                _FORM_INSTANT_EXT_REPUTATION(_FORM_CHAR_ARRAY($AccountDBHost, $AccountDB, $DBUser, $DBPassword, $RealmID, $GUID) /* CHECK FOR SESSION ARRAY */,
                                            $RealmID, $DBUser, $DBPassword, $GUID, $PriceForExaltedReputation, $REALSON);
            } else echo _RDiv($L[60]);
        } else echo _RDiv($L[9]);
    } else echo _RDiv($L[9]);

    include_once('_template/_footer.php');
    ob_end_flush();

    function _FORM_INSTANT_EXT_REPUTATION($SA, $RealmID, $DBUser, $DBPassword, $GUID,
                                            $PRICE, $REALSON = "") {
        global $L;
        global $ExchangeArray;
        echo "
        <form action = ". $_SERVER['PHP_SELF'] ." method = 'POST'>
        <fieldset>
        <div class = 'text-center'>". $REALSON ."
            <h2>". $L[265] ."</h2>
            <fieldset>";
        _FORM_CHAR_BLOCK($SA, null, true);
        _FORM_CHAR_BLOCK($SA, null, true);
        $BUY = isset($_GET['buy']) ? "class = 'active'" : null;
        $SELL = null;
        if(!isset($BUY))
            $SELL = isset($_GET['sell']) ? "class = 'active'" : "class = 'active'";
        echo "
            <ul class = 'nav nav-tabs'>
            <li ". $BUY ."><a href = '?buy'>". $L[268] ."</a></li>
            <li ". $SELL ."><a href = '?sell'>". $L[267] ."</a></li>
            </ul>
            </fieldset>
            </div>";
        $action = isset($_GET['sell']) ? "sell" : "buy";
        foreach($ExchangeArray[$action] as $key => $value) {
            if($action === "buy") {
                echo "
                    <a href = '?". $action ."=". $key ."'><div class = 'alert service stockBlock'>
                        <div class = 'service_icon'><img src = '_template/img/reputation.png'></div>
                        <h4>". $value ." <img alt = '' src = '_template/img/gold_coin.png'></h4>
                        <div class = 'service_desc'>". $key ." <i class = 'icon-fire'></i></div>
                    </div></a>";
            } else {
                echo "
                    <a href = '?". $action ."=". $key ."'><div class = 'alert service stockBlock'>
                        <div class = 'service_icon'><img src = '_template/img/reputation.png'></div>
                        <h4>". $value ." <i class = 'icon-fire'></i></h4>
                        <div class = 'service_desc'>". $key ." <img alt = '' src = '_template/img/gold_coin.png'></div>
                    </div></a>";
            }
        }
    echo "</fieldset>";
    }
?>