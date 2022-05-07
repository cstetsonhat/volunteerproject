<?php


/**
 * @author [Cathleen Stetson, IT4400, Final Project]
 * @email [cpm4bf@virginia.edu]
 * @create date 2022-05-06 18:54:44
 * @modify date 2022-05-06 18:55:47
 */


function renderOpportunity($profile)
{

    $view_profile = <<<PROFILE
    <div class="d-flex flex-col">
        <h2>My profile</h2>

        <form action="" class="d-flex flex-col gap" style="--gap:1rem">
            <div class="form_group d-flex justify-space"  style="--gap:1rem">
                <label for="">Email</label>
                <input type="text" name="email" readonly value="{$profile['email']}">
            </div>
          
            <div class="form_group d-flex justify-space"  style="--gap:1rem">
                <label for="">Occupation</label>
                <input type="text" name="occupation" readonly value="{$profile['occupation']}">
            </div>
            <div class="form_group d-flex justify-space"  style="--gap:1rem">
                <label for="">V. Position</label>
                <input type="text" name="opportunity" readonly value="{$profile['position']}">
            </div>
        </form>
    </div>
PROFILE;

    print($view_profile);
}


renderOpportunity($_SESSION['user']);
// renderOpportunity(['username'=> 'someuser','occupation'=>'doctor','opportunity'=> 'meal prep']);
