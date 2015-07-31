<?php


    include_once('_template/_header.php');

    if(!_getUsername())
        Header('Location: index.php');

    $SA             = null;
    $RealmID        = null;
    $RealmName      = null;
    $GUID           = null;
    $CharName       = null;
    $REALSON        = null;
    $ItemID         = null;
    $QuestID        = null;
    $CompleteQuest  = null;

    if(isset($_GET['realmid']) && isset($_GET['guid'])) {
        unset($_SESSION['TCA']);
        unset($_SESSION['pQuest']);
        $RealmID                        = (int)$_GET['realmid'];
        $GUID                           = (int)$_GET['guid'];
        $_SESSION['TCA']['CharGUID']    = $GUID;
        $_SESSION['TCA']['RealmID']     = $RealmID;
    } else if(isset($_SESSION['TCA'])) {
        $SA         = $_SESSION['TCA'];
        $RealmID    = $_SESSION['TCA']['RealmID'];
        $RealmName  = $_SESSION['TCA']['RealmName'];
        $GUID       = $_SESSION['TCA']['CharGUID'];
        $CharName   = $_SESSION['TCA']['CharName'];
        $PriceForInstantQuestComplete = _isQuestFreeToComplete($QuestID, $PriceForInstantQuestComplete);
        if(isset($_POST['QuestList2'])) {
            $ItemID         = (int)$_POST['QuestList2'];
            $QuestID        = $_SESSION['pQuest'];
            $CompleteQuest  = true;
        } else if(isset($_POST['QuestList1']) && $_POST['QuestList1'] > 0) {
            if(
         isset($_SESSION['pQuest'])
            && $_SESSION['pQuest'] == $_POST['QuestList1']
            && isset($SA['qList'][$_SESSION['pQuest']])
            && $SA['qList'][$_SESSION['pQuest']] == true) {
                $QuestID        = $_SESSION['pQuest'];
                $CompleteQuest  = true;
            } else $_SESSION['pQuest'] = (int)$_POST['QuestList1'];
        }
    } else Header('Location: _userside.php'); // die("EXEPTION");

    if($CompleteQuest) {
        $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
        if(_isEnoughMythCoins($PriceForInstantQuestComplete, $connection)) {
            _SpendMythCoins($PriceForInstantQuestComplete, 15, $CharName, $GUID, $RealmName, $RealmID, "", $connection);
            mysql_close($connection) or die(mysql_error());
            $REALSON = _GDiv($L[17]);
            if($ItemID)
                _sendQuestReward($GUID, $QuestID, $CharName, $ItemID, $RealmID, $DBUser, $DBPassword, $SOAPUser, $SOAPPassword);
            $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
            mysql_query("INSERT INTO `character_queststatus_rewarded`(`guid`,`quest`) VALUES (". $GUID .",". $QuestID .");", $connection) or die(mysql_error());
            mysql_query("UPDATE `character_queststatus` SET `status` = 0 WHERE `guid` = ". $GUID ." AND `quest` = ". $QuestID .";", $connection) or die(mysql_error());
            mysql_close($connection);
            unset($_SESSION['pQuest']);
            unset($_SESSION['TCA']['qList']);
        } else {
            mysql_close($connection) or die(mysql_error());
            $REALSON = _getNotEnoughtFireSTR();
        }
    }

    if(_doesRealmExists($RealmID, $DBUser, $DBPassword)) {
        if(_doesCharacterExistsOnAccount($DBUser, $DBPassword, $RealmID, $GUID, _getAccountID())) {
            if(_doesCharacterNotOnlineATM($DBUser, $DBPassword, $RealmID, $GUID)) {
                if(!_doesCharacterHaveActiveQuests($GUID, $RealmID, $DBUser, $DBPassword))
                    echo _getAlreadyEffectSTR($L[72]);
                else
                    _FORM_QUEST_COMPLETE($SA ? $SA : _FORM_CHAR_ARRAY($AccountDBHost, $AccountDB, $DBUser, $DBPassword, $RealmID, $GUID) /* CHECK FOR SESSION ARRAY */,
                        $RealmID, $AccountDBHost, $AccountDB, $DBUser, $DBPassword, $GUID, $PriceForInstantQuestComplete, $REALSON);        
            } else echo _RDiv($L[60]);
        } else echo _RDiv($L[9]);
    } else echo _RDiv($L[9]);

    include_once('_template/_footer.php');
    ob_end_flush();

    function _sendQuestReward($GUID, $QuestID, $CharacterName, $ItemID, $RealmID, $DBUser, $DBPassword, $SOAPUser, $SOAPPassword) {
        global $InstantQuestCompleteLetterTitle, $InstantQuestCompleteLetterMessage;
        _SOAP_SentRemoteCommand($SOAPUser, $SOAPPassword, $RealmID,
        trim('.send items '. $CharacterName .' "'. $InstantQuestCompleteLetterTitle .'" "'. $InstantQuestCompleteLetterMessage .'" '. $ItemID .':1'));
    }

    function _FORM_QUEST_COMPLETE($SA, $RealmID, $AccountDBHost, $AccountDB, $DBUser, $DBPassword, $GUID, $PRICE, $REALSON = "") {
        global $L;
        $QueryString  = "";
        $connection   = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _CharacterDBSwitch($RealmID));
        $query        = mysql_query("SELECT `quest` FROM `character_queststatus` WHERE `status` = 3 AND `guid` = ". (int)$GUID .";",$connection) or die(mysql_error());
        while($result = mysql_fetch_array($query))
            $QueryString .= $result['quest'] . ", ";

        $QueryString = substr($QueryString, 0, -2);
        mysql_close($connection) or die(mysql_error());
        if($QueryString != "" ) {
            $connection = _MySQLConnect(_HostDBSwitch($RealmID), $DBUser, $DBPassword, _WorldDBSwitch($RealmID));
            $query      = mysql_query("SELECT 
                `Id` AS `id`,
                `RewardChoiceItemId1` AS `I1`,
                `RewardChoiceItemId2` AS `I2`,
                `RewardChoiceItemId3` AS `I3`,
                `RewardChoiceItemId4` AS `I4`,
                `RewardChoiceItemId5` AS `I5`,
                `RewardChoiceItemId6` AS `I6`
            FROM
                `quest_template` WHERE `Id` IN (". $QueryString .");", $connection) or die(mysql_error());

            while($result = mysql_fetch_array($query))
            {
                if($result['I1'] < 1 && $result['I2'] < 1 && $result['I3'] < 1
                && $result['I4'] < 1 && $result['I5'] < 1 && $result['I6'] < 1)
                    $SA['qList'][$result['id']] = true;
                else {
                    $ID = $result['id'];
                    $i = 1;
                    foreach($result as $ITEM) {
                        if($ITEM === $ID)
                            continue;
                        if($ITEM < 1)
                            continue;
                        if(isset($SA['qList'][$ID]) && in_array($ITEM, $SA['qList'][$ID]))
                            continue;
                        $SA['qList'][$ID][$i] = $ITEM;
                        $i++;
                    }
                }
            }
            mysql_close($connection) or die(mysql_error());
        } else die("EXCEPTION QUESTLIST-CREATE");
        $_SESSION['TCA'] = $SA;

        echo "
        <fieldset>
        <form action = ". $_SERVER['PHP_SELF'] ." method = 'POST'>
            <div class = 'text-center'>". $REALSON ."
                <h2>". $L[89] ."</h2>
                <fieldset>";

                _FORM_CHAR_BLOCK($SA, null, true);

                echo "
                <div class = 'charBoxClean'>";
                $STEP = null;
                $BN   = null;
                if(isset($_SESSION['pQuest'])
                && isset($SA['qList'][$_SESSION['pQuest']])
                && $SA['qList'][$_SESSION['pQuest']] != true) {
                    $STEP = 2;
                    $BN   = $L[291];
                    $Q    = $_SESSION['pQuest'];
                    echo "
                    <ul class = 'nav nav-pills'>
                        <li class = 'dropdown'>
                            <a id = 'QuestSelector". $STEP ."' class = 'dropdown-toggle' role = 'button' data-toggle = 'dropdown' href = '#'>". $L[71] .":<b class = 'caret'></b></a>
                            <ul id = 'QuestSelectorList". $STEP ."' class = 'dropdown-menu' role = 'menu'>";
                            foreach($SA['qList'][$Q] as $ItemID) {
                                if($ItemID < 2)
                                    continue;
                                echo "<li><a tabindex = '-1' href= '#' onclick = 'QuestJS($(this).html());'>". $ItemID ."</a></li>";
                            }
                       echo "</ul>
                        </li>
                    </ul>";
                } else {
                    $STEP = 1;
                    $BN   = $L[290];
                    $DN   = isset($_SESSION['pQuest']) ? $L[288] . $_SESSION['pQuest'] : $L[70];
                    echo "
                    <ul class = 'nav nav-pills'>
                        <li class = 'dropdown'>
                            <a id = 'QuestSelector". $STEP ."' class = 'dropdown-toggle' role = 'button' data-toggle = 'dropdown' href = '#'>". $DN .":<b class = 'caret'></b></a>
                            <ul id = 'QuestSelectorList". $STEP ."' class = 'dropdown-menu' role = 'menu'>";
                        foreach($SA['qList'] as $QuestID => $ENABLED)
                          echo "<li><a tabindex = '-1' href= '# ' onclick = 'QuestJS($(this).html());'>". $QuestID ."</a></li>";
                      echo "</ul>
                        </li>
                    </ul>";
                }
                echo "
                </div>
                </fieldset>
                <br/>";
            if(isset($_SESSION['pQuest'])) {
                echo _BDiv(_PRICE_STR($PRICE));
                echo "<input type = 'hidden' name = 'QuestList". $STEP ."' value = '". $_SESSION['pQuest'] ."'/>";
            } else {
                $BN = $L[146];
                echo "<input type = 'hidden' name = 'QuestList". $STEP ."' value = ''/>";
            }
          echo "<button class = 'btn btn-info' type = 'submit'>". $BN ."</button>
            </div>
        </form>
        </fieldset>
    <script>
        $('#QuestSelector". $STEP ."').click(function() {
            $('#QuestSelectorList". $STEP ."').toggle();
        })
        function QuestJS(html) {
            $('#QuestSelectorList". $STEP ."').hide();
            $('#QuestSelector". $STEP ."').html(html);
            $('input[name = \"QuestList". $STEP ."\"]').val($('#QuestSelector". $STEP ."').text());
        }
    </script>";
    }
?>