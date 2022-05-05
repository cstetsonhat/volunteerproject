<?php

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
                <label for="">Username</label>
                <input type="text" name="username" readonly value="{$profile['username']}">
            </div>
            <div class="form_group d-flex justify-space"  style="--gap:1rem">
                <label for="">Occupation</label>
                <input type="text" name="occupation" readonly value="{$profile['occupation']}">
            </div>
            <div class="form_group d-flex justify-space"  style="--gap:1rem">
                <label for="">Opportunity</label>
                <input type="text" name="opportunity" readonly value="{$profile['opportunity']}">
            </div>
        </form>
    </div>
PROFILE;

    print($view_profile);
}


renderOpportunity($_SESSION['user']);
// renderOpportunity(['username'=> 'someuser','occupation'=>'doctor','opportunity'=> 'meal prep']);
