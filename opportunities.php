<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Volts - Opportunities</title>
</head>

<body>

    <?php include_once('navbar.php') ?>




    <div class="page_content d-flex justify-center c-m-t" style="--c-m-t:3rem">
        <?php $opportunities = getOpportunities(); ?>

        <table id="manage_opportunities">
            <thead>
                <tr>
                    <td># Opportunities: <?php print(count($opportunities)); ?></td>
                    <td>Position</td>
                    <td>Date</td>
                    <td>Time</td>
                    <td>Assigned</td>
                </tr>
            </thead>

            <tbody>
                <?php
                renderOpportunities("opportunities.php",$opportunities, true,false)
                ?>
            </tbody>

    </div>

</body>

</html>