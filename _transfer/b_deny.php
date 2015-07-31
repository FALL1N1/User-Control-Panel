<?php


    include_once('../_core/_config.php');
    include_once('../_core/_functions.php');
    include_once('../_core/_dbfunctions.php');

    ob_start();
    session_start();

    if(!isset($_POST['ID']) || !isset($_POST['R']) || !isset($_POST['GUID']))
        die($L[9]);

    $ID      = (int)$_POST['ID'];
    $RealmID = (int)$_POST['R'];
    $GUID    = (int)$_POST['GUID'];
    $REASON  = $_POST['REALSON'];

    if(!isset($REASON) || empty($REASON))
        $REASON = $L[230];

    if(!_doesRealmExists($RealmID, $DBUser, $DBPassword) ||
       !_doesCharacterExistsOnAccount($DBUser, $DBPassword, $RealmID, $GUID))
    die($L[9]);

    if(!_doesCharacterNotOnlineATM($DBUser, $DBPassword, $RealmID, $GUID))
        die($L[60]);

    $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);

    if(!_isGMAllowed($connection)) { mysql_close($connection) or die(mysql_error()); die($L[224]); }
    if(!_getMigrationStatus($connection, $ID) == 0) { mysql_close($connection) or die(mysql_error()); die($L[225]); }

    _AddComment($connection, $ID, $REASON);
    _updateMigrationStatus($connection, $ID, 2);
    mysql_close($connection) or die(mysql_error());
    _CancelORDenyCharacterTransfer($DBUser, $DBPassword, $RealmID, $GUID, $STORAGE);
    ob_end_flush();
    die($L[231] .": ". $GUID . " ". $L[231] ." ". $REASON);

    function _button_AddComment($connection, $ID, $REASON) { mysql_query("UPDATE `account_transfer` SET `Reason` = '". _X($REASON) ."' WHERE `id` = ". (int)$ID .";", $connection) or die(mysql_error()); }
?>