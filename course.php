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
        'title' => 'Featured Courses',
        'instructor' => 'Instructor:',
        'duration' => 'Duration:',
        'view_details' => 'View Details',
        'free' => 'FREE',
        'currency' => 'RM ',
        'language' => 'Language'
    ],
    'zh' => [
        'title' => '精选课程',
        'instructor' => '讲师：',
        'duration' => '课程时长：',
        'view_details' => '查看课程详情',
        'free' => '免费课程',
        'currency' => '马币 ',
        'language' => '选择语言'
    ],
    'ms' => [
        'title' => 'Kursus Pilihan',
        'instructor' => 'Pengajar:',
        'duration' => 'Tempoh:',
        'view_details' => 'Lihat Butiran',
        'free' => 'PERCUMA',
        'currency' => 'RM ',
        'language' => 'Pilih Bahasa'
    ]
];

try {
    $conn->set_charset("utf8mb4");
    $stmt = $conn->prepare("SELECT id, title, description, instructor, duration, price, image FROM courses");
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    die("<div class='error'>System Error: " . $e->getMessage() . "</div>");
}
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang[$current_lang]['title'] ?> - SkillBridge</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <style>
    /* 粒子背景样式 */
    #particles-js {
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 0;
        opacity: 0.3;
        background: linear-gradient(45deg, #2a2a72 0%, #009ffd 100%);
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
        color: #2A2A72;
        cursor: pointer;
        appearance: none;
    }

    /* 保持原有样式 */
    *,
    *::before,
    *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, rgba(248,249,250,0.95) 0%, rgba(233,236,239,0.95) 100%);
        min-height: 100vh;
        color: #2d3436;
        position: relative;
        padding: 20px;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    h2 {
        text-align: center;
        font-size: 2.8rem;
        margin: 2rem 0;
        position: relative;
        color: #2A2A72;
    }

    h2::after {
        content: '';
        display: block;
        width: 60px;
        height: 4px;
        background: #FF0076;
        margin: 10px auto;
        border-radius: 2px;
    }

    .course-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2rem;
        padding: 2rem 0;
    }

    .course-card {
        background: rgba(255,255,255,0.95);
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        animation: fadeIn 0.6s ease forwards;
        opacity: 0;
        display: flex;
        flex-direction: column;
        backdrop-filter: blur(5px);
    }

    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.12);
    }

    .course-card img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-bottom: 3px solid #009FFD;
        transition: transform 0.3s ease;
        flex-shrink: 0;
    }

    .price-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(45deg, #009FFD, #2A2A72);
        color: white;
        padding: 6px 15px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        z-index: 1;
    }

    .price-badge.free {
        background: linear-gradient(45deg, #00b894, #55efc4);
    }

    .course-card-content {
        padding: 1.5rem;
        position: relative;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .course-card h3 {
        font-size: 1.4rem;
        margin: 0 0 1rem 0;
        color: #2A2A72;
        line-height: 1.3;
        min-height: 3.5em;
    }

    .course-card p {
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1rem;
        color: #555;
    }

    .course-card p:first-of-type {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex-grow: 1;
    }

    .instructor {
        font-style: italic;
        color: #777 !important;
        border-left: 3px solid #009FFD;
        padding-left: 10px;
        margin: 1rem 0 0;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.8rem 1.8rem;
        background: linear-gradient(45deg, #2A2A72, #009FFD);
        color: white !important;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        width: calc(100% - 1rem);
        margin: 15px auto 0;
        margin-top: auto;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(42, 42, 114, 0.3);
        background: linear-gradient(45deg, #009FFD, #2A2A72);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
        h2 { font-size: 2.2rem; }
        .course-container { padding: 1rem; gap: 1.5rem; }
        .course-card { margin-bottom: 0; }
        .btn { padding: 0.7rem 1.5rem; }
        .language-switcher {
            top: 15px;
            right: 15px;
        }
    }

    .error {
        background: #ffebee;
        color: #b71c1c;
        padding: 20px;
        border-radius: 8px;
        margin: 20px;
        text-align: center;
    }
    </style>
</head>
<body>
    <div id="particles-js"></div>
    
    <div class="container">
        <!-- 语言切换器 -->
        <div class="language-switcher">
            <select class="lang-select" onchange="window.location.href='?lang='+this.value">
                <option value="en" <?= $current_lang === 'en' ? 'selected' : '' ?>>English</option>
                <option value="zh" <?= $current_lang === 'zh' ? 'selected' : '' ?>>中文</option>
                <option value="ms" <?= $current_lang === 'ms' ? 'selected' : '' ?>>Bahasa Melayu</option>
            </select>
        </div>

        <h2><?= $lang[$current_lang]['title'] ?></h2>
        <div class="course-container">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="course-card">
                    <div class="price-badge <?= $row['price'] == 0 ? 'free' : '' ?>">
                        <?= $row['price'] == 0 
                            ? $lang[$current_lang]['free'] 
                            : $lang[$current_lang]['currency'] . number_format($row['price'], 2) ?>
                    </div>
                    <div class="course-card-content">
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <p><?= htmlspecialchars($row['description']) ?></p>
                        <p class="instructor"><?= $lang[$current_lang]['instructor'] ?><?= htmlspecialchars($row['instructor']) ?></p>
                        <p><strong><?= $lang[$current_lang]['duration'] ?></strong><?= htmlspecialchars($row['duration']) ?></p>
                        <a href="course_details.php?id=<?= $row['id'] ?>" class="btn"><?= $lang[$current_lang]['view_details'] ?></a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
    // 粒子系统初始化
    particlesJS('particles-js', {
        particles: {
            number: { value: 80, density: { enable: true, value_area: 800 } },
            color: { value: ['#2A2A72', '#009FFD', '#FF0076'] },
            shape: { type: "circle" },
            opacity: { value: 0.5 },
            size: { value: 3, random: true },
            line_linked: {
                enable: true,
                distance: 150,
                color: "#2A2A72",
                opacity: 0.4,
                width: 1
            },
            move: {
                enable: true,
                speed: 4,
                direction: "none",
                out_mode: "out"
            }
        },
        interactivity: {
            detect_on: "canvas",
            events: {
                onhover: { enable: true, mode: "repulse" },
                onclick: { enable: true, mode: "push" },
                resize: true
            },
            modes: {
                repulse: { distance: 100 },
                push: { particles_nb: 4 }
            }
        },
        retina_detect: true
    });
    </script>
</body>
</html>
<?php
$conn->close();
?>