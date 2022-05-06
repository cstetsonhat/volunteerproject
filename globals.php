<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$con = mysqli_connect("localhost", "root", "", "foodbankvolunteers");

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
 * @description return an Array set for select and return an integer update|insert|delete queries
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

function editOpportunity($data)
{
    //grab fields from data
    ['date' => $date, 'position' => $position, 'time' => $time, 'id' => $id] = $data;

    //execute the query and get the result
    $successful_edits = runQuery('update opportunities set position=?, date=?, time=? where id=?;', "insert", "ssss", [$position, $date, $time, $id]);

    if ($successful_edits == 0) {
        throw new Error('Error failed to update the specified opportunity');
    }

    makeAlert('Edit successful');
}


function getOpportunities()
{

    try {

        $volunteer_id = $_SESSION['user']['id'];
        return runQuery('SELECT opportunities.*, ( SELECT COUNT(op_aps.id) >= 1 FROM opportunity_applications op_aps WHERE op_aps.assigned_at != "" AND op_aps.id = opportunities.id ) assigned, (SELECT COUNT(_op_aps.id) from opportunity_applications _op_aps where _op_aps.volunteer_id = 1) as applied from FROM opportunities LIMIT 100; ', 'select', '', []);
    } catch (\Throwable $th) {
        makeAlert($th->getTraceAsString());
        throw $th;
    }
}


function applyToOpportunity($data)
{
    ['opportunity_id' => $opportunity_id] = $data;
    $volunteer_id = $_SESSION['user']['id'];

    $new_application_count = runQuery('insert into opportunity_applications (volunteer_id, opportunity_id) values (?,?)', 'insert', 'dd', [$volunteer_id, $opportunity_id]);

    if ($new_application_count == 0) {
        throw new Error("Error: failed to submit the application");
    }

    makeAlert("$new_application_count application submited");
}

function assignOpportunity($data)
{
    //update state and set the assigned at field to now()

    ['opportunity_id' => $opportunity_id, 'volunteer_id' => $volunteer_id, 'assignment_value' => $assignment_temp_value] = $data;

    $assignment_value = strcasecmp($assignment_temp_value, 'unassign') == 0 ? '""' : "now()";
    $updated_assignment_count = runQuery("update opportunity_applications set assigned_at = $assignment_value where opportunity_id = ? and volunteer_id = ?", 'insert', 'dd', [$opportunity_id, $volunteer_id]);

    if ($updated_assignment_count == 0) {
        throw new Error("Error: failed to update the opportunity_application");
    }

    makeAlert("$updated_assignment_count opportunity_application updated");
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
        case 'edit_opportunity': {
                editOpportunity($json_data);
                break;
            }

        case 'apply_to_opportunity': {
                applyToOpportunity($json_data);
                break;
            }
        case 'assign_opportunity': {
                assignOpportunity($json_data);
                break;
            }
        default: {
                throw new Error('Error invalid action value provided: ' . $action);
                // makeLog('message', $error);
                // makeAlert($error);
                break;
            }
    }
}

function renderOpportunities($base_url, $opportunities, $show_apply_btn = false, $show_delete = false)
{

    // loop over all opportunities and make a row for each one
    foreach ($opportunities as $key => $opportunity) {
        $index = ((int)$key) + 1;

        //will be used to generate classes to style colors of rowss
        $assigned = $opportunity['assigned'] != 0 ? 'assigned' : 'unassigned';

        //code for the apply button is showsn if $show_apply_btn is true
        $apply_btn = !$show_apply_btn ? "" : sprintf("<td><a href='opportunities.php?action=apply_to_opportunity&opportunity_id=%s'>Apply</a></td>", $opportunity['id']);

        $delete_btn_html = !$show_delete ? "" : "<a href='$base_url?action=delete_opportunity&opportunity_id={$opportunity['id']}'>Delete</a>";

        //generate the code for a single row
        $output = <<<OPPORTUNITY
        <tr class="opportunity_is_$assigned">

        <td>{$index}</td>
        <td>{$opportunity['position']}</td>
        <td>{$opportunity['date']}</td>
        <td>{$opportunity['time']}</td>
        <td>{$assigned}</td>
        <td>$delete_btn_html</td>
        <td><a href="manage_opportunities.php?opportunity_id={$opportunity['id']}">Details</a></td>
        $apply_btn
        
        </tr>
OPPORTUNITY;
        printf($output);
    };
}

function renderOpportunityApplications()
{
    if (!isset($_REQUEST['opportunity_id'])) {
        return;
    }

    $volunteers = runQuery(
        "select v.*, case when  ops_app.assigned_at= \"\" then \"false\" else \"true\" end as assignment_value from opportunity_applications ops_app left join volunteers v on v.id = ops_app.volunteer_id where ops_app.opportunity_id = ?",
        "select",
        "d",
        [$_REQUEST['opportunity_id']]
    );

    foreach ($volunteers as $key => $volunteer) {
        $assignment_label = $volunteer['assignment_value'] == 'false' ? 'Assign' : 'Unassign';
        $volunteer_row = <<<VOLUNTEERROW
            <tr>
                <td>$key</td>
                <td>{$volunteer['id']}</td>
                <td>{$volunteer['firstname']} {$volunteer['lastname']}</td>
                <td>{$volunteer['occupation']}</td>
                <td>{$volunteer['email']}</td>
                <td><a href="manage_opportunities.php?action=assign_opportunity&volunteer_id={$volunteer['id']}&opportunity_id={$_REQUEST['opportunity_id']}&assignment_value={$assignment_label}">{$assignment_label}</a></td>
            </tr>
    VOLUNTEERROW;

        printf($volunteer_row);
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
