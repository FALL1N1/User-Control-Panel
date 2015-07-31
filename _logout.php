<?php


    include_once('_template/_header.php');

    session_unset();
    session_destroy(); ?>

    <div class = 'row text-center'>
        <fieldset>
            <h2><?php echo $L[6]; ?></h2>
            <a href = 'index.php'><button class = 'btn btn-primary' type = 'submit'><?php echo $L[7]; ?></button></a>
        </fieldset>
    </div>
<?php include_once('_template/_footer.php');
    ob_end_flush(); ?>