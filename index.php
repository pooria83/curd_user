<?php

// Start session
session_start();

// Get session data
$sessData = !empty($_SESSION['sessData'])?$_SESSION['sessData']:'';

// Get status message from session
if(!empty($sessData['status']['msg'])){
    $statusMsg = $sessData['status']['msg'];
    $statusMsgType = $sessData['status']['type'];
    unset($_SESSION['sessData']['status']);
}

// Load pagination class
require_once 'Pagination.class.php';

// Load and initialize database class
require_once 'DB.class.php';
$db = new DB();

//Load CSS and JS files
require_once 'public/includeCenter.php';

// Page offset and limit
$perPageLimit = 2;
$offset = !empty($_GET['page'])?(($_GET['page']-1)*$perPageLimit):0;

// Get search keyword
$searchKeyword = !empty($_GET['sq'])?$_GET['sq']:'';
$searchStr = !empty($searchKeyword)?'?sq='.$searchKeyword:'';

// Search DB query
$searchArr = '';
if(!empty($searchKeyword)){
    $searchArr = array(
        'name' => $searchKeyword,
        'email' => $searchKeyword,
        'phone' => $searchKeyword
    );
}

// Get count of the users
$con = array(
    'like_or' => $searchArr,
    'return_type' => 'count'
);
$rowCount = $db->getRows('users', $con);

// Initialize pagination class
$pagConfig = array(
    'baseURL' => 'index.php'.$searchStr,
    'totalRows' => $rowCount,
    'perPage' => $perPageLimit
);
$pagination = new Pagination($pagConfig);

// Get users from database
$con = array(
    'like_or' => $searchArr,
    'start' => $offset,
    'limit' => $perPageLimit,
    'order_by' => 'id DESC',
);
$users = $db->getRows('users', $con);

?>

<!-- Display status message -->
<?php if(!empty($statusMsg) && ($statusMsgType == 'success')){ ?>
    <div class="alert alert-success"><?php echo $statusMsg; ?></div>
<?php }elseif(!empty($statusMsg) && ($statusMsgType == 'error')){ ?>
    <div class="alert alert-danger"><?php echo $statusMsg; ?></div>
<?php } ?>
<br><br><br>
<div class="container">
<div class="row">
    <div class="col-md-12 search-panel">
        <!-- Search form -->
        <form>
            <div class="input-group">
                <input type="text" name="sq" class="form-control" placeholder="Search by keyword..." value="<?php echo $searchKeyword; ?>">
                <div class="input-group-btn">
                    <button class="btn btn-default" type="submit">
                        <i class="glyphicon glyphicon-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <!-- Add link -->
        <span class="pull-right">
            <a href="addEdit.php" class="btn btn-primary"><i class="glyphicon glyphicon-plus"></i> New User</a>
        </span>
        <br><br>

    </div>

    <!-- Data list table -->
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th></th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(!empty($users)){ $count = 0;
            foreach($users as $user){ $count++;
                if ($user['status'] == 1)
                {
                    $classString = 'btn btn-info';
                    $buttonString = 'Inactive';

                }
                else
                {
                    $classString = 'btn btn-success';
                    $buttonString = 'Active';
                }
                ?>
                <tr>
                    <td><?php echo '#'.$count; ?></td>
                    <td><?php echo $user['name']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['phone']; ?></td>
                    <td>
                        <a href="addEdit.php?action_type=edit&id=<?php echo $user['id']; ?>" class="btn btn-warning">Edit</a>
                        <a href="userAction.php?action_type=delete&id=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure to delete?')">Delete</a>
                        <a href="addEdit.php?action_type=<?= $buttonString ?>&id=<?php echo $user['id']; ?>" class="<?= $classString ?>"><?= $buttonString ?></a>

                    </td>
                </tr>
            <?php } }else{ ?>
            <tr><td colspan="5">No user(s) found......</td></tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Display pagination links -->
    <?php echo $pagination->createLinks(); ?>
</div>
</div>