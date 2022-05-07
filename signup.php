<?php


/**
 * @author [Cathleen Stetson, IT4400, Final Project]
 * @email [cpm4bf@virginia.edu]
 * @create date 2022-05-06 18:54:44
 * @modify date 2022-05-06 18:55:47
 */

include_once("globals.php");
onNoUserSessionThenRedirect();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Volts - Signup</title>
</head>

<body>

    <?php include_once('navbar.php') ?>


    <!-- banner help the society -->

    <div class="banner d-flex c-height justify-center c-margin-top" style="--c-height:500px;--c-margin-top:3rem;">
        <div class="banner_img c-width" style="--c-width:50%">
            <img src="https://media.istockphoto.com/photos/group-of-volunteers-unpack-donated-items-picture-id1124307261" class="w-100 h-100" alt="">
        </div>

        <div class="banner_content border c-padding d-flex flex-col" style="--c-padding:3rem">

            <h2>Sharing food is sharing happiness</h2>
            <p>
                Come volunteer or share with us
            </p>

            <?php include_once('signup_cmp.php') ?>
        </div>
    </div>

    <?php include_once('footer.php'); ?>
</body>

</html>