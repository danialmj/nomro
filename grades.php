<?php
$conn = new mysqli("localhost", "root", "", "school");
if ($conn->connect_error) die("خطا در اتصال");

$student_id = intval($_GET['student_id'] ?? 0);
if ($student_id <= 0) die("شناسه نامعتبر!");

// اطلاعات دانش‌آموز
$stmt = $conn->prepare("SELECT first_name, last_name FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$student) die("دانش‌آموز پیدا نشد!");

// اضافه کردن درس جدید
if (isset($_POST['add_subject'])) {
    $new_subject = trim($_POST['new_subject_name']);
    if ($new_subject && strlen($new_subject) >= 2) {
        $chk = $conn->prepare("SELECT id FROM subjects WHERE subject_name = ?");
        $chk->bind_param("s", $new_subject);
        $chk->execute();
        if ($chk->get_result()->num_rows == 0) {
            $ins = $conn->prepare("INSERT INTO subjects (subject_name) VALUES (?)");
            $ins->bind_param("s", $new_subject);
            $ins->execute();
        }
    }
}

// لیست دروس (بعد از اضافه کردن، دوباره خونده بشه)
$subjects = $conn->query("SELECT id, subject_name FROM subjects ORDER BY id")->fetch_all(MYSQLI_ASSOC);

// نمرات فعلی
$grades = [];
$res = $conn->query("SELECT subject_id, grade_student FROM grades WHERE student_id = $student_id");
while ($row = $res->fetch_assoc()) {
    $grades[$row['subject_id']] = $row['grade_student'];
}

// ذخیره نمرات
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['add_subject'])) {
    $upd = $conn->prepare("UPDATE grades SET grade_student = ? WHERE student_id = ? AND subject_id = ?");
    $ins = $conn->prepare("INSERT INTO grades (student_id, subject_id, grade_student) VALUES (?, ?, ?)");

    foreach ($subjects as $sub) {
        $g = round(max(0, min(20, floatval($_POST['grade_'.$sub['id']] ?? 0))), 2);
        if (isset($grades[$sub['id']])) {
            $upd->bind_param("dii", $g, $student_id, $sub['id']);
            $upd->execute();
        } elseif ($g > 0) {
            $ins->bind_param("iid", $student_id, $sub['id'], $g);
            $ins->execute();
        }
    }
    header("Location: students_list.php?saved=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>نمرات <?= htmlspecialchars($student['first_name'].' '.$student['last_name']) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    :root{--p:#6366f1;--s:#10b981;}
    body{background:linear-gradient(135deg,#1e3a8a,#1e40af);min-height:100vh;padding:30px;font-family:'Vazirmatn',sans-serif;}
    .card{max-width:950px;margin:30px auto;background:white;border-radius:25px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,0.4);}
    .header{background:linear-gradient(135deg,var(--p),#4338ca);color:white;padding:40px;text-align:center;}
    .header h1{font-size:2.6rem;margin-bottom:8px;}
    .grades-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:25px;padding:40px;}
    .sub{background:linear-gradient(135deg,#f8fafc,#e0e7ff);border-radius:20px;padding:30px;text-align:center;box-shadow:0 10px 30px rgba(0,0,0,0.1);transition:0.4s;border:3px solid transparent;}
    .sub:hover{transform:translateY(-12px);box-shadow:0 25px 50px rgba(99,102,241,0.3);border-color:var(--p);}
    .sub-name{font-size:1.7rem;font-weight:bold;margin-bottom:20px;color:#1e293b;}
    input[type=number]{width:100%;padding:18px;font-size:1.8rem;text-align:center;border:3px solid #ddd;border-radius:15px;}
    input:focus{border-color:var(--p);outline:none;box-shadow:0 0 25px rgba(99,102,241,0.4);}
    .save{width:100%;padding:22px;background:var(--s);color:white;border:none;border-radius:20px;font-size:1.7rem;cursor:pointer;margin-top:30px;}
    .save:hover{background:#059669;transform:scale(1.05);}
    .back{display:block;text-align:center;margin:30px;color:white;font-size:1.3rem;text-decoration:none;}
    .add-subject-btn{background:#8b5cf6;color:white;padding:15px 30px;border-radius:50px;cursor:pointer;font-size:1.3rem;margin:20px auto;display:block;width:fit-content;}
    .add-subject-btn:hover{background:#7c3aed;}
    .modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);justify-content:center;align-items:center;z-index:999;}
    .modal-content{background:white;padding:40px;border-radius:25px;width:90%;max-width:500px;text-align:center;}
    .close{float:left;font-size:2rem;cursor:pointer;color:#aaa;}
    .close:hover{color:#000;}
</style>
</head>
<body>
<div class="card">
    <div class="header">
        <h1>نمرات دروس</h1>
        <h3><?= htmlspecialchars($student['first_name'].' '.$student['last_name']) ?></h3>
    </div>

    <form method="POST">
        <div class="grades-grid">
            <?php foreach ($subjects as $sub): ?>
                <div class="sub">
                    <div class="sub-name"><?= htmlspecialchars($sub['subject_name']) ?></div>
                    <input type="number" step="0.01" min="0" max="20" name="grade_<?= $sub['id'] ?>"
                           value="<?= $grades[$sub['id']] ?? '' ?>" placeholder="0.00">
                </div>
            <?php endforeach; ?>
        </div>
        <div style="padding:0 40px 20px;">
            <button type="submit" class="save">ذخیره همه نمرات</button>
        </div>
    </form>

    <div onclick="document.getElementById('modal').style.display='flex'" class="add-subject-btn">
        افزودن درس جدید
    </div>

    <a href="students_list.php" class="back">بازگشت به لیست دانش‌آموزان</a>
</div>

<!-- پاپ‌آپ افزودن درس -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('modal').style.display='none'">×</span>
        <h2>افزودن درس جدید</h2>
        <form method="POST" style="margin-top:30px;">
            <input type="text" name="new_subject_name" placeholder="نام درس جدید (مثلاً: فیزیک)" required style="width:100%;padding:18px;font-size:1.3rem;border-radius:15px;border:2px solid #ddd;">
            <input type="hidden" name="add_subject" value="1">
            <button type="submit" style="margin-top:20px;width:100%;padding:18px;background:#8b5cf6;color:white;border:none;border-radius:15px;font-size:1.4rem;cursor:pointer;">افزودن درس</button>
        </form>
    </div>
</div>

<script>
    // بستن پاپ‌آپ با کلیک بیرون
    window.onclick = function(e) {
        if (e.target == document.getElementById('modal')) {
            document.getElementById('modal').style.display = 'none';
        }
    }
</script>
</body>
</html>