<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT e.course_id, c.title, c.description, e.progress, e.status 
                            FROM enrollments e 
                            JOIN courses c ON e.course_id = c.id 
                            WHERE e.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $stmt = $conn->prepare("SELECT ja.job_id, j.title, j.company, j.location, ja.status 
                            FROM job_applications ja
                            JOIN jobs j ON ja.job_id = j.id
                            WHERE ja.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $job_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduVision | 智能学习平台</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2A2E35;
            --accent: #00B4D8;
            --gradient: linear-gradient(135deg, #00B4D8 0%, #006D77 100%);
            --shadow: 0 8px 24px rgba(0,0,0,0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #F8F9FA;
            color: var(--primary);
            overflow-x: hidden;
        }

        .particles {
            position: fixed;
            width: 100vw;
            height: 100vh;
            z-index: -1;
        }

        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            padding: 1.5rem 5%;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            z-index: 1000;
        }

        .logo {
            width: 100px;
            transition: transform 0.3s;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--primary);
            font-weight: 500;
            position: relative;
            padding: 0.5rem 0;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: width 0.3s;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 0 5%;
            margin-top: 80px;
        }

        .hero-content {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-text h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: textReveal 1s ease-out;
        }

        .hero-text p {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            opacity: 0;
            animation: fadeInUp 0.8s 0.3s forwards;
        }

        .course-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow);
            transform: translateY(50px);
            opacity: 0;
            animation: cardFloat 4s infinite alternate, fadeInUp 0.8s 0.6s forwards;
        }

        .course-progress {
            margin: 1.5rem 0;
        }

        .progress-bar {
            height: 12px;
            background: #e9ecef;
            border-radius: 6px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--gradient);
            width: 65%;
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

        .translatable {
            transition: opacity 0.3s ease;
        }

        .language-selector {
            padding: 8px 12px;
            border-radius: 8px;
            border: 2px solid var(--accent);
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .language-selector:hover {
            background: var(--accent);
            color: white;
        }

        @keyframes textReveal {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes cardFloat {
            0% { transform: translateY(0); }
            100% { transform: translateY(-20px); }
        }

        @media (max-width: 768px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-text h1 {
                font-size: 2.5rem;
            }

            .nav-links {
                display: none;
            }
        }
    </style>
</head>
<body>
    <canvas class="particles"></canvas>

    <nav class="navbar">
        <img src="C:\Users\eeqin\Downloads\jpg2png\DALL·E 2025-02-24 14.01.00 - A minimalist logo representing learning, featuring the letters 'RQ'. The design should be clean and simple, using white as the primary color for a mod.png" alt="EduVision" class="logo">
        <div class="nav-links">
            <a href="user_dashboard.php" class="translatable" data-key="home" onclick="loadPage(event, 'user_dashboard.php')">首页</a>
            <a href="course.php" class="translatable" data-key="courses" onclick="loadPage(event, 'course.php')">课程</a>
            <a href="jobs.php" class="translatable" data-key="progress" onclick="loadPage(event, 'jobs.php')">进度</a>
            <a href="contact.html" class="translatable" data-key="contact" onclick="loadPage(event, 'contact.html')">联系</a>
        </div>
        <select class="language-selector" id="languageSelector">
            <option value="zh">中文</option>
            <option value="en">English</option>
            <option value="ms">Bahasa Melayu</option>
        </select>
    </nav>

    <main>
        <section class="hero">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="translatable" data-key="title">开启您的智能学习之旅</h1>
                    <p class="translatable" data-key="description">通过个性化学习路径和实时进度跟踪，掌握最新技术趋势</p>
                    <button class="cta-button translatable" data-key="cta">立即体验</button>
                </div>
                
                <div class="course-card">
                    <h3 class="translatable" data-key="courseProgress">全栈开发课程进度</h3>
                    <div class="course-progress">
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
    <div id="content"></div>
    <h2>My Enrolled Courses</h2>
    <table border="1">
    <tr>
        <th>Course</th>
        <th>Description</th>
        <th>Progress</th>
        <th>Continue</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['title']; ?></td>
        <td><?php echo $row['description']; ?></td>
        <td>
            <div class="progress-bar-container">
            <div class="progress-bar">
                <div class="progress-fill" id="progress-bar-<?php echo $row['course_id']; ?>" style="width: <?php echo $row['progress']; ?>%;"></div>
            </div>
            <span id="progress-text-<?php echo $row['course_id']; ?>"><?php echo $row['progress']; ?>%</span>
            </div>
        </td>
        <td><a href="course_lessons.php?course_id=<?php echo $row['course_id']; ?>">View</a></td>
    </tr>
    <?php endwhile; ?>
</table>
<h2>My Job Applications</h2>
<table border="1">
    <tr>
        <th>Job Title</th>
        <th>Company</th>
        <th>Location</th>
        <th>Status</th>
    </tr>
    <?php while ($job = $job_result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $job['title']; ?></td>
        <td><?php echo $job['company']; ?></td>
        <td><?php echo $job['location']; ?></td>
        <td><?php echo $job['status']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>

    <script>
        function loadPage(event, page) {
        event.preventDefault(); // Prevent default link behavior

         // Clear everything except the navbar
        document.body.innerHTML = document.querySelector('.navbar').outerHTML + `<div id="content">Loading...</div>`;

        fetch(page)
            .then(response => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.text();
            })
            .then(data => {
                document.getElementById("content").innerHTML = data;
            })
            .catch(error => console.error("Error loading the page:", error));
        }



    
        const translations = {
            en: {
                home: "Home",
                courses: "Courses",
                progress: "Progress",
                contact: "Contact",
                title: "Start Your Smart Learning Journey",
                description: "Master the latest technology trends with personalized learning paths and real-time progress tracking.",
                cta: "Try Now",
                courseProgress: "Full Stack Development Progress",
                progressDetails: "Current Progress: 65% • Estimated Completion: March 2024",
                hours: "28 hours learned",
                projects: "15 projects completed"
            },
            ms: {
                home: "Laman Utama",
                courses: "Kursus",
                progress: "Kemajuan",
                contact: "Hubungi",
                title: "Mulakan Perjalanan Pembelajaran Pintar Anda",
                description: "Kuasi trend teknologi terkini dengan laluan pembelajaran diperibadikan dan penjejakan kemajuan masa nyata.",
                cta: "Cuba Sekarang",
                courseProgress: "Kemajuan Pembangunan Full Stack",
                progressDetails: "Kemajuan Semasa: 65% • Anggaran Selesai: Mac 2024",
                hours: "28 jam dipelajari",
                projects: "15 projek diselesaikan"
            },
            zh: {
                home: "首页",
                courses: "课程",
                progress: "进度",
                contact: "联系",
                title: "开启您的智能学习之旅",
                description: "通过个性化学习路径和实时进度跟踪，掌握最新技术趋势",
                cta: "立即体验",
                courseProgress: "全栈开发课程进度",
                progressDetails: "当前进度：65% • 预计完成时间：2024年3月",
                hours: "已学习 28 小时",
                projects: "完成 15 个项目"
            }
        };

        function updateLanguage(lang) {
            document.querySelectorAll(".translatable").forEach(element => {
                const key = element.getAttribute("data-key");
                if (translations[lang][key]) {
                    if(element.tagName === 'BUTTON') {
                        element.textContent = translations[lang][key];
                    } else {
                        element.innerHTML = translations[lang][key];
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const defaultLang = document.getElementById('languageSelector').value;
            updateLanguage(defaultLang);
            
            // 粒子动画
            const canvas = document.querySelector('.particles');
            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            class Particle {
                constructor() {
                    this.reset();
                }

                reset() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.size = Math.random() * 2 + 1;
                    this.speedX = Math.random() * 3 - 1.5;
                    this.speedY = Math.random() * 3 - 1.5;
                }

                update() {
                    this.x += this.speedX;
                    this.y += this.speedY;

                    if (this.x > canvas.width || this.x < 0 || this.y > canvas.height || this.y < 0) {
                        this.reset();
                    }
                }

                draw() {
                    ctx.fillStyle = 'rgba(0,180,216,0.5)';
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    ctx.fill();
                }
            }

            const particles = Array(50).fill().map(() => new Particle());

            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                particles.forEach(particle => {
                    particle.update();
                    particle.draw();
                });
                requestAnimationFrame(animate);
            }
            animate();

            window.addEventListener('resize', () => {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            });
        });

        document.getElementById("languageSelector").addEventListener("change", function() {
            updateLanguage(this.value);
        });
        
        function updateProgressBar(userId, courseId) {
            fetch(`fetch_progress.php?user_id=${userId}&course_id=${courseId}`)
            .then(response => response.json())
            .then(data => {
                let progressBar = document.querySelector(`#progress-bar-${courseId}`);
                let progressText = document.querySelector(`#progress-text-${courseId}`);

                if (progressBar && progressText) {
                    let percentage = data.progress_percentage;
                    progressBar.style.width = percentage + "%"; // Update bar width
                    progressText.innerText = percentage + "%";  // Update text
                }
            })
            .catch(error => console.error("Error fetching progress:", error));
        }


    // Call updateProgressBar after marking lesson as complete
    document.querySelectorAll('.complete-lesson-btn').forEach(button => {
        button.addEventListener('click', function() {
            let lessonId = this.getAttribute('data-lesson-id');
            let userId = this.getAttribute('data-user-id');
            let courseId = this.getAttribute('data-course-id'); // Fix: Get course_id from button attribute

            fetch('update_progress.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `lesson_id=${lessonId}&user_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                updateProgressBar(userId, courseId);
            })
            .catch(error => console.error("Error updating progress:", error));
        });
    });

    </script>
</body>
</html>

<?php
$conn->close();
?>