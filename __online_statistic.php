<?php


    include_once('_template/_header.php');

    echo "
    <div class = 'text-center'>
        <h2>". $L[250] ."</h2>
    </div>";

    $RealmID    = isset($_GET['R']) ? (int)$_GET['R'] : null;
    $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
    _getRealmArray($connection);
    mysql_close($connection) or die(mysql_error());

    echo "
        <ul class = 'nav nav-tabs'>";
            echo !isset($RealmID) ? "<li class = 'active'>" : "<li>";
            echo "<a href = '?page=_online.php&ALL'>". $L[110] ."</a></li>";
        foreach($_SESSION['R']['W'] as $ID => $NA) {
            echo $RealmID === (int)$ID ? "<li class = 'active'>" : "<li>";
            echo "<a href = '?page=_online.php&R=". $ID ."'>". $NA['N'] ."</a></li>";
        }
    echo "</ul>";

    foreach($_SESSION['R']['W'] as $ID => $NA) {
        if(!isset($RealmID) || (int)$RealmID === (int)$ID)
        {
            $_RealmID       = isset($RealmID) ? $RealmID : (int)$ID;
            $HCount         = null;
            $ACount         = null;
            $CCount         = 0;
            $MAX_ONLINE     = _MaxOnlineForR($_RealmID);
            $CUR_RATE       = null;
            $CUR_HORDE      = null;
            $CUR_ALLIANCE   = null;
            $connection = _MySQLConnect(_HostDBSwitch($_RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($_RealmID));
            $query      = mysql_query("SELECT `race`, COUNT(*) FROM `characters` WHERE `online` = 1 GROUP BY `race`;", $connection);
            while($result = mysql_fetch_array($query)) {
                if(_isHorde((int)$result[0]))
                    $HCount += (int)$result[1];
                else $ACount += (int)$result[1];
                $CCount += (int)$result[1];
            }
            if($CCount > 0) { 
                $CUR_RATE       = $CCount/$MAX_ONLINE*100;
                $CUR_HORDE      = $HCount/$CCount * 100;
                $CUR_ALLIANCE   = $ACount/$CCount * 100;
            }
            mysql_close($connection) or die(mysql_error());
            echo "
            <label for = 'r". $_RealmID ."'>
            <legend>
                <h4>". $NA['N'] ."
                <span class = 'pull-right'>". $CCount ." / ". $MAX_ONLINE ."</span></h4>
            </legend>
            </label>
            <div name = 'r". $_RealmID ."' class = 'progress progress-success progress-striped active'>
                <div class = 'bar' style = 'width: ". $CUR_RATE ."%'></div>
            </div>
            <label for = 'ah". $_RealmID ."'>
                <span class = 'pull-left fIcon'>
                    <img src = '_template/img/alliance.png'/>
                </span>
                <span class = 'pull-right fIcon'>
                    <img src = '_template/img/horde.png'/>
                </span>
            </label>
            <div class = 'progress' name = 'ah". $_RealmID ."' >
                <div class = 'bar' style = 'width: ". $CUR_ALLIANCE ."%;'></div>
                <div class = 'bar bar-danger' style = 'width: ". $CUR_HORDE ."%;'></div>
            </div>";
        }
    }

    include_once('_template/_footer.php');
    ob_end_flush();
?>