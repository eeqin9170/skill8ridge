<?php
include 'db.php';
session_start();

// 语言配置
$available_langs = ['en' => 'English', 'zh' => '中文', 'ms' => 'Bahasa Melayu'];
$default_lang = 'en';

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

$current_lang = $_SESSION['lang'] ?? $default_lang;

// 多语言文本
$lang = [
    'en' => [
        'title' => 'Password Reset',
        'email_placeholder' => 'Your registered email',
        'password_placeholder' => 'New password (min 8 characters)',
        'submit' => 'Reset Password',
        'remember' => 'Remember password?',
        'login' => 'Back to Login',
        'success' => 'Password updated successfully!',
        'error_email' => 'Invalid email format',
        'error_password' => 'Minimum 8 characters required',
        'error_general' => 'Update failed'
    ],
    'zh' => [
        'title' => '重设密码',
        'email_placeholder' => '请输入注册邮箱',
        'password_placeholder' => '新密码（至少8位）',
        'submit' => '立即重设',
        'remember' => '想起密码了？',
        'login' => '返回登录',
        'success' => '密码更新成功！',
        'error_email' => '邮箱格式不正确',
        'error_password' => '密码至少需要8位',
        'error_general' => '更新失败'
    ],
    'ms' => [
        'title' => 'Tetapkan Semula Kata Laluan',
        'email_placeholder' => 'Emel berdaftar anda',
        'password_placeholder' => 'Kata laluan baru (minimum 8 aksara)',
        'submit' => 'Tetapkan Semula',
        'remember' => 'Ingat kata laluan?',
        'login' => 'Kembali ke Log Masuk',
        'success' => 'Kata laluan berjaya dikemaskini!',
        'error_email' => 'Format emel tidak sah',
        'error_password' => 'Minimum 8 aksara diperlukan',
        'error_general' => 'Kemaskini gagal'
    ]
];

// 业务逻辑处理
$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $new_password = $_POST['new_password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = $lang[$current_lang]['error_email'];
    } elseif (strlen($new_password) < 8) {
        $error = $lang[$current_lang]['error_password'];
    } else {
        try {
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt->bind_param("ss", $hashed_password, $email);
            
            if ($stmt->execute()) {
                $success = $lang[$current_lang]['success'];
            } else {
                $error = $lang[$current_lang]['error_general'] . ": " . $stmt->error;
            }
            $stmt->close();
        } catch (Exception $e) {
            $error = $lang[$current_lang]['error_general'] . ": " . $e->getMessage();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="<?= $current_lang ?>" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang[$current_lang]['title'] ?> | SkillBridge</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .gradient-text {
            background: linear-gradient(45deg, #8B5CF6, #EC4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-fuchsia-50 to-indigo-50">
    <div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <!-- 语言切换 -->
        <div class="absolute top-6 right-6 space-y-2">
            <select 
                onchange="window.location.href='?lang='+this.value"
                class="glass-effect rounded-xl border-0 px-4 py-2 text-sm text-purple-600 shadow-lg ring-1 ring-black/10 transition-all hover:ring-purple-300 focus:outline-none focus:ring-2 focus:ring-purple-400"
            >
                <option value="en" <?= $current_lang === 'en' ? 'selected' : '' ?>>English</option>
                <option value="zh" <?= $current_lang === 'zh' ? 'selected' : '' ?>>中文</option>
                <option value="ms" <?= $current_lang === 'ms' ? 'selected' : '' ?>>Bahasa Melayu</option>
            </select>
        </div>

        <div class="glass-effect max-w-md w-full space-y-8 rounded-[2rem] p-10 shadow-2xl shadow-purple-100/40 ring-1 ring-black/5">
            <div class="text-center space-y-4">
                <div class="animate-float inline-flex rounded-2xl bg-gradient-to-br from-purple-100 to-pink-100 p-4 shadow-lg">
                    <svg class="h-12 w-12 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h2 class="text-4xl font-bold gradient-text">
                    <?= $lang[$current_lang]['title'] ?>
                </h2>
            </div>

            <?php if($error): ?>
                <div class="animate-fade-in rounded-xl bg-pink-50/80 p-4 shadow-inner backdrop-blur">
                    <div class="flex items-center space-x-3 text-pink-600">
                        <svg class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-sm font-medium"><?= $error ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="animate-fade-in rounded-xl bg-green-50/80 p-4 shadow-inner backdrop-blur">
                    <div class="flex items-center space-x-3 text-green-600">
                        <svg class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-medium"><?= $success ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" method="POST">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2 pl-1">
                            <?= $current_lang === 'zh' ? '电子邮箱' : 'Email' ?>
                        </label>
                        <input
                            name="email"
                            type="email"
                            required
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            placeholder="<?= $lang[$current_lang]['email_placeholder'] ?>"
                            class="w-full px-4 py-3 rounded-xl border-0 bg-white/80 shadow-sm ring-1 ring-black/10 transition-all placeholder:text-gray-400 focus:ring-2 focus:ring-purple-400 focus:shadow-purple-200/50"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2 pl-1">
                            <?= $current_lang === 'zh' ? '新密码' : 'Password' ?>
                        </label>
                        <input
                            name="new_password"
                            type="password"
                            required
                            minlength="8"
                            placeholder="<?= $lang[$current_lang]['password_placeholder'] ?>"
                            class="w-full px-4 py-3 rounded-xl border-0 bg-white/80 shadow-sm ring-1 ring-black/10 transition-all placeholder:text-gray-400 focus:ring-2 focus:ring-purple-400 focus:shadow-purple-200/50"
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 text-white font-semibold shadow-lg shadow-purple-200/40 hover:shadow-purple-300/50 transition-all hover:scale-[1.02]"
                >
                    <?= $lang[$current_lang]['submit'] ?>
                </button>

                <p class="text-center text-sm text-gray-500">
                    <?= $lang[$current_lang]['remember'] ?> 
                    <a 
                        href="login.html" 
                        class="font-semibold text-purple-600 hover:text-purple-500 transition-colors"
                    >
                        <?= $lang[$current_lang]['login'] ?>
                    </a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>