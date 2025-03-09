<?php
include 'db.php';
session_start();

// 检查用户登录状态
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

// 处理表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $job_id = mysqli_real_escape_string($conn, $_POST['job_id']);
    $cover_letter = mysqli_real_escape_string($conn, trim($_POST['cover_letter']));

    // 使用预处理语句
    $stmt = $conn->prepare("INSERT INTO job_applications (user_id, job_id, cover_letter) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $job_id, $cover_letter);

    if ($stmt->execute()) {
        $success = "Application submitted successfully!";
    } else {
        $error = "Error: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job | CareerHub</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem 5%;
        }

        .brand {
            font-size: 1.5rem;
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #1e293b;
            margin-bottom: 1.5rem;
            font-size: 2rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            resize: vertical;
            min-height: 200px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            display: block;
            width: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 2px solid #86efac;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #fca5a5;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #64748b;
            text-decoration: none;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #2563eb;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="#" class="brand">CareerHub</a>
    </nav>

    <div class="container">
        <h2>📝 Apply for Job</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                ✅ <?php echo $success; ?>
                <p style="margin-top: 1rem;">
                    <a href="jobs.php" class="back-link">View other jobs →</a>
                </p>
            </div>
        <?php elseif ($error): ?>
            <div class="alert alert-error">
                ❌ <?php echo $error; ?>
                <p style="margin-top: 1rem;">
                    <a href="javascript:history.back()" class="back-link">← Go back to fix</a>
                </p>
            </div>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($_GET['job_id']); ?>">
                
                <div class="form-group">
                    <label for="cover_letter" style="display: block; margin-bottom: 0.5rem; color: #475569;">
                        Cover Letter:
                    </label>
                    <textarea 
                        name="cover_letter" 
                        id="cover_letter"
                        placeholder="Write your cover letter here..."
                        required
                    ></textarea>
                </div>

                <button type="submit" class="btn">
                    🚀 Submit Application
                </button>
                
                <a href="jobs.php" class="back-link">
                    ← Return to Job Listings
                </a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>