<?php
session_start();
include 'include/db_connect.php';

// Check if logged in
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_email'])) {
    header("Location: login_admin.php");
    exit;
}

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$admin_email = $_SESSION['admin_email'] ?? 'admin@example.com';

/* =======================
    DASHBOARD QUERIES
   ======================= */

// New Orders (unpaid)
$newOrders = $conn->query("SELECT COUNT(*) AS c FROM orders WHERE status='unpaid'")
                 ->fetch_assoc()['c']; 

// Recent Orders (latest 5)
$recentOrders = $conn->query("SELECT user_id, order_date, status FROM orders ORDER BY id DESC LIMIT 5");

// To-Do list
$todos = $conn->query("SELECT * FROM admin_todo ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="style.css?v=20">

	<title>AdminHub</title>

    <style>
        .brand img { width:40px; }
        .todo input { width:100%; padding:10px; margin-top:10px; border:1px solid #aaa; border-radius:6px; }
        .todo button { margin-top:10px; padding:8px 12px; background:#3a86ff; border:none; color:#fff; border-radius:6px; cursor:pointer; }
    </style>
        <style>
    .brand img { width:40px; }
    
    /* Add task input */
    .todo input { 
        width:100%; 
        padding:10px; 
        margin-top:10px; 
        border:1px solid #aaa; 
        border-radius:6px; 
    }

    /* Add button */
    .todo button { 
        margin-top:10px; 
        padding:8px 12px; 
        background:#3a86ff; 
        border:none; 
        color:#fff; 
        border-radius:6px; 
        cursor:pointer; 
        display:block; /* ensures margin-bottom works */
    }

    /* Space below button */
    .todo form { 
        margin-bottom:15px; 
    }

    /* Space between tasks */
    .todo-list li { 
        margin-bottom:10px; 
        display:flex; 
        align-items:center; 
        justify-content:space-between; 
        padding:6px 10px; 
        border:1px solid #ddd; 
        border-radius:6px; 
    }

    /* Optional: task text */
    .todo-list li p { 
        margin:0; 
    }

    .todo-list li.completed p { 
        text-decoration: line-through; 
        color: #888; 
    }

    .todo-list li.not-completed p { 
        color:#000; 
    }

    .todo-list li a { 
        margin-left:5px; 
        color:#3a86ff; 
        text-decoration:none; 
    }
</style>

</head>

<body>
<!-- SIDEBAR -->
<section id="sidebar">
    <a href="#" class="brand">
        <img src="img/logo.png">
        <span class="text">Midnight Brew</span>
    </a>
    <ul class="side-menu top">
        <li class="active"><a href="#"><i class='bx bxs-dashboard'></i><span class="text">Dashboard</span></a></li>
        <li><a href="orders.php"><i class='bx bxs-truck'></i><span class="text">Orders</span></a></li>
        <li><a href="menu.php"><i class='bx bxs-coffee'></i><span class="text">Menu</span></a></li>
        <li><a href="team.php"><i class='bx bxs-group'></i><span class="text">Team</span></a></li>
    </ul>
    <ul class="side-menu">
        <li><a href="logout.php" class="logout"><i class='bx bxs-log-out-circle'></i><span class="text">Logout</span></a></li>
    </ul>
</section>

<!-- CONTENT -->
<section id="content">

<nav>
    <i class='bx bx-menu'></i>
    <a class="profile">
        <img src="img/people.jpg">
        <div class="profile-info">
            <h4><?= htmlspecialchars($admin_name) ?></h4>
            <p><?= htmlspecialchars($admin_email) ?></p>
        </div>
    </a>
</nav>

<main>
    <div class="head-title">
        <div class="left">
            <h1>Dashboard</h1>
        </div>
    </div>

    <!-- DASHBOARD BOXES -->
    <ul class="box-info">
        <li>
            <i class='bx bxs-calendar-check'></i>
            <span class="text">
                <h3><?= $newOrders ?></h3>
                <p>New Orders</p>
            </span>
        </li>
    </ul>

    <div class="table-data">
        <!-- RECENT ORDERS -->
        <div class="order">
            <div class="head">
                <h3>Recent Orders</h3>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Date Order</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = $recentOrders->fetch_assoc()): ?>
                    <tr>
                        <td>User ID: <?= $row['user_id'] ?></td>
                        <td><?= $row['order_date'] ?></td>
                        <td><span class="status"><?= ucfirst($row['status']) ?></span></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- TO DO LIST -->
        <div class="todo">
            <div class="head">
                <h3>To-Dos</h3>
            </div>

            <form action="todo_add.php" method="POST">
                <input type="text" name="task" placeholder="Add new task..." required>
                <button type="submit">Add</button>
            </form>

            <ul class="todo-list">
                <?php while($t = $todos->fetch_assoc()): ?>
                <li class="<?= $t['is_done'] ? 'completed' : 'not-completed' ?>">
                    <p><?= htmlspecialchars($t['task']) ?></p>
                    <a href="todo_toggle.php?id=<?= $t['id'] ?>"><i class='bx bx-check'></i></a>
                    <a href="todo_delete.php?id=<?= $t['id'] ?>"><i class='bx bx-trash'></i></a>
                </li>
                <?php endwhile; ?>
            </ul>
        </div>

    </div>

</main>
</section>

</body>
</html>
