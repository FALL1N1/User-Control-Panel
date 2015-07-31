<?php

    include_once('_template/_header.php');

    if(!_getUsername())
        Header('Location: index.php');

    $REALSON    = null;
    $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);

    $query  = mysql_query("SELECT FROM_UNIXTIME(`bandate`) AS `BAN_DATE`, FROM_UNIXTIME(`unbandate`) AS `UNBAN_DATE`,`bannedby`,`banreason` FROM `account_banned` WHERE `id` = ". (int)_getAccountID() ." AND `active` = 1;", $connection) or die(mysql_error());
    $result = mysql_fetch_array($query);
    if(!$result) {
        $_SESSION['TCA'] = false;
        echo _getAlreadyEffectSTR("<h2>". $L[38] ."</h2>");
    } else {
        if(isset($_SESSION['TCA']) && $_SESSION['TCA']) {
            if(_isEnoughMythCoins($PriceForUnban, $connection)) {
                _SpendMythCoins($PriceForUnban, 9, $_SESSION['AccountUN'], (int)_getAccountID(), 0, 0, !empty($result['bannedby']) ? $result['bannedby'] : "Server" . " : " . $result['banreason'], $connection);
                //_UnbanAccount($connection);
                $REALSON = _GDiv($L[17]);
                $_SESSION['TCA'] = false;
            } else $REALSON = _RDiv($L[18]);
        } else $_SESSION['TCA'] = true;
        echo "
        <div class = 'text-center'>". $REALSON ."
            <h2>". $L[94] ."</h2>
        </div>
        <fieldset>
            <div class = 'charBox alert alert-error'>
                <table>
                    <tr>
                        <td width = '73'>
                            <img class = 'img-rounded' src = '_template/img/_faces/0-0-0-0.png' border = 'none'>
                        </td>
                        <td width = '340'>
                        <h4>". $result['banreason'] ."</h4>
                            <span class = ''>". $L[165] ."</span>". _AU_BAN_AUTHOR_STR($result['bannedby']) ."<br/>
                            <span class = ''>". $L[166] ."</span>". $result['BAN_DATE'] ."<br/>
                            <span class = ''>". $L[167] ."</span>". $result['UNBAN_DATE'] ."
                        </td>
                    </tr>
                </table>
            </div>
            <div class = 'charBox alert alert-success'>
                <table>
                    <tr>
                        <td width = '73'>
                            <img class = 'img-rounded' src = '_template/img/_faces/0-0-0-0.png' border = 'none'>
                        </td>
                        <td width = '340'>
                        <h4>". $result['banreason'] ."</h4>
                            <span class = ''>". $L[165] ."</span>". _AU_BAN_AUTHOR_STR($result['bannedby']) ."<br/>
                            <span class = ''>". $L[166] ."</span>". $result['BAN_DATE'] ."<br/>
                            <span class = ''>". $L[167] ."</span>". $L[168] ."
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
        <fieldset>
            <form action = ". $_SERVER['PHP_SELF'] ." method = 'POST'>
                ". _BDiv(_PRICE_STR($PriceForUnban)) ."
                <div class = 'text-center'>
                    <button class = 'btn btn-primary' type = 'submit'>". _getPriceButtonSTR($PriceForUnban) ."</button>
                </div>
            </form>
        </fieldset>";
    }

    mysql_close($connection) or die(mysql_error());
    include_once('_template/_footer.php');
    ob_end_flush();

    function _AU_BAN_AUTHOR_STR($X) { global $L; return empty($X) ? $L[39] : $X; }
?>