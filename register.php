<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register Organization | EXPIROCHAIN</title>
    
    <link rel="shortcut icon" href="images/favicon/android-chrome-192x192.png" />
    <link rel="stylesheet" href="css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

</head>

<body>
    <header class="app-header">
        <div class="header-left">
            <img src="images/Logo.png" alt="EXPIROCHAIN Logo">
        </div>
        <div class="header-right">
            <a href="login.php" class="login-btn">Login</a>
        </div>
    </header>
    <div class="page-title">
        <h2>Register Your Organization</h2>
        <p>Join EXPIROCHAIN to manage medicine expiry and prevent waste</p>
    </div>

    <div class="page-wrapper">
        
        <div class="register-card">
            <form action="send_otp.php" method="post" enctype="multipart/form-data">
                <div class="field">
                    <label>Firm Name</label>
                    <input type="text" placeholder="Medical store name" name="firm_name">
                </div>

                <div class="field">
                    <label>Owner Name</label>
                    <input type="text" placeholder="Owner's full name" name="owner_name">
                </div>


                <div class="field">
                    <label>Organization Type</label>
                    <select name="user_type">
                        <option>Select type</option>
                        <option>Medical Store</option>
                        <option>Clinic</option>
                        <option>NGO</option>
                    </select>
                </div>

                <div class="field">
                    <label>Email Address</label>
                    <input type="email" placeholder="organization@example.com" name="email_id">
                </div>

                <div class="field">
                    <label>Mobile Number</label>
                    <input type="text" maxlength="10" inputmode="numeric" placeholder="Enter Mobile Number" name="phn_no">
                </div>

                <div class="field">
                    <label>GST Number</label>
                    <input type="text" maxlength="15" placeholder="GST No." name="gstno">
                </div>

                <div class="field">
                    <div class="upload-box">
                        <input type="file" accept="image/*, application/pdf, .pdf" style="display:none;" name="gst_file">
                        <span class="upload-icon">⬆</span>
                        <span class="upload-text">Upload GST Certificate</span>
                        <span class="upload-remove"><i class="fa-solid fa-x"></i></span>
                    </div>
                </div>


                <div class="field">
                    <label>Address</label>
                    <textarea placeholder="Complete address" name="address"></textarea>
                </div>

                
                <div class="field">
                    <label>DL1 Number</label>
                    <input type="text" maxlength="30" placeholder="Drug License 1" name="dl1">
                </div>

                
                <div class="field">
                    <div class="upload-box">
                        <input type="file" accept="image/*, application/pdf, .pdf" style="display:none;" name="dl1_file">
                        <span class="upload-icon">⬆</span>
                        <span class="upload-text">Upload Drug License 1</span>
                        <span class="upload-remove"><i class="fa-solid fa-x"></i></span>
                    </div>

                </div>

                
                <div class="field">
                    <label>DL2 Number</label>
                    <input type="text" maxlength="30" placeholder="Drug License 2" name="dl2">
                </div>

                
                <div class="field">
                    <div class="upload-box">
                        <input type="file" accept="image/*, application/pdf, .pdf" style="display:none;" name="dl2_file">
                        <span class="upload-icon">⬆</span>
                        <span class="upload-text">Upload Drug License 2</span>
                        <span class="upload-remove"><i class="fa-solid fa-x"></i></span>
                    </div>

                </div>

                <div class="field password-field">
                    <label>Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" placeholder="Enter Password" name="user_pass">
                        <span class="toggle-eye"> <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="field password-field">
                    <label>Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirmPassword" placeholder="Re-enter password" name="re_password">
                        <span class="toggle-eye"> <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>
                </div>
                <button type="submit" class="register-btn">Register</button>

                <?php
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                        include 'dbconnect.php';

                        // ---------------- FORM DATA ----------------
                        $firm_name   = $_POST['firm_name'];
                        $owner_name  = $_POST['owner_name'];
                        $user_type   = $_POST['user_type'];
                        $email_id    = $_POST['email_id'];
                        $phn_no      = $_POST['phn_no'];
                        $gstno       = $_POST['gstno'];
                        $dl1         = $_POST['dl1'];
                        $dl2         = $_POST['dl2'];
                        $address     = $_POST['address'];
                        $user_pass   = $_POST['user_pass'];
                        $re_password = $_POST['re_password'];

                        // ---------------- BASIC CHECK ----------------
                        if ($user_pass != $re_password || empty($user_pass)) {
                            die("<script>alert('Password mismatch');</script>");
                        }

                        // ---------------- 1. INSERT USER ----------------
                        $insert_user = "
                            INSERT INTO $user_table
                            (firm_name, owner_name, user_type, email_id, phn_no, gstno, dl1, dl2, address, user_pass, registered_at)
                            VALUES
                            ('$firm_name', '$owner_name', '$user_type', '$email_id', '$phn_no', '$gstno', '$dl1', '$dl2', '$address', '$user_pass', CURRENT_TIMESTAMP())
                        ";

                        if (!mysqli_query($conn, $insert_user)) {
                            die("User registration failed");
                        }

                        // ---------------- 2. GET AUTO_INCREMENT ID ----------------
                        $user_id = mysqli_insert_id($conn);

                        // ---------------- 3. PREPARE firm_slug ----------------
                        $firm_slug = strtolower(trim($firm_name));
                        $firm_slug = preg_replace('/\s+/', '_', $firm_slug);
                        $firm_slug = $firm_slug . "_" . $user_id;

                        // ---------------- 4. ENSURE FOLDERS ----------------
                        // ---------------- 4. ENSURE FOLDERS ----------------
                        $base_dir = "uploads/";
                        $firm_dir = $base_dir . $firm_slug . "/";

                        // ---------------- 5. CHECK FILE SELECTION ----------------
                        if (
                            empty($_FILES['gst_file']['name']) ||
                            empty($_FILES['dl1_file']['name']) ||
                            empty($_FILES['dl2_file']['name'])
                        ) {
                            mysqli_query($conn, "DELETE FROM $user_table WHERE user_id = $user_id");
                            die("<script>alert('All documents are mandatory');</script>");
                        }

                        // ---------------- UPLOAD FUNCTION ----------------
                        function uploadFile($file, $type, $firm_dir) {

                            if ($file['error'] != 0) {
                                return false;
                            }

                            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                            $new_name = $type . "." . $ext;

                            if (move_uploaded_file($file['tmp_name'], $firm_dir . $new_name)) {
                                return $new_name;
                            }

                            return false;
                        }

                        // ---------------- 6. UPLOAD FILES ----------------
                        $gst_file = uploadFile($_FILES['gst_file'], "gst", $firm_dir);
                        $dl1_file = uploadFile($_FILES['dl1_file'], "dl1", $firm_dir);
                        $dl2_file = uploadFile($_FILES['dl2_file'], "dl2", $firm_dir);

                        // ---------------- 7. ROLLBACK IF UPLOAD FAILS ----------------
                        if (!$gst_file || !$dl1_file || !$dl2_file) {

                            mysqli_query($conn, "DELETE FROM $user_table WHERE user_id = $user_id");

                            @unlink($firm_dir . $gst_file);
                            @unlink($firm_dir . $dl1_file);
                            @unlink($firm_dir . $dl2_file);
                            @rmdir($firm_dir);

                            die("<script>alert('Document upload failed. Registration stopped');</script>");
                        }

                        // ---------------- 8. SUCCESS ----------------
                        echo "<script>alert('Registration successful for $firm_name');</script>";


                        // --------------USER-VERIFICATION-------------

                        $gst_path = $firm_dir . $gst_file;
                        $dl1_path = $firm_dir . $dl1_file;
                        $dl2_path = $firm_dir . $dl2_file;

                        $dbname = $firm_slug;

                        $created_at = mysqli_query(
                            $conn,
                            "SELECT registered_at FROM $user_table WHERE user_id = $user_id"
                        );
                        $row = mysqli_fetch_assoc($created_at);
                        $created_at = $row['registered_at'];

                        $sql = "
                            INSERT INTO $verification_table
                            (user_id,firm_name, gst_proof_path, dl1_proof_path, dl2_proof_path, dbname, registered_at)
                            VALUES
                            ('$user_id','$firm_name', '$gst_path', '$dl1_path', '$dl2_path', '$dbname', '$created_at')
                        ";
                        $result = mysqli_query($conn, $sql);
                        if(!$result){
                            mysqli_query($conn, "DELETE FROM $user_table WHERE user_id = $user_id");
                            @unlink($firm_dir . $gst_file);
                            @unlink($firm_dir . $dl1_file);
                            @unlink($firm_dir . $dl2_file);
                            @rmdir($firm_dir);
                            die("<script>alert('Registration Failes');</script>");
                        }
                        echo "<script>
                            console.log($firm_name);
                            alert('Sucessfully Registered $firm_name');
                        </script>";

                    }
                ?>
            </form>
        </div>
        <script src="JS/register.js"></script>
    </div>
    </div>
</body>

</html>