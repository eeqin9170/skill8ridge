<?php
include 'db.php';

// 安全获取ID参数
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 使用预处理语句防止SQL注入
    $stmt = $conn->prepare("UPDATE jobs SET title=?, company=?, location=?, description=?, salary=? WHERE job_id=?");
    $stmt->bind_param("sssssi", 
        $_POST['title'],
        $_POST['company'],
        $_POST['location'],
        $_POST['description'],
        $_POST['salary'],
        $_POST['job_id']
    );

    if ($stmt->execute()) {
        header("Location: admin_homepage.php");
        exit();
    } else {
        $error = "更新失败：" . $stmt->error;
    }
}

// 获取职位数据
$stmt = $conn->prepare("SELECT * FROM jobs WHERE job_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$job = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑职位 | CareerHub</title>
    <style>
        :root {
            --primary: #8338ec;
            --secondary: #ff006e;
            --bg: #f8f9fa;
            --text: #212529;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, sans-serif;
        }

        body {
            min-height: 100vh;
            background: var(--bg);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .container {
            background: white;
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 12px 48px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 680px;
            transition: transform 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
        }

        h1 {
            color: var(--text);
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text);
            font-weight: 500;
            font-size: 0.9rem;
        }

        input, textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus, textarea:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(131, 56, 236, 0.1);
        }

        textarea {
            height: 120px;
            resize: vertical;
        }

        button {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        button:hover {
            opacity: 0.9;
            transform: scale(0.98);
        }

        .error {
            background: #ffe3e3;
            color: #ff6b6b;
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 640px) {
            body {
                padding: 1rem;
            }
            .container {
                padding: 1.5rem;
            }
            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 编辑职位</h1>
        
        <?php if(isset($error)): ?>
            <div class="error">⚠️ <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="job_id" value="<?= htmlspecialchars($job['job_id']) ?>">
            
            <div class="form-group">
                <label>职位名称</label>
                <input type="text" name="title" 
                       value="<?= htmlspecialchars($job['title']) ?>" 
                       placeholder="请输入职位名称" required>
            </div>

            <div class="form-group">
                <label>公司名称</label>
                <input type="text" name="company" 
                       value="<?= htmlspecialchars($job['company']) ?>" 
                       placeholder="请输入公司全称" required>
            </div>

            <div class="form-group">
                <label>工作地点</label>
                <input type="text" name="location" 
                       value="<?= htmlspecialchars($job['location']) ?>" 
                       placeholder="例如：上海徐汇区" required>
            </div>

            <div class="form-group">
                <label>职位描述</label>
                <textarea name="description" 
                          placeholder="详细描述职位要求和职责..."
                          required><?= htmlspecialchars($job['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label>薪资范围</label>
                <input type="text" name="salary" 
                       value="<?= htmlspecialchars($job['salary']) ?>" 
                       placeholder="例如：20-35K·15薪">
            </div>

            <button type="submit">更新职位信息</button>
        </form>
    </div>
</body>
</html>