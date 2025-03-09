<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 安全处理输入数据
    $title = htmlspecialchars($_POST['title']);
    $company = htmlspecialchars($_POST['company']);
    $location = htmlspecialchars($_POST['location']);
    $description = htmlspecialchars($_POST['description']);
    $salary = htmlspecialchars($_POST['salary']);

    // 使用预处理语句防止SQL注入
    $stmt = $conn->prepare("INSERT INTO jobs (title, company, location, description, salary) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $company, $location, $description, $salary);
    
    if ($stmt->execute()) {
        header("Location: jobs.php");
        exit();
    } else {
        $error = $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>发布新职位</title>
    <style>
        :root {
            --primary: #6366f1;
            --secondary: #8b5cf6;
            --glass: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(45deg, #1e1e2f, #16162d);
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .background {
            position: absolute;
            width: 200vw;
            height: 200vh;
            background: radial-gradient(circle, 
                rgba(99, 102, 241, 0.1) 0%, 
                rgba(139, 92, 246, 0.05) 30%, 
                transparent 70%);
            animation: float 20s linear infinite;
            filter: blur(80px);
        }

        @keyframes float {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .container {
            background: var(--glass);
            backdrop-filter: blur(12px);
            padding: 2.5rem;
            border-radius: 1.5rem;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            width: 90%;
            max-width: 600px;
            z-index: 1;
            transition: transform 0.3s ease;
        }

        h1 {
            color: #fff;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            position: relative;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -0.5rem;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--primary);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.8rem;
            color: #e0e0ff;
            font-size: 0.95rem;
            font-weight: 500;
        }

        input, textarea {
            width: 100%;
            padding: 0.8rem 1.2rem;
            background: rgba(255,255,255,0.08);
            border: 2px solid rgba(255,255,255,0.1);
            border-radius: 0.8rem;
            color: #fff;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        input:focus, textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.3);
            background: rgba(255,255,255,0.12);
        }

        button {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 0.8rem;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            font-weight: 600;
            position: relative;
            overflow: hidden;
        }

        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255,255,255,0.2),
                transparent
            );
            transition: 0.5s;
        }

        button:hover::before {
            left: 100%;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(99, 102, 241, 0.4);
        }

        .error {
            color: #ff6b6b;
            text-align: center;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
                margin: 1rem;
            }
            
            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="background" style="left:30%;top:20%"></div>
    <div class="background" style="left:70%;top:80%"></div>
    
    <div class="container">
        <h1>💼 发布新职位</h1>
        
        <?php if(isset($error)): ?>
            <div class="error">⚠️ <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>职位名称</label>
                <input type="text" name="title" required placeholder="例如：前端工程师">
            </div>

            <div class="form-group">
                <label>公司名称</label>
                <input type="text" name="company" required placeholder="输入公司全称">
            </div>

            <div class="form-group">
                <label>工作地点</label>
                <input type="text" name="location" required placeholder="例如：北京朝阳区">
            </div>

            <div class="form-group">
                <label>职位描述</label>
                <textarea name="description" required placeholder="详细描述职位要求..."></textarea>
            </div>

            <div class="form-group">
                <label>薪资范围</label>
                <input type="text" name="salary" placeholder="例如：20-35K·15薪">
            </div>

            <button type="submit">🚀 立即发布</button>
        </form>
    </div>
</body>
</html>