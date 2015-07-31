<?php


    include_once('_template/_header.php');

    if(!_getUsername())
        Header('Location: index.php');
?>
    <fieldset>
        <legend><?php echo $L[262]; ?></legend>
        <div>
            <a href = '_d_paypal.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/1358712013_paypal.png'></div>
                <h4><?php echo $L[175]; ?></h4>
                <div class = 'service_desc'><?php echo $L[176]; ?></div>
            </div></a>
            <a href = '_d_moneybookers.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/1358711986_moneybookers.png'></div>
                <h4><?php echo $L[177]; ?></h4>
                <div class = 'service_desc'><?php echo $L[178]; ?></div>
            </div></a>
            <a href = '_d_webmoney.php'><div class = 'alert service'>
                <div class = 'service_icon'><img src = '_template/img/1358711685_webmoney.png'></div>
                <h4><?php echo $L[179]; ?></h4>
                <div class = 'service_desc'><?php echo $L[180]; ?></div>
            </div></a>
        </div>
    </fieldset>

<?php include_once('_template/_footer.php');
    ob_end_flush();
?>