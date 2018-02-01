<!DOCTYPE html>
<html dir="ltr" lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta charset="UTF-8">

        <?= $this->header() ?>
        <?= $this->css() ?>
        <?= $this->js() ?>
    </head>

    <body>
                <?=$this->module('modals')?>
            <?= $this->maincontent ?> 
    
</html>