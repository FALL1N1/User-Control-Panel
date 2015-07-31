<?php


    include_once('_template/_header.php');

    if(!_getUsername())
        Header('Location: index.php');
    if(isset($_GET['CL']))
        unset($_SESSION['R']);

    $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
    if(_isGMAllowed($connection) && isset($_POST['HowMuch']) && isset($_POST['HowMuchReason']))
        _modifyFirePoints($_POST['HowMuch'], _isset($_POST['HowMuchReason']) ? $_POST['HowMuchReason'] : _getUsername(), $connection, $_SESSION['R']['ID']);

    _AH_FORM($connection, _isGMAllowed($connection));

    mysql_close($connection) or die(mysql_error());
    include_once('_template/_footer.php');
    ob_end_flush();

    function _AH_FORM($connection, $ADMIN_MODE = false) {
        global $L;
        $RealmID    = isset($_GET['R']) ? (int)$_GET['R'] : null;
        $PostQuery  = isset($_POST['U']) ? trim($_POST['U']) : null;
        if(isset($PostQuery))
            $PostQuery  = !empty($PostQuery) ? $PostQuery : null;
        $Done           = null;
        $H              = $ADMIN_MODE ? $L[221] : $L[95];
        $Q_STR          = "SELECT DATE(`whenItDone`),`action`,`text`,`realmName`,`charName`,`myth_coins_spend`,`myth_coins_balance`,`IP`
                                    FROM `account_billing_history` ";
        _getRealmArray($connection);

        echo "
            <div class = 'text-center'>
                <h2>". $H ."</h2>";

        if($ADMIN_MODE) {
            if(isset($PostQuery) && isset($_SESSION['R']['Username']) && $PostQuery === $_SESSION['R']['Username'])
                $Done = true;
            else if(!isset($PostQuery) && isset($_SESSION['R']['Username']))
                $Done = true;
            else $_SESSION['R']['Username'] = isset($PostQuery) ? $PostQuery : null;

            if(!isset($Done) && isset($_SESSION['R']['Username']))
                $_SESSION['R']['ID'] = _lookForAccount($connection, $_SESSION['R']['Username']);

            $PLACEHOLDER    = isset($_SESSION['R']['ID']) ? $_SESSION['R']['Username'] : $L[219];
            if(isset($_SESSION['R']['ID'])) {
                $InfoAccountQuery   = mysql_query("SELECT
                `t2`.`myth_coins`,`t1`.`id`,`t1`.`email`,`t1`.`last_ip`,DATE(`t1`.`joindate`) AS `joindate`,DATE(`t1`.`last_login`) AS `last_login`
                FROM `account` `t1` LEFT JOIN `account_details` `t2` ON `t1`.`id` = `t2`.`id` WHERE `t1`.`id` = ". $_SESSION['R']['ID'] .";", $connection) or die(mysql_error());
                $InfoAccountResult  = mysql_fetch_array($InfoAccountQuery);
                echo "
                <div class = 'modal hide' id = 'ModifyFirePoints'>
                    <div class = 'modal-header'>
                        <button type = 'button' class = 'close' data-dismiss = 'modal'>×</button>
                        <h3>". $_SESSION['R']['Username'] ."</h3>
                    </div>
                    <form action = ". $_SERVER['PHP_SELF'] ." method = 'POST'>
                        <div class = 'modal-body'>
                        <p class = 'text-center'>". $L[253] ."</p>
                            <input name = 'HowMuch' type = 'text' size = '60' placeholder = '". $L[255]  ."'>
                            <input name = 'HowMuchReason' type = 'text' size = '100' placeholder = '". $L[256]  ."'>
                        </div>
                        <div class = 'modal-footer'>
                            <a href = '#' class = 'btn' data-dismiss = 'modal'>". $L[254] ."</a>
                            <input class = 'btn' type = 'submit' value = '". $L[146] ."' />
                        </div>
                    </form>
                </div>
                <div class = 'modal hide' id = 'AccountInfo'>
                    <div class = 'modal-header'>
                        <button type = 'button' class = 'close' data-dismiss = 'modal'>×</button>
                        <h3>". $_SESSION['R']['Username'] ."</h3>
                    </div>
                    <div class = 'modal-body'>
                        <table class = 'table table-hover'>
                            <tr class = 'menuBar'>
                                <td>#</td>
                                <td>". $_SESSION['R']['ID'] ."</td>
                            </tr><tr>
                                <td>". $L[271] ."</td>
                                <td>". $InfoAccountResult['email'] ."</td>
                            </tr><tr>
                                <td>". $L[272] ."</td>
                                <td>". $InfoAccountResult['joindate'] ."</td>
                            </tr><tr>
                                <td>". $L[273] ."</td>
                                <td>". $InfoAccountResult['last_login'] ."</td>
                            </tr><tr>
                                <td>". $L[274] ."</td>
                                <td>". $InfoAccountResult['last_ip'] ."</td>
                            </tr>
                            </tr><tr>
                                <td><i class = 'icon-fire'></i></td>
                                <td>". $InfoAccountResult['myth_coins'] ."</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <script>
                $('ModifyFirePoints').click(function() {
                    $('#ModifyFirePoints').modal('toggle');
                })
                $('AccountInfo').click(function() {
                    $('#AccountInfo').modal('toggle');
                })
                </script>";
            }
            $D = !isset($_SESSION['R']['Username']) ? "disabled = 'disabled'" : "";
            echo "
            <form action = ". $_SERVER['PHP_SELF'] ." method = 'POST'>
                <input name = 'U' type = 'text' size = '60' placeholder = '". $PLACEHOLDER  ."'>
                <input class = 'btn btn-info BillingB' type = 'submit' value = '". $L[220] ."' />
                <a href = '?CL'><input class = 'btn btn-inverse BillingB' ". $D ." value = '". $L[251] ."'/></a>
                <a class = 'btn BillingB' data-toggle = 'modal' href = '#ModifyFirePoints' ". $D .">". $L[252] ."<i class = 'icon-fire'></i></a>
                <a class = 'btn BillingB' data-toggle = 'modal' href = '#AccountInfo' ". $D .">". $L[261] ."</a>
            </form>";

            $STR_P1 = isset($_SESSION['R']['ID']) ? "`id` = ". (int)$_SESSION['R']['ID'] : "";
            $STR_P2 = _AH_REALMID(isset($RealmID) ? $RealmID : -1);

            if(!empty($STR_P1) || !empty($STR_P2))
                $Q_STR .= " WHERE ";
                $Q_STR .= $STR_P1;
            if(!empty($STR_P1) && !empty($STR_P2))
                $Q_STR .= " AND ";
            $Q_STR .= $STR_P2;
        } else {
            $Q_STR .= " WHERE `id` = ". (int)_getAccountID();
            if(isset($RealmID))
                $Q_STR .= " AND ";
            $Q_STR .= _AH_REALMID(isset($RealmID) ? $RealmID : -1);
        }
        $Q_STR .= " ORDER BY `whenItDone` DESC;";

        echo "
            </div>
            <ul class = 'nav nav-tabs'>";
                echo !isset($RealmID) ? "<li class = 'active'>" : "<li>";
                echo "<a href = '?ALL'>". $L[110] ."</a></li>";
            foreach($_SESSION['R']['W'] as $ID => $NA) {
                echo $RealmID === (int)$ID ? "<li class = 'active'>" : "<li>";
                echo "<a href = '?R=". $ID ."'>". $NA['N'] ."</a></li>";
            }
        echo "</ul>";

        $query      = mysql_query($Q_STR, $connection) or die(mysql_error());
        $row_num    = mysql_num_rows($query);
        if($row_num < 1)
            echo _getAlreadyEffectSTR("<h2>". $L[163] ."</h2>");
        else {
            echo "
            <fieldset>
            <table class = 'table table-hover'>
                <tr class = 'menuBar'>
                    <td>". $L[140] ."</td>
                    <td>". $L[141] ."</td>
                    <td>". $L[119] ."</td>
                    <td>". $L[142] ."</td>
                    <td>". $L[64] ."</td>
                    <td>". $L[260] ."</td>
                    <td>". $L[143] ."</td>
                </tr>";
                while($result = mysql_fetch_array($query)) {
                    if($result['action'] === 14 && _isset($result['text'])) {
                        $STR            = explode('_REPLACE_', $result['text']);
                        $result['text'] = $STR[0] . _getWHLanguage() . $STR[1];
                    }
                   echo "<tr class = '". _AH_TR_STR($result, $result['action']) ."'>
                            <td>". $result[0] ."</td>
                            <td>". _AH_STR($result['action'], !empty($result['text']) ? $result['text'] : "") ."</td>
                            <td>". $result['realmName'] ."</td>
                            <td>". $result['charName']  ."</td>
                            <td>". _AH_PRICE_STR($result['myth_coins_spend']) ."</td>
                            <td>". $result['myth_coins_balance'] ." <i class = 'icon-fire'></i></td>
                            <td>". $result['IP']        ."</td>
                        </tr>";
                }
          echo "
            </table>
            </fieldset>";
        }
    }
?>