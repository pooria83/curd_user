<?php
print_r($_GET);
// Start session
session_start();

//Load CSS and JS files
require_once 'public/includeCenter.php';

//Include database Class
require_once 'DB.class.php';
$db = new DB();

$postData = $userData = array();

// Get session data
$sessData = !empty($_SESSION['sessData'])?$_SESSION['sessData']:'';

// Get status message from session
if(!empty($sessData['status']['msg'])){
    $statusMsg = $sessData['status']['msg'];
    $statusMsgType = $sessData['status']['type'];
    unset($_SESSION['sessData']['status']);
}

// Get posted data from session
if(!empty($sessData['postData'])){
    $postData = $sessData['postData'];
    unset($_SESSION['sessData']['postData']);
}

// Edit user data
if (isset($_GET['action_type']) && $_GET['action_type'] == 'edit' && !empty($_GET['id']))
{
    // Get user data
    $conditions['where'] = [
        'id' => $_GET['id']
    ];
    $conditions['return_type'] = 'single';
    $userData = $db->getRows('users' , $conditions);

    //Active or inactive users
}
if (isset($_GET['action_type']) && ($_GET['action_type'] == 'active' ||
        $_GET['action_type'] == 'inactive') && !empty($_GET['id']))
{
    $redirectURL = "userAction.php?id={$_GET['id']}&action_type={$_GET['action_type']}";
    header("Location: ".$redirectURL);

}


// Pre-filled data
$userData = !empty($postData)?$postData:$userData;

// Define action
$actionLabel = !empty($_GET['id'])?'Edit':'Add';

?>

<!-- Display status message -->
<?php if(!empty($statusMsg) && ($statusMsgType == 'success')){ ?>
    <div class="alert alert-success"><?php echo $statusMsg; ?></div>
<?php }elseif(!empty($statusMsg) && ($statusMsgType == 'error')){ ?>
    <div class="alert alert-danger"><?php echo $statusMsg; ?></div>
<?php } ?>

<!-- Add/Edit form -->
<div class="container">
<div class="panel panel-default">
    <div class="panel-heading"><?php echo $actionLabel; ?> User <a href="index.php" class="glyphicon glyphicon-arrow-left"></a></div>
    <div class="panel-body">
        <form method="post" action="userAction.php" class="form">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo !empty($userData['name'])?$userData['name']:''; ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" class="form-control" name="email" value="<?php echo !empty($userData['email'])?$userData['email']:''; ?>">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" class="form-control" name="phone" value="<?php echo !empty($userData['phone'])?$userData['phone']:''; ?>">
            </div>
            <input type="hidden" name="id" value="<?php echo !empty($userData['id'])?$userData['id']:''; ?>">
            <input type="submit" name="userSubmit" class="btn btn-success" value="SUBMIT"/>
        </form>
    </div>
</div>
</div>