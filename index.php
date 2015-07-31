<?php


    if(is_dir("_!_DELETE_AFTER_INSTALL_!_") && $_SERVER['REMOTE_ADDR'] != "127.0.0.1")
        die("DELETE INSTALATION FOLDER");

    include_once('_template/_header.php');

    if(_getUsername())
        Header('Location: _userside.php');

    if(!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['CaptchaText']) ||
        empty($_POST['username']) || empty($_POST['password'])  || empty($_POST['CaptchaText']))
        $REASON = _RDiv($L[147]);
    else if($_SESSION['capcha'] != strtolower($_POST['CaptchaText']))
        $REASON = _RDiv($L[145]);
    else {
        $username       = _Z($_POST['username']);
        $SHA1Password   = SHA1Password($username, _Z($_POST['password']));

        $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);

        $query  = mysql_query("SELECT `id`,`username` FROM `account` WHERE `username` = '". _X($username) ."' AND `sha_pass_hash` = '". _X($SHA1Password) ."';", $connection) or die(mysql_error());
        $result = mysql_fetch_array($query);

        if(empty($result['username'])) {
            $REASON = _RDiv($L[157]);
            mysql_close($connection) or die(mysql_error());
        } else {
            $_SESSION['AccountID'] = $result['id'];
            $_SESSION['AccountUN'] = strtoupper($result['username']);
            $query  = mysql_query("SELECT `id` FROM `account_details` WHERE `id` = ". (int)_getAccountID() .";", $connection) or die(mysql_error());
            $result = mysql_fetch_array($query);
            if(empty($row[0]))
                mysql_query("INSERT IGNORE INTO `account_details`(`id`) VALUES (". (int)_getAccountID() .");", $connection) or die(mysql_error());
            mysql_close($connection) or die(mysql_error());
            Header('Location: _userside.php');
        }
    }
?>
    <div class = 'text-center'>
        <h2><?php echo $L[1]; ?></h2>
        <?php echo $REASON; ?>
        <form action = '<?php echo $_SERVER['PHP_SELF'] ?>' method = 'POST' id = 'login'>
            <fieldset>
                <div class = 'clearfix'>
                    <input name = 'username' type = 'text' placeholder = <?php _PPSTR($L[151]) ?> >
                </div>
                <div class = 'clearfix'>
                    <input name = 'password' type = 'password' placeholder = <?php _PPSTR($L[152]) ?> >
                </div>
                <br/>
                <img id = 'captcha' alt = '' src = '_captcha/_captcha.php'/>
                <div class = 'clearfix'>
                    <br/>
                    <input name = 'CaptchaText' type = 'text' maxlength = '8' placeholder = <?php _PPSTR($L[156]) ?> >
                </div>
                <button class = 'btn btn-primary' type = 'submit'><?php echo $L[2]; ?></button>
                <br/>
                <br/>
                <p><a href = '_acc_registration.php'><i><?php echo $L[150]; ?></i></a></p>
            </fieldset>
        </form>
    </div>
<?php include_once('_template/_footer.php');
    ob_end_flush();
?>