<?php
$conn = new mysqli("localhost", "root", "", "school");
if ($conn->connect_error) die("خطا در اتصال");

// نمایش پیام موفقیت (اگر از grades.php اومده باشه)
$save_message = '';
if (isset($_GET['saved'])) {
    $save_message = '<div id="alert" style="position:fixed;top:20px;left:50%;transform:translateX(-50%);background:#10b981;color:white;padding:18px 40px;border-radius:15px;z-index:9999;font-size:1.4rem;box-shadow:0 10px 30px rgba(0,0,0,0.3);font-family:Vazirmatn,sans-serif;">نمرات با موفقیت ذخیره شد!</div>';
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>سیستم مدیریت نمرات</title>
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    :root{--p:#6366f1;--s:#10b981;--d:#1e293b;}
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Vazirmatn',sans-serif;}
    body{background:linear-gradient(135deg,#667eea,#764ba2);min-height:100vh;padding:40px 20px;color:white;}
    .c{max-width:1100px;margin:0 auto;}
    h1{text-align:center;font-size:3rem;margin-bottom:40px;text-shadow:0 4px 20px rgba(0,0,0,0.3);}
    .card{background:white;color:#333;border-radius:20px;overflow:hidden;box-shadow:0 20px 50px rgba(0,0,0,0.3);}
    .h{background:var(--p);color:white;padding:25px;text-align:center;font-size:1.6rem;}
    table{width:100%;border-collapse:collapse;}
    th{background:var(--d);color:white;padding:18px;}
    td{padding:18px;text-align:center;background:#f8fafc;}
    tr:hover td{background:#e0e7ff;}
    .btn{padding:12px 28px;background:var(--p);color:white;border:none;border-radius:50px;text-decoration:none;display:inline-block;}
    .btn:hover{background:#4f46e5;transform:translateY(-3px);box-shadow:0 10px 20px rgba(99,102,241,0.4);}
    .add{text-align:center;margin-top:50px;}
    .btn-add{background:var(--s);font-size:1.4rem;padding:18px 40px;}
    .btn-add:hover{background:#059669;}
</style>
</head>
<body>
<?= $save_message ?>

<div class="c">
    <h1>سیستم مدیریت نمرات</h1>
    <div class="card">
        <div class="h">لیست دانش‌آموزان</div>
        <table>
            <tr><th>شناسه</th><th>نام</th><th>نام خانوادگی</th><th>عملیات</th></tr>
            <?php
            $result = $conn->query("SELECT id, first_name, last_name FROM students ORDER BY id");
            while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['first_name']) ?></td>
                <td><?= htmlspecialchars($row['last_name']) ?></td>
                <td><a href="grades.php?student_id=<?= $row['id'] ?>" class="btn">نمرات دروس</a></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <div class="add">
        <a href="add_student.php" class="btn btn-add">+ ثبت دانش‌آموز جدید</a>
    </div>
</div>

<?php if (isset($_GET['saved'])): ?>
<script>
    setTimeout(() => {
        document.getElementById('alert').style.opacity = '0';
        setTimeout(() => document.getElementById('alert').remove(), 600);
    }, 4000);
</script>
<?php endif; ?>
</body>
</html>
