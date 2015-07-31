<?php


    include_once('_template/_header.php');

    if(!_getUsername())
        Header('Location: index.php');

    $SA         = null;
    $RealmID    = null;
    $GUID       = null;
    $REALSON    = null;
    $ItemID     = null;

    if(isset($_GET['itemID']) && is_numeric($_GET['itemID']))
    {
        $ItemID = (int)$_GET['itemID'];
        $_SESSION['TCA']['SItem'] = $ItemID;
    }

    if(isset($_GET['realmid']) && isset($_GET['guid'])) {
        unset($_SESSION['TCA']);
        $RealmID    = (int)$_GET['realmid'];
        $GUID       = (int)$_GET['guid'];
        if(!is_numeric($RealmID) || !is_numeric($GUID))
            Header('Location: _userside.php');
    } else if(isset($_POST['isItemSame'])
           && empty($_POST['itemID'])
           && isset($_SESSION['TCA']['SItem'])
           && $_POST['isItemSame'] == $_SESSION['TCA']['SItem']) {
        $SA         = $_SESSION['TCA'];
        $RealmID    = $_SESSION['TCA']['RealmID'];
        $RealmName  = $_SESSION['TCA']['RealmName'];
        $GUID       = $_SESSION['TCA']['CharGUID'];
        $CharName   = $_SESSION['TCA']['CharName'];

        $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
        if(_isEnoughMythCoins($_SESSION['TCA']['SItemPrice'], $connection)) {
            _SpendMythCoins($_SESSION['TCA']['SItemPrice'], 14, $CharName, $GUID, $RealmName, $RealmID, "
                <a href = 'http://_REPLACE_.wowhead.com/item=". $_SESSION['TCA']['SItem'] ."' target = '_blank'></a>
                ", $connection);
            mysql_close($connection) or die(mysql_error());
            $REALSON = _GDiv($L[238]);
            _sendSingleItem($CharName, $_SESSION['TCA']['SItem'], $RealmID, $SOAPUser, $SOAPPassword);
        } else {
            mysql_close($connection) or die(mysql_error());
            $REALSON = _RDiv($L[18]);
        }
    } else if(isset($_SESSION['TCA'])) {
        if(isset($_POST['itemID'])) { 
            $ItemID     = (int)$_POST['itemID'];
            $_SESSION['TCA']['SItem'] = $ItemID;
        }
        $SA         = $_SESSION['TCA'];
        $RealmID    = $_SESSION['TCA']['RealmID'];
        $GUID       = $_SESSION['TCA']['CharGUID'];
    } else Header('Location: _userside.php'); // die("EXEPTION");

    if(_doesRealmExists($RealmID, $DBUser, $DBPassword)) {
        if(_doesCharacterExistsOnAccount($DBUser, $DBPassword, $RealmID, $GUID)) {
            if(_doesCharacterNotOnlineATM($DBUser, $DBPassword, $RealmID, $GUID)) {
                _FORM_ITEM_SHOP($SA ? $SA : _FORM_CHAR_ARRAY($AccountDBHost, $AccountDB, $DBUser, $DBPassword, $RealmID, $GUID) /* CHECK FOR SESSION ARRAY */,
                    $RealmID, $DBUser, $DBPassword, $GUID,
                    isset($ItemID) ? $ItemID : null, $MinPriceForItem, $MultiplicatorForItemPrice, $MinPriceForDisplayInTable, $ItemDiscountMode, $DiscountAmmount, $REALSON);
            } else echo _RDiv($L[60]);
        } else echo _RDiv($L[9]);
    } else echo _RDiv($L[9]);

    include_once('_template/_footer.php');
    ob_end_flush();

    function _FORM_ITEM_SHOP($SA, $RealmID, $DBUser, $DBPassword, $GUID, $ItemID, $MinPrice, $PriceX, $DisplayPrice, $ITEM_DISCOUNT_MODE, $DISCOUNT_CONF, $REALSON = "") {
        global $L, $AccountDBHost, $AccountDB, $PageListRecordsAmmount;
        echo "
        <fieldset>
            <div class = 'text-center'>". $REALSON ."
                <h2>". $L[82] ."</h2>";
        _FORM_CHAR_BLOCK($SA, null, true);
        echo "</fieldset>";
        $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _WorldDBSwitch($RealmID));
// -----------------------------------------------------------------
        if(isset($ItemID)) {
            $query      = mysql_query("
                SELECT
                  `entry` AS `id`,
                  `class`,
                  `subclass`,
                  `ItemLevel`,
                  `RequiredLevel`,
                  `Quality`,
                  `name`
                FROM `item_template`
            WHERE `entry` = ". $ItemID .";", $connection) or die(mysql_error());
            $QueryResult = mysql_fetch_array($query);
            if(!empty($QueryResult['id'])) {
                $PRICE    = _CalculatePrise($QueryResult, $MinPrice, $PriceX);
                $DISCOUNT = null;
                if($PRICE > 1 && $ITEM_DISCOUNT_MODE)
                    $DISCOUNT = round($PRICE - $PRICE * $DISCOUNT_CONF);
                $_SESSION['TCA']['SItemPrice'] = isset($DISCOUNT) ? $DISCOUNT : $PRICE;
                $_SESSION['TCA']['SItemName'] = $QueryResult['name'];
                echo "
                <fieldset class = 'text-center'>
                <div style = 'width: 800px;' class = 'alert service alert-success'>
                    <div class = 'service_icon'></div>
                    <h4><a href = 'http://old.wowhead.com/item=". $QueryResult['id']  ."' target = '_blank'></a></h4>
                    <div class = 'service_desc'>". _PRICE_STR($PRICE, true, isset($DISCOUNT) && $PRICE > $DISCOUNT ? $DISCOUNT : null) ."</div>
                </div>
                </fieldset></br>";
            }
        }
// -----------------------------------------------------------------
        $ITEM_CLASS = _refleshSessionValue(
        isset($_SESSION['TCA']['ItemClass']) ? $_SESSION['TCA']['ItemClass'] : null,
        isset($_GET['C']) ? (int)$_GET['C'] : null, 2);
        if(!isset($_SESSION['TCA']['ItemClass']) || $_SESSION['TCA']['ItemClass'] != $ITEM_CLASS)
           { unset($_GET['page']); unset($_SESSION['TCA']['P']); }
        $_SESSION['TCA']['ItemClass'] = $ITEM_CLASS;

        $PAGE_INDEX = _refleshSessionValue(
        isset($_SESSION['TCA']['P']) ? $_SESSION['TCA']['P'] : null,
        isset($_GET['page']) ? (int)$_GET['page'] : null, 1);
        $PAGE_INDEX = $PAGE_INDEX < 1 ? 1 : $PAGE_INDEX;
        $_SESSION['TCA']['P'] = $PAGE_INDEX;
        if(isset($ITEM_CLASS) && $ITEM_CLASS === 7)
            $ITEM_CLASS_Q_STR = isset($ITEM_CLASS) ? "WHERE `class` IN (5, 6, 7) ORDER BY `class`,`itemlevel`" : null;
        else
            $ITEM_CLASS_Q_STR = isset($ITEM_CLASS) ? "WHERE `class` = ". $ITEM_CLASS ." ORDER BY `itemlevel`" : null;
        
        $query = mysql_query("
            SELECT
              `entry` AS `id`,
              `class`,
              `subclass`,
              `ItemLevel`,
              `RequiredLevel`,
              `Quality`,
              `name`
            FROM `item_template` ". $ITEM_CLASS_Q_STR ."
        DESC LIMIT ". ($PAGE_INDEX * $PageListRecordsAmmount - $PageListRecordsAmmount) .", ". $PageListRecordsAmmount .";", $connection) or die(mysql_error());
        mysql_close($connection);
        echo "
            <fieldset class = 'text-center'>
            <form action = ". $_SERVER['PHP_SELF'] ." method = 'POST'>
            <div class = 'clearfix'>";
        if(isset($ItemID)) {
            echo "     
                <input name = 'itemID' type = 'text' placeholder = '". $L[19] ."'>
            </div>
            <p>
                <button class = 'btn btn-primary' type = 'submit'>". _getPriceButtonSTR(11) ."</button>
            </p>
            <input type = 'hidden' name = 'isItemSame' value = '". $ItemID ."'/>";
        } else {
            echo "
                <input name = 'itemID' type = 'text' placeholder = '". $L[49] ."'>
            </div>
            <p>
                <button class = 'btn btn-primary' type = 'submit'>". $L[48] ."</button>
            </p>";
        }
        echo "
            </form>
            </fieldset>
            <ul class = 'nav nav-tabs'>
                <li". _getBISelector(2, $ITEM_CLASS) ."><a href = '?C=2'>". $L[282] ."</a></li> <!-- Weapon -->
                <li". _getBISelector(4, $ITEM_CLASS) ."><a href = '?C=4'>". $L[284] ."</a></li> <!-- Armor -->
                <li". _getBISelector(16, $ITEM_CLASS) ."><a href = '?C=16'>". $L[296] ."</a></li> <!-- Glyph -->
                <li". _getBISelector(3, $ITEM_CLASS) ."><a href = '?C=3'>". $L[283] ."</a></li> <!-- Gem -->
                <li". _getBISelector(9, $ITEM_CLASS) ."><a href = '?C=9'>". $L[289] ."</a></li> <!-- Recipe -->

                <li". _getBISelector(0, $ITEM_CLASS) ."><a href = '?C=0'>". $L[280] ."</a></li> <!-- Consumable -->
                <li". _getBISelector(1, $ITEM_CLASS) ."><a href = '?C=1'>". $L[281] ."</a></li> <!-- Container --> 
                <li". _getBISelector(7, $ITEM_CLASS, "title = '". $L[285] .", ". $L[286] .", ". $L[287] ."' ") ."><a href = '?C=7'>". $L[287] ."</a></li> <!-- Trade Goods -->
                <li". _getBISelector(12, $ITEM_CLASS) ."><a href = '?C=12'>". $L[292] ."</a></li> <!-- Quest -->
                <li". _getBISelector(13, $ITEM_CLASS) ."><a href = '?C=13'>". $L[293] ."</a></li> <!-- Key -->
                <li". _getBISelector(15, $ITEM_CLASS) ."><a href = '?C=15'>". $L[295] ."</a></li> <!-- Miscellaneous -->
            </ul>
            ". _pushPageNav($PAGE_INDEX) ."
            <table class = 'table table-condensed'>
                <tr class = 'menuBar'>
                    <td>". $L[275] ."</td>
                    <td>". $L[64] ."</td>
                    <td></td>
                </tr>";
        while($QueryResult    = mysql_fetch_array($query)) {
            $PRICE    = _CalculatePrise($QueryResult, $MinPrice, $PriceX);
            $DISCOUNT = null;
            if($PRICE > 1 && $ITEM_DISCOUNT_MODE)
                $DISCOUNT = round($PRICE - $PRICE * 0.1);
            $class = isset($DISCOUNT) && $PRICE > $DISCOUNT ? "success" : "info";
            echo "
                <tr class = '". $class ."'>
                <td><a href = 'http://". _getWHLanguage() ."wowhead.com/item=". $QueryResult['id']  ."' target = '_blank'></a></td>";
            if(isset($DISCOUNT) && $PRICE > $DISCOUNT)
                echo "<td>". _PRICE_STR($PRICE, true, $DISCOUNT) ."</td>";
            else
                echo "
                <td>". _PRICE_STR($PRICE, true) ."</td>";
            echo "<td><a href = '". _AddGETAtributeToURL("itemID", $QueryResult['id']) ."'>". $L[299] ."</a></td>
            </tr>";
        }
        echo "
            </table>". _pushPageNav($PAGE_INDEX);
    }
    
    function _getBISelector($X, $Y, $TITLE = null) { 
        if($X === $Y)
            return $TITLE ? " class = 'tip active' ". $TITLE : " class = 'active' ";
        else
            return $TITLE ? " class = 'tip' ". $TITLE : " ";
    }

    function _AddGETAtributeToURL($VARIABLE, $VALUE) {
        if(strpos($_SERVER['REQUEST_URI'], "?")) {
            $part       = explode('?', $_SERVER['REQUEST_URI']);
            $part[1]    = null;
            foreach($_GET as $index => $value){
                if($index != $VARIABLE)
                    $part[1] .= $index .'='. $value .'&';
            }
            $part[1] = rtrim($part[1],'&');
            return $part[0] ."?". $part[1] ."&". $VARIABLE ."=". $VALUE;
        } else
            return $_SERVER['REQUEST_URI']. "?". $VARIABLE ."=". $VALUE;
    }

    function _refleshSessionValue($S /* SESSION */, $V /* VALUE */, $D /* DEFAULT */) {
        if(isset($V))
            return $V;
        else if(isset($S))
            return $S;
        return $D;
    }

    function _pushPageNav($PAGE_INDEX, $PAGES = 999) {
        global $L;
        $LEFT   = $PAGE_INDEX < 2 ? "disabled" : "";
        $L_URL  = $PAGE_INDEX < 2 ? "#" : _AddGETAtributeToURL("page", $PAGE_INDEX-1);
        $RIGHT  = $PAGE_INDEX > $PAGES ? "disabled" : "";
        $R_URL  = $PAGE_INDEX > $PAGES ? "#" : _AddGETAtributeToURL("page", $PAGE_INDEX+1);
        return "
            <div class = 'text-center'>
                <a href = '". $L_URL ."' class = 'btn btn-inverse btn-mini ". $LEFT ."'><i class = 'icon-white icon-circle-arrow-left'></i>". $L[298] ."</a>
                <a href = '#' class = 'btn btn-inverse btn-mini disabled'> ". $PAGE_INDEX ." </a>
                <a href = '". $R_URL ."' class = 'btn btn-inverse btn-mini ". $RIGHT ."'>". $L[297] ." <i class = 'icon-white icon-circle-arrow-right'></i></a>
            </div>";
    }
?>