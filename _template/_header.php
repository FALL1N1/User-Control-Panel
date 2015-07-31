<?php


    ob_start();
    session_start();

    include_once('_core/_config.php');
    include_once('_core/_language.php');
    include_once('_core/_functions.php');
    include_once('_core/_dbfunctions.php'); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv = 'Content-Type' content = 'text/html; charset=UTF-8'/>
        <meta name = 'robots' content = 'index, follow'/>
        <link href = 'favicon.ico' rel = 'icon' type = 'image/ico'/>
        <link rel = 'stylesheet' type = 'text/css' href = '_template/css/bootstrap.min.css'/>
        <link rel = 'stylesheet' type = 'text/css' href = '_template/css/bootstrap-formhelpers-countries.flags.css'/>
        <link rel = 'stylesheet' type = 'text/css' href = '_template/css/bootstrap-formhelpers.css'/>
        <script type = 'text/javascript' src = '_template/js/jquery-1.7.2.min.js'></script>
        <script type = 'text/javascript' src = '_template/js/bootstrap.min.js'></script>
        <script type = 'text/javascript' src = '_template/js/bootstrap-formhelpers-languages.js'></script>
        <script type = 'text/javascript' src = '_template/js/bootstrap-formhelpers-selectbox.js'></script>
        <script type = 'text/javascript' src = '_template/js/power.js'></script>
        <title><?php echo $L[8]; ?></title>
        <style>
        body { background-color: #f5f5f5; }
        .container,
        .navbar-static-top .container,
        .navbar-fixed-top .container,
        .navbar-fixed-bottom .container { width: 900px; }
        .container > .content {
            background-color: #fff;
            padding: 20px;
            margin: 0 -20px;
            -webkit-border-radius: 10px 10px 10px 10px;
            -moz-border-radius: 10px 10px 10px 10px;
            border-radius: 10px 10px 10px 10px;
            -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.15);
            -moz-box-shadow: 0 1px 2px rgba(0,0,0,.15);
            box-shadow: 0 1px 2px rgba(0,0,0,.15);
        }
        .service {
            display: block;
            position: relative;
            float: left;
            padding-bottom: 5px;
            padding-top: 5px;
            width: 240px;
            height: auto;
            margin: 1px;
            text-align: left;
            font-size: 11px;
        }
        .service:hover{
            color: #3a87ad;
            background-color: #d9edf7;
            border-color: #bce8f1;
        }
        .service_icon {
            position: relative;
            float: left;
            height: 35px;
            width: 45px;
            margin-left: 10px;
            padding-top: 6px;
        }
        .service_desc {
            font-family: Calibri, newCalibri, Arial;
            padding-top: -20px;
            font-size: 11px;
        }
        .service h4 {
            font-size: 14px;
            padding-left: -5px;
        }
        .charBox {
            position: relative;
            width: 435px;
            float: left;
            margin: 3px;
            padding: 3px;
        }
        .charBoxClean {
            position: relative;
            width: 435px;
            float: left;
            margin: 3px;
            padding: 3px;
        }
        .charBox:hover {
            color: #3a87ad;
            background-color: #d9edf7;
            border-color: #bce8f1;
        }
        .discountSpan { Text-decoration: line-through; }
        .reputationBlock { width: 393px; }
        .stockBlock { width: 172px; }
        .menuBar th, .menuBar td { border-top: none; }
        .fIcon { display:inline; position:relative; top:-7px; }
        .BillingB { position:relative; left: 5px; top: -5px; }
        </style>
        <script>
        var wowhead_tooltips = { 'colorlinks': true, 'iconizelinks': true, 'renamelinks': true }

        $(document).on('mouseover', '.tip', function () {
            if( $(this).data('isTooltipLoaded') == true )
            { return; }
            $(this).data('isTooltipLoaded', true).tooltip().trigger('mouseover');
        });

        function _languageSubmit() { $('#LSF').submit(); }

        var BFHLanguagesList = {
           'en': 'English',
           'de': 'Deutsch',
           'fr': 'Français',
           'es': 'Español',
           'pt': 'Português',
           'ru': 'Русский',
           'uk': 'Українська'
        }
        </script>
    </head>
    <body>

    <div class = 'container'>
        <div class = 'navbar navbar-inverse navbar-fixed-top'>
            <div class = 'navbar-inner'>
                <div class = 'container'>
                    <a class = 'brand' href = 'index.php'><?php echo $L[8] ?></a>
                    <div class = 'nav-collapse'>
                        <ul class = 'nav'>
                            <li class = 'active'><a><?php echo mb_strtoupper(_getUsername() ? _getUsername() : $L[16] , 'UTF-8'); ?></a></li>
                            <?php
                                if(_getUsername()) {
                                    echo "
                                    <li><a href = '_userside.php'>". $L[4] ."</a></li>
                                    <li><a href = '_logout.php'>". $L[5] ."</a></li>";

                                    $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
                                    $query      = mysql_query("SELECT `myth_coins` FROM `account_details` WHERE `id` = ". _getAccountID() ." ;", $connection) or die(mysql_error());
                                    $row        = mysql_fetch_array($query);
                                    echo "<li><a href = '_acc_history.php'><i class = 'icon-white icon-fire'></i>". $row[0] ." ". $L[37] ."</a></li>";
                                    mysql_close($connection) or die(mysql_error());
                                };
                                ?>
                            <li><a href = '__online_statistic.php'><?php echo $L[250]; ?></a></li>
                            <li>
                                <form style = 'display:inline; position:relative; left: 5px; top:5px;' id = 'LSF' method = 'post'>
                                    <div class = 'bfh-selectbox bfh-languages' data-language = '<?php echo _getAMLanguage() ?>' data-available = '<?php echo $AvailableLanguages ?>' data-flags = 'true'>
                                        <input onchange = '_languageSubmit();' type = 'hidden' name = 'LSec' value = ''>
                                        <a class = 'bfh-selectbox-toggle' role = 'button' data-toggle = 'bfh-selectbox' href = '#'>
                                        <span class = 'bfh-selectbox-option input-medium' data-option = ''></span>
                                        <b class = 'caret'></b>
                                        </a>
                                        <div class = 'bfh-selectbox-options'>
                                            <div role = 'listbox'>
                                                <ul role = 'option'></ul>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class = 'content'>
        <br/>
        <br/>