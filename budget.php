<?php
require('header.php'); 

// 捕捉错误，防止白屏
try {
    $current_user_id = $_SESSION['user_id'] ?? 1; // 固定的登录用户 ID

    // 1. 修复后的预算主查询（移除了 b.user_id 的错误绑定）
    $query = "
        SELECT 
            b.id AS budget_id,
            b.amount AS budget_limit,          
            c.category_name AS category_name,  
            COALESCE(SUM(e.amount), 0) AS total_spent 
        FROM b18_finalproject.budget b
        LEFT JOIN b18_finalproject.categories c ON b.category_id = c.id
        -- 注意：这里根据实际情况，只筛选 expenses 属于当前用户的记录
        LEFT JOIN b18_finalproject.expenses e ON b.category_id = e.category_id AND e.user_id = ?
        GROUP BY b.id
    ";

    $stmt = $db->prepare($query);
    $stmt->execute([$current_user_id]);
    $budgets = $stmt->fetchAll(); 

    // 2. 补全模态框需要的 categories 下拉数据
    $stmt_cat = $db->prepare("SELECT * FROM b18_finalproject.categories");
    $stmt_cat->execute();
    $categories = $stmt_cat->fetchAll();

    // 3. 补全模态框需要的 payments 下拉数据
    $stmt_pay = $db->prepare("SELECT * FROM b18_finalproject.payments");
    $stmt_pay->execute();
    $payments = $stmt_pay->fetchAll();

} catch (PDOException $e) {
    // 如果还是报错，直接在页面打印出来，不要闷声留白
    die("数据库查询失败: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Tracker Lite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: system-ui, -apple-system, sans-serif; }
        .card { border-radius: 12px; border: none; box-shadow: 0 2px 12px rgba(0,0,0,0.04); }
        .nav-pills .nav-link.active { background-color: #fff; color: #000; border: 1px solid #dee2e6; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .nav-pills .nav-link { color: #6c757d; }
        .progress { height: 8px; border-radius: 4px; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Budget Tracker Lite <span class="fs-6 fw-normal text-muted">Keep track of your income and expenses</span></h4>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-dark btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#budgetModal">+ Add Budget</button>
            <button class="btn btn-outline-secondary btn-sm rounded-3"><i class="bi bi-box-arrow-right"></i> Logout</button>
        </div>
    </div>

        <a class="text-black border-black" href="index.php"><i class="bi bi-arrow-left-square-fill"></i> Back</a>
      
    </ul>

    <div class="mb-4">
        <h5 class="fw-bold mb-0">Budget Manager</h5>
        <small class="text-muted">Set and track your spending limits</small>
    </div>

    <div class="row">
    <?php if (empty($budgets)): ?>
        <div class="col-12 text-center py-5 text-muted">
            <i class="bi bi-wallet2 fs-2 d-block mb-2"></i>
            暂无预算数据，请点击右上角添加。
        </div>
    <?php else: ?>
        <?php foreach ($budgets as $bgt): 
            $limit = $bgt['budget_limit'];
            $spent = $bgt['total_spent'];
            
            $percentage = $limit > 0 ? ($spent / $limit) * 100 : 0;
            $remaining = $limit - $spent;
            
            $bar_color = $percentage > 100 ? 'bg-danger' : 'bg-success';
        ?>
        <div class="col-md-4 mb-3">
            <div class="card p-3 bg-white">
                <h6 class="fw-bold"><?=($bgt['category_name'] ?? '未命名分类') ?></h6>
                
                <div class="d-flex justify-content-between small text-muted">
                    <span>已花: $<?= number_format($spent, 2) ?></span>
                    <span>预算: $<?= number_format($limit, 2) ?></span>
                </div>
                
                <div class="progress my-2" style="height: 8px;">
                    <div class="progress-bar <?= $bar_color ?>" role="progressbar" style="width: <?= min($percentage, 100) ?>%"></div>
                </div>
                
                <div class="d-flex justify-content-between small">
                    <span class="fw-bold text-dark"><?= number_format($percentage, 1) ?>% 已使用</span>
                    <span class="text-muted">剩余: $<?= number_format(max($remaining, 0), 2) ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="budgetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Add Budget Limit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" required>
                <?php foreach($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Limit Amount ($)</label>
            <input type="number" step="0.01" name="amount_limit" class="form-control text-start" placeholder="e.g. 50000" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Time Frame</label>
            <select name="time_frame" class="form-select">
                <option value="monthly">Monthly</option>
                <option value="yearly">Yearly</option>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="add_budget" class="btn btn-dark w-100">Save Budget</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="transactionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="addPostForm">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Add New Expense Transaction</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Title / Description</label>
            <input type="text" id="title" name="title" class="form-control text-start" placeholder="e.g. Electric Bill Payment" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Amount ($)</label>
            <input type="number" step="0.01" id="amount" name="amount" class="form-control text-start" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" id="expense_date" name="date" class="form-control text-start" value="<?= date('Y-m-d'); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select id="category_id" name="category_id" class="form-select" required>
                <?php foreach($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select id="payment_method_id" name="payment_method_id" class="form-select" required>
                <?php foreach($payments as $payment): ?>
                    <option value="<?= $payment['id'] ?>"><?= htmlspecialchars($payment['payment_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-dark w-100">Log Transaction</button>
      </div>
    </form>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  $('#addPostForm').on('submit', (function(event){
      event.preventDefault();
      console.log("Form submitted");
      $.ajax({
        url: "http://localhost/finalproject/backend/index.php",
        type: "POST",
        data: {
          action: "addNewPost",
          title: $('#title').val(),
          amount: $('#amount').val(),
          category_id: $('#category_id').val(),
          payment_method_id: $('#payment_method_id').val(),
          expense_date: $('#expense_date').val(),
        },
        success: function(response){
            console.log(response);
            alert("Successfully Inserted Data!");
            window.location.reload(); // 数据插入后刷新本页看到新进度条
        },
        error: function(xhr, status, error){
            console.log("Error: ", error);
        }
      });
  }));
</script>
</body>
</html>