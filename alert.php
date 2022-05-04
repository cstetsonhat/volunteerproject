<?php


/**
 * @description information or messages can be placed in the alert key on the
 *  session object which will be displayed to the user
 */
function showAlert($session)
{

    if (!isset($session, $session['alert'])) {
        return;
    }

    //show some the alert 
    print("alert(\"{$session['alert']['message']}\");");
    
    unset($session['alert']);
}

// showAlert([
//     'alert' =>
//     ['message' => 'will show any message passed to it']
// ]);

/**
 * @descriptiion show logs for debugging
 */
function showLog($session){
    if (!isset($session, $session['LOG_CONSOLE'])){
        return ;
    }
  
    $message = implode(" __ ",$session['LOG_CONSOLE']['message']);
    
    print("console.log(\"php_log=>\",\"{ $message}\");");

    unset($session['LOG_CONSOLE']);
}

showAlert($_SESSION);

showLog($_SESSION);
