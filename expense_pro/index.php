<?php 
include('db.php'); 
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$uid = $_SESSION['user_id'];

// 1. DELETE Logic
if (isset($_GET['delete'])) {
    $id = sanitize($conn, $_GET['delete']);
    $conn->query("DELETE FROM expenses WHERE id=$id AND user_id=$uid");
    header("Location: index.php");
}

// 2. ADD / UPDATE Logic
if (isset($_POST['save'])) {
    $title = sanitize($conn, $_POST['title']);
    $amt = sanitize($conn, $_POST['amount']);
    $cat = sanitize($conn, $_POST['category']);
    
    if (!empty($_POST['id'])) {
        $id = sanitize($conn, $_POST['id']);
        $conn->query("UPDATE expenses SET title='$title', amount='$amt', category='$cat' WHERE id=$id AND user_id=$uid");
    } else {
        $conn->query("INSERT INTO expenses (user_id, title, amount, category) VALUES ($uid, '$title', '$amt', '$cat')");
    }
    header("Location: index.php");
}

// 3. FETCH DATA
$balance = $conn->query("SELECT SUM(amount) as total FROM expenses WHERE user_id=$uid")->fetch_assoc()['total'] ?? 0;
$expenses = $conn->query("SELECT * FROM expenses WHERE user_id=$uid ORDER BY date_added DESC");

// 4. EDIT FETCH (If editing)
$row_to_edit = ['id'=>'', 'title'=>'', 'amount'=>'', 'category'=>'Other'];
if (isset($_GET['edit'])) {
    $eid = sanitize($conn, $_GET['edit']);
    $row_to_edit = $conn->query("SELECT * FROM expenses WHERE id=$eid AND user_id=$uid")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
    <div class="container">
        <header style="margin-bottom: 20px;">
            <a href="logout.php" class="logout">Logout</a>
            <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>
        </header>

        <div class="card" style="text-align: center;">
            <span style="color:#666">TOTAL SPENT</span>
            <h1 style="margin:5px 0; color:var(--primary)">₹<?php echo number_format($balance, 2); ?></h1>
        </div>

        <div class="card">
            <h3><?php echo $row_to_edit['id'] ? 'Edit' : 'Add New'; ?> Expense</h3>
            <form method="POST" style="display:grid; grid-template-columns: 2fr 1fr 1fr 0.5fr; gap:10px;">
                <input type="hidden" name="id" value="<?php echo $row_to_edit['id']; ?>">
                <input type="text" name="title" placeholder="What for?" value="<?php echo $row_to_edit['title']; ?>" required>
                <input type="number" step="0.01" name="amount" placeholder="Amount" value="<?php echo $row_to_edit['amount']; ?>" required>
                <select name="category">
                    <?php foreach(['Food','Bills','Tech','Other'] as $c): ?>
                        <option value="<?php echo $c; ?>" <?php if($row_to_edit['category']==$c) echo 'selected'; ?>><?php echo $c; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="save" class="btn-primary">Save</button>
            </form>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr><th>Item</th><th>Category</th><th>Amount</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php while($r = $expenses->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $r['title']; ?></td>
                        <td><span class="badge"><?php echo $r['category']; ?></span></td>
                        <td>₹<?php echo $r['amount']; ?></td>
                        <td>
                            <a href="?edit=<?php echo $r['id']; ?>" class="edit-btn">Edit</a>
                            <a href="?delete=<?php echo $r['id']; ?>" class="delete-btn" onclick="return confirm('Delete this?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>