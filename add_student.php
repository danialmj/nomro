<?php
$conn = new mysqli("localhost", "root", "", "school");
if ($conn->connect_error) die("خطا در اتصال");

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $national_code = trim($_POST['national_code'] ?? '');
    $father_name = trim($_POST['father_name'] ?? '');

    if ($first_name && $last_name && $national_code && $father_name) {
        // چک کردن تکراری نبودن کد ملی
        $check = $conn->prepare("SELECT id FROM students WHERE national_code = ?");
        $check->bind_param("s", $national_code);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $message = "این کد ملی قبلاً ثبت شده!";
        } else {
            $stmt = $conn->prepare("INSERT INTO students (first_name, last_name, national_code, father_name) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $first_name, $last_name, $national_code, $father_name);
            $stmt->execute();
            $message = "دانش‌آموز با موفقیت ثبت شد!";
        }
    } else {
        $message = "همه فیلدها الزامی هستند!";
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>ثبت دانش‌آموز جدید</title>
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    body{background:linear-gradient(135deg,#667eea,#764ba2);min-height:100vh;display:flex;align-items:center;justify-content:center;font-family:'Vazirmatn',sans-serif;}
    .card{background:white;padding:50px;border-radius:25px;box-shadow:0 20px 50px rgba(0,0,0,0.4);max-width:550px;width:90%;}
    h1{text-align:center;color:#6366f1;margin-bottom:30px;font-size:2.4rem;}
    input{width:100%;padding:18px;margin:12px 0;border:2px solid #ddd;border-radius:15px;font-size:1.2rem;}
    input:focus{border-color:#6366f1;outline:none;}
    button{width:100%;padding:18px;background:#10b981;color:white;border:none;border-radius:15px;font-size:1.4rem;cursor:pointer;margin-top:10px;}
    button:hover{background:#059669;}
    .msg{padding:15px;background:#10b981;color:white;border-radius:12px;text-align:center;margin:20px 0;font-size:1.1rem;}
    .error{background:#ef4444 !important;}
    .back{display:block;text-align:center;margin-top:25px;color:#6366f1;font-size:1.2rem;text-decoration:none;}
</style>
</head>
<body>
<div class="card">
    <h1>ثبت دانش‌آموز جدید</h1>
    <?php if ($message): ?>
        <div class="msg <?= strpos($message, 'موفقیت') !== false ? '' : 'error' ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="first_name" placeholder="نام" required>
        <input type="text" name="last_name" placeholder="نام خانوادگی" required>
        <input type="text" name="national_code" placeholder="کد ملی (۱۰ رقمی)" maxlength="10" pattern="[0-9]{10}" required>
        <input type="text" name="father_name" placeholder="نام پدر" required>
        <button type="submit">ثبت دانش‌آموز</button>
    </form>
    <a href="students_list.php" class="back">بازگشت به لیست دانش‌آموزان</a>
</div>
</body>
</html>