<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert error">🔐 请先登录系统！</div>';
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 保持原有数据库处理逻辑不变
    // ...
    if ($stmt->execute()) {
        $message = '<div class="alert success">🎉 课程发布成功！</div>';
    } else {
        $message = '<div class="alert error">⚠️ 错误：'.$stmt->error.'</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>创建新课程</title>
    <style>
        :root {
            --primary: #6366f1;
            --secondary: #8b5cf6;
            --glass: rgba(255,255,255,0.9);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', '微软雅黑', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(45deg, #1a1a2e, #16213e);
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .fluid-bg {
            position: absolute;
            width: 200vmax;
            height: 200vmax;
            background: radial-gradient(var(--primary) 0%, transparent 70%);
            opacity: 0.1;
            animation: fluid 20s linear infinite;
            filter: blur(80px);
        }

        @keyframes fluid {
            0% { transform: translate(-50%, -50%) scale(1) rotate(0deg); }
            50% { transform: translate(-50%, -50%) scale(1.2) rotate(180deg); }
            100% { transform: translate(-50%, -50%) scale(1) rotate(360deg); }
        }

        .container {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(12px);
            padding: 40px;
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            width: 95%;
            max-width: 700px;
            z-index: 1;
            transition: transform 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
        }

        h2 {
            color: #fff;
            text-align: center;
            margin-bottom: 32px;
            font-size: 2.4rem;
            letter-spacing: 1px;
            position: relative;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--primary);
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            margin-bottom: 12px;
            color: #e0e0ff;
            font-weight: 500;
            font-size: 1.1rem;
        }

        input, textarea {
            width: 100%;
            padding: 14px 20px;
            background: rgba(255,255,255,0.08);
            border: 2px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: #fff;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input::placeholder, textarea::placeholder {
            color: rgba(255,255,255,0.6);
        }

        input:focus, textarea:focus {
            background: rgba(255,255,255,0.12);
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.3);
        }

        .file-upload {
            position: relative;
            background: rgba(255,255,255,0.05);
            border: 2px dashed rgba(255,255,255,0.2);
            padding: 20px;
            text-align: center;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload:hover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
        }

        button {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 16px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            font-weight: 600;
            letter-spacing: 1px;
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

        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            text-align: center;
            animation: slideIn 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }

        .success {
            background: rgba(72, 187, 120, 0.15);
            border: 1px solid #48bb78;
            color: #48bb78;
        }

        .error {
            background: rgba(245, 101, 101, 0.15);
            border: 1px solid #f56565;
            color: #f56565;
        }

        @keyframes slideIn {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @media (max-width: 768px) {
            .container {
                padding: 24px;
                margin: 15px;
            }
            h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="fluid-bg" style="left:30%;top:20%"></div>
    <div class="fluid-bg" style="left:70%;top:80%"></div>
    
    <div class="container">
        <?php echo $message; ?>
        <h2>📚 创建新课程</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>课程标题</label>
                <input type="text" name="title" required placeholder="输入课程名称（必填）">
            </div>

            <div class="form-group">
                <label>课程描述</label>
                <textarea name="description" placeholder="详细描述课程内容..."></textarea>
            </div>

            <div class="form-group">
                <label>讲师名称</label>
                <input type="text" name="instructor" required placeholder="输入主讲老师姓名">
            </div>

            <div class="form-group">
                <label>课程分类</label>
                <input type="text" name="category" required placeholder="例如：人工智能开发">
            </div>

            <div class="form-group">
                <label>课程时长</label>
                <input type="text" name="duration" placeholder="例如：8周强化课程">
            </div>

            <div class="form-group">
                <label>课程价格（USD）</label>
                <input type="number" step="0.01" name="price" placeholder="输入价格 例如：99.99">
            </div>

            <div class="form-group">
                <label>上传课程封面</label>
                <div class="file-upload">
                    <span>📷 点击上传封面图片（推荐尺寸：1200×628）</span>
                    <input type="file" name="image" accept="image/*">
                </div>
            </div>

            <button type="submit">🚀 立即发布课程</button>
        </form>
    </div>
</body>
</html>