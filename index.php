<?php
require('header.php');
$user_id = $_SESSION['user']['id'];

// expensesQuery for Total data 的 total expenses
$expenseQuery = "SELECT IFNULL(SUM(amount), 0) AS total_expenses FROM expenses WHERE user_id = :user_id"; /*IF no amount， will set as 0 as，， add up all expense amounts from expenses*/
$stmtExpense = $db->prepare($expenseQuery);
$stmtExpense->execute([':user_id' => $user_id]);
$totalExpenses = $stmtExpense->fetch(PDO::FETCH_ASSOC)['total_expenses'];

$expenses = file_get_contents ("http://localhost/finalproject/backend/index.php?action=getAllExpenses&user_id=$user_id");
$expenses = json_decode($expenses, true);

//total category的sum， for piechart算法用的
$totalRowStmt = $db->prepare("SELECT SUM(amount) as total FROM expenses WHERE user_id = :user_id");
$totalRowStmt->execute([':user_id' => $user_id]);
$totalRow = $totalRowStmt->fetch(PDO::FETCH_ASSOC);
$total = $totalRow['total'] ? ($totalRow['total']) : 1; 

//category的一个的sum
$category_results = file_get_contents("http://localhost/finalproject/backend/index.php?action=getCategorySum&user_id=$user_id");
$category_results = json_decode($category_results, true);
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

$payment_results = file_get_contents("http://localhost/finalproject/backend/index.php?action=getPaymentSum&user_id=$user_id");
$payment_results = json_decode($payment_results, true);
$payment_colors = [
    1 => '#BCD2E8',
    2 => '#91BAD6',
    3 => '#73A5C6',
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
    <link rel="stylesheet" href="theme.css" />
    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
    <style type="text/css">
        body {
            background: #f1f1f1;
        }
        .expenses-table{
            font-size: 0.75rem;
            color: #6c7a8f;
        }
        .dashboard{
            max-width:900px;
        }
        .tableno{
            max-height:58vh;
            overflow:hidden;
        }
        .pie{ 
            width: 180px;
            height: 180px;
            border-radius: 50%;
            margin: 0 auto; 
        }
        .profile-logo {
            border: 1px #000000 solid;
            border-radius: 50%;
            padding: 1px 4px;
        }
        .card{
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        }
        .carousel-btn button{
            width: 25px;
            height: 25px;
            border-radius: 50%;
        }
    </style>
</head>

<body>
<!-- NAVBARRRRRR------------------>
    <div class="container-fluid">
    <div class="mx-auto dashboard">
        <nav class="navbar navbar-expand-lg pb-0">            
            <span id="themeToggle" class="fw-bold fs-3 p-2">Money Manager  <i class="bi bi-toggle-on"></i></span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2 p-2">
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
        <div class="row justify-content-start justify-content-around pb-4 mt-0">
            <div class="row col-12">
                <div class="card">
                    <div class="card-body">
                        <p class="fw-bold">Total Expenses</p>
                        <h2 class="fs-1 fw-bold">RM<?= ($totalExpenses) ?></h2>                
                    </div>
                </div>
            </div>
        </div>    
<!-- Total Datassss------------------------------ -->
<!-- Expenses ------------------------>
        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-body">       
                        <div class="tableno">      
                            <table class="table">
                            <thead>
                                <tr>
                                <th scope="col">Title</th>
                                <th scope="col-mb-2" >Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($expenses as $expense): ?>
                                <tr>
                                <td>
                                <span class="me-5 fw-bold"><?= $expense['title']?></span>
                                <br>
                                <span class="expenses-table"><?= $expense['category_name']?></span>
                                <span class="date expenses-table"><?= $expense['expense_date']?></span>
                                <td>RM<?= $expense['amount']?></td> 
                                </td>             
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                            </table>
                        </div> <!--div class="tableno"-->
                        <div class="text-center mt-3">
                            <a href="manage-expenses.php" class="btn-sm text-black fw-bold text-decoration-none"><i class="bi bi-hand-index-thumb"></i> View More</a>
                        </div>
                    </div> <!--div class="card-body"-->    
                </div>
            </div>
<!-- Expenses ------------------------>
<!-- Category Pie Chart -->
            <div class="col-md-5">
                <div class="card">        
                        <div id="chartCarousel" class="carousel slide" data-bs-interval="false">
                            <div class="carousel-inner">
                                
                                <div class="carousel-item active">
                                    <div class="p-4"> 
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="fw-bold mb-0 text-muted small text-uppercase tracking-wider">
                                                Analytics <span class="text-dark fw-bold ms-1" style="font-size: 0.75rem;">(By Category)</span>
                                            </h6>
                                            
                                            <div class="carousel-btn d-flex align-items-center gap-1">
                                                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center p-0 shadow-sm" type="button" data-bs-target="#chartCarousel" data-bs-slide="prev"><i class="bi bi-chevron-left" style="font-size: 0.75rem;"></i></button>
                                                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center p-0 shadow-sm" type="button" data-bs-target="#chartCarousel" data-bs-slide="next"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i></button>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <div class="pie mx-auto mb-3" style="background: conic-gradient(
                                                <?php 
                                                $accumulated = 0; /*从0开始累计 usign the cummulaive method to add on%*/ 
                                                foreach ($category_results as $row) {
                                                    $percent = (($row['total_category_amount']) / $total) * 100; /*（那个category总共÷全部category的总共）×100=多少%*/
                                                    if ($percent > 0) { 
                                                        $accumulated += $percent; /*跟着%增加调整颜色停哪里*/ 
                                                        echo $colors[$row['category_id']] . " 0 " . $accumulated . "%,"; /*output the color part according the category color have set 补充多少%的圆格*/
                                                    }
                                                }
                                                ?> #e2e8f0 0);">
                                            </div>
                                            
                                            <ul class="list-group list-group-flush">
                                                <?php foreach ($category_results as $row): 
                                                    $percent = round((($row['total_category_amount']) / $total) * 100); /*round of decimal number*/
                                                    if ($percent > 0): ?> <!--Only show categories that have spending-->
                                                    <li class="list-group-item d-flex justify-content-between align-items-center p-0" style="border:none; background:transparent;">
                                                        <div>
                                                            <span style="width:12px; height:12px; border-radius:3px; display:inline-block; margin-right:8px; background:<?= $colors[$row['category_id']] ?>;"></span> <!--小方块-->
                                                            <strong><?= $row['category_name'] ?></strong>
                                                        </div>
                                                        <span class="text-muted" style="font-size: 0.85rem;"><?= $percent ?>% (RM <?= ($row['total_category_amount']) ?>)</span>
                                                    </li>
                                                <?php endif; endforeach; ?>
                                            </ul>
                                        </div> <!--div class="text-center"-->

                                    </div> <!--div class="p-4" -->
                                </div> <!--div class="carousel-item active"-->

                                <div class="carousel-item">
                                    <div class="p-4"> 
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="fw-bold mb-0 text-muted small text-uppercase tracking-wider">Analytics 
                                                <span class="text-dark fw-bold ms-1" style="font-size: 0.75rem;">(By Payment)</span>
                                            </h6>
                                            
                                            <div class="carousel-btn d-flex align-items-center gap-1">
                                                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center p-0" type="button" data-bs-target="#chartCarousel" data-bs-slide="prev"><i class="bi bi-chevron-left" style="font-size: 0.75rem;"></i></button>
                                                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center p-0" type="button" data-bs-target="#chartCarousel" data-bs-slide="next"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i></button>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <div class="pie mx-auto mb-3" style="background: conic-gradient(
                                                <?php 
                                                $accumulated_payment = 0;
                                                foreach ($payment_results as $row) {
                                                    $percent = (($row['total_payment_amount']) / $total) * 100;
                                                    if ($percent > 0) { 
                                                        $accumulated_payment += $percent;
                                                        echo ($payment_colors[$row['payment_id']] ?? '#6c7a8f') . " 0 " . $accumulated_payment . "%,";
                                                    }
                                                }
                                                ?> #e2e8f0 0);">
                                            </div>
                                            
                                            <ul class="list-group list-group-flush">
                                                <?php foreach ($payment_results as $row): 
                                                    $percent = round((($row['total_payment_amount']) / $total) * 100);
                                                    if ($percent > 0): ?>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center p-2" style="border:none; background:transparent;">
                                                        <div>
                                                            <span style="width:12px; height:12px; border-radius:3px; display:inline-block; margin-right:8px; background:<?= $payment_colors[$row['payment_id']] ?? '#6c7a8f' ?>;"></span>
                                                            <strong><?= $row['payment_name'] ?></strong>
                                                        </div>
                                                        <span class="text-muted" style="font-size: 0.85rem;"><?= $percent ?>% (RM <?= (($row['total_payment_amount'])) ?>)</span>
                                                    </li>
                                                <?php endif; endforeach; ?>
                                            </ul>
                                        </div> <!--div class="text-center"-->

                                    </div> <!--div class="p-4"-->
                                </div> <!--div class="carousel-item"-->

                            </div> <!--div class="carousel-inner"-->
                        </div> <!--div id="chartCarousel" class="carousel slide" data-bs-interval="false"-->
                </div>
            </div> 

        </div> <!--div class="row"-->
<!-- Category Pie Chart -->
        <div class="text-center gap-3 mx-auto pt-1">
            <a href="feedback.php" class="text-decoration-none fw-bold text-black">Feedback<i class="bi bi-headset"></i></a>
        </div>
    </div> <!--second div-->
    </div> <!--first div-->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
        <script src="theme.js"></script>
</body>

</html>