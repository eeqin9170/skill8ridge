<?php
session_start();
require 'db.php';
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT e.course_id, c.title, c.description, e.progress, e.status 
        FROM enrollments e 
        JOIN courses c ON e.course_id = c.id 
        WHERE e.user_id = $user_id";
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduVision | 智能学习平台</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3A3A3A;
            --accent: #FF758F;
            --gradient: linear-gradient(135deg, #FF758F 0%, #FF7EB3 100%);
            --shadow: 0 8px 24px rgba(0,0,0,0.06);
            --radius: 24px;
            --beige: #F7F3F0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        body {
            background: var(--beige);
            color: var(--primary);
            overflow-x: hidden;
        }

        /* 动态粒子背景 */
        .particles-canvas {
            position: fixed;
            top: 0;
            left: 0;
            z-index: -1;
            pointer-events: none;
        }

        /* 导航栏 */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            padding: 1.5rem 5%;
            background: rgba(247,243,240,0.85);
            backdrop-filter: blur(12px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            z-index: 1000;
        }

        .logo {
            font-family: 'Pacifico', cursive; /* 使用手写字体 */
            font-size: 1.8rem;
            color: var(--accent);
            position: relative;
            padding: 0.5rem 1.2rem;
            background: 
                linear-gradient(145deg, 
                    rgba(255,255,255,0.9) 0%, 
                    rgba(255,255,255,0.6) 100%);
            border-radius: 15px;
            backdrop-filter: blur(5px);
            box-shadow: 
                0 4px 6px rgba(0,0,0,0.05),
                inset 0 0 0 1px rgba(255,255,255,0.8);
            transform-style: preserve-3d;
            transition: all 0.3s ease;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .logo::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: var(--gradient);
            border-radius: 16px;
            z-index: -1;
            opacity: 0.3;
            transition: opacity 0.3s ease;
        }

        .logo:hover {
            transform: 
                rotate(-2deg) 
                scale(1.05) 
                translateY(-2px);
            box-shadow: 
                0 8px 15px rgba(0,0,0,0.1),
                inset 0 0 0 1px rgba(255,255,255,0.9);
        }

        .logo:hover::before {
            opacity: 0.5;
        }


        .nav-links {
            display: flex;
            gap: 2.5rem;
            align-items: center;
        }

        .nav-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem;
            position: relative;
            transition: color 0.3s ease;
            font-size: 0.95rem;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* 主内容区 */
        .hero-section {
            min-height: 100vh;
            padding: 120px 5% 60px;
            display: flex;
            align-items: center;
        }

        .hero-container {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-content {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.8s 0.2s forwards;
        }

        .hero-title {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
            font-weight: 700;
            letter-spacing: -0.03em;
        }

        .hero-description {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 2.5rem;
            line-height: 1.7;
            max-width: 480px;
        }

        /* 课程卡片 */
        .course-card {
            background: #fff;
            border-radius: var(--radius);
            padding: 2.5rem;
            box-shadow: var(--shadow);
            transform: translateY(50px);
            opacity: 0;
            animation: cardFloat 4s infinite alternate, fadeInUp 0.8s 0.4s forwards;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .progress-container {
            margin: 2rem 0;
        }

        .progress-bar {
            height: 12px;
            background: #e9ecef;
            border-radius: 6px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            width: 65%;
            background: var(--gradient);
            border-radius: 6px;
            position: relative;
            transition: width 1s ease;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4));
        }

        /* 交互元素 */
        .cta-button {
            background: var(--gradient);
            color: white;
            border: none;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: transform 0.3s ease;
            position: relative;
            overflow: hidden;
            display: inline-block;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(255,117,143,0.3);
        }

        .cta-button::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .cta-button:hover {
            transform: translateY(-3px);
        }

        .cta-button:hover::after {
            opacity: 1;
        }

        /* 语言选择器 */
        .language-selector {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: 2px solid var(--accent);
            background: transparent;
            color: var(--accent);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .language-selector:hover {
            background: var(--accent);
            color: white;
        }

        /* 动画 */
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes cardFloat {
            0% { transform: translateY(0); }
            100% { transform: translateY(-20px); }
        }

        /* 响应式设计 */
        @media (max-width: 768px) {
            .hero-container {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .nav-links {
                display: none;
            }

            .course-card {
                margin-top: 2rem;
            }
        }

        /* 新增Ins风格元素 */
        .course-stats {
            display: flex;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            color: #666;
        }

        .stat-item i {
            color: var(--accent);
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <canvas class="particles-canvas"></canvas>

    <nav class="navbar">
    <div class="logo">SkillBridge</div>
        <div class="nav-links">
            <a href="homepage.php" class="nav-link translatable" data-key="home">首页</a>
            <a href="course_details.php" class="nav-link translatable" data-key="courses">课程</a>
            <a href="progress.php" class="nav-link translatable" data-key="progress">进度</a>
            <a href="contact.html" class="nav-link translatable" data-key="contact">联系</a>
            <select class="language-selector" id="languageSelector">
                <option value="zh">中文</option>
                <option value="en">English</option>
                <option value="ms">Bahasa Melayu</option>
            </select>
        </div>
    </nav>

    <main>
        <section class="hero-section">
            <div class="hero-container">
                <div class="hero-content">
                    <h1 class="hero-title translatable" data-key="title">开启您的智能学习之旅</h1>
                    <p class="hero-description translatable" data-key="description">通过个性化学习路径和实时进度跟踪，掌握最新技术趋势</p>
                    <a href="course.php" class="cta-button translatable" data-key="cta">立即体验</a>
                </div>

                <div class="course-card">
                    <h3 class="translatable" data-key="courseProgress">全栈开发课程进度</h3>
                    <div class="progress-container">
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                        <p class="translatable" data-key="progressDetails">当前进度：65% • 预计完成时间：2024年3月</p>
                    </div>
                    <div class="course-stats">
                        <div class="stat-item">
                            <i class="fas fa-book-open"></i>
                            <span class="translatable" data-key="hours">已学习 28 小时</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-tasks"></i>
                            <span class="translatable" data-key="projects">完成 15 个项目</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        // 多语言支持
        const translations = {
            zh: {
                home: "首页", courses: "课程", progress: "进度", contact: "联系",
                title: "开启您的智能学习之旅", 
                description: "通过个性化学习路径和实时进度跟踪，掌握最新技术趋势",
                cta: "立即体验", courseProgress: "全栈开发课程进度",
                progressDetails: "当前进度：65% • 预计完成时间：2024年3月",
                hours: "已学习 28 小时", projects: "完成 15 个项目"
            },
            en: {
                home: "Home", courses: "Courses", progress: "Progress", contact: "Contact",
                title: "Start Your Smart Learning Journey",
                description: "Master the latest technology trends with personalized learning paths and real-time tracking",
                cta: "Get Started", courseProgress: "Full Stack Development Progress",
                progressDetails: "Current Progress: 65% • ETA: March 2024",
                hours: "28 hours learned", projects: "15 projects completed"
            },
            ms: {
                home: "Utama", courses: "Kursus", progress: "Kemajuan", contact: "Hubungi",
                title: "Mulakan Perjalanan Pembelajaran Pintar Anda",
                description: "Kuasi trend teknologi terkini dengan laluan pembelajaran diperibadikan",
                cta: "Mulakan Sekarang", courseProgress: "Kemajuan Pembangunan Full Stack",
                progressDetails: "Kemajuan Semasa: 65% • Anggaran Selesai: Mac 2024",
                hours: "28 jam dipelajari", projects: "15 projek diselesaikan"
            }
        };

        function updateLanguage(lang) {
            document.querySelectorAll('.translatable').forEach(el => {
                const key = el.dataset.key;
                if (translations[lang][key]) {
                    el.textContent = translations[lang][key];
                }
            });
        }

        // 粒子动画（颜色调整为Ins风格）
        function initParticles() {
            const canvas = document.querySelector('.particles-canvas');
            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            class Particle {
                constructor() {
                    this.reset();
                    this.size = Math.random() * 2 + 1;
                }

                reset() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.vx = (Math.random() - 0.5) * 2;
                    this.vy = (Math.random() - 0.5) * 2;
                }

                update() {
                    this.x += this.vx;
                    this.y += this.vy;

                    if (this.x < 0 || this.x > canvas.width || 
                        this.y < 0 || this.y > canvas.height) {
                        this.reset();
                    }
                }

                draw() {
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    ctx.fillStyle = `rgba(255,117,143,${this.size/3})`;
                    ctx.fill();
                }
            }

            const particles = Array(100).fill().map(() => new Particle());

            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                particles.forEach(p => {
                    p.update();
                    p.draw();

                    particles.forEach(p2 => {
                        const dx = p.x - p2.x;
                        const dy = p.y - p2.y;
                        const distance = Math.sqrt(dx*dx + dy*dy);

                        if (distance < 100) {
                            ctx.beginPath();
                            ctx.moveTo(p.x, p.y);
                            ctx.lineTo(p2.x, p2.y);
                            ctx.strokeStyle = `rgba(255,117,143,${0.2 - distance/500})`;
                            ctx.lineWidth = 0.5;
                            ctx.stroke();
                        }
                    });
                });

                requestAnimationFrame(animate);
            }

            animate();
            window.addEventListener('resize', () => {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            });
        }

        // 初始化
        document.addEventListener('DOMContentLoaded', () => {
            initParticles();
            document.getElementById('languageSelector').addEventListener('change', function() {
                updateLanguage(this.value);
            });
            updateLanguage('en');
        });
    </script>
</body>
</html>