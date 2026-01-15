<?php
session_start();
include 'include/db_connect.php';

// CHECK LOGIN
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_email'])) {
    header("Location: login_admin.php");
    exit;
}

$admin_name  = $_SESSION['admin_name'] ?? 'Admin';
$admin_email = $_SESSION['admin_email'] ?? 'admin@example.com';

// STATUS FILTER
$filter = $_GET['status'] ?? 'all';
$allowedFilters = ['all','unpaid','paid','shipped','received','cancelled'];
if (!in_array($filter, $allowedFilters)) $filter = 'all';

// BUILD QUERY
$query = "SELECT * FROM orders";
if ($filter !== 'all') {
    $query .= " WHERE status='" . $conn->real_escape_string($filter) . "'";
}
$query .= " ORDER BY id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Orders - AdminHub</title>
<link rel="stylesheet" href="style.css?v=22">
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<style>
/* Sidebar & profile styles */
.brand img { width:40px; height:auto; margin-right:3px; vertical-align:middle; }
.side-menu li a { color:#FFD700; }
.side-menu li.active a { background: rgba(0,0,0,0.8); color:#FFD700; }

/* Filter buttons */
.status-btns { display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; }
.status-btns a { padding:8px 14px; background:rgba(0,0,0,0.7); border-radius:6px; text-decoration:none; color:#FFD700; font-weight:600; }
.status-btns a.active { background:#FFD700; color:#000; }

/* Table styling */
table { width:100%; border-collapse:collapse; margin-top:10px; background: rgba(0,0,0,0.7); color:#FFD700; border-radius:10px; overflow:hidden; }
table th, table td { padding:12px 16px; text-align:center; vertical-align:top; }
table th { background: rgba(0,0,0,0.85); color:#FFD700; }
table tr:nth-child(even) { background: rgba(255,255,255,0.05); }
table tr:hover { background: rgba(255,215,0,0.1); }

/* Items list */
.items-list { text-align:left; font-size:14px; }
.items-list div { margin-bottom:4px; }

/* Action buttons */
.actions { display:flex; gap:5px; justify-content:center; flex-wrap:nowrap; }
.action-btn { padding:6px 12px; border-radius:6px; border:none; cursor:pointer; color:#fff; font-weight:600; min-width:70px; display:flex; align-items:center; justify-content:center; gap:4px; white-space:nowrap; }
.btn-paid { background:#2a9d8f; }
.btn-ship { background:#e09f3e; }
.btn-received { background:#4a4e69; }
.btn-cancel { background:#d90429; }
.btn-remove { background:#ff0000; }
.btn-remove i { font-size:18px; }
</style>
</head>
<body>

<!-- SIDEBAR -->
<section id="sidebar">
    <a class="brand">
        <img src="img/logo.png">
        <span class="text">Midnight Brew</span>
    </a>
    <ul class="side-menu top">
        <li><a href="index.php"><i class='bx bxs-dashboard'></i><span>Dashboard</span></a></li>
        <li class="active"><a href="orders.php"><i class='bx bxs-truck'></i><span>Orders</span></a></li>
        <li><a href="menu.php"><i class='bx bxs-coffee'></i><span>Menu</span></a></li>
        <li><a href="team.php"><i class='bx bxs-group'></i><span>Team</span></a></li>
    </ul>
    <ul class="side-menu">
        <li><a href="logout.php" class="logout"><i class='bx bxs-log-out-circle'></i><span>Logout</span></a></li>
    </ul>
</section>

<!-- CONTENT -->
<section id="content">
<nav>
    <i class='bx bx-menu'></i>
    <div class="profile">
        <img src="img/people.jpg">
        <div class="profile-info">
            <h4><?= htmlspecialchars($admin_name) ?></h4>
            <p><?= htmlspecialchars($admin_email) ?></p>
        </div>
    </div>
</nav>

<main>
<h2>Orders Management</h2>

<!-- FILTER BUTTONS -->
<div class="status-btns">
<?php
foreach ($allowedFilters as $s) {
    $active = ($filter == $s) ? "active" : "";
    echo "<a class='$active' href='orders.php?status=$s'>".ucfirst($s)."</a>";
}
?>
</div>

<!-- ORDER TABLE -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Order Code</th>
            <th>User</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
            <th>Items</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr id="row-<?= $row['id'] ?>">
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['order_code']) ?></td>
            <td><?= $row['user_id'] ?></td>
            <td>₱<?= number_format($row['total_amount'],2) ?></td>
            <td><span id="status-<?= $row['id'] ?>"><?= $row['status'] ?></span></td>
            <td><?= $row['order_date'] ?></td>
            <td class="items-list">
                <?php
                $itemsResult = $conn->query("SELECT product_name, quantity, price FROM order_items WHERE order_id=" . $row['id']);
                while ($item = $itemsResult->fetch_assoc()) {
                    echo "<div>{$item['product_name']} - ₱{$item['price']} × {$item['quantity']}</div>";
                }
                ?>
            </td>
            <td>
                <div class="actions">
                <?php if ($row['status'] == 'unpaid'): ?>
                    <button class="action-btn btn-paid" onclick="updateStatus(<?= $row['id'] ?>, 'paid')">Paid</button>
                    <button class="action-btn btn-cancel" onclick="updateStatus(<?= $row['id'] ?>, 'cancelled')">Cancel</button>
                <?php elseif ($row['status'] == 'paid'): ?>
                    <button class="action-btn btn-ship" onclick="updateStatus(<?= $row['id'] ?>, 'shipped')">Ship</button>
                    <button class="action-btn btn-paid" onclick="updateStatus(<?= $row['id'] ?>, 'unpaid')">Unpaid</button>
                <?php elseif ($row['status'] == 'shipped'): ?>
                    <button class="action-btn btn-received" onclick="updateStatus(<?= $row['id'] ?>, 'received')">Received</button>
                <?php elseif ($row['status'] == 'received' || $row['status'] == 'cancelled'): ?>
                    <button class="action-btn btn-remove" onclick="removeOrder(<?= $row['id'] ?>)"><i class='bx bxs-trash'></i></button>
                <?php endif; ?>
                </div>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

</main>
</section>

<script>
function updateStatus(orderId, newStatus) {
    if(!confirm("Change status to " + newStatus + "?")) return;

    const formData = new FormData();
    formData.append('order_id', orderId);
    formData.append('status', newStatus);

    fetch("update_order_status.php", { method:'POST', body:formData })
        .then(r => r.json())
        .then(res => {
            if(res.success){
                document.getElementById("status-" + orderId).innerText = newStatus;
                alert("Order updated successfully!");
                const currentFilter = "<?= $filter ?>";
                if(currentFilter != 'all' && currentFilter != newStatus){
                    const row = document.getElementById("row-" + orderId);
                    if(row) row.remove();
                }
            } else {
                alert(res.message);
            }
        });
}

function removeOrder(orderId){
    if(!confirm("Are you sure you want to remove this order?")) return;
    const formData = new FormData();
    formData.append('order_id', orderId);

    fetch("remove_order.php", { method:'POST', body:formData })
        .then(r => r.json())
        .then(res => {
            if(res.success){
                const row = document.getElementById("row-" + orderId);
                if(row) row.remove();
                alert("Order removed successfully!");
            } else {
                alert(res.message);
            }
        });
}
</script>

</body>
</html>
