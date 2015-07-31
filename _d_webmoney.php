<?php


    include_once('_template/_header.php');
?>

<h2> NEED IMPLEMENT BY SERVER OWNER </h2>
<div class = 'alert alert-error'>
    For enable accept donations, you need make a script of conditions, and at the end add the function call: _GiveMythCoins(5, "COMMENT TYPE: TEXT", $connection);
    if comment we suggest make a payment system name, for example:
    $connection is LoginDB Argument. Please make sure that is it opened. don`t forget close it after operation
    <i>_GiveMythCoins(5, "PayPal", $connection)</i> or <i>_GiveMythCoins(5, "Moneybookers", $connection)</i> or  <i>_GiveMythCoins(5, "WebMoney", $connection)</i>
    <a href = 'index.php'>Click here for back to menu</a>
</div>

<?php
    include_once('_template/_footer.php');

/*  for( $i = 1; $i < 101; $i++)
    {
        if($i%5 == 0)
            echo "EUR: ". ($i - 0.01) ."    NetCoins: ". round($i * 4 + round($i - 5)) . "<br/>";
    }*/
?>