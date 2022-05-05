<?php include_once("globals.php"); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Volts - Manage Opportunities</title>
</head>

<body>

    <?php include_once('navbar.php') ?>


    <!-- banner help the society -->

    <div class="section_opportunities d-flex flex-col">
        <form class="d-flex c-margin gap c-padding" action="manage_opportunities.php?action=add_opportunity" method="GET" style="--c-margin:1rem;--gap:3rem;--c-padding:1rem">
            <legend>New Opportunity</legend>

            <?php
            $opportunity;

            if (isset($_REQUEST['opportunity_id'])) {
                [$opportunity] = runQuery('select * from opportunities where id =?','select','d',[$_REQUEST['opportunity_id']]);
            }
            ?>
            <div class="form-group">
               
                <label for="">ID</label><input type="text" name='id' required readonly <?php isset($opportunity)? printf("value=\"%s\"",$opportunity['id']):'';?> >
            </div>


            <div class="form-group">
               
                <label for="">Position</label><input type="text" name='position' required <?php isset($opportunity)? printf("value=\"%s\"",$opportunity['position']):'';?> >
            </div>

            <div class="form-group">

                <label for="">date</label><input type="date" name='date' required  <?php isset($opportunity)? printf("value=\"%s\"",$opportunity['date']):'';?> >
            </div>

            <div class="form-group">

                <label for="">time</label><input type="time" name='time' required  <?php isset($opportunity)? printf("value=\"%s\"",$opportunity['time']):'';?> >
            </div>

            <div class="formg-group">
                <?php

                if (isset($_REQUEST['opportunity_id'])) {
                    print('<button type="submit" name="action" value="edit_opportunity">edit</button>');
                } else
                    print(' <button type="submit" name="action" value="add_opportunity">Add</button>');
                ?>
            </div>

        </form>

        <div class="opportunities_recent d-flex">
            <?php $opportunities = getOpportunities();?>
           
            <table>
                <thead>
                    <tr>
                        <td># Opportunities: <?php print(count($opportunities));?></td>
                        <td>Position</td>
                        <td>Date</td>
                        <td>Time</td>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    renderOpportunities($opportunities)
                    ?>
                </tbody>
            </table>

            <table id="opportunity_applications_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Volunteer id</th>
                        <th>Name</th>
                        <th>Occupation</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php renderOpportunityApplications(); ?>
                </tbody>
            </table>
        </div>

    </div>

    <?php include_once('footer.php'); ?>
</body>

</html>