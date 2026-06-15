<?php
require('header.php');
$user_id = $_SESSION['user']['id'];
if (!isset($_SESSION['my_goals'][$user_id])) {
    $_SESSION['my_goals'][$user_id] = [];
}

// Add Goals
if (isset($_POST['add_goal'])) {
    $_SESSION['my_goals'][$user_id][] = [
        'title' => $_POST['goalName'],
        'target' => $_POST['goalTarget'],
        'current' => $_POST['saved']
    ];
}

// Edit Goals
if (isset($_POST['edit_goal'])) {
    $key = $_POST['goal_id'];
    $_SESSION['my_goals'][$user_id][$key]['current'] = $_POST['updated_current']; 
}

// Delete Goals
if (isset($_POST['delete_goal'])) {
    $key = $_POST['goal_id'];
    unset($_SESSION['my_goals'][$user_id][$key]);
    
    if (is_array($_SESSION['my_goals'][$user_id])) {
        $_SESSION['my_goals'][$user_id] = array_values($_SESSION['my_goals'][$user_id]);
    } else {
        $_SESSION['my_goals'][$user_id] = [];
    }
}

$user_goals = $_SESSION['my_goals'][$user_id];
$category_results = file_get_contents ("http://localhost/finalproject/backend/index.php?action=getCategorySum&user_id=$user_id");
$category_results = json_decode($category_results, true);

$payment_results = file_get_contents("http://localhost/finalproject/backend/index.php?action=getPaymentSum&user_id=$user_id");
$payment_results = json_decode($payment_results, true);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Budget</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="theme.css" />
    
    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
    <style type="text/css">
        body {
            background: #f1f1f1;
        }
        .dashboard{
            max-width: 900px;
        }
        .tableno{
            max-height: 210px;
            overflow-y: auto;
        }
        .scrollbar{
            max-height: 200px;
            overflow-y: auto;
            padding-right: 4px;
        }
        .title a {
            text-decoration: none;
            color: black ;
            font-weight: bold;
        }
        .title a:hover {
            color: #333 ;
        }
        .card{
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);

        }
    </style>
</head>
<body>
    <div class="container py-4 dashboard">
        <div class="title a">
            <a href="index.php" class="fs-3 p-2"><i class="bi bi-arrow-left-circle"></i> Limits and Goals</a>
        </div>

<!--Limit by Category------------------>
    <div class="row g-3">
            <div class="col-md-6">
                <div class="card p-3 h-100 " style="border: none;">
                    <h6 class="fw-bold mb-1">Limits by Category</h6>
                    <p class="text-muted small mb-2">Set monthly budget thresholds</p>
                    
                    <div class="tableno">
                        <?php foreach ($category_results as $row): ?>
                            <div class="row align-items-center py-2 border-bottom g-2 mx-0">
                                <div class="col-6 p-0">
                                    <span class="fw-bold d-block text-truncate small"><?= $row['category_name'] ?></span>
                                    <span class="text-muted" style="font-size: 0.75rem;">
                                        RM <?= $row['total_category_amount'] ?> / <?= $row['budget_limit'] ?>
                                    </span>
                                </div>
                                <div class="col-6 p-0">
                                    <form method="POST" class="categoryBudgetForm d-flex justify-content-end align-items-center m-0">
                                        <input type="hidden" name="category_id" value="<?= $row['category_id'] ?>">
                                        <div class="input-group input-group-sm" style="max-width: 115px;">
                                            <input type="number" step="0.01" name="amount" class="form-control row-amount" placeholder="Limit" value="<?= $row['budget_limit'] ?>" required style="font-size: 0.75rem;">
                                            <button type="submit" class="btn btn-dark btn-sm">Set</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-12 p-0 mt-1">
                                    <div class="progress overflow-hidden" style="height: 5px;">
                                        <?php 
                                        $percent = ($row['total_category_amount'] / $row['budget_limit']) * 100; 
                                        ?>
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $percent ?>%; max-width: 100%;"></div>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                </div>
            </div>
<!--Limit by Category------------------>
<!--Limit by Payment------------------>
            <div class="col-md-6">
                <div class="card p-3 h-100" style="border: none;">
                    <h6 class="fw-bold mb-1">Limits by Payment</h6>
                    <p class="text-muted small mb-2">Monitor account-wide limits</p>
                    
                    <div class="tableno">
                        <?php foreach ($payment_results as $row): ?>
                            <div class="row align-items-center py-2 border-bottom g-2 mx-0">
                                <div class="col-6 p-0">
                                    <span class="fw-bold d-block text-truncate small"><?= $row['payment_name'] ?></span>
                                    <span class="text-muted" style="font-size: 0.75rem;">
                                        RM <?= $row['total_payment_amount'] ?> / <?= $row['payment_limit'] ?>
                                    </span>
                                </div>
                                <div class="col-6 p-0">
                                    <form method="POST" class="paymentBudgetForm d-flex justify-content-end align-items-center m-0">
                                        <input type="hidden" name="payment_id" value="<?= $row['payment_id'] ?>">
                                        <div class="input-group input-group-sm" style="max-width: 115px;">
                                            <input type="number" step="0.01" name="amount" class="form-control row-amount" placeholder="Limit" value="<?= $row['payment_limit'] ?>" required style="font-size: 0.75rem;">
                                            <button type="submit" class="btn btn-dark btn-sm">Set</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-12 p-0 mt-1">
                                    <div class="progress overflow-hidden" style="height: 5px;">
                                        <?php 
                                        $percent = ($row['total_payment_amount'] / $row['payment_limit']) * 100; 
                                        ?>
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $percent ?>%; max-width: 100%;"></div>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
    </div> <!--div class="row g-3"-->
<!--Limit by Payment------------------>
<!--Goals-->
        <div class="card p-3 my-3">
            <h6 class="fw-bold mb-2">My Savings Goals</h6>

            <form method="POST" class="row g-2 align-items-end mb-2 pb-2 border-bottom">
                <div class="col-md-4">
                    <input type="text" name="goalName" class="form-control form-control-sm" placeholder="Goal Name" required>
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" name="goalTarget" class="form-control form-control-sm" placeholder="Target (RM)" required>
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" name="saved" class="form-control form-control-sm" placeholder="Saved (RM)" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="add_goal" class="btn btn-sm btn-dark w-100">Add Goal</button>
                </div>
            </form>

            <div class="scrollbar">
                    <?php if (!empty($user_goals) && is_array($user_goals)): ?> <!--only run if code is not empty, if empty then "No active savings goals found. Add one above!"-->
                    <?php foreach ($user_goals as $key => $goal): ?> 
                        <?php if (!is_array($goal)) { continue; } //if its not proper array then skip (which mean just ignore and dont broke your page)
                            $percent = 0; 
                        if (isset($goal['target']) && $goal['target'] > 0) { //only calc if target exists ; more than 0
                            $percent = ($goal['current'] / $goal['target']) * 100;
                        }
                        ?>
                            <div class="row align-items-center py-2 border-bottom g-2 mx-0">
                                <div class="col-sm-4 p-0">
                                    <span class="fw-bold d-block text-truncate small"><?= $goal['title'] ?></span>
                                    <small class="text-muted" style="font-size: 0.75rem;">Target: RM <?= $goal['target'] ?> (<?= (int)$percent ?>%)</small>
                                </div>
                                <div class="col-sm-4">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-success" style="width: <?= $percent ?>%;"></div>
                                    </div>
                                </div>
                                <div class="col-sm-4 p-0 d-flex align-items-center justify-content-end gap-2">
                                    <form method="POST" class="d-flex align-items-center m-0 gap-1">
                                        <input type="hidden" name="goal_id" value="<?= $key ?>">
                                        <div class="input-group input-group-sm" style="max-width: 170px;">
                                            <span class="input-group-text px-1" style="font-size: 0.7rem;">RM</span>
                                            <input type="number" step="0.01" name="updated_current" class="form-control text-center" value="<?= $goal['current'] ?>" required style="font-size: 0.75rem;">
                                            <button type="submit" name="edit_goal" class="btn btn-outline-secondary">Update</button>
                                        </div>
                                    </form>
                                    <form method="POST" class="m-0">
                                        <button type="submit" name="delete_goal" class="btn btn-sm btn-outline-danger">Delete</button>
                                        <input type="hidden" name="goal_id" value="<?= $key ?>">
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted small text-center my-2">No active savings goals found. Add one above!</p>
                    <?php endif; ?> <!--if (!empty($user_goals) && is_array($user_goals)):-->
            </div>
        </div> <!--div class="card p-3 my-3"-->

    </div> <!--div class="container py-4 dashboard"-->
<!--Goals-->

    <script src="theme.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $('.categoryBudgetForm').on('submit', function(event) {
            event.preventDefault();
            
            // $(this)让那个'set'特定for那一行 //var $那个范围 = $(范围特定的一个区)
            var $form = $(this); 
            
            $.ajax({
                url: "http://localhost/finalproject/backend/index.php",
                type: "POST",
                data: {
                    action: "editCategoryBudget",
                    category_id: $form.find('input[name="category_id"]').val(), // $范围里.找('叫category_id的名.抽出来()
                    amount: $form.find('.row-amount').val()
                },
                success: function(response){
                    alert("Successfully Set Category Limit!");
                    window.location.reload(); 
                },
                error: function(xhr, status, error){
                    console.log("Error: ", error);
                }
            });
        });

        $('.paymentBudgetForm').on('submit', function(event) {
            event.preventDefault();

            var $form = $(this);
            
            $.ajax({
                url: "http://localhost/finalproject/backend/index.php",
                type: "POST",
                data: {
                    action: "editPaymentBudget",
                    payment_id: $form.find('input[name="payment_id"]').val(),
                    payment_amount: $form.find('.row-amount').val()
                },
                success: function(response){
                    alert("Successfully Set Payment Limit!");
                    window.location.reload(); 
                },
                error: function(xhr, status, error){
                    console.log("Error: ", error);
                }
            });
        });
</script>   
</body>
</html>