<?php
header("Content-Type: application/json");
include "../dbconnect.php";

/* ---------------- CHECK REQUEST ---------------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
    exit();
}

/* ---------------- GET INPUT ---------------- */
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    echo json_encode([
        "status" => "error",
        "message" => "Username and password required"
    ]);
    exit();
}

/* ---------------- ESCAPE INPUT ---------------- */
$username_safe = mysqli_real_escape_string($conn, $username);

/* ---------------- IDENTIFY EMAIL OR PHONE ---------------- */
if (ctype_digit($username)) {
    $where = "u.phn_no = '$username_safe'";
} else {
    $where = "LOWER(TRIM(u.email_id)) = LOWER(TRIM('$username_safe'))";
}

/* ---------------- FETCH USER ---------------- */
$query = "
SELECT 
    u.user_id,
    u.firm_name,
    u.user_pass,
    v.isApproved
FROM $user_table u
JOIN $verification_table v ON u.user_id = v.user_id
WHERE $where
LIMIT 1
";

$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid credentials"
    ]);
    exit();
}

$user = mysqli_fetch_assoc($result);

/* ---------------- PASSWORD CHECK (Supports Hash & Plain Text) ---------------- */
$password_matches = ($password === $user['user_pass']) || password_verify($password, $user['user_pass']);
if (!$password_matches) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid credentials"
    ]);
    exit();
}

/* ---------------- APPROVAL CHECK ---------------- */
if ($user['isApproved'] != 1) {
    echo json_encode([
        "status" => "error",
        "message" => "Account not approved by admin"
    ]);
    exit();
}

/* ---------------- SUCCESS ---------------- */
echo json_encode([
    "status" => "success",
    "user_id" => $user['user_id'],
    "firm_name" => $user['firm_name']
]);
exit();
?>
