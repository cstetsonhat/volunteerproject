<?php
/**
 * @author [Cathleen Stetson, IT4400, Final Project]
 * @email [cpm4bf@virginia.edu]
 * @create date 2022-05-06 18:54:44
 * @modify date 2022-05-06 18:55:47
 */


/**
 * @description information or messages can be placed in the alert key on the
 *  session object which will be displayed to the user
 */
function showAlert($session)
{

    if (!isset($session, $session['alert']) || count($session['alert']) == 0) {
        return;
    }

    $messages = json_encode(implode('\n', $session['alert']['message']));

    print(<<<OUTPUT
    
    setTimeout(()=> { 
        console.log(`%c $messages`,"background-COLOR:orange;color:black;padding:0.02rem;");
        alert(`$messages`)
    },300);

OUTPUT
    );


    unset($_SESSION['alert']);
}



/**
 * @descriptiion show logs for debugging
 */
function showLog($session)
{
    if (!isset($session, $session['LOG_CONSOLE'])) {
        return;
    }


    foreach ($session['LOG_CONSOLE']['message'] as $key => $message) {
        printf("console.log('ph->log: {$key}',`%s`);",json_encode($message));
    }

    unset($_SESSION['LOG_CONSOLE']);
}

showAlert($_SESSION);

showLog($_SESSION);
