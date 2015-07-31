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

    if(!_doesRealmExists($RealmID, $DBUser, $DBPassword) ||
       !_doesCharacterExistsOnAccount($DBUser, $DBPassword, $RealmID, $GUID))
    die($L[9]);

    if(!_doesCharacterNotOnlineATM($DBUser, $DBPassword, $RealmID, $GUID))
        die($L[60]);

    $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);

    if(!_isGMAllowed($connection)) { mysql_close($connection) or die(mysql_error()); die($L[224]); }
    if(!_getMigrationStatus($connection, $ID) == 0) { mysql_close($connection) or die(mysql_error()); die($L[225]); }

    _updateMigrationStatus($connection, $ID, 1);
    mysql_close($connection) or die(mysql_error());
    $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
    _ApproveCharacterTransfer($connection, $GUID);
    mysql_close($connection) or die(mysql_error());
    die($L[227] ." ". $ID ." ". $L[228]);
    ob_end_flush();
?>