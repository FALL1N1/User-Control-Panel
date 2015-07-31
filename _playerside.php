<?php

    include_once('_template/_header.php');

    if(!_getUsername())
        Header('Location: index.php');

    $AuthConnection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);

    echo "<script type = 'text/javascript'>";
    if(!_isGMAllowed($AuthConnection)) {
        echo "
        function DoCancel( id, Realm, Guid ) {
            $.ajax({
            type : 'POST',
            url : '_transfer/b_cancel.php',
            data : {
                cancel : id,
                RealmlistList : Realm,
                GUID : Guid
            },
            success : function( data ) {
                $( '#' + id ).hide( );
                location.reload( true );
                alert( data );
            },
            error : function( XMLHttpRequest, textStatus, errorThrown ) {
                alert( textStatus + ' -- ' + errorThrown );
            }
            });
        }";
    } else {
        echo "
        function DoApprove( id, Realm, Guid ) {
            $.ajax({
            type : 'POST',
            url : '_transfer/b_approve.php',
            data : {
                ID : id,
                R : Realm,
                GUID : Guid
            },
            success : function( data ) {
                $( '#' + id ).hide( );
                location.reload( true );
                alert( data );
            },
            error : function( XMLHttpRequest, textStatus, errorThrown ) {
                alert( textStatus + ' -- ' + errorThrown );
            }
            });
        }
        function DoDeny( id, Realm, Guid ) {
            var Reason = prompt( 'Reason:', '' );
            $.ajax({
            type : 'POST',
            url : '_transfer/b_deny.php',
            data : {
                ID : id,
                R : Realm,
                GUID : Guid,
                REALSON : Reason,
            },
            success : function( data ) {
                $( '#' + id ).hide( );
                location.reload( true );
                alert( data );
            },
            error : function( XMLHttpRequest, textStatus, errorThrown ) {
                alert( textStatus + ' -- ' + errorThrown );
            }
            });
        }
        function DoResend( id, Realm, Guid ) {
            $.ajax({
            type : 'POST',
            url : '_transfer/b_resend.php',
            data : {
                ID : id,
                R : Realm,
                GUID : Guid,
            },
            success : function( data ) {
                $( '#' + id ).hide( );
                location.reload( true );
                alert( data );
            },
            error : function( XMLHttpRequest, textStatus, errorThrown ) {
                alert( textStatus + ' -- ' + errorThrown );
                alert( data );
            }
            });
        }";
    }
    echo "</script>";

    if     (isset($_POST['load']))   $step = 1;
    else if(isset($_POST['rename'])) $step = 2;
    else                             $step = 3;

    switch($step) {
        case 1: include('_transfer/step1.php'); break;
        case 2: include('_transfer/step2.php'); break;
        case 3: _flushStatisticTable($AuthConnection, $GMLevel);
            break;
    }

    include_once('_template/_footer.php');
    ob_end_flush();

    function _flushStatisticTable($connection, $GMLevel)
    {
        global $L;
        echo "<fieldset>";
        if(_isGMAllowed($connection)) {
            $query = mysql_query("SELECT * FROM `account_transfer` WHERE `gmAccount` = ". _getAccountID() ." ORDER BY `id` DESC LIMIT 9;", $connection) or die(mysql_error());

            echo _BDiv($L[27] ."
            <button class = 'btn btn-success'>". $L[13] ."</button>
            <button class = 'btn btn-danger'>". $L[14] ."</button>
            <button class = 'btn btn-warning'>". $L[15] ."</button>
                ". $L[28]) ."
                <br/>
                <table class = 'table table-condensed'>
                    <tr class = 'menuBar'>
                        <td>'OUR' & 'OLD' Name:     </td>
                        <td>'OUR' & 'OLD' Realm:    </td>
                        <td>Realmlist:              </td>
                        <td>Account:                </td>
                        <td>Password:               </td>
                        <td>Admin Options:          </td>
                    </tr>";
            while($row = mysql_fetch_array($query)) {
                if($row["cStatus"] == 0) {
                    echo "
                    <tr>
                        <td>". $row["cNameNEW"] ." / ". $row["cNameOLD"] ."</td>
                        <td>". _getRealmNameFromID($connection, $row["cRealm"])   ." / ". $row["oRealm"] ."</td>
                        <td><a href = '".$row["oServer"]."'>". $row["oRealmlist"] ."</a></td>
                        <td>". $row["oAccount"]                 ."</td>
                        <td>". base64_decode($row["oPassword"]) ."</td>
                        <td width = 240px class = 'text-center'>
                            <button class = 'btn btn-success' name = 'Approve' id = '".$row["id"]."' onclick = \"DoApprove('".$row["id"]."', '".$row["cRealm"]."', '".$row["GUID"]."');\">". $L[13] ."</button>
                            <button class = 'btn btn-danger' name = 'Deny' id = '".$row["id"]."' onclick = \"DoDeny('".$row["id"]."', '".$row["cRealm"]."', '".$row["GUID"]."');\">". $L[14] ."</button>
                            <button class = 'btn btn-warning' name = 'Resend' id = '".$row["id"]."' onclick = \"DoResend('".$row["id"]."', '".$row["cRealm"]."', '".$row["GUID"]."\");'>". $L[15] ."</button>
                        </td>
                    </tr>";
                }
            }
        } else {
            $query = mysql_query("SELECT * FROM `account_transfer` WHERE `cAccount` = ". _getAccountID() ." ORDER BY `id` DESC LIMIT 9;", $connection) or die(mysql_error());

            echo _BDiv($L[25] ." <a href = 'v335.699.rar'>3.3.5a</a><br/>". $L[26]) ."
            <div>
                <form action = '". $_SERVER["SCRIPT_NAME"] ."' method = 'post' enctype = 'multipart/form-data'>
                    <input type = 'submit' class = 'btn btn-info' name = 'load' value = '". $L[10] ."'/>
                </form>
            </div>
            <table class = 'table table-condensed'>
                <tr class = 'menuBar'>
                <td>#               </td>
                <td>Character Name: </td>
                <td>Realm:          </td>
                <td>                </td>
                </tr>";
            while($row = mysql_fetch_array($query)) {
                echo "
                    <tr class = 'tip ". _returnTABLEStatusSTR($row["cStatus"]) ."' title ='". _returnStatusSTR($row["cStatus"], $row["Reason"]) ."'>
                        <td class = 'text-center'>". $row["id"]         ."</td>
                        <td class = 'text-center'>". $row["cNameNEW"]   ."</td>
                        <td class = 'text-center'>". _getRealmNameFromID($connection, $row["cRealm"]) ."</td>
                        <td class = 'text-center'>";
                if($row["cStatus"] == 0)
                    echo "<button style = 'float: right;' class = 'btn btn-warning' name = 'cancel' id = '".$row["id"]."' onclick = \"DoCancel('".$row["id"]."', '".$row["cRealm"]."', '".$row["GUID"]."');\">". $L[12] ."</button>";
                echo "  </td>
                    </tr>";
            }
        }
        echo "</table>
            </fieldset>";
        mysql_close($connection) or die(mysql_error());
    }
?>