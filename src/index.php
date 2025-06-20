<?php
session_start();

// Tạo thư mục upload riêng cho session
if (!isset($_SESSION['dir'])) {
    $_SESSION['dir'] = 'upload/' . session_id();
}
$dir = $_SESSION['dir'];
if (!file_exists($dir))
    mkdir($dir);

if (isset($_GET["debug"])) die(highlight_file(__FILE__));

$error = '';
$success = '';

if (isset($_FILES["file"])) {
    try {
        $filename = $_FILES["file"]["name"];
        $tmp_name = $_FILES["file"]["tmp_name"];

        // Kiểm tra phần mở rộng bị cấm
        $blacklist_ext = ['php', 'phtml', 'phar'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $blacklist_ext)) {
            throw new Exception("Hack detected! (bad extension)");
        }

        // Kiểm tra MIME thực tế (magic bytes)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($tmp_name);
        if (!$mime_type || $mime_type === 'text/x-php' || strpos($mime_type, 'php') !== false) {
            throw new Exception("Invalid or dangerous MIME type: $mime_type");
        }

        // Kiểm tra nếu file đã tồn tại → chặn ghi đè
        $destination = $dir . "/" . $filename;
        if (file_exists($destination)) {
            throw new Exception("File already exists.");
        }

        if (!move_uploaded_file($tmp_name, $destination)) {
            throw new Exception("File upload failed");
        }

        $success = 'Successfully uploaded file at: <a href="/' . htmlspecialchars($destination) . '">/' . htmlspecialchars($destination) . '</a><br>';
        $success .= 'View all uploaded files at: <a href="/' . htmlspecialchars($dir) . '/">/' . htmlspecialchars($dir) . '</a>';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>PHP upload Level 2</title>

    <!-- This is for UI only -->
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css" integrity="sha512-P5MgMn1jBN01asBgU0z60Qk4QxiXo86+wlFahKrsQf37c9cro517WzVSPPV1tDKzhku2iJ2FVgL67wG03SGnNA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body>
    <br />
    <br />
    <h3 class="display-4 text-center">File upload workshop</h3>
    <h4 class="display-4 text-center">Level 2</h4>
    <p class="display-5 text-center">Goal: RCE me!</p>

    <br />
    <div class="container">
        <a href="/?debug">Debug source</a>
        <form method="post" enctype="multipart/form-data">
            Select file to upload:
            <input type="file" name="file" id="file">
            <br />
            <input type="submit">
        </form>
        <span style="color:red"><?php echo $error; ?></span>
        <span style="color:green"><?php echo $success; ?></span>
    </div>

</body>

</html>
