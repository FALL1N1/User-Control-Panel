<?php

    include_once('_template/_header.php');

    if(!_getUsername())
        Header('Location: index.php');

    if(!isset($_POST['cur_password']) || !isset($_POST['new_password']) || !isset($_POST['new_password2']) ||
        empty($_POST['cur_password']) || empty($_POST['new_password'])  || empty($_POST['new_password2']))
        $reason = _BDiv($L[214]);
    else if($_POST['new_password'] !== $_POST['new_password2'])
        $reason = _RDiv($L[211]);
    else {
        $SHA1Password    = SHA1Password(_getUsername(), _Z($_POST['cur_password']));
        $SHA1PasswordNEW = SHA1Password(_getUsername(), _Z($_POST['new_password']));

        $connection = _MySQLConnect($AccountDBHost, $DBUser, $DBPassword, $AccountDB);
        $query  = mysql_query("SELECT `id` FROM `account` WHERE `username` = '". _getUsername() ."' AND `sha_pass_hash` = '". _X($SHA1Password) ."';", $connection) or die(mysql_error());
        $result = mysql_fetch_array($query);

        if(!empty($result['id'])) {
            mysql_query("UPDATE `account` SET `sha_pass_hash` = '". _X($SHA1PasswordNEW) ."',`sessionkey` = '',`v` = '',`s` = '' WHERE `username` = '". _getUsername() ."';", $connection) or die(mysql_error());
            _SpendMythCoins(0, 13, "", 0, "", 0, "", $connection);
            $reason = _BDiv($L[213]);
            mysql_close($connection) or die(mysql_error());
        } else {
            $reason = _RDiv($L[212]);
            mysql_close($connection) or die(mysql_error());
        }
    }
?>
    <div class = 'text-center'>
        <h2><?php echo $L[93]; ?></h2>
        <?php echo $reason; ?>
        <fieldset>
        <form action = '<?php echo $_SERVER['PHP_SELF'] ?>' method = 'POST' id = 'login'>
            <div class = 'clearfix'>
                <input name = 'cur_password' type = 'password' placeholder = <?php _PPSTR($L[215]) ?> >
            </div>
            <div class = 'clearfix'>
                <input name = 'new_password' type = 'password' placeholder = <?php _PPSTR($L[216]) ?> >
            </div>
            <div class = 'clearfix'>
                <input name = 'new_password2' type = 'password' placeholder = <?php _PPSTR($L[217]) ?> >
            </div>
            <button class = 'btn btn-primary' type = 'submit'><?php echo $L[218]; ?></button>
        </form>
        </fieldset>
    </div>
<?php include_once('_template/_footer.php');
    ob_end_flush(); ?>