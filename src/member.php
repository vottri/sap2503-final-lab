<?php
session_start();
require_once 'db.php';

if (!empty($_POST['username']) && !empty($_POST['password'])) {

        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("CALL CheckLogin(:username, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $login_result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if ($login_result && $login_result['result'] === 'SUCCESS') {

                $_SESSION['username'] = $login_result['user_username'];
                $_SESSION['type']     = $login_result['user_type'];

                if ($login_result['user_type'] === 'STUDENT') {
                        header("Location: student.php");
                        exit;
                } elseif ($login_result['user_type'] === 'EMPLOYEE') {
                        header("Location: employee.php");
                        exit;
                }
        }

        // Login failed → fall through and show HTML message
}
?>

<!DOCTYPE html>
<html>
<head>
        <title>member</title>
        <meta charset="utf-8">
</head>
<body>

<?php
if (isset($login_result) && $login_result['result'] !== 'SUCCESS') {
        echo "Đăng nhập thất bại<br>";
        echo "Sai username hoặc password";
} elseif (empty($_POST)) {
        echo "Where are you from?";
}
?>

</body>
</html>
