<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'spifit';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {
        // Create users table if it doesn't exist
        $sql = file_get_contents('create_users_table.sql');
        $pdo->exec($sql);
        
        // Insert a default admin user
        $defaultAdmin = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $defaultAdmin->execute(['Admin', 'admin@spifit.com', password_hash('admin123', PASSWORD_DEFAULT), 'admin']);
    }

    // Fetch all users from the database with error handling
    try {
        $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalUsers = count($users);
    } catch (PDOException $e) {
        $error = "Error fetching users: " . $e->getMessage();
        $users = [];
        $totalUsers = 0;
    }

    // Count total calculations with error handling
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM calculations");
        $totalCalculations = $stmt->fetchColumn();
    } catch (PDOException $e) {
        $totalCalculations = 0;
    }

} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SPIFIT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Arial', sans-serif; 
            background-color: #f4f6f9;
            color: #333;
        }
        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            color: #2c3e50;
            font-size: 24px;
        }
        .logout-btn {
            background-color: #e74c3c;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            color: #7f8c8d;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .stat-card p {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .actions-bar {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .action-btn {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .action-btn:hover {
            background-color: #2980b9;
        }
        table {
            width: 100%;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        tr:hover {
            background-color: #f8f9fa;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .edit-btn, .delete-btn {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }
        .edit-btn {
            background-color: #2ecc71;
            color: white;
        }
        .delete-btn {
            background-color: #e74c3c;
            color: white;
        }
        .edit-btn:hover {
            background-color: #27ae60;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
        .error-message {
            background-color: #fee;
            color: #e74c3c;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #e74c3c;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-tachometer-alt"></i> SPIFIT Admin Dashboard</h1>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="container">
        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-card">
                <h3><i class="fas fa-users"></i> Total Users</h3>
                <p><?php echo $totalUsers; ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-calculator"></i> Total Calculations</h3>
                <p><?php echo $totalCalculations; ?></p>
            </div>
        </div>

        <div class="actions-bar">
            <a href="view_calculations.php" class="action-btn">
                <i class="fas fa-chart-bar"></i> View Calculations
            </a>
            <a href="add_user.php" class="action-btn" style="background-color: #27ae60;">
                <i class="fas fa-user-plus"></i> Add New User
            </a>
        </div>

        <?php if (empty($users)): ?>
            <div class="info-message" style="text-align: center; padding: 20px;">
                <i class="fas fa-info-circle"></i> No users found in the database.
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td class="actions">
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="delete-btn" 
                                   onclick="return confirm('Are you sure you want to delete this user?');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>