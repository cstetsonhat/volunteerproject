<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$con = mysqli_connect("localhost", "root", "password", "foodbankvolunteers");

session_start();

function makeLog($key, $body)
{

    if (!isset($_SESSION['LOG_CONSOLE']['message'])) {
        $_SESSION['LOG_CONSOLE']['message'] = [];
    }

    array_unshift($_SESSION['LOG_CONSOLE']['message'], $body);
}

function  makeAlert($message)
{
    if (!isset($_SESSION['alert']['message'])) {
        $_SESSION['alert']['message'] = [];
    }

    array_unshift($_SESSION['alert']['message'], $message);
}

function onNoUserSessionThenRedirect()
{
    if (!isset($_SESSION, $_SESSION['user'])) {
        header("Location", "index.php");
    }
}

function signIn($data)
{
    makeLog('message', 'Sign in triggered');


    ['email' => $email, 'password' => $password] = $data;

    [$user] = runQuery("select * from volunteers where email=? and password=?", 'select', 'ss', [$email, hash('sha256', $password)]);

    if (!isset($user)) {
        throw new Error('Invalid credentials');
    }

    $_SESSION['user'] = $user;
}



function signUp($data)
{
    makeLog('message', 'sign up triggered');

    ['email' => $email, 'password' => $password, 'password_confirm' => $password_confirm, 'occupation' => $occupation, 'firstname' => $firstname, 'lastname' => $lastname] = $data;

    if (runQuery("select count(*) as 'exists' from volunteers where email=?;", 'select', 's', [$email])[0]['exists']) {
        throw new Exception("User exists already, please try with a new email");
    };


    if (strcmp($password, $password_confirm) != 0) {
        throw new \Error("Password confirmation does not match");
    }


    //insert into the database

    $result = runQuery("insert into volunteers (email, password, occupation,firstname,lastname) values (?,?,?,?,?)", 'insert', "sssss", [$email, hash('sha256', $password), $occupation, $firstname, $lastname]);

    if (!isset($result) || $result <= 0) {
        throw new \Error("Error: insert failed. " . mysqli_error(getMysqliConnection()));
    };


    [$user] = runQuery("select * from volunteers where email=?", 'select', 's', [$email]);

    if (!$user) {
        throw new \Error(mysqli_error(getMysqliConnection()));
    }


    // set session for the user 
    $_SESSION['user'] = $user;

    //go to login page
    header("Location", "index.php");
}


/**
 * @return Array|int
 */
function runQuery($query, $query_type, $bind_types, $params)
{


    switch ($query_type) {
        case 'insert':

            // validation 
            if (!isset($query, $params, $bind_types) || strlen($bind_types) != count($params)) {
                throw new \Error(sprintf('Insert error query or params invalid bind_types:%s  $params: %s', strlen($bind_types), count($params)));
            }

            $stmt = mysqli_prepare(getMysqliConnection(), $query);

            if (strlen($bind_types) > 1 && !mysqli_stmt_bind_param($stmt, $bind_types, ...$params)) {

                throw new \Error(sprintf("bind error: %s  sql_error:%s", mysqli_stmt_error($stmt), mysqli_error(getMysqliConnection())));
            }

            if (!mysqli_stmt_execute($stmt)) {
                throw new Error(mysqli_stmt_error(getMysqliConnection()));
            }


            return mysqli_affected_rows(getMysqliConnection());


            break;

        case 'select':
            $stmt = mysqli_prepare(getMysqliConnection(), $query);

            if (count($params) > 0 && !mysqli_stmt_bind_param($stmt, $bind_types, ...$params)) {
                throw new \Error(mysqli_error(getMysqliConnection()));
            }

            if (!mysqli_stmt_execute($stmt)) {
                throw new \Error(mysqli_error(getMysqliConnection()));
            }

            $result = mysqli_stmt_get_result($stmt);

            if (!$result) {
                throw new \Error(mysqli_error(getMysqliConnection()));
            }

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
            break;

        default:
            throw new \Error("Error: $query_type invalid");
            break;
    }
}


function getMysqliConnection()
{
    return $GLOBALS['con'];
}


function signUpSuccess($data)
{
    //get data for inserted user and add to session then redirect to index
    $query = "select * from volunteers where email=?";

    $query_types = 'd';
    $params = ['email' => $data['email']];
}

function signOut()
{
    unset($_SESSION['user']);
    onNoUserSessionThenRedirect();
}


function addOpportunity($data)
{
    ['date' => $date, 'position' => $position, 'time' => $time] = $data;

    $count = runQuery('insert into opportunities (position,date,time) values (?,?,?)', 'insert', 'sss', [$position, $date, $time]);

    if ($count == 0) {
        throw new Error("Opportunity was not added");
    };

    makeAlert("$count opportunity added");
}


function getOpportunities()
{

    try {

        return runQuery('select * from opportunities limit 100', 'select', '', []);
    } catch (\Throwable $th) {
        makeAlert($th->getTraceAsString());
        throw $th;
    }
}


function handleAction()
{
    // $raw_data = file_get_contents('php://input');

    if (array_key_exists('action', $_REQUEST) == false) {
        makeLog('key', ['action', $_REQUEST, array_search('action', $_REQUEST) == false]);
        makeLog('key', 'no action provided');
        return;
    }


    $action = $_REQUEST['action'];

    $json_data = $_REQUEST;


    if (!isset($action)) {
        makeLog('message', 'handleAction failed');
        return;
    }

    makeLog('message', 'action value=> ' . $json_data['action']);

    switch ($action) {
        case 'sign_in': {
                signIn($json_data);
                break;
            }
        case 'sign_up': {

                signUp($json_data);

                break;
            }
        case 'sign_out': {
                unset($_SESSION['user']);
                header('Location', 'index.php');
                break;
            }
        case 'add_opportunity': {
                addOpportunity($json_data);
                break;
            }
        default: {
                $error = 'Error invalid action value provided: '. $action;
                makeLog('message', $error);
                makeAlert($error);
                break;
            }
    }
}


try {

    $_SESSION['LOG_CONSOLE'] = NULL;
    $_SESSION['ALERT'] = NULL;

    mysqli_begin_transaction(getMysqliConnection());
    handleAction();
    mysqli_commit(getMysqliConnection());
} catch (\Throwable $th) {

    mysqli_rollback(getMysqliConnection());
    $error = $th->getMessage();
    makeAlert($error);
    makeLog('message', $error);
    // throw $th;
}
