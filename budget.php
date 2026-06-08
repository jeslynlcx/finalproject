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
    <title>Analytics</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css"
    />
<style>
    .pie{
        width: 250px;
        height: 250px;
        border-radius:
        50%; margin: 0 auto;
        }
</style>

</head>
<body class="bg-light">
<div class="container my-5" style="max-width: 750px;">
    <div class="card p-4 row flex-row align-items-center" style="border-radius: 20px;">
        
        <div class="col-md-6 text-center">
            <div class="pie" style="background: conic-gradient(<?php $accumulated = 0;
                foreach ($results as $row) {
                            $pct = (abs($row['total_category_amount']) / $total) * 100;
                    if ($pct > 0) {
                                $accumulated += $pct;
                        echo $colors[$row['category_id']] . " 0 " . $accumulated . "%,";
                            }
                }
            ?> #e2e8f0 0% 100%);"></div>
        </div>
        
        <div class="col-md-6">
            <h5 class="fw-bold mb-3">Spending by Category</h5>
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
    <div class="text-center mt-3">
        <a href="index.php" class="btn-sm text-black fw-bold text-decoration-none"><i class="bi bi-arrow-left-circle"></i> Back To Dashboard</a>
    </div>
</div>
</body>
</html>