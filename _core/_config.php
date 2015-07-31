<?php

    $InstantQuestCompleteLetterTitle    = "Quest Manager";
    $InstantQuestCompleteLetterMessage  = "Items from quest autocomplete";
    $InstantItemBuyLetterTitle          = "ItemShop Manager";
    $InstantItemBuyLetterMessage        = "Item from item shop";

    $PriceForInstant80lvl               = 10; /* Myth Coins */
    $PriceForUnban                      = 10; /* Myth Coins */
    $PriceForCharChangeCustomize        = 15; /* Myth Coins */
    $PriceForCharChangeFaction          = 50; /* Myth Coins */
    $PriceForCharChangeName             = 10; /* Myth Coins */
    $PriceForExaltedReputation          = 50; /* Myth Coins */
    $PriceForCharChangeRace             = 25; /* Myth Coins */
    $PriceForInstantQuestComplete       = 1; /* Myth Coins */
    $PriceForTeleportDalaran            = 5; /* Myth Coins */
    $PriceForTeleportUnstruck           = 0; /* Myth Coins */
    $PriceForRemoveDeserter             = 5; /* Myth Coins */
    $MinPriceForItem                    = 1; /* Myth Coins */
    $MinPriceForDisplayInTable          = 10; /* Myth Coins */
    $ItemDiscountMode                   = 1; /* 1 - Enable, 0 - Disable */
    $DiscountAmmount                    = 0.1; /* 1 - 100% Discount. 0.01 - 1% Discount */
    $MultiplicatorForItemPrice          = 1; /* Multiplicator, for Rise or pull down item prices, but min price will be MinPriceForItem */
    $PageListRecordsAmmount             = 25; /* Used for ItemShop, how much much items will be displayed on table */

    $TransferLetterTitle    = "Migration Manager";              // Letter Title
    $TransferLetterMessage  = "Items from character migration"; // Letter Message

    $DEFAULT_LANGUAGE       = "en_US";                          // Default WebPage Language
    $AvailableLanguages     = "en_US,de_DE,es_ES,fr_FR,ru_RU";  // Available languages on webpage
                                                                // "en_US" - English, "de_DE" - German, "es_ES" - Spanish, "ru_RU" - Russian, "fr_FR" - French
    $AchievementsCheck      = 0;            // ENABLE (1) / DISABLE (0) FORMULA: must have more then Level > 10 OR AchievementsMinCount param
    $AchievementsMinCount   = 50;           // Minimum ammount of Achievements.
    $PLAYTIME               = 10;           // Minimum Playtime. Counted as: last archievment date - first archievment date

    $AccountDB          = "wow_335a_login"; // Your Account DB Name
    $AccountDBHost      = "127.0.0.1";      // Your Account DB Host
    $DBUser             = "root";           // Your DB User
    $DBPassword         = "";               // Your DB Password

    $SOAPUser           = "admin";          // SOAP USER
    $SOAPPassword       = "admin";          // SOAP USER PASSWORD
    $GMLevel            = "(3, 4, 5, 6, 7)";// GM LEVEL ACCESS AVAIBLE CHECK TRANSFERS. IN BRACKETS AND SEPARATE WITH COMMA. EXAMPLE: "(3,4)"

    $STORAGE            = 0;                // Account Where story Rejected or Canceled Transfers
    $MaxMoney           = 200000000;        // Max Money, if more then it, then only this. put values in copper coins
    $MaxHP              = 75000;            // Max Honor Points, if more then it, then only this.
    $MaxCL              = 80;               // Max Character level, if more then it, then only this. Used for Instant leveling & character migration tool
    $MaxAP              = 5000;             // Max Arena Points, if more then it, then only this.
                                            // if do not exist stay -1, if no then put info
    function _SOAPURISwitch($RealmID) {     // Realm ID = Realm ID From Realmlist table
        $SOAPURI1  = "MC";                  // Realm 1 SOAP URI
        $SOAPURI2  = -1;                    // Realm 2 SOAP URI
        $SOAPURI3  = -1;                    // Realm 3 SOAP URI
        $SOAPURI4  = -1;                    // Realm 4 SOAP URI
        $SOAPURI5  = -1;                    // Realm 5 SOAP URI
        $SOAPURIUNK = -1;                   // if 6+ Realm exist return Error
        switch($RealmID) {
            case 1:     return $SOAPURI1;
            case 2:     return $SOAPURI2;
            case 3:     return $SOAPURI3;
            case 4:     return $SOAPURI4;
            case 5:     return $SOAPURI5;
            default:    return $SOAPURIUNK;
        }
    }
                                            // if do not exist stay -1, if no then put info
    function _SOAPHSwitch($RealmID) {       // Realm ID = Realm ID From Realmlist table
        $SOAPHost1  = "127.0.0.1";          // Realm 1 SOAP HOST
        $SOAPHost2  = -1;                   // Realm 2 SOAP HOST
        $SOAPHost3  = -1;                   // Realm 3 SOAP HOST
        $SOAPHost4  = -1;                   // Realm 4 SOAP HOST
        $SOAPHost5  = -1;                   // Realm 5 SOAP HOST
        $SOAPHostUNK = -1;                  // if 6+ Realm exist return Error
        switch($RealmID) {
            case 1:     return $SOAPHost1;
            case 2:     return $SOAPHost2;
            case 3:     return $SOAPHost3;
            case 4:     return $SOAPHost4;
            case 5:     return $SOAPHost5;
            default:    return $SOAPHostUNK;
        }
    }
                                            // if do not exist stay -1, if no then put info
    function _SOAPPSwitch($RealmID) {       // Realm ID = Realm ID From Realmlist table
        $SOAPPort1  = 7878;                 // Realm 1 SOAP PORT
        $SOAPPort2  = -1;                   // Realm 2 SOAP PORT
        $SOAPPort3  = -1;                   // Realm 3 SOAP PORT
        $SOAPPort4  = -1;                   // Realm 4 SOAP PORT
        $SOAPPort5  = -1;                   // Realm 5 SOAP PORT
        $SOAPPortUNK = -1;                  // if 6+ Realm exist return Error
        switch($RealmID) {
            case 1:     return $SOAPPort1;
            case 2:     return $SOAPPort2;
            case 3:     return $SOAPPort3;
            case 4:     return $SOAPPort4;
            case 5:     return $SOAPPort5;
            default:    return $SOAPPortUNK;
        }
    }
                                              // if do not exist stay -1, if no then put info, FOR CHARACTERS DBs
    function _HostDBSwitch($RealmID) {        // Realm ID = Realm ID From Realmlist table
        $HostDB1      = "127.0.0.1";          // Realm 1 Host DB
        $HostDB2      = -1;                   // Realm 2 Host DB
        $HostDB3      = -1;                   // Realm 3 Host DB
        $HostDB4      = -1;                   // Realm 4 Host DB
        $HostDB5      = -1;                   // Realm 5 Host DB
        $HostDBUNK    = -1;                   // if 6+ Realm exist return Error
        switch($RealmID) {
            case 1:     return $HostDB1;
            case 2:     return $HostDB2;
            case 3:     return $HostDB3;
            case 4:     return $HostDB4;
            case 5:     return $HostDB5;
            default:    return $HostDBUNK;
        }
    }
                                                    // if do not exist stay -1, if no then put info, FOR CHARACTERS DBs
    function _CharacterDBSwitch($RealmID) {         // Realm ID = Realm ID From Realmlist table
        $CharactersDB1      = "wow_335a_characters";// Realm 1 Character DB
        $CharactersDB2      = -1;                   // Realm 2 Character DB
        $CharactersDB3      = -1;                   // Realm 3 Character DB
        $CharactersDB4      = -1;                   // Realm 4 Character DB
        $CharactersDB5      = -1;                   // Realm 5 Character DB
        $CharactersDBUNK    = -1;                   // if 6+ Realm exist return Error
        switch($RealmID) {
            case 1:     return $CharactersDB1;
            case 2:     return $CharactersDB2;
            case 3:     return $CharactersDB3;
            case 4:     return $CharactersDB4;
            case 5:     return $CharactersDB5;
            default:    return $CharactersDBUNK;
        }
    }
                                            // if do not exist stay -1, if no then put info, FOR CHARACTERS DBs
    function _WorldDBSwitch($RealmID) {          // Realm ID = Realm ID From Realmlist table
        $WorldDB1      = "wow_335a_world";  // Realm 1 World DB
        $WorldDB2      = -1;                // Realm 2 World DB
        $WorldDB3      = -1;                // Realm 3 World DB
        $WorldDB4      = -1;                // Realm 4 World DB
        $WorldDB5      = -1;                // Realm 5 World DB
        $WorldDBUNK    = -1;                // if 6+ Realm exist return Error
        switch($RealmID) {
            case 1:     return $WorldDB1;
            case 2:     return $WorldDB2;
            case 3:     return $WorldDB3;
            case 4:     return $WorldDB4;
            case 5:     return $WorldDB5;
            default:    return $WorldDBUNK;
        }
    }

    function _CheckWrongOrNoItem($RealmID, $ID) {
        switch($RealmID) {      // case ID: = realm id from realmlist table
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            default:
                switch($ID) {   // IF YOU WANT REJECT ITEM. ADD CASE <ID> : BEFORE RETURN TRUE. ITEM DROP OUR FROM DELIVERY LIST.
                    case 17:    // UBER CHEST: Martin Fury
                    case 43651: // UBER FINISH POLE: Crafty's Pole
                    case 25596: // UBER MOUNT: Peep's Whistle
                    case 17782: // UBER NECK: Talisman of Binding Shard
                    case 12947: // UBER RING: Alex's Ring of Audacity
                    case 192:   // UBER STAFF: Martin's Broken Staff
                    case 22989: // UBER BLADE: The Breaking
                    case 36942: // UBER BLADE: Frostmourne
                    case 32824: // UBER BLADE: Tigole's Trashbringer
                    case 18582: // UBER BLADE: The Twin Blades of Azzinoth
                    case 18583: // UBER BLADE: Warglaive of Azzinoth (Right)
                    case 18584: // UBER BLADE: Warglaive of Azzinoth (Left)
                        return true;
                    default: return false;
                }
                break;
        }
    }

    function _GetChangedItem($RealmID, $ID) {
        if(_CheckWrongOrNoItem($RealmID, $ID))
            return -1;
        switch($RealmID) {      // case ID: = realm id from realmlist table
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            default:
                switch($ID) {   // IF YOU WANT CHANGE ITEM. ADD CASE <ID> : RETURN < NEW ID>. ITEM WILL BE CHANGED IN DELIVERY LIST.
                    case 49623: return 49888;   // Shadowmourne to Shadow's Edge
                    default:
                        return $ID;
                }
                break;
        }
    }

    function _CheckWrongOrNoAchievement($ID) {
    /* for prevent transfer ACHIEVEMENTs with ID xxx */
        switch($ID) {
            //case XX: return false;
            default: return true;
        }
    }

    function _isDisabledSpell($ID) {
    /* for prevent transfer SPELLs with ID xxx */
        switch($ID) {
            //case XX: return true;
            default: return false;
        }
    }

    function _isDisabledMountOrCompanion($ID) {
    /* for prevent transfer MOUNTs or COMPANIONs with ID xxx */
        switch($ID) {
            //case XX: return false;
            default: return true;
        }
    }

    function _MaxOnlineForR($RealmID) {
    /* MAX online for Realm. Used for Display online statistic */
        switch($RealmID) {
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            default:
                return 100;
        }
    }

    function _isQuestFreeToComplete($QuestID) {
        global $PriceForInstantQuestComplete;
        switch($QuestID) {
            // case ID: price;
            default:
                return $PriceForInstantQuestComplete;
        }
    }
?>