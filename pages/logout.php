<?php
session_start();
require_once('../php/config.php');

// تسجيل النشاط قبل الخروج
if (isset($_SESSION['user_id'])) {
    logActivity('LOGOUT', 'users', $_SESSION['user_id'], 'تسجيل خروج');
}

// تدمير الجلسة
session_destroy();

// إعادة التوجيه لصفحة تسجيل الدخول
header("Location: login.php");
exit();
?>