<?php
$pdo = new PDO("mysql:host=localhost;dbname=skillbridge", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Function to update user progress when a lesson is completed
function updateUserProgress($userId, $courseId) {
    global $pdo;

    // Count total lessons in the course
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM course_lessons WHERE course_id = ?");
    $stmt->execute([$courseId]);
    $totalLessons = $stmt->fetchColumn();

    // Count completed lessons for the user
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM course_progress WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$userId, $courseId]);
    $completedLessons = $stmt->fetchColumn();

    // Calculate progress percentage
    $progressPercentage = ($totalLessons > 0) ? ($completedLessons / $totalLessons) * 100 : 0;

    // Update progress in `course_progress`
    $stmt = $pdo->prepare("INSERT INTO course_progress (user_id, course_id, progress_percentage) 
                           VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE progress_percentage = ?");
    $stmt->execute([$userId, $courseId, $progressPercentage, $progressPercentage]);

    // Update progress in `enrollments`
    $stmt = $pdo->prepare("UPDATE enrollments SET progress = ?, status = CASE WHEN ? = 100 THEN 'Completed' ELSE 'In Progress' END 
                           WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$progressPercentage, $progressPercentage, $userId, $courseId]);

    return $progressPercentage;
}

// Function to mark a lesson as completed
function completeLesson($userId, $courseId, $lessonId) {
    global $pdo;

    // Check if the lesson is already completed
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM course_progress WHERE user_id = ? AND course_id = ? AND id = ?");
    $stmt->execute([$userId, $courseId, $lessonId]);
    $isCompleted = $stmt->fetchColumn();

    if (!$isCompleted) {
        // Insert completion record
        $stmt = $pdo->prepare("INSERT INTO course_progress (user_id, course_id, id, progress_percentage) VALUES (?, ?, ?, 0)");
        $stmt->execute([$userId, $courseId, $lessonId]);
    }

    // Update course progress
    return updateUserProgress($userId, $courseId);
}

// Function to get user's progress percentage for a course
function getUserProgress($userId, $courseId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT progress FROM enrollments WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$userId, $courseId]);
    return $stmt->fetchColumn();
}

// Example: Mark lesson as completed and update progress
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lesson_id'], $_POST['user_id'], $_POST['course_id'])) {
    $lessonId = $_POST['lesson_id'];
    $userId = $_POST['user_id'];
    $courseId = $_POST['course_id'];

    $newProgress = completeLesson($userId, $courseId, $lessonId);
    echo json_encode(["progress" => $newProgress]);
    exit;
}

// Example: Display progress
$userId = 123; // Example user
$courseId = 1; // Example course
$progress = getUserProgress($userId, $courseId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>学习进度 | SkillBridge</title>
    <style>
        :root {
            --primary: #FF6B6B;
            --secondary: #4ECDC4;
            --accent: #8338ec;
            --bg: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f6f7f9 0%, #e9ecef 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .dashboard {
            background: rgba(255,255,255,0.98);
            padding: 2.5rem;
            border-radius: 2rem;
            box-shadow: 0 12px 48px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 600px;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .progress-container {
            margin: 2rem 0;
            position: relative;
        }

        .progress-track {
            height: 16px;
            background: rgba(0,0,0,0.05);
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            width: <?= $progress ?>%;
            transition: width 0.8s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            position: relative;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3));
            animation: shine 2s infinite;
        }

        .progress-percent {
            text-align: center;
            margin-top: 1rem;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--accent);
            position: relative;
        }

        .progress-percent::after {
            content: '%';
            font-size: 1.2rem;
            color: #666;
            margin-left: 4px;
        }

        .form-container {
            margin-top: 2rem;
            padding: 1.5rem;
            background: rgba(79, 192, 254, 0.05);
            border-radius: 1rem;
        }

        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        input[type="number"] {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            appearance: none;
        }

        input:focus {
            border-color: var(--accent);
            outline: none;
            box-shadow: 0 0 0 3px rgba(131, 56, 236, 0.1);
        }

        button {
            background: linear-gradient(135deg, var(--accent), var(--primary));
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
            position: relative;
            overflow: hidden;
        }

        button:hover {
            opacity: 0.95;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        @media (max-width: 640px) {
            body {
                padding: 1rem;
            }
            .dashboard {
                padding: 1.5rem;
                border-radius: 1.5rem;
            }
            .progress-percent {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>📚 课程进度追踪</h1>
        
        <div class="progress-container">
            <div class="progress-track">
                <div class="progress-bar"></div>
            </div>
            <div class="progress-percent"><?= number_format($progress, 1) ?></div>
        </div>

        <div class="form-container">
            <form method="POST" id="progressForm">
                <input type="hidden" name="user_id" value="<?= $userId ?>">
                <input type="hidden" name="course_id" value="<?= $courseId ?>">
                
                <div class="input-group">
                    <input type="number" name="lesson_id" 
                           placeholder="输入完成的课程ID" 
                           min="1" 
                           required
                           oninput="this.value = Math.abs(this.value)">
                </div>

                <button type="submit">
                    <span class="checkmark">✓</span>
                    标记为已完成
                </button>
            </form>
        </div>
    </div>

    <script>
        // AJAX提交表单
        document.getElementById('progressForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const button = this.querySelector('button');
            
            button.disabled = true;
            button.innerHTML = '更新中...';

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.querySelector('.progress-bar').style.width = data.progress + '%';
                document.querySelector('.progress-percent').textContent = data.progress.toFixed(1);
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = '✓ 标记为已完成';
            });
        });
    </script>
</body>
</html>