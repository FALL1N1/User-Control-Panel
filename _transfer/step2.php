<?php


    if(!file_exists('_template/_header.php') || !isset($_SESSION['AccountUN']) || isset($_SESSION['STEP2']))
        Header('Location: ../index.php');

    include_once('_template/_header.php');

    if(isset($_POST['rename'])) {
        $CHAR_NAME  = mb_convert_case(trim($_POST['rename']), MB_CASE_TITLE, 'UTF-8');
        $GUID       = $_SESSION['guid'];
        $RealmID    = $_SESSION['realm'];
        $ID         = $_SESSION['dumpID'];
        $realson    = null;

        $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);

        $result = mysql_query("SELECT `name`,`address`,`port` FROM `realmlist` WHERE `id` = ". $RealmID .";", $connection) or die(mysql_error());
        $row    = mysql_fetch_array($result);
        $SPT    = $row['port'];
        $SNA    = $row['name'];
        $SIP    = $row['address'];
        mysql_close($connection) or die(mysql_error());

        if(!isset($_SESSION['guid']) || !isset ($_SESSION['realm']) || !isset($_SESSION['dumpID']))
            $realson = $L[58];
        else if(preg_match('/[\'^?$%&*()}{@#~?><>,|=_+¬-]./', $CHAR_NAME))
            $realson = $L[52];
        else if(strstr( $CHAR_NAME, " "))
            $realson = $L[53];
        else if(preg_match("/[0-9]/", $CHAR_NAME))
            $realson = $L[54];
        else if(mb_strlen($CHAR_NAME, 'UTF-8') > 16 && mb_strlen($CHAR_NAME, 'UTF-8') > 1)
            $realson = $L[55];
        else if(_CheckCharacterName($DBUser, $DBPassword, $RealmID, $CHAR_NAME) > 0)
            $realson = $L[56] ." '". $CHAR_NAME ."' ". $L[57];
        else if(!_ServerOn($SIP, $SPT))
            $realson = $L[119] ." '". $SNA ."' <u>". $L[119] ."!</u>";

        if(!empty($realson))
            _Migration_STEP2FORM($realson);
        else {
            unset($_SESSION['STEP2']);

            $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
            _updateCharacterName($connection, $CHAR_NAME, $GUID);
            _TalentsReset($connection, $GUID);
            _MoveToGMAccount($connection, $GUID);
            mysql_close($connection) or die(mysql_error());

            $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
            _updateMigrationStatusAndCharacterName($connection, $ID, $CHAR_NAME, 0);
            mysql_close($connection) or die(mysql_error());

            _PreparateMails(_LoadItemRoW($DBUser, $DBPassword, $ID),
                $CHAR_NAME, $TransferLetterTitle, $TransferLetterMessage, $SOAPUser, $SOAPPassword, $RealmID);
            echo _GDiv($L[51]);
        }
    } else _Migration_STEP2FORM($L[56] ." '". $CHAR_NAME ."' ". $L[57]);

    function _Migration_STEP2FORM($TXT) {
        global $L;
        echo _RDiv($TXT). "
            <div class = 'alert text-center'>". $L[50] ."</div>
                <div class = 'text-center'>
                <br/>
                    <form action = '".$_SERVER['PHP_SELF']."' method = 'post' enctype = 'multipart/form-data'>
                        <input class = 'clearfix' type = 'text' name = 'rename'>
                        <p><button class = 'btn btn-primary' type = 'submit'>". $L[146] ."</button></p>
                    </form>
                </div>
            </div>";
    }
?>