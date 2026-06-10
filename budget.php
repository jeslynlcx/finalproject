<?php
require('header.php');

$user_id = 1;


$categoryQuery = "SELECT 
                categories.id AS category_id,
                categories.category_name, 
            IFNULL(( 
                SELECT SUM(expenses.amount) FROM expenses 
                WHERE expenses.category_id = categories.id AND expenses.user_id = :user_id
                ), 0) AS total_category_amount,
                
                IFNULL(budgets.budget_amount, 1000) AS budget_limit
             FROM categories
             LEFT JOIN budgets ON categories.id = budgets.category_id AND budgets.user_id = :user_id
             GROUP BY categories.id, categories.category_name, budgets.budget_amount";

$stmtCategory = $db->prepare($categoryQuery);
$stmtCategory->execute([':user_id' => $user_id]);
$results = $stmtCategory->fetchAll(PDO::FETCH_ASSOC);

$paymentQuery = "SELECT 
                payments.id AS payment_id,
                payments.payment_name, 
            IFNULL(( 
                SELECT SUM(expenses.amount) FROM expenses 
                WHERE expenses.payment_method_id = payments.id AND expenses.user_id = :user_id
                ), 0) AS total_payment_amount,
                
                IFNULL(budgets.payment_amount, 1000) AS payment_limit
             FROM payments
             LEFT JOIN budgets ON payments.id = budgets.payment_id AND budgets.user_id = :user_id
             GROUP BY payments.id, payments.payment_name, budgets.payment_amount";
             

$stmtPayment = $db->prepare($paymentQuery);
$stmtPayment->execute([':user_id' => $user_id]);
$payment_results = $stmtPayment->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Budget</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css" />
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
        .goals-scroll{
            max-height: 200px;
            overflow-y: auto;
            padding-right: 4px;
        }
        .title a {
            display: inline-block;
            font-size: 25px;
            text-decoration: none;
            color: black !important;
            font-weight: bold;
            padding-left: 10px;
            margin-bottom: 15px;
        }
        .title a:hover {
            color: #333 !important;
        }
    </style>
</head>
<body>
    <div class="container py-4 dashboard">
        <div class="title a">
            <a href="index.php">Limits and Goals</a>
        </div>
        <div class="row g-3">

            <div class="col-md-6">
                    <div class="card p-3 h-100 shadow-sm" style="border: none;">
                        <h6 class="fw-bold mb-1">Limits by Category</h6>
                        <p class="text-muted small mb-2">Set monthly budget thresholds</p>
                        
                        <div class="tableno">
                            <?php foreach ($results as $category_row): ?>
                                <div class="row align-items-center py-2 border-bottom g-2 mx-0">
                                    <div class="col-6 p-0">
                                        <span class="fw-bold d-block text-truncate small"><?= $category_row['category_name'] ?></span>
                                        <span class="text-muted" style="font-size: 0.75rem;">
                                            RM <?= $category_row['total_category_amount'] ?> / <?= $category_row['budget_limit'] ?>
                                        </span>
                                    </div>
                                    <div class="col-6 p-0">
                                        <form method="POST" class="categoryBudgetForm d-flex justify-content-end align-items-center m-0">
                                            <input type="hidden" name="category_id" value="<?= $category_row['category_id'] ?>">
                                            <div class="input-group input-group-sm" style="max-width: 115px;">
                                                <input type="number" step="0.01" name="amount" class="form-control row-amount" placeholder="Limit" value="<?= $category_row['budget_limit'] ?>" required style="font-size: 0.75rem;">
                                                <button type="button" class="btn btn-dark btn-sm btn-submit-cat">Set</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-12 p-0 mt-1">
                                        <div class="progress overflow-hidden" style="height: 5px;">
                                            <?php 
                                            $percent = ($category_row['total_category_amount'] / $category_row['budget_limit']) * 100; 
                                            ?>
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $percent ?>%; max-width: 100%;"></div>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            <div class="col-md-6">
                <div class="card p-3 h-100 shadow-sm" style="border: none;">
                    <h6 class="fw-bold mb-1">Limits by Payment</h6>
                    <p class="text-muted small mb-2">Monitor account-wide limits</p>
                    
                    <div class="tableno">
                            <?php foreach ($payment_results as $payment_row): ?>
                                <div class="row align-items-center py-2 border-bottom g-2 mx-0">
                                    <div class="col-6 p-0">
                                        <span class="fw-bold d-block text-truncate small"><?= $payment_row['payment_name'] ?></span>
                                        <span class="text-muted" style="font-size: 0.75rem;">
                                            RM <?= $payment_row['total_payment_amount'] ?> / <?= $payment_row['payment_limit'] ?>
                                        </span>
                                    </div>
                                    <div class="col-6 p-0">
                                        <form method="POST" class="paymentBudgetForm d-flex justify-content-end align-items-center m-0">
                                            <input type="hidden" name="payment_id" value="<?= $payment_row['payment_id'] ?>">
                                            <div class="input-group input-group-sm" style="max-width: 115px;">
                                                <input type="number" step="0.01" name="amount" class="form-control row-amount" placeholder="Limit" value="<?= $payment_row['payment_limit'] ?>" required style="font-size: 0.75rem;">
                                                <button type="button" class="btn btn-dark btn-sm btn-submit-pay">Set</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-12 p-0 mt-1">
                                        <div class="progress overflow-hidden" style="height: 5px;">
                                            <?php 
                                            $percent = ($payment_row['total_payment_amount'] / $payment_row['payment_limit']) * 100; 
                                            ?>
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $percent ?>%; max-width: 100%;"></div>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                </div>
            </div>
        </div> 
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
</body>
</html>