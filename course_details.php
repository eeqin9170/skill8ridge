<?php
require 'db.php';
session_start();

// 语言处理
$available_langs = ['en' => 'English', 'zh' => '中文', 'ms' => 'Bahasa Melayu'];
$default_lang = 'en';

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
    header("Location: ".strtok($_SERVER["REQUEST_URI"],'?'));
    exit;
}

$current_lang = isset($_SESSION['lang']) && array_key_exists($_SESSION['lang'], $available_langs) 
                ? $_SESSION['lang'] 
                : $default_lang;

// 多语言文本
$lang = [
    'en' => [
        'instructor' => 'Instructor',
        'duration' => 'Duration',
        'description' => 'Course Description',
        'enroll' => 'Enroll Now',
        'back' => 'Back to Courses',
        'language' => 'Language'
    ],
    'zh' => [
        'instructor' => '讲师',
        'duration' => '课程时长',
        'description' => '课程介绍',
        'enroll' => '立即报名',
        'back' => '返回课程列表',
        'language' => '选择语言'
    ],
    'ms' => [
        'instructor' => 'Pengajar',
        'duration' => 'Tempoh Kursus',
        'description' => 'Deskripsi Kursus',
        'enroll' => 'Daftar Sekarang',
        'back' => 'Kembali ke Senarai',
        'language' => 'Pilih Bahasa'
    ]
];

try {
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();

    if (!$course) {
        throw new Exception($current_lang === 'zh' ? "课程不存在" : ($current_lang === 'ms' ? "Kursus tiada" : "Course not found"));
    }
} catch (Exception $e) {
    die("<div class='error'>".htmlspecialchars($e->getMessage())."</div>");
}
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($course['title']) ?> - SkillBridge</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <style>
    :root {
        --primary: #2A2A72;
        --secondary: #009FFD;
        --accent: #FF0076;
        --gradient: linear-gradient(45deg, var(--primary), var(--secondary));
        --light-bg: #f8f9fa;
        --text-dark: #2d3436;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, rgba(248,249,250,0.95) 0%, rgba(233,236,239,0.95) 100%);
        min-height: 100vh;
        color: var(--text-dark);
        position: relative;
        padding: 20px;
    }

    .container {
        max-width: 800px;
        margin: 80px auto 40px;
        position: relative;
        z-index: 1;
    }

    /* 语言切换器 */
    .language-switcher {
        position: fixed;
        top: 30px;
        right: 30px;
        z-index: 1000;
        background: rgba(255,255,255,0.9);
        border-radius: 25px;
        padding: 5px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .lang-select {
        padding: 8px 15px;
        border: none;
        background: transparent;
        font-family: 'Poppins', sans-serif;
        font-size: 0.95rem;
        color: var(--primary);
        cursor: pointer;
        appearance: none;
    }

    .course-detail {
        background: rgba(255,255,255,0.95);
        border-radius: 20px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        padding: 3rem;
        position: relative;
        backdrop-filter: blur(5px);
        animation: fadeInUp 0.6s ease;
    }

    .course-title {
        font-size: 2.5rem;
        color: var(--primary);
        margin-bottom: 2rem;
        line-height: 1.2;
    }

    .course-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin: 2rem 0;
    }

    .meta-item {
        background: var(--light-bg);
        padding: 1.5rem;
        border-radius: 15px;
        text-align: center;
        border-left: 4px solid var(--secondary);
    }

    .meta-label {
        color: var(--secondary);
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .meta-value {
        font-weight: 600;
        color: var(--primary);
        font-size: 1.1rem;
    }

    .course-description {
        margin: 3rem 0;
        line-height: 1.8;
    }

    .btn-group {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn {
        padding: 1rem 2rem;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        flex: 1;
        text-align: center;
    }

    .btn-enroll {
        background: var(--gradient);
        color: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .btn-back {
        background: var(--light-bg);
        color: var(--primary);
        border: 2px solid var(--primary);
    }

    @media (max-width: 768px) {
        .container {
            margin: 60px auto 20px;
        }
        
        .course-title {
            font-size: 2rem;
        }
        
        .btn-group {
            flex-direction: column;
        }
        
        .language-switcher {
            top: 15px;
            right: 15px;
        }
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    </style>
</head>
<body>
    <div class="language-switcher">
        <select class="lang-select" onchange="window.location.href='?lang='+this.value">
            <option value="en" <?= $current_lang === 'en' ? 'selected' : '' ?>>English</option>
            <option value="zh" <?= $current_lang === 'zh' ? 'selected' : '' ?>>中文</option>
            <option value="ms" <?= $current_lang === 'ms' ? 'selected' : '' ?>>Bahasa Melayu</option>
        </select>
    </div>

    <div class="container">
        <div class="course-detail">
            <h1 class="course-title"><?= htmlspecialchars($course['title']) ?></h1>
            
            <div class="course-meta">
                <div class="meta-item">
                    <div class="meta-label"><?= $lang[$current_lang]['instructor'] ?></div>
                    <div class="meta-value"><?= htmlspecialchars($course['instructor']) ?></div>
                </div>
                <div class="meta-item">
                    <div class="meta-label"><?= $lang[$current_lang]['duration'] ?></div>
                    <div class="meta-value"><?= htmlspecialchars($course['duration']) ?></div>
                </div>
            </div>

            <div class="course-description">
                <h3><?= $lang[$current_lang]['description'] ?></h3>
                <p><?= nl2br(htmlspecialchars($course['description'])) ?></p>
            </div>

            <div class="btn-group">
                <a href="enroll.php?id=<?= $course['id'] ?>" class="btn btn-enroll"><?= $lang[$current_lang]['enroll'] ?></a>
                <a href="courses.php" class="btn btn-back"><?= $lang[$current_lang]['back'] ?></a>
            </div>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>