<?php
session_start();

require_once('..\config\db.php');

if ($_SERVER["REQUEST_METHOD"] === 'POST') {

    $email = $_POST["email"] ?? NULL;
    $password = $_POST["password"] ?? NULL;

    if ($email === NULL || $password === NULL) {

        $_SESSION['isLogged'] = false;
        header("Location: index.php");
        exit();
    }

    $stmt = $db->prepare("SELECT id, username, profile_picture, email, date_of_birth, password, login_tries, is_admin from users WHERE email = :email");
    $stmt->execute([
        ':email' => $email
    ]);

    $user = $stmt->fetch();

    if (!$user) {

        $_SESSION['isLogged'] = false;
        header("Location: login.php");
        exit();
    }

    if ($user['login_tries'] <= 0) {

        $_SESSION['isLogged'] = false;
        header("Location: login.php");
        exit();
    }

    if (password_verify($password, $user['password'])) {

        $stmt = $db->prepare("UPDATE users SET login_tries = 3 WHERE email = :email");
        $stmt->execute([':email' => $email]);

        $_SESSION['isLogged'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['profile_picture'] = $user['profile_picture'];
        $_SESSION['date_of_birth'] = $user['date_of_birth'];
        $_SESSION['is_admin'] = $user['is_admin'];

        header("Location: ..\index.php");
        exit;
    } else {

        $stmt2 = $db->prepare("UPDATE users SET login_tries = login_tries - 1  WHERE email = :email");
        $stmt2->execute([
            ':email' => $email,
        ]);

        $_SESSION['isLogged'] = false;
        header("Location: login.php");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> SoundWave - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

</head>


<body class="bg-gray-50 dark:bg-gray-900">
    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <a href="..\index.php" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                <img class="w-12 h-12 mr-2" src="..\images\logo.svg" alt="logo">
                Soundwave
            </a>
            <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                        Login to your account
                    </h1>
                    <form action="login.php" method="post" class="space-y-4 md:space-y-6">
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your email</label>
                            <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="name@company.com" required="">
                        </div>
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                            <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
                        </div>
                        <div class="flex items-center justify-between">

                            <a href="#" class="text-sm font-medium text-primary-600 hover:underline dark:text-gray-300">Forgot password?</a>
                        </div>
                        <button type="submit" class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Sign in</button>
                        <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                            Don’t have an account yet? <a href="register.php" class="font-medium text-primary-600 hover:underline dark:text-primary-500">Sign up</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>

    </section>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>

</html>