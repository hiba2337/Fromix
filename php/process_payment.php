<?php
/**
 * معالجة عملية الدفع
 * process_payment.php
 */

require_once 'config.php';

// التحقق من طريقة الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo error_response('طريقة الطلب غير مسموحة');
    exit;
}

try {
    // التحقق من تسجيل الدخول
    $user_id = get_user_id();
    if (!$user_id) {
        throw new Exception('يجب تسجيل الدخول أولاً');
    }
    
    // جلب بيانات الدفع
    $payment_method = isset($_POST['payment_method']) ? clean_input($_POST['payment_method']) : '';
    $total_amount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;
    
    if (empty($payment_method)) {
        throw new Exception('يرجى اختيار طريقة الدفع');
    }
    
    if ($total_amount <= 0) {
        throw new Exception('المبلغ غير صحيح');
    }
    
    // جلب محتوى السلة
    $cart_sql = "SELECT course_id, 
                        (SELECT price FROM courses WHERE id = panier.course_id) as price
                 FROM panier 
                 WHERE user_id = $user_id";
    
    $cart_result = mysqli_query($conn, $cart_sql);
    
    if (mysqli_num_rows($cart_result) == 0) {
        throw new Exception('السلة فارغة');
    }
    
    // بدء معاملة (Transaction) لضمان سلامة البيانات
    mysqli_begin_transaction($conn);
    
    $enrolled_courses = [];
    
    // معالجة كل تكوين
    while ($item = mysqli_fetch_assoc($cart_result)) {
        $course_id = $item['course_id'];
        $price = $item['price'];
        
        // التحقق من عدم التسجيل المسبق
        $check_enrollment = "SELECT * FROM enrollments 
                            WHERE user_id = $user_id AND course_id = $course_id";
        $check_result = mysqli_query($conn, $check_enrollment);
        
        if (mysqli_num_rows($check_result) > 0) {
            // تخطي التكوينات المسجلة مسبقاً
            continue;
        }
        
        // إنشاء سجل التسجيل
        $enroll_sql = "INSERT INTO enrollments 
                      (user_id, course_id, enrollment_date, payment_status, completion_status)
                      VALUES 
                      ($user_id, $course_id, NOW(), 'paid', 'in_progress')";
        
        if (!mysqli_query($conn, $enroll_sql)) {
            throw new Exception('فشل التسجيل في التكوين');
        }
        
        $enrollment_id = mysqli_insert_id($conn);
        
        // إنشاء سجل الدفع
        $payment_sql = "INSERT INTO payments 
                       (user_id, enrollment_id, amount, payment_method, payment_date, status)
                       VALUES 
                       ($user_id, $enrollment_id, $price, '$payment_method', NOW(), 'completed')";
        
        if (!mysqli_query($conn, $payment_sql)) {
            throw new Exception('فشل حفظ معلومات الدفع');
        }
        
        $enrolled_courses[] = $course_id;
    }
    
    // حذف السلة بعد إتمام الدفع
    $delete_cart = "DELETE FROM panier WHERE user_id = $user_id";
    if (!mysqli_query($conn, $delete_cart)) {
        throw new Exception('فشل تفريغ السلة');
    }
    
    // تأكيد المعاملة
    mysqli_commit($conn);
    
    echo success_response('تم الدفع بنجاح! تم تسجيلك في التكوينات', [
        'enrolled_count' => count($enrolled_courses),
        'payment_method' => $payment_method,
        'total_paid' => $total_amount
    ]);
    
} catch (Exception $e) {
    // إلغاء المعاملة في حالة الخطأ
    if (isset($conn)) {
        mysqli_rollback($conn);
    }
    echo error_response($e->getMessage());
}

if (isset($conn)) {
    mysqli_close($conn);
}
?>