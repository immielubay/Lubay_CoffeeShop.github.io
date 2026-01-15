<?php
session_start();
include 'include/db_connect.php'; // Database connection

// Redirect if not logged in
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

// Retrieve session variables
$admin_name = $_SESSION['admin_name'];
$admin_email = $_SESSION['admin_email'];

// Handle update request
if (isset($_POST['update_admin'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admins SET name=?, email=?, password=?, role=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $email, $hashed, $role, $id);
    } else {
        $stmt = $conn->prepare("UPDATE admins SET name=?, email=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $role, $id);
    }
    $stmt->execute();
    echo "<script>alert('Admin updated successfully!'); window.location='team.php';</script>";
}

// Handle add new admin
if (isset($_POST['add_admin'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admins (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed, $role);
    $stmt->execute();
    echo "<script>alert('New admin added successfully!'); window.location='team.php';</script>";
}

// Fetch all admins
$result = $conn->query("SELECT * FROM admins");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Team | Midnight Brew</title>
<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>

<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="style.css?v=14">

<style>
/* Adjust logo */
.brand img {
    width: 40px;
    height: auto;
    margin-right: 3px;
    vertical-align: middle;
}

/* Table styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: rgba(0,0,0,0.7);
    color: #FFD700;
    border-radius: 10px;
    overflow: hidden;
}
table th, table td {
    padding: 12px 16px;
    text-align: left;
}
table th {
    background: rgba(0,0,0,0.8);
    color: #FFD700;
}
table tr:nth-child(even) {
    background: rgba(255,255,255,0.05);
}
table tr:hover {
    background: rgba(255,215,0,0.1);
}
button.edit-btn, button.add-btn {
    background: #91aeffff;
    color: #000;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
}
button.edit-btn:hover, button.add-btn:hover {
    background: #e6c200;
}

/* Modal overlay */
.modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background: rgba(0,0,0,0.7);
}

/* Modal content */
.modal-content {
    background: rgba(0,0,0,0.9);
    margin: 10% auto;
    padding: 20px;
    border-radius: 10px;
    width: 90%;
    max-width: 500px;
    color: #FFD700;
    position: relative;
}

/* Close button */
.close {
    position: absolute;
    right: 15px;
    top: 10px;
    font-size: 28px;
    font-weight: bold;
    color: #FFD700;
    cursor: pointer;
}

.close:hover {
    color: #e6c200;
}

.modal form label {
    display: block;
    margin-top: 10px;
}
.modal form input, .modal form select {
    width: 100%;
    padding: 8px 10px;
    margin-top: 4px;
    border-radius: 6px;
    border: 1px solid #FFD700;
    background: rgba(255,255,255,0.1);
    color: #FFD700;
}
.modal form button {
    margin-top: 15px;
    padding: 10px 15px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    background: #FFD700;
    color: #000;
}
.modal form button:hover {
    background: #e6c200;
}

/* Add button style */
h1 {
    display: inline-block;
    margin-right: 20px;
}
.add-btn {
    float: right;
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
        <li>
            <a href="index.php">
                <i class='bx bxs-dashboard'></i>
                <span class="text">Dashboard</span>
            </a>
        </li> 
        <li>
            <a href="orders.php">
					<i class='bx bxs-truck' ></i>
					<span class="text">Orders</span>
			</a>
        </li>
        <li>
            <a href="menu.php">
                <i class='bx bxs-coffee'></i>
                <span class="text">Menu</span>
            </a>
        </li>
        <li class="active">
            <a href="team.php">
                <i class='bx bxs-group'></i>
                <span class="text">Team</span>
            </a>
        </li>
    </ul>
    <ul class="side-menu"> 
        <li>
            <a href="logout.php" class="logout">
                <i class='bx bxs-log-out-circle'></i>
                <span class="text">Logout</span>
            </a>
        </li>
    </ul>
</section>

<!-- CONTENT -->
<section id="content">
    <nav>
        <i class='bx bx-menu'></i>
        <a href="#" class="profile">
            <img src="img/people.jpg" alt="Profile">
            <div class="profile-info">
                <h4><?php echo htmlspecialchars($admin_name); ?></h4>
                <p><?php echo htmlspecialchars($admin_email); ?></p>
            </div>
        </a>
    </nav>

    <main>
        <h1>Admin Management</h1>
        <button class="add-btn" onclick="openAddModal()">+ Add</button>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td><?= ucfirst($row['role']); ?></td>
                    <td>
                        <button class="edit-btn" 
                            onclick="openEditModal(<?= $row['id']; ?>, '<?= addslashes($row['name']); ?>', '<?= addslashes($row['email']); ?>', '<?= $row['role']; ?>')">Edit</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </main>
</section>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Edit Admin</h2>
        <form action="team.php" method="POST">
            <input type="hidden" name="id" id="modalId">

            <label>Name:</label>
            <input type="text" name="name" id="modalName" required>

            <label>Email:</label>
            <input type="email" name="email" id="modalEmail" required>

            <label>New Password (leave blank to keep current):</label>
            <input type="password" name="password" id="modalPassword">

            <label>Role:</label>
            <select name="role" id="modalRole" required>
                <option value="admin">Admin</option>
                <option value="barista">Barista</option>
                <option value="inventory manager">Inventory Manager</option>
            </select>

            <button type="submit" name="update_admin">Save Changes</button>
        </form>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAddModal()">&times;</span>
        <h2>Add New Admin</h2>
        <form action="team.php" method="POST">
            <label>Name:</label>
            <input type="text" name="name" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Role:</label>
            <select name="role" required>
                <option value="admin">Admin</option>
                <option value="barista">Barista</option>
                <option value="inventory manager">Inventory Manager</option>
            </select>

            <button type="submit" name="add_admin">Add Admin</button>
        </form>
    </div>
</div>

<script>
// Edit modal functions
function openEditModal(id, name, email, role) {
    document.getElementById('modalId').value = id;
    document.getElementById('modalName').value = name;
    document.getElementById('modalEmail').value = email;
    document.getElementById('modalRole').value = role;
    document.getElementById('editModal').style.display = 'block';
}
function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Add modal functions
function openAddModal() {
    document.getElementById('addModal').style.display = 'block';
}
function closeAddModal() {
    document.getElementById('addModal').style.display = 'none';
}

// Close modals if clicking outside
window.onclick = function(event) {
    if (event.target == document.getElementById('editModal')) {
        closeModal();
    }
    if (event.target == document.getElementById('addModal')) {
        closeAddModal();
    }
}
</script>
<script src="script.js"></script>
</body>
</html>
