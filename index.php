<?php
require('header.php');

$query = "SELECT 
            expenses.*, 
            categories.category_name, 
            payments.payment_name 
          FROM expenses
          LEFT JOIN categories ON expenses.category_id = categories.id
          LEFT JOIN payments ON expenses.payment_method_id = payments.id
          ORDER BY expenses.id DESC";

        $stmt = $db->prepare($query);
        $stmt->execute([]);
        $expenses = $stmt->fetchAll();
        
$totalRow = $db->query("SELECT SUM(amount) as total FROM expenses")->fetch(PDO::FETCH_ASSOC);
$total = $totalRow['total'] ? abs($totalRow['total']) : 1; 

$results = $db->query("SELECT 
                        expenses.category_id,
                        categories.category_name, 
                        SUM(expenses.amount) as total_category_amount
                       FROM expenses 
                       LEFT JOIN categories ON expenses.category_id = categories.id 
                       GROUP BY expenses.category_id, categories.category_name")->fetchAll(PDO::FETCH_ASSOC);

$colors = [
    1 => '#BCD2E8',
    2 => '#91BAD6',
    3 => '#73A5C6',
    4 => '#528AAE',
    5 => '#2E5984',
    6 => '#1E3F66',
    7 => '#173150',
    8 => '#0d1d31'
];

?>
<!DOCTYPE html>
<html>

<head>
    <title>Simple CMS</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
        crossorigin="anonymous" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css" />
    <style type="text/css">
        body {
            background: #f1f1f1;
        }
        .profile-logo{
            border: 1px black solid;
            border-radius: 50%;
        }
        /* .date{
            font-size: 10px;
            color: black;
        } */
        .expenses-table{
            font-size: 0.75rem;
            color: #6c7a8f;
        }
        .dashboard{
            max-width:900px;
            max-height:95vh;
            overflow: hidden;
        }
        .tableno{
            max-height:45vh;
            overflow:hidden;
        }
        .pie{ 
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto; 
        }
    </style>
</head>

<body>
<!-- NAVBARRRRRR------------------>
    <div class="container-fluid py-3">
    <div class="mx-auto dashboard">
        <nav class="navbar navbar-expand-lg custom-navbar">            
            <a href="#" class="navbar-brand fw-bold fs-3">Money Manager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <li class="nav-item">
                        <a href="manage-expenses-add.php" class="nav-link custom-link active-link"><i class="bi bi-plus-circle"></i> Add Expenses</a>
                    </li>

                    <li class="nav-item">
                        <a href="budget.php" class="nav-link custom-link"><i class="bi bi-wallet2"></i> Budget</a>
                    </li>

                    <li class="nav-item">
                        <a href="logout.php" class="nav-link custom-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
                    </li>
                    <?php 
                    if($_SESSION['user']['role'] == "admin" ): ?>
                    <li class="nav-item ms-2">
                        <a href="manage-user.php" class="profile-logo"><i class="bi bi-person-fill"></i></a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    
<!-- NAVBARRRRRR------------------>
<!-- Total Datassss------------------------------ -->
        <div class="row justify-content-center justify-content-around pb-4 mt-0">
            <div class="row g-4 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <p>Total Income</p>
                        <h2>RM</h2>                
                    </div>
                </div>
            </div>
            <div class="row g-4 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <p>Total Expenses</p>
                        <h2>RM</h2>                
                    </div>
                </div>
            </div>
            <div class="row g-4 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <p>Balance</p>
                        <h2>RM</h2>
                    </div>
                </div>
            </div>
        </div>    
<!-- Total Datassss------------------------------ -->
<!-- Expenses ------------------------>
        <div class="row">
            <div class="col-md-7">
            <div class="card dashboard-card">
                <div class="card-body">       
                <div class="tableno">      
                    <table class="table">
                    <thead>
                        <tr>
                        <th scope="col">Title</th>
                        <th scope="col-mb-2" >Amount</th>
                        </tr>
                    </thead>
                    <tbody class="">
                    <?php foreach($expenses as $expense): ?>
                        <tr>
                        <td>
                        <span class="me-3 fw-bold"><?= $expense['title']?></span>
                        <br>
                        <span class="expenses-table"><?= $expense['category_name']?></span>
                        <span class="date expenses-table"><?= $expense['expense_date']?></span>
                        <td>RM<?= $expense['amount']?></td> 
                        </td>             
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    </table>
                </div>
                        <div class="text-center mt-3">
                            <a href="manage-expenses.php" class="btn-sm text-black fw-bold text-decoration-none"><i class="bi bi-hand-index-thumb"></i> View More</a>
                        </div>
                </div>      
            </div>
            </div>
<!-- Expenses ------------------------>
<!-- Category Pie Chart -->
            <div class="col-md-5">
            <div class="card p-4">
                <div class="text-center">
                    <div class="pie" style="background: conic-gradient(
                        <?php $accumulated = 0;
                        foreach ($results as $row) {
                            $pct = (abs($row['total_category_amount']) / $total) * 100;
                            if ($pct > 0) {
                                $accumulated += $pct;
                                echo $colors[$row['category_id']] . " 0 " . $accumulated . "%,";
                            }
                        }
                        ?> #e2e8f0 0); col-md-5">
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($results as $row): 
                            $pct = round((abs($row['total_category_amount']) / $total) * 100);
                            if ($pct > 0): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center p-0" style="border:none; background:transparent;">
                                <div>
                                    <span style="width:12px; height:12px; border-radius:3px; display:inline-block; margin-right:8px; background:<?= $colors[$row['category_id']] ?>;"></span>
                                    <strong><?= $row['category_name'] ?></strong>
                                </div>
                                <span class="text-muted"><?= $pct ?>% (RM <?= number_format(abs($row['total_category_amount']), 2) ?>)</span>
                            </li>
                        <?php endif; endforeach; ?>
                    </ul>
                </div>
            </div>
            </div>
<!-- Category Pie Chart -->
        </div> <!--expenses (div class="row")-->

    </div> <!--second div-->
    </div> <!--first div-->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
</body>

</html>