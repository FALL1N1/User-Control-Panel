<?php


    include_once('_template/_header.php');

    if(!_getUsername())
        Header('Location: index.php');

    unset($_SESSION['TCA']);
    $URL = $_GET['go'];
    $_80 = isset($_GET['lvl']) ? $_GET['lvl'] : 0;
    _PushCharactersTable($AccountDBHost, $DBUser, $DBPassword, $AccountDB, $URL, $_80);

    include_once('_template/_footer.php');
    ob_end_flush();

    function _PushCharactersTable($AccountDBHost, $DBUser, $DBPassword, $AccountDB, $URL, $_80) {
        global $L;
        $connection  = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
        $REALM_ARRAY = array();
        $CHAR_ARRAY  = array();
        $query  = mysql_query("SELECT `id`,`name` FROM `realmlist`;", $connection) or die(mysql_error());
        while($result = mysql_fetch_array($query))
            $REALM_ARRAY[$result['name']] = $result['id'];

        foreach($REALM_ARRAY as $RealmName => $RealmID)
        {
            $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
            $query  = mysql_query("SELECT `guid`,`name`,`level`,`race`,`class`,`gender` FROM `characters` WHERE `account` = ". (int)_getAccountID() .";", $connection) or die(mysql_error());
            while($result = mysql_fetch_array($query)) {
                if($_80 == 80 && $result['level'] < 80)
                    continue;
                $CHAR_ARRAY[$RealmID . $result['guid']]['CharName']     = $result['name'];
                $CHAR_ARRAY[$RealmID . $result['guid']]['CharGUID']     = $result['guid'];
                $CHAR_ARRAY[$RealmID . $result['guid']]['CharClass']    = $result['class'];
                $CHAR_ARRAY[$RealmID . $result['guid']]['CharRace']     = $result['race'];
                $CHAR_ARRAY[$RealmID . $result['guid']]['CharLevel']    = $result['level'];
                $CHAR_ARRAY[$RealmID . $result['guid']]['CharGender']   = $result['gender'];
                $CHAR_ARRAY[$RealmID . $result['guid']]['RealmName']    = $RealmName;
                $CHAR_ARRAY[$RealmID . $result['guid']]['RealmID']      = $RealmID;
            }
            mysql_close($connection) or die(mysql_error());
        }
        $TRIGGER = "";
        $H2 = empty($CHAR_ARRAY) ? $L[62] : $L[61];
        echo "
            <div class = 'text-center'>
                <h2>". $H2 ."</h2>
                <fieldset>";
        foreach($CHAR_ARRAY as $key => $value)
        {
            if($TRIGGER != $value['RealmID'])
            {
                echo "<legend>". $value['RealmName'] ."</legend>";
                $TRIGGER = $value['RealmID'];
            }

            echo "
            <a href = ". $URL ."?realmid=". $value['RealmID'] ."&guid=". $value['CharGUID'] ."><div style = 'width:273px;' class = 'charBox alert'>
            <table>
                <tr>
                    <td width = '73'>
                        <img class = 'img-rounded' src = '_template/img/_faces/".
                        _getAvatarPicString($value['CharLevel'], $value['CharGender'], $value['CharRace'], $value['CharClass']) ."'
                        border = 'none'>
                    </td>
                    <td width = '200'>
                    <h4>". $value['CharName'] ."</h4>
                        <span class = ''>". _getCharacter_L_R_S_C_STR($value['CharLevel'], $value['CharGender'], $value['CharRace'], $value['CharClass']) ."</span><br/>
                    </td>
                </tr>
            </table>
            </div></a>";
        }
        echo "</fieldset>
            </div>";
    }
?>