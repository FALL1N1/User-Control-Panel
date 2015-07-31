<?php

    include_once('_template/_header.php');

    if(_getUsername())
        Header('Location: _userside.php');

    if(!isset($_POST['username'])
    || !isset($_POST['password'])
    || !isset($_POST['password2'])
    || !isset($_POST['email'])
    || !isset($_POST['email2'])
    || !isset($_POST['CaptchaText']))
        $REASON = _RDiv($L[147]);
    else if($_SESSION['capcha'] != strtolower($_POST['CaptchaText']))
        $REASON = _RDiv($L[145]);
    else if($_POST['password'] !== $_POST['password2'])
        $REASON = _RDiv($L[148]);
    else if($_POST['email'] !== $_POST['email2'])
        $REASON = _RDiv($L[149]);
    else if(!_is_e_mail_ok($_POST['email']))
        $REASON = _RDiv($L[158]);
    else if(!_is_details_already_used($AccountDBHost, $AccountDB, $DBUser, $DBPassword, 'email', $_POST['email']))
        $REASON = _RDiv($L[159]);
    else if(!_is_details_already_used($AccountDBHost, $AccountDB, $DBUser, $DBPassword, 'username', $_POST['username']))
        $REASON = _RDiv($L[160]);
    else {
        $username       = _Z($_POST['username']);
        $email          = _Z($_POST['email']);
        $SHA1Password   = SHA1Password($username, _Z($_POST['password']));
        $connection     = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
        mysql_query("INSERT INTO `account`(`username`,`sha_pass_hash`,`email`) VALUES ('". _X($username) ."', '". _X($SHA1Password) ."', '". _X($email) ."');", $connection) or die(mysql_error());

        $_SESSION['AccountID']  = (int)mysql_insert_id($connection);
        $_SESSION['AccountUN']  = $username;
        _SpendMythCoins(0, 12, "", 0, "", 0, "", $connection);
        _GiveMythCoins(5, $L[162], $connection);
        mysql_close($connection) or die(mysql_error());
        $REASON = _GDiv($L[17]);
        Header('Location: _userside.php');
    }
?>
    <div class = "text-center">
        <h2><?php echo $L[97]; ?></h2>
        <?php echo isset($REASON) ? $REASON : ""; ?>
        <form action = "<?php echo $_SERVER['PHP_SELF'] ?>" method = "POST" id = "login">
            <fieldset>
                <div class = 'clearfix'>
                    <input name = 'username' type = 'text' placeholder = <?php _PPSTR($L[151]) ?> >
                </div>
                <div class = 'clearfix'>
                    <input name = 'password' type = 'password' placeholder = <?php _PPSTR($L[152]) ?> >
                </div>
                <div class = 'clearfix'>
                    <input name = 'password2' type = 'password' placeholder = <?php _PPSTR($L[153]) ?> >
                </div>
                <div class = 'clearfix'>
                    <input name = 'email' type = 'text' placeholder = <?php _PPSTR($L[154]) ?> >
                </div>
                <div class = 'clearfix'>
                    <input name = 'email2' type = 'text' placeholder = <?php _PPSTR($L[155]) ?> >
                </div>
                <img id = 'captcha' alt = '' src = '_captcha/_captcha.php'/>
                <div class = 'clearfix'>
                    <br/><input name = 'CaptchaText' type = 'text' maxlength = '8' placeholder = <?php _PPSTR($L[156]) ?> >
                </div>
                <button class = "btn btn-primary" type = "submit"><?php echo $L[146]; ?></button>
            </fieldset>
        </form>
    </div>
<?php include_once('_template/_footer.php');
    ob_end_flush();

    function _is_e_mail_ok($X) {
        if(!filter_var($X, FILTER_VALIDATE_EMAIL))
            return false;
        return true;
    }

    function _is_details_already_used($AccountDBHost, $AccountDB, $DBUser, $DBPassword, $D, $X) {
        $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
        $query      = mysql_query("SELECT `id` FROM `account` WHERE `". $D ."` = '". _X($X) ."';", $connection) or die(mysql_error());
        $result     = mysql_num_rows($query);
        mysql_close($connection) or die(mysql_error());
        if($result > 0)
            return false;
        return true;
    }
?>