<?php
$hash = "";
$verify = "";

if (isset($_POST['make_hash'])) {

    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
}

if (isset($_POST['check_hash'])) {

    $password = $_POST['password'];
    $saved_hash = $_POST['saved_hash']; 

    if (password_verify($password, $saved_hash)) {
        $verify = "<span class='text-success fw-bold'>صحيح</span>";
    } else {
        $verify = "<span class='text-danger fw-bold'>تحقق صحيح</span>";
    }
    $hash = $saved_hash;
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تكليف تاني</title>
                            <!-- ملاحظة ي هندسة انا استخدمت بوت سراب -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow p-4">
        <h3 class="text-center mb-4">Password Hashing</h3>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">أدخل كلمة المرور:</label>
                <input type="text" name="password" class="form-control" required>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary w-50" name="make_hash">Hash</button>
                <button class="btn btn-success w-50" name="check_hash">Verify</button>
            </div>
            <input type="hidden" name="saved_hash" value="<?php echo $hash; ?>">
        </form>
        <hr>
        <h5>الهاش:</h5>
        <div class="p-3 bg-white border rounded">
            <?php echo $hash; ?>
        </div>
        <h5 class="mt-3">نتيجة التحقق:</h5>
        <div class="p-3 bg-white border rounded">
            <?php echo $verify; ?>
        </div>
    </div>
</div>
</body>
</html>
