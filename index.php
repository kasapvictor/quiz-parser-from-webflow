<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    header('Content-Type: text/html; charset=utf-8');

    $done = '';
    if (isset($_GET['parse']) && $_GET['parse'] === 'true')  {
        $done = "<small class='success'>Quiz was parsed</small>";
    }
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta content="Quiz" property="og:title">
    <meta content="Quiz" property="twitter:title">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta content="Quiz" name="generator">
    <meta name="robots" content="noindex, nofollow">
    <meta name="author" content="Kasap Victor">
    <link rel="stylesheet" href="./assets/styles/main.css">
    <link href="./assets/images/32x32.jpg" rel="apple-touch-icon">
    <link href="./assets/images/256x256.jpg" rel="shortcut icon" type="image/x-icon">
    <title>Give me a QUIZ!!!</title>
</head>

<body>

    <header>
        <div class="container">
            <h1>Give me a QUIZ!!!</h1>
        </div>
    </header>
    <main>
        <div class="container">
            <section>
                <h2>Parse form</h2>
                <?= $done; ?>
                <form action="test.php" class="form-parse" method="post">
                    <div class="from-row">
                        <label for="source">Destination<span class="important">*</span></label>
                        <input type="text" placeholder="https://...." id="source" required name="source">
                    </div>
                    <input type="submit" value="Parse">
                </form>
            </section>
        </div>
    </main>

    <footer>
        <div class="container">
            <small>Copyright &copy;</small>
        </div>
    </footer>

<script src="./assets/scripts/main.js"></script>
</body>
</html>