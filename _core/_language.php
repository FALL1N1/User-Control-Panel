<?php


    if(isset($_POST['LSec']))
        $_SESSION['SL'] = _isLanguageSupported($_POST['LSec']) ? $_POST['LSec'] : $DEFAULT_LANGUAGE;
    else if(!isset($_SESSION['SL']))
        $_SESSION['SL'] = $DEFAULT_LANGUAGE;

    switch(_getAMLanguage()) {
        case "ru_RU": /* RUSSIAN */
        case "fr_FR": /* FRENCH */
        case "de_DE": /* GERMAN */
        case "es_ES": /* SPANISH - SPAIN */
        case "en_US": /* ENGLISH */
        default: {  /* ENGLISH */
            $L[1] = 'Member login';
            $L[2] = 'Sign in';
            $L[3] = 'OFFLINE';
            $L[4] = 'Home';
            $L[5] = 'Quit';
            $L[6] = 'You have successfully left your account';
            $L[7] = 'Go Back';
            $L[8] = 'Private Server';
            $L[9] = 'ERROR, IF YOU HAVEN`T DO THAT, CONTACT ADMINISTRATION';

            $L[10] = 'Start Transfer, STEP 1';
            $L[11] = 'Continue Transfer, STEP 2';
            $L[12] = 'Cancel';
            $L[13] = 'Approve';
            $L[14] = 'Deny';
            $L[15] = 'Resend';
            $L[16] = 'Not Logged';
            $L[17] = 'Success operation';
            $L[18] = 'Not enought';
            $L[19] = 'Wrong item? enter new id here';

            $L[20] = 'In Progress';
            $L[21] = 'Approved, by GM';
            $L[22] = 'DENIED, by GM';
            $L[23] = 'Canceled, by you';
            $L[24] = 'DENIED BY SERVER, bad DUMP or not finished migration procedure';
            $L[25] = 'Before start character migration, download & install the game addon "<b>SaveMe</b>" for ';
            $L[26] = 'Enter "<b>/saveme</b>" in the chat frame start make a character dumping.<br/>
            To dump "Bank inventory", open all bags in it, before launch the /saveme command,
            to dump "Profession recipes" turn open the frames of the professions.
            Make log out and the dump file will be saved in the WTF \ Account \ %Username% \ SavedVariables \ saveme.lua <br/>
            New character will appear on <b>GM Account</b>, not your, after review it will be approved, denied or you can cancel it manually, if you want';
            $L[27] = 'Welcome to <b>ADMIN PANEL</b> Click';
            $L[28] = 'for approving or deny their transfers, of if need resend items';
            $L[29] = 'Success operation, Your character is available for customisation, log in on your account, and do that.';

            $L[30] = 'You have so much characters on Realm:';
            $L[31] = '(more then 9)';
            $L[32] = 'For Realm: ';
            $L[33] = 'All Queues FULL! Sorry, you need to wait, until one of reviewers be free, Thank you for Patience!';
            $L[34] = 'Transfer from this Server Rejected.';
            $L[35] = 'Allowed only 1 Attempt for character';
            $L[36] = 'Your SaveMe.lua is corrupted or out of date, please new addon.';
            $L[37] = 'Coins';
            $L[38] = 'You are not banned!';
            $L[39] = 'Console (Server)';

            $L[40] = 'I want transfer to Realm:';
            $L[41] = 'Selected file:';
            $L[42] = 'Please submit your "old" server account details, <b>GM</b> use their, to check your character. <br/> Please enter Valid Data, else migration rejected.';
            $L[43] = 'Your "old" server account';
            $L[44] = 'Your "old" server account`s password';
            $L[45] = 'Server URL, where Game Master can read info or identify your "old" server.';
            $L[46] = 'Select created file "saveme.lua"';
            $L[47] = 'Select in which realm you want to transfer your character';
            $L[48] = 'Sign Item';
            $L[49] = 'Enter item ID';

            $L[50] = 'Choice new name for character, only if your character name will be free your transfer can be allowed for checking';
            $L[51] = 'Character signed for review';
            $L[52] = '"\'^?$%&*()}{@#~?><>,|=_.+¬-\'" characters not allowed';
            $L[53] = 'Spaces not allowed in Character name';
            $L[54] = 'Numeric characters not allowed';
            $L[55] = 'Character name lenght can be from 2 to 16 characters.';
            $L[56] = 'Character with name:';
            $L[57] = 'already exists';
            $L[58] = 'Session Error, clean Cookie, try again, if happen again contact with adminstrator';
            $L[59] = 'Max username lenght 32 letters';

            $L[60] = 'Your character is Online, please log off with it, before make any actions!';
            $L[61] = 'Select your character';
            $L[62] = 'You do not have any character';
            $L[63] = 'Free';
            $L[64] = 'Price';
            $L[65] = 'Donate Points';
            $L[66] = 'Unban account';
            $L[67] = 'Success operation, Your character is teleported.';
            $L[68] = 'This character available for customisation process, please log in it, and finish it';
            $L[69] = 'Quest AutoComplete';

            $L[70] = 'Select quest for autocomplete';
            $L[71] = 'Select reward (item) from quest';
            $L[72] = 'Character do not have active quests for autocomplete';
            $L[73] = 'You status:';
            $L[74] = 'Exalted';
            $L[75] = 'Revered';
            $L[76] = 'Honored';
            $L[77] = 'Friendly';
            $L[78] = 'Neutral';
            $L[79] = 'Unfriendly';
            $L[80] = 'Hated';

            $L[81] = 'Select faction';
            $L[82] = 'Character: Buy item';
            $L[83] = 'Character: Rename';
            $L[84] = 'Character: Customization';
            $L[85] = 'Character: Change Faction';
            $L[86] = 'Character: Change Race';
            $L[87] = 'Character: Instant 80 level';
            $L[88] = 'Character: Instant exalted reputation';
            $L[89] = 'Character: Instant quest complate';
            $L[90] = 'Character: Instant remove deserter debuff';

            $L[91] = 'Character: Instant teleport to Dalaran';
            $L[92] = 'Character: Unstruck';
            $L[93] = 'Account: Password change';
            $L[94] = 'Account: Unban';
            $L[95] = 'Account: Billing History';
            $L[96] = 'Account: Оbtaining';
            $L[97] = 'Account: Registration';
            $L[98] = 'Small Playtime!';
            $L[99] = 'Server URL';

            $L[100] = 'Select Realm';
            $L[101] = 'Warrior';
            $L[102] = 'Paladin';
            $L[103] = 'Hunter';
            $L[104] = 'Rogue';
            $L[105] = 'Priest';
            $L[106] = 'Death Knight';
            $L[107] = 'Shaman';
            $L[108] = 'Mage';
            $L[109] = 'Warlock';
            $L[110] = 'All';
            $L[111] = 'Druid';

            $L[112] = 'Female';
            $L[113] = 'Male';
            $L[114] = 'before';
            $L[115] = 'after';
            $L['x'] = ''; // NOT USED 'Honor points';
            $L['x'] = ''; // NOT USED 'Arena points';
            $L[118] = 'Total Kills';
            $L[119] = 'Realm';

            $L[120] = 'Unknown';
            $L[121] = 'Human';
            $L[122] = 'Orc';
            $L[123] = 'Dwarf';
            $L[124] = 'Night Elf';
            $L[125] = 'Undead';
            $L[126] = 'Tauren';
            $L[127] = 'Gnome';
            $L[128] = 'Troll';
            $L[130] = 'Blood Elf';
            $L[131] = 'Draenei';

            $L[140] = 'Action Date';
            $L[141] = 'Action Description';
            $L[142] = 'Character name';
            $L[143] = 'IP Address';
            $L[144] = 'No active deserter effects on selected character';
            $L[145] = 'Wrong captcha!';
            $L[146] = 'Submit';
            $L[147] = 'Enter all details before submit';
            $L[148] = 'Passwords not match!';
            $L[149] = 'e-Mails not match!';

            $L[150] = 'not registered?';
            $L[151] = 'Username';
            $L[152] = 'Password';
            $L[153] = 'Confirm password';
            $L[154] = 'e-Mail';
            $L[155] = 'Confirm e-Mail';
            $L[156] = 'Enter captcha code here';
            $L[157] = 'Wrong Password!';
            $L[158] = 'Wrong e-Mail';
            $L[159] = 'e-Mail already used! Select other e-Mail';

            $L[160] = 'Username already used! Select other username';
            $L[161] = 'LIMITED OFFER!';
            $L[162] = 'Registration welcome bonus';
            $L[163] = 'No records';
            $L[164] = 'Success operation, Your character is available for rename, log in on your account, for do that.';
            $L[165] = 'Banned by:';
            $L[166] = 'Banned on:';
            $L[167] = 'Expried on:';
            $L[168] = 'NOW';
            $L[169] = 'Cashback!';

            $L[170] = 'I want to buy it!';
            $L[171] = 'spend';
            $L[172] = 'Wallet management';
            $L[173] = 'Account tools';
            $L[174] = 'Character tools:';
            $L[175] = 'PayPal';
            $L[176] = 'Donate via PayPal';
            $L[177] = 'Moneyboookers';
            $L[178] = 'Donate via Moneyboookers';
            $L[179] = 'WebMoney';

            $L[180] = 'Donate via WebMoney';
            $L[181] = 'Unban';
            $L[182] = 'Violate terms of use? Never mind!';
            $L[183] = 'Change Password';
            $L[184] = 'Does your password reliable? isn`t it?';
            $L[185] = 'Account History';
            $L[186] = 'Billing';
            $L[187] = 'Name Change';
            $L[188] = 'Change name, if you don`t like it';
            $L[189] = 'Customize';

            $L[190] = 'Change gender and face';
            $L[191] = 'Faction Change';
            $L[192] = 'Horde >> Alliance >> Horde';
            $L[193] = 'Instant 80';
            $L[194] = 'Lazy for leveling? DO IT NOW!';
            $L[195] = 'Unstruck';
            $L[196] = 'Stuck somewhere? That will help you!';
            $L[197] = 'Race Change';
            $L[198] = 'Wan`t be a orc? Try play with troll!';
            $L[199] = 'Remove Deserter';

            $L[200] = 'That was a mistake for live BG, isn`t it?';
            $L[201] = 'Teleport to Dalaran';
            $L[202] = 'All roads come do Dalaran!';
            $L[203] = 'Instant Exalted Reputation';
            $L[204] = 'Find new friends or huge communities!';
            $L[205] = 'Character Migration';
            $L[206] = 'Why need to spend time for leveling?';
            $L[207] = 'Buy an item instantly';
            $L[208] = 'Why need to spend time for get it?';
            $L[209] = 'Complete quest';

            $L[210] = 'Not work quest? Complete it here!';
            $L[211] = 'New passwords not match each other!';
            $L[212] = 'Wrong current password';
            $L[213] = 'Password successfully changed';
            $L[214] = 'Please enter current and new password (password is case sensitive!)';
            $L[215] = 'Current password';
            $L[216] = 'New password';
            $L[217] = 'Confirm new password';
            $L[218] = 'Change Password';
            $L[219] = 'Looking for someone?';
            $L[220] = 'Look account billing';

            $L[221] = 'ADMIN: Billing';
            $L[222] = 'Seems bad characters, not enough achievements!';
            $L[223] = 'Wrong file!';
            $L[224] = 'Acess denied';
            $L[225] = 'Wrong Migration status. Require "In Progress"';
            $L[226] = 'Items re-sended!';
            $L[227] = 'Migration with ID:';
            $L[228] = 'Approved';
            $L[229] = 'Canceled';
            $L[230] = 'Not meet requirements!';

            $L[231] = 'Character with GUID';
            $L[232] = 'Denied, because';
            $L[233] = 'This character available for renaming process, please log in it, and finish it';
            $L[234] = 'Success operation, Your character is available for change faction process, log in on your account, and do that.';
            $L[235] = 'This character available for change faction process, please log in it, and finish it.';
            $L[236] = 'Success operation, Your character is available for change race, log in on your account, and do that.';
            $L[237] = 'This character available for change race process, please log in it, and finish it.';
            $L[238] = 'Success operation, Item will be arrive via mailbox, check you character ingame!';
            $L[239] = 'Success operation, Character teleported!';
            $L[240] = 'About Us';

            $L[241] = 'Online';
            $L[242] = 'How to connect';
            $L[243] = 'Forum';
            $L[244] = 'Account Panel';
            $L[245] = 'Character Migration';
            $L[246] = 'Registration';
            $L[247] = 'Login';
            $L[248] = 'Changelog';
            $L[249] = 'News feed';
            $L[250] = 'Online Statistic';

            $L[251] = 'RESET';
            $L[252] = 'Modify';
            $L[253] = 'Enter amount of <i class = "icon-fire"></i> what you want to add, or remove from player.
            if you want remove. type -Amount. Example: -500, if you want to add +500 or just 500,
            in comment enter your comment (max 100 chars) if you left it empty, your username will used like a comment';
            $L[254] = 'Close';
            $L[255] = 'Enter amount';
            $L[256] = 'Enter comment';
            $L[257] = 'Account: Remove <i class = "icon-fire"></i> by GM';
            $L[258] = 'Goblin';
            $L[259] = 'Worgen';
            $L[260] = 'Balance';

            $L[261] = 'INSPECT';
            $L[262] = 'Donate via';
            $L[263] = 'Choice how to donate';
            $L[264] = 'Here listed all avaible methods';
            $L[265] = 'Gold Stock';
            $L[266] = 'Exchange gold coins to <i class = "icon-white icon-fire"></i> and back';
            $L[267] = 'I want to sell gold!';
            $L[268] = 'I want to buy gold!';
            $L[269] = 'Account: buy a gold';
            $L[270] = 'No available factions for this action';

            $L[271] = 'e-mail';
            $L[272] = 'Joined';
            $L[273] = 'Last Login';
            $L[274] = 'Last IP';
            $L[275] = 'Item';

            $L[280] = 'Consumable';
            $L[281] = 'Containers';
            $L[282] = 'Weapons';
            $L[283] = 'Gems';
            $L[284] = 'Armor';
            $L[285] = 'Reagents';
            $L[286] = 'Projectiles';
            $L[287] = 'Trade Goods';
            $L[288] = 'Select quest for autocomplete, or leave it, for complete: ';
            $L[289] = 'Recipes';
            $L[290] = 'Complete Quest: Step 1';
            $L[291] = 'Complete Quest: Step 2';
            $L[292] = 'Quest';
            $L[293] = 'Keys';
            $L['x'] = ''; // NOT USE
            $L[295] = 'Misc.'; // 'Miscellaneous'
            $L[296] = 'Glyphs';
            $L[297] = 'Next';
            $L[298] = 'Previous';
            $L[299] = 'Checkout';
            $L['about_us'] =
            '<h2>About us</h2>
                We are community of interests who improve opensource projects related to WoW Emulation. We like this game, we find that as very existing
                to create our own world. We welcome you in our Realms, here you can check lastest and problably one of the best solutions for WoW emulation.
                We are not asking for material benefits to play on our realms, if you like support us, we always accept donations, for pay for cost of production.
                We will thank you for your donation via donation coins (called like Coins or Fire stamps)
                You can withdraw your <i class = \'icon-fire\'></i> every time, just check a conditions, which allow you do that. Fixed rate for withdraw 1 <i class = \'icon-fire\'></i> = 0.09 €
                ';
            $L['how_to_connect'] =
            '<h2>How to connect</h2>
                <ul>
                    <li>Download Game client. (torrent, en_GB, ru_RU)</li>
                    <ul>
                        <li>If you have already have a game client, need change to set realmlist <ADDRESS> </li>
                    </ul>
                    <li>Register & Confirm account</li>
                    <li>Create character</li>
                    <li>Play!</li>
                </ul>';
            break;
        }
    }

    function _getAMLanguage() { global $_SESSION; global $DEFAULT_LANGUAGE; return isset($_SESSION['SL']) ? $_SESSION['SL'] : $DEFAULT_LANGUAGE; }

    function _setAMLanguage($X) {
        global $_SESSION;
        global $DEFAULT_LANGUAGE;
        $_SESSION['SL'] = $X;
        if(_isLanguageSupported($X))
            $_SESSION['SL'] = $X;
        else
            $_SESSION['SL'] = $DEFAULT_LANGUAGE;
    }

    function _isLanguageSupported($X) {
        global $AvailableLanguages;
        $match = null;
        $LANG = explode(',', $AvailableLanguages);
        foreach($LANG as $KEY) {
            $match = $KEY == $X;
            if($match)
                return true;
        }
        return $match;
    }

    function _getWHLanguage() {
        switch(_getAMLanguage()) {
            case "ru_RU": /* RUSSIAN */
                return "ru";
            case "fr_FR": /* FRENCH */
                return "fr";
            case "de_DE": /* GERMAN */
                return "de";
            case "es_ES": /* SPANISH - SPAIN */
                return "es";
            case "en_US": /* ENGLISH */
            default:
                return "old";
        }
    }
?>