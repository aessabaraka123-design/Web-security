<?php
// index.php
// صفحة بسيطة لتشفير وفك تشفير باستخدام AES-256-CBC
// حفظ الملف وتشغيله على سيرفر يدعم PHP

// --- إعداد المفتاح (استخدم عبارة سرية خاصة بك هنا) ---
$password_for_key = 'my_strong_password_here_ChangeThis!'; // غيّرها قبل التسليم
$key = hash('sha256', $password_for_key, true); // مفتاح 32 بايت (binary)

// دالة مساعدة للتعقيم عند الإخراج للعرض بالمتصفح
function h($s) {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// متغيرات النتائج لعرضها في النموذج
$plaintext_input = '';
$encrypted_output = '';
$decrypted_output = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // إذا نقر المستخدم زر التشفير
    if (isset($_POST['action']) && $_POST['action'] === 'encrypt') {
        $plaintext_input = isset($_POST['plaintext']) ? $_POST['plaintext'] : '';
        if ($plaintext_input === '') {
            $error = 'المرجو إدخال نص للتشفير.';
        } else {
            $method = 'aes-256-cbc';
            $ivlen = openssl_cipher_iv_length($method);
            $iv = openssl_random_pseudo_bytes($ivlen);
            // نستخدم OPENSSL_RAW_DATA لنتعامل بالبايتات ثم نعمل base64 لاحقًا
            $cipher_raw = openssl_encrypt($plaintext_input, $method, $key, OPENSSL_RAW_DATA, $iv);
            if ($cipher_raw === false) {
                $error = 'فشل التشفير — تحقق من إعدادات السيرفر/OpenSSL.';
            } else {
                // نضع IV في بداية البايت ستريم ثم نعمل base64
                $combined = $iv . $cipher_raw;
                $encrypted_output = base64_encode($combined);
            }
        }
    }

    // إذا نقر المستخدم زر فك التشفير
    if (isset($_POST['action']) && $_POST['action'] === 'decrypt') {
        $encrypted_input = isset($_POST['encrypted']) ? $_POST['encrypted'] : '';
        if ($encrypted_input === '') {
            $error = 'المرجو إدخال النص المُشفر (Base64) لفك التشفير.';
        } else {
            $method = 'aes-256-cbc';
            $ivlen = openssl_cipher_iv_length($method);
            $decoded = base64_decode($encrypted_input, true);
            if ($decoded === false) {
                $error = 'سلسلة Base64 غير صحيحة.';
            } elseif (strlen($decoded) <= $ivlen) {
                $error = 'البيانات المُدخلة قصيرة جداً (لا تحتوي IV + بيانات).';
            } else {
                $iv = substr($decoded, 0, $ivlen);
                $cipher_raw = substr($decoded, $ivlen);
                $decrypted = openssl_decrypt($cipher_raw, $method, $key, OPENSSL_RAW_DATA, $iv);
                if ($decrypted === false) {
                    $error = 'فشل فك التشفير — قد يكون المفتاح/البيانات غير صحيحة.';
                } else {
                    $decrypted_output = $decrypted;
                }
            }
        }
    }
}
?>
<!doctype html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <title>تكليف التشفير</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body { font-family: Tahoma, Arial, sans-serif; direction: rtl; padding: 20px; background:#f7f7f9; }
        .container { max-width:800px; margin:0 auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
        textarea { width:100%; min-height:120px; padding:10px; font-size:14px; }
        input[type="submit"], button { padding:10px 16px; font-size:14px; margin-top:10px; }
        .row { margin-bottom:16px; }
        .label { font-weight:700; margin-bottom:6px; display:block; }
        .output { background:#f5f5f7; padding:10px; border-radius:4px; word-break:break-all; }
        .error { color:#b00020; margin-bottom:10px; }
        .small { font-size:13px; color:#666; }
    </style>
</head>
<body>
<div class="container">
    <h2>تشفير وفك تشفير — AES-256-CBC (PHP)</h2>

    <?php if ($error): ?>
        <div class="error"><?= h($error) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
        <div class="row">
            <label class="label">نص لتشفيره :</label>
            <textarea name="plaintext" placeholder="اكتب النص هنا..."><?= h($plaintext_input) ?></textarea>
            <div class="small">اضغط زر "تشفير" لعرض النص المشفّر (Base64).</div>
        </div>

        <div class="row">
            <input type="hidden" name="action" value="encrypt">
            <input type="submit" value="تشفير">
        </div>
    </form>

    <hr>

    <form method="post" novalidate>
        <div class="row">
            <label class="label">النص المشفر (Base64) لفك التشفير:</label>
            <textarea name="encrypted" placeholder="أكتب النص المشفّر هنا..."><?= h($encrypted_output) ?></textarea>
            <div class="small">إذا كان النص ناتجاً من نفس النموذج فهو يحتوي على IV في بدايته بعد فك الـBase64.</div>
        </div>

        <div class="row">
            <input type="hidden" name="action" value="decrypt">
            <input type="submit" value="فك تشفير">
        </div>
    </form>

    <hr>

    <div class="row">
        <label class="label">النص المشفّر :</label>
        <div class="output"><?= $encrypted_output ? h($encrypted_output) : 'لا يوجد نص مشفّر بعد' ?></div>
    </div>

    <div class="row">
        <label class="label">النص بعد فك التشفير :</label>
        <div class="output"><?= $decrypted_output ? h($decrypted_output) : 'لا توجد نتيجة لفك التشفير بعد' ?></div>
    </div>

</div>
</body>
</html>
