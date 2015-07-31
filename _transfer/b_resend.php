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

    mysql_close($connection) or die(mysql_error());
    _PreparateMails(_LoadItemRoW($DBUser, $DBPassword, $ID), _GetCharacterNameAndDeleteMails($DBUser, $DBPassword, $RealmID, $GUID), $SOAPUser, $SOAPPassword, $RealmID);
    mysql_close($connection) or die(mysql_error());
    ob_end_flush();
    die($L[226]);
?>