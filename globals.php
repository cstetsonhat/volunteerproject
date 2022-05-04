<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$con = mysqli_connect("localhost", "root", "", "foodbankvolunteers");

session_start();

function makeLog($key, $body)
{

    if (!isset($_SESSION['LOG_CONSOLE']['message'])) {
        $_SESSION['LOG_CONSOLE']['message'] = [];
    }

    array_push($_SESSION['LOG_CONSOLE']['message'], $body);
}

function  makeAlert($message)
{
    if (!isset($_SESSION['alert']['message'])) {
        $_SESSION['alert']['message'] = [];
    }

    array_push($_SESSION['alert']['message'], $message);
}

function onNoUserSessionThenRedirect()
{
    if (!isset($_SESSION, $_SESSION['user'])) {
        header("Location", "index.php");
    }
}

function signIn($data)
{
    makeLog('message', 'Sign up triggered');
    ['email' => $email, 'password' => $password] = $data;
}

function signUp($data)
{
    makeLog('message', 'sign in triggered');
    ['email' => $email, 'password' => $password, 'password_confirm' => $password_confirm, 'occupation' => $occupation] = $data;

    if (strcmp($password, $password_confirm) != 0) {
        throw new \Error("Password confirmation does not match");
    }



    //insert into the database

    $result = runQuery("insert into volunteers (email, password, occupation) values (?,?,?)", 'insert_volunteer', "sss", compact('email', 'password', 'occupation'));

    if (!isset($result) || $result <= 0) {
        var_dump("errors", $result);
        throw new \Error("Error: insert failed. " . mysqli_error(getMysqliConnection()));
    }

    $user = runQuery("select from volunteers where email=:email", 'select', 's', compact('email'));

    if (!$user) {
        throw new \Error(mysqli_error(getMysqliConnection()));
    }

    // set session for the user 
    $_SESSION['user'] = $user;

    //go to login page
    header("Location", "index.php");
}

function runQuery($query, $query_type, $bind_types, ...$params)
{


    switch ($query_type) {
        case 'insert_volunteer':
            // validation 
            if (!isset($query, $params, $bind_types) || strlen($bind_types) != count($params[0])) {

                throw new \Error(sprintf('Insert error query or params invalid bind_types:%s  $params: %s', strlen($bind_types), count($params[0])));
            }

            ['email' => $email, 'password' => $password, 'occupation' => $occupation] = $params[0];
            var_dump($query, $bind_types, $email, $password, $occupation);

            // $insert_query = sprintf("insert into volunteers (email, password, occupation) values ('%s','%s','%s'); ", mysqli_real_escape_string(getMysqliConnection(), $email), mysqli_real_escape_string(getMysqliConnection(), $password), mysqli_real_escape_string(getMysqliConnection(), $occupation));

            // var_dump('insert_query', $insert_query);
            // $stmt = mysqli_prepare(
            //     getMysqliConnection(),
            //     $insert_query
            // );

            $stmt = mysqli_prepare(getMysqliConnection(), $query);




            $_params = [$email, $password, $occupation];

            if (
                mysqli_stmt_bind_param($stmt, 'sss', $email, $password, $occupation) 
            ) {

                throw new \Error(sprintf("bind error: %s  sql_error:%s", mysqli_stmt_error($stmt), mysqli_error(getMysqliConnection())));
            }

            if ( mysqli_stmt_execute($stmt)){
                throw new Error(mysqli_stmt_error());
            }

            var_dump("insert should work");
            return mysqli_affected_rows(getMysqliConnection());
            // $result = mysqli_stmt_get_result($stmt);


            // return mysqli_fetch_all($result, MYSQLI_ASSOC);

            break;

        case 'select':
            $stmt = mysqli_prepare(getMysqliConnection(), $query);
            if (!mysqli_stmt_execute($stmt, $params[0])) {
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


function handleAction()
{
    // $raw_data = file_get_contents('php://input');

    $json_data = $_POST;


    if (!isset($json_data, $json_data['submit'])) {
        makeLog('message', 'handleAction failed');
        return;
    }

    makeLog('message', 'submit value=> ' . $json_data['submit']);

    switch ($json_data['submit']) {
        case 'Sign in': {
                signIn($json_data);
                break;
            }
        case 'Sign up': {

                signUp($json_data);

                break;
            }
        case 'Sign out': {
                break;
            }
        default: {
                makeLog('message', 'Error invalid action value provided');
                break;
            }
    }
}


try {
    handleAction();
} catch (\Throwable $th) {
    print($th->getMessage());
    throw $th;
}
