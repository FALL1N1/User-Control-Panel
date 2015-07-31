<?php

    include_once('_soap.php');

    function _ServerOn($IP, $PORT) {
        $STATUS = @fsockopen($IP, $PORT, $ERROR_NO, $ERROR_STR,(float)0.5);
        if($STATUS) {
            @fclose($STATUS);
            return true;
        } else return false;
    }

    function _CheckCurrency($ID) {
        switch($ID) {
            case 43307: // ARENA POINTS
            case 43308: // HONOR POINTS
                return false;
            default: return true;
        }
    }

    function _checkItemCount($count) {
        $count = $count < 1 ? 1 : $count;
        $count = $count > 1000 ? 1000 : $count;
        return $count;
    }

    function _PreparateMails($row, $PlayerName, $SOAPUser, $SOAPPassword, $RealmID) {
        global $TransferLetterTitle, $TransferLetterMessage;
        $item_array = explode(" ", trim($row));
        $by10       = 1;
        $toSend     = "";
        $needSend   = count($item_array);
        for($i = 0; $i < count($item_array); $i++) {
            $toSend .= $item_array[$i];
            $toSend .= " ";
            if($by10 == 10) {
                _SOAP_SentRemoteCommand($SOAPUser, $SOAPPassword, $RealmID,
                trim(".send items ". $PlayerName ." \"". $TransferLetterTitle ."\" \"". $TransferLetterMessage ."\" ". $toSend));
                $needSend = $needSend - $by10;
                $by10    = 1;
                $toSend = "";
            } else if($needSend - $by10 == 0) {
                _SOAP_SentRemoteCommand($SOAPUser, $SOAPPassword, $RealmID,
                trim(".send items ". $PlayerName ." \"". $TransferLetterTitle ."\" \"". $TransferLetterMessage ."\" ". $toSend));
                $toSend = "";
            } else $by10++;
        }
    }

    function _RemoveRaceBonus($RaceID, $SkillID, $value) {
        switch($RaceID) {
            case 6:     // Tauren
                switch($SkillID) {
                    case 182:
                    $value = $value - 5;
                        return $value;
                    default: return $value;
                }
                break;
            case 7:     // Gnome
                switch($SkillID) {
                    case 202:
                    $value = $value - 15;
                        return $value;
                    default: return $value;
                }
                break;
            case 10:    // Blood Elf
                switch($SkillID) {
                    case 333:
                    $value = $value - 10;
                        return $value;
                    default: return $value;
                }
                break;
            case 11:    // Draenei
                switch($SkillID) {
                    case 755:
                    $value = $value - 5;
                        return $value;
                    default: return $value;
                }
                break;
            default: return $value;
        }
    }

    function _CheckExtraSpell($skill) {
        switch($skill) {
            case 393: // Skinning
            case 182: // Herbalism
            case 185: // Cooking
            case 186: // Mining
            case 333: // Enchanting
            case 755: // Jewelcrafting
            case 773: // Inscription
                return true;
            default: return false;
        }
    }

    function _GetExtraSpellForSkill($skill, $cur, $GUID, $connection) {
        switch($skill) {
            case 393: // Skinning
                switch(_CheckSkillLevel($cur)) {
                    case 75:    return 53125;
                    case 150:   return 53662;
                    case 225:   return 53663;
                    case 300:   return 53664;
                    case 375:   return 53665;
                    case 450:   return 53666;
                    default:    return -1;
                }
            case 182: // Herbalism
                _LearnSeparateSpell(2383, $GUID, $connection);
                switch(_CheckSkillLevel($cur)) {
                    case 75:    return 55428;
                    case 150:   return 55480;
                    case 225:   return 55500;
                    case 300:   return 55501;
                    case 375:   return 55502;
                    case 450:   return 55503;
                    default:    return -1;
                }
            case 186: // Mining
                _LearnSeparateSpell(2656, $GUID, $connection);
                _LearnSeparateSpell(2580, $GUID, $connection);
                switch(_CheckSkillLevel($cur)) {
                    case 75:    return 53120;
                    case 150:   return 53121;
                    case 225:   return 53122;
                    case 300:   return 53123;
                    case 375:   return 53124;
                    case 450:   return 53040;
                    default:    return -1;
                }
            case 185: // Cooking
                return 818;
            case 333: // Enchanting
                return 13262;
            case 755: // Jewelcrafting
                return 31252;
            case 773: // Inscription
                return 51005;
        }
    }

    function _CheckSkillLevel($cur) {
        if($cur >= 1 && 74 >= $cur)
            return 0;
        else if($cur >= 75 && 149 >= $cur)
            return 75;
        else if($cur >= 150 && 224 >= $cur)
            return 150;
        else if($cur >= 225 && 299 >= $cur)
            return 225;
        else if($cur >= 300 && 374 >= $cur)
            return 300;
        else if($cur >= 375 && 449 >= $cur)
            return 375;
        else if($cur == 450)
            return 450;
    }

    function _Y($A) {
        $A = _X($A);
        //$A = str_replace(" ", "", $A);
        $A = str_replace("\t", "", $A);
        $A = str_replace("\n", "", $A);
        $A = str_replace("\r", "", $A);
        $A = str_replace("\x0B", "", $A);
        $A = str_replace("\x00", "", $A);
        return $A;
    }

    function _CHECK_KEY($X) { return strrev(base64_decode(strrev($X))); }
    function _DECRYPT($X) { return strrev(base64_decode(strrev(strrev(base64_decode(strrev($X)))))); }
    function _AH_TABLE_COLOUR($X) { if($X < 0) return "error"; if($X > 0) return "success"; return ""; }
    function _PRICE_STR($X /* MAIN PRICE */, $T = null /* TABLE VALUE */, $Y = null /* PRICE WITH DISCOUNT */) {
        global $L;
        if(isset($Y))
            return $L[169] ." <span class = 'discountSpan'>" .$X ."</span> <i class = 'icon-fire'></i> ". $Y ." <i class = 'icon-fire'></i>". $L[161];
        if(isset($T))
            return $X > 0 ? $X ." <i class = 'icon-fire'></i>" : $L[63];
        return $X > 0 ? $L[64] ." : ". $X ." <i class = 'icon-fire'></i>" : $L[63];
    }

    function _MaxValue($VALUE1, $VALUE2) { return $VALUE1 > $VALUE2 ? $VALUE2 : $VALUE1; }
    function SHA1Password($username, $password) { return SHA1($username .':'. $password); }

    function _getAccountID() { return $_SESSION['AccountID']; }
    function _getUsername() { return isset($_SESSION['AccountUN']) ? $_SESSION['AccountUN'] : null; }
    function _getAlreadyEffectSTR($X) { return _GDiv("<a href = '_userside.php'>". $X ."</a>"); }
    function _getNotEnoughtFireSTR() { global $L; return _RDiv($L[18] . " <i class = 'icon-fire'></i>"); }
    function _getNotEnoughtGoldSTR() { global $L; return _RDiv($L[18] . " <img alt = '' src = '_template/img/gold_coin.png'>"); }
    function _getPriceMessage($Price_INT, $FreeMessage_STR) { return $Price_INT > 0 ? $Price_INT : $FreeMessage_STR; }
    function _getAvatarPicString($LVL, $GENDER, $RACE, $CLASS) { return _getLevelForAvatar($LVL) ."-". $GENDER ."-". $RACE ."-". $CLASS .".png"; }
    function _getCharacter_L_R_S_C_STR($CLevel, $CSex, $CRace, $CClass) { return $CLevel ." ". _getCharacterRaceSTR($CRace) ." ". _getCharacterSexSTR($CSex) ." ". _getCharacterClassSTR($CClass); }
    function _PPSTR($TEXT) { echo "'". $TEXT ."'"; }
    function _AH_REALMID($X) { return $X > 0 ? " `RealmID` = ". (int)$X : ""; }
    function _isset($X) { if(!isset($X)) return false; $X = trim($X); if(empty($X)) return false; return true; }

    function _RDiv($TEXT) { return "<div class = 'alert alert-error text-center'>". $TEXT ."</div>"; }
    function _GDiv($TEXT) { return "<div class = 'alert alert-success text-center'>". $TEXT ."</div>"; }
    function _BDiv($TEXT) { return "<div class = 'alert alert-info text-center'>". $TEXT ."</div>"; }

    function _getLevelForAvatar($X) {
        if($X < 59)
            return 0;
        if($X < 69)
            return 60;
        if($X < 79)
            return 70;
        if($X == 80)
            return 80;
        return 0;
    }

    function _getReputationRank($S) {
        global $L;
        if($S > 42000)
            return $L[73] ." ". $L[74];
        if($S > 21000)
            return $L[73] ." ". $L[75];
        if($S > 9000)
            return $L[73] ." ". $L[76];
        if($S > 3000)
            return $L[73] ." ". $L[77];
        if($S > 0)
            return $L[73] ." ". $L[78];
        if($S < 0)
            return $L[73] ." ". $L[79];
        if($S < -3000)
            return $L[73] ." ". $L[80];
        return $L[73] ." ". $L[78];
    }

    function _getColorOfReputationBlock($S) {
        if($S > 3000) // Friendly/Honored/Exalted
            return "alert-success";
        if($S < 0)    // Enemy/Unfriendly
            return "alert-error";
        if($S < 3000) // Neutral
            return "";
        return "";
    }

    function _getCharacterRaceSTR($CharRace) {
        global $L;
        switch($CharRace) {
            case 1: return $L[121]; // Human
            case 2: return $L[122]; // Orc
            case 3: return $L[123]; // Dwarf
            case 4: return $L[124]; // Night Elf
            case 5: return $L[125]; // Undead
            case 6: return $L[126]; // Tauren
            case 7: return $L[127]; // Gnome
            case 8: return $L[128]; // Troll
            case 10: return $L[130]; // Blood Elf
            case 11: return $L[131]; // Draenei
            default:
                return "???"; // Default - Human
        }
    }

    function _getCharacterSexSTR($G) {
        global $L;
        switch($G) {
            case 1: return $L[112]; // Female
            case 0: return $L[113]; // Male
            default:
                return "???"; // Default - Male
        }
    }

    function _AH_PRICE_STR($X) {
        global $L;
        $STR = $X != 0 ? $X . "<i class = 'icon-fire'></i>" : $L[63];
        return $X > 0 ? "+". $STR : $STR;
    }

    function _AH_TR_STR($X, $Y) {
        if(!empty($X['text']) && $Y != 14)
            return "tip ". _AH_TABLE_COLOUR($X['myth_coins_spend']) ."' title ='". $X['text'];
        else return _AH_TABLE_COLOUR($X['myth_coins_spend']);
    }

    function _AH_STR($X, $Y = "") {
        global $L;
        switch($X) {
            case 1: return $L[87]; /* Instant 80 LVL */
            case 2: return $L[86]; /* Change Race */
            case 3: return $L[84]; /* Customization */
            case 4: return $L[83]; /* Rename */
            case 5: return $L[85]; /* Change Faction */
            case 6: return $L[91]; /* Teleport to Dalaran */
            case 7: return $L[92]; /* Unstruck */
            case 8: return $L[90]; /* Remove Deserter */
            case 9: return $L[94]; /* Unban */
            case 10: return $L[88]; /* Instant Exalted Reputation */
            case 11: return $L[96] ." <i class = 'icon-fire'></i>"; /* Obtain donate points */
            case 12: return $L[97]; /* Reg Account */
            case 13: return $L[93]; /* Password Change */
            case 14: return $L[82] ." ". $Y; /* Buy an item */
            case 15: return $L[89]; /* Instant Quest Complate */
            case 16: return $L[257]; /* Remove points by GM */
            case 17: return $L[269]; /* Gold buy */
            default:
                return $L[120] . " ". $X;
        }
    }

    function _getCharacterClassSTR($class) {
        global $L;
        switch($class) {
            case 1: return $L[101]; // Warrior
            case 2: return $L[102]; // Paladin
            case 3: return $L[103]; // Hunter
            case 4: return $L[104]; // Rogue
            case 5: return $L[105]; // Priest
            case 6: return $L[106]; // Death Knight
            case 7: return $L[107]; // Shaman
            case 8: return $L[108]; // Mage
            case 9: return $L[109]; // Warlock
            case 11: return $L[111]; // Druid
            default:
                return "???"; // Default - Warrior
        }
    }

    function _FORM_TO_CHAR_ACTIONS($CA, $HEADER, $REALSON = "", $PRICE, $NL = null /* NEW LEVEL */, $NN = null /* NEW NAME */, $NR = null /* NEW RACE */, $NC = null /* NEW CLASS */, $C = null /* CUSTOMIZE */) {
        global $L;
        echo "
        <form action = ". $_SERVER['PHP_SELF'] ." method = 'POST'>
        <fieldset>
            <div class = 'text-center'>". $REALSON ."
                <h2>". $HEADER ."</h2>
                <fieldset>";
                _FORM_CHAR_BLOCK($CA, $L[114]);
                _FORM_CHAR_BLOCK($CA, $L[115], true, $NL /* NEW LEVEL */, $NN /* NEW NAME */, $NR /* NEW RACE */, $NC /* NEW CLASS */, $C /* CUSTOMIZE */);
           echo "</fieldset>". _BDiv(_PRICE_STR($PRICE)) ."
                <p><button class = 'btn btn-primary' type = 'submit'>". _getPriceButtonSTR($PRICE) ."</button></p>
            </div>
        </fieldset>
        </form>";
    }

    function _FORM_CHAR_BLOCK($CA, $H = null, $G = null /* Green or Orange color */, $NL = null /* NEW LEVEL */, $NN = null /* NEW NAME */,
                                                   $NR = null /* NEW RACE */, $NC = null /* NEW CLASS */, $C = null /* CUSTOMIZE */) {
        global $L;
        $class_STR = isset($class_STR) ? "alert alert-success" : "alert";
        $H = isset($H) ? " (". $H .")" : "";

        echo "
            <div class = 'charBox ". $class_STR ."'>
            <table>";
                $NAME_AFTER = isset($C)  ? "???" : $CA['CharName'];
                $NAME_AFTER = isset($NN) ? "???" : $CA['CharName'];
                echo "
                <tr>
                    <td width = '73'>
                        <img class = 'img-rounded' src = '_template/img/_faces/";
                        if(isset($NR) || isset($NC) || isset($C))
                            echo "0-0-0-0.png";
                        else
                            echo _getAvatarPicString(isset($NL) ? $NL : $CA['CharLevel'], $CA['CharGender'], $CA['CharRace'], $CA['CharClass']);
                        echo "' border = 'none'></img>
                    </td>
                    <td width = '362'><h4>". $NAME_AFTER . $H ."</h4>
                        <span class = 'pull-left'>".
                        _getCharacter_L_R_S_C_STR(
                            isset($NL)  ? $NL   : $CA['CharLevel'],
                            isset($C)   ? -1    : $CA['CharGender'],
                            isset($NR)  ? "???" : $CA['CharRace'],
                            isset($NC)  ? "???" : $CA['CharClass'])
                        ."<br/>". _getCharMoneySTR($CA['CharMoney']) ."</span>";
                        echo "
                        <span class = 'pull-right'>
                        ". $L[119] .": ". $CA['RealmName'] ."<br/>
                        ". $L[118] .": ". $CA['CharHKills'] ."</span>
                    </td>
                </tr>
            </table>
        </div>";
    }

    function _getCharMoneySTR($M) {
        $L = intval($M / 10000);
        /* Gold */   $AU = $L;
        $L = $M - $AU * 10000;
        $L = intval($L / 100);
        /* Silver */ $AG = $L;
        /* Copper */ $CU = $M - $AU * 10000 - $AG * 100;
        return $AU ."<img alt = '' src = '_template/img/gold_coin.png'> ".
             $AG ."<img alt = '' src = '_template/img/silver_coin.png'> ".
             $CU ."<img alt = '' src = '_template/img/copper_coin.png'> ";
    }

    function _isHorde($race) {
        switch($race)
        {
            case 1: // Human
            case 3: // Dwarf
            case 4: // Night Elf
            case 7: // Gnome
            case 11:// Dranei
                return false;
            case 2: // Orc
            case 5: // Undead
            case 6: // Tauren
            case 8: // Troll
            case 10:// Blood Elf
            default:
                return true;
        }
    }

    function _getPriceButtonSTR($PRICE) {
        global $L;
        if($PRICE > 0)
            return $L[170] ." (". $L[171] ." <i class = 'icon-white icon-fire'></i> )";
        return $L[170] ." (". $L[63] .")";
    }

    function _sendSingleItem($CharacterName, $ItemID, $RealmID, $SOAPUser, $SOAPPassword) {
        global $InstantItemBuyLetterTitle, $InstantItemBuyLetterMessage;
        _SOAP_SentRemoteCommand($SOAPUser, $SOAPPassword, $RealmID,
        trim('.send items '. $CharacterName .' "'. $InstantItemBuyLetterTitle .'" "'. $InstantItemBuyLetterMessage .'" '. $ItemID .':1'));
    }

    function _CalculatePrise($QueryResult, $MinPrice, $PriceX) {
        $PricePoints = null;
        if($QueryResult['RequiredLevel'] < 71 && $QueryResult['ItemLevel'] < 176 && $QueryResult['class'] != 15)
            return $MinPrice;
        else switch($QueryResult['Quality'])
        {
            case 0 : // Grey | Poor
            case 1 : // White| Common
            case 2 : // Green| Uncommon
                $PricePoints = 1;
                break;
            case 3 : // Blue | Rare
            case 7 : // Gold | Bind to Account
                $PricePoints = 2;
                break;
            case 4 : // Purple | Epic
                $PricePoints = _MinPrise10(abs(_CalculatePrisePoints($QueryResult['class'], $QueryResult['subclass']) * $QueryResult['ItemLevel'] - 190)) * $PriceX;
                break;
            case 5 : // Orange | Legendary
            case 6 : // Red    | Artifact
                $PricePoints = _MinPrise10(abs(_CalculatePrisePoints($QueryResult['class'], $QueryResult['subclass']) * $QueryResult['ItemLevel'] - 190)) * $PriceX;
                break;
        }
        return round($PricePoints);
    }

    function _MinPrise10($X) { return $X < 10 ? 10 : $X; }

    function _CalculatePrisePoints($Class, $SubClass /*, $ItemLevel, $RequiredLevel*/) {
        switch($Class)
        {
            case 2: //  Weapon
            switch($SubClass) {
                case 1: //  Axe 2H
                case 2: //  Bow
                case 3: //  Gun
                case 5: //  Mace 2H
                case 6: //  Polearm
                case 8: //  Sword 2H
                case 0: //  Axe
                case 4: //  Mace
                case 7: //  Sword
                case 13: //  Fist Weapon
                case 15: //  Dagger
                case 10: //  Staff
                case 18: //  Crossbow
                    return 3;
                case 16: //  Thrown
                case 17: //  Spear
                case 19: //  Wand
                    return 1.2;
                case 9: //  Obsolete
                case 11: //  Exotic
                case 12: //  Exotic
                case 14: //  Miscellaneous
                case 20: //  Fishing Pole
                    return 1;
            } break;
            case 4: //  Armor
            switch($SubClass) {
                case 1: //  Cloth
                case 2: //  Leather
                case 3: //  Mail
                case 4: //  Plate
                    return 2;
                case 6: //  Shield
                case 7: //  Libram
                case 8: //  Idol
                case 9: //  Totem
                case 10: //  Sigil
                    return 1.2;
                case 0: //  Miscellaneous
                case 5: //  Buckler(OBSOLETE)
                    return 0.5;
            } break;
            case 15: //  Miscellaneous
            switch($SubClass) {
                case 5: //  Mount
                case 2: //  Pet
                    return 0.9;
                case 0: //  Junk
                case 1: //  Reagent
                case 3: //  Holiday
                case 4: //  Other
                    return 0.5;
            } break;
            case 0: //  Consumable
            case 1: //  Container
            case 3: //  Gem
            case 5: //  Reagent
            case 6: //  Projectile
            case 9: //  Recipe
            case 11: //  Quiver
            case 12: //  Quest
            case 13: //  Key
            case 16: //  Glyph
                return 0.2;
            case 14: //  Permanent(OBSOLETE)
            case 10: //  Money(OBSOLETE)
            case 8: //  Generic(OBSOLETE)
            case 7: //  Trade Goods
            default:
                return -1;
        }
    }
?>