<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die('<div class="error">请先登录</div>');
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>个人中心 - StyleHub</title>
    <style>
        :root {
            --primary: #8338ec;
            --secondary: #ff006e;
            --glass: rgba(255, 255, 255, 0.9);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(45deg, #f3e8ff 0%, #f0f4ff 100%);
            display: flex;
            justify-content: center;
            padding: 2rem;
        }

        .profile-container {
            width: 100%;
            max-width: 800px;
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-radius: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            margin-top: 4rem;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .avatar-container {
            position: absolute;
            top: -60px;
            left: 50%;
            transform: translateX(-50%);
        }

        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            background: white;
            padding: 4px;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .profile-header h1 {
            font-size: 2.2rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.6);
            padding: 1.5rem;
            border-radius: 1.5rem;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .info-card:hover {
            transform: translateY(-5px);
        }

        .info-label {
            color: var(--primary);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-value {
            color: #2d3436;
            font-size: 1.1rem;
            padding-left: 2rem;
        }

        .edit-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 1.2rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(131, 56, 236, 0.3);
        }

        .edit-btn:hover {
            transform: scale(0.98);
            opacity: 0.9;
        }

        .decorative-line {
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            width: 60%;
            margin: 2rem auto;
            border-radius: 2px;
        }

        @media (max-width: 768px) {
            .profile-container {
                padding: 2rem 1.5rem;
                margin-top: 6rem;
            }
            .avatar {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="avatar-container">
            <img src="uploads/<?= htmlspecialchars($user['profile_picture']) ?>" class="avatar">
        </div>

        <div class="profile-header">
            <h1><?= htmlspecialchars($user['username']) ?></h1>
            <div class="decorative-line"></div>
        </div>

        <div class="info-card">
            <div class="info-label">
                <i class="fas fa-envelope"></i>
                电子邮箱
            </div>
            <div class="info-value"><?= htmlspecialchars($user['email']) ?></div>
        </div>

        <div class="info-card">
            <div class="info-label">
                <i class="fas fa-user-edit"></i>
                个人简介
            </div>
            <div class="info-value"><?= $user['bio'] ? htmlspecialchars($user['bio']) : '暂无简介' ?></div>
        </div>

        <div class="info-card">
            <div class="info-label">
                <i class="fas fa-tools"></i>
                技能专长
            </div>
            <div class="info-value"><?= $user['skills'] ? htmlspecialchars($user['skills']) : '尚未添加技能' ?></div>
        </div>

        <a href="edit_profile.php" class="edit-btn">
            <i class="fas fa-edit"></i>
            编辑个人资料
        </a>
    </div>

    <script src="https://kit.fontawesome.com/your-kit-code.js"></script>
</body>
</html>