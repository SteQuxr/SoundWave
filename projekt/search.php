<?php

session_start();

require_once('config\db.php');

$_SESSION["isLogged"] ?? false;

if ($_SESSION["isLogged"] != true) {
    header("Location: login/login.php");
    exit;
}

$title = $_GET['title'] ?? NULL;
$last_page = isset($_GET['last_page']) ? urldecode($_GET['last_page']) : '/projekt/index.php';

if ($title === NULL) {
    header("Location: $last_page");
    exit;
}


$stmt = $db->prepare("
    SELECT 
        songs.id,
        songs.title, 
        songs.duration, 
        songs.picture_path, 
        songs.file_path, 
        songs.date_added, 
        songs.is_explicit, 
        users.username, 
        users.id AS user_id,
        genre1.name AS genre_name,
        genre2.name AS sub_genre_name
    FROM songs
    JOIN users ON songs.artist_id = users.id
    JOIN genres AS genre1 ON songs.genre_id = genre1.id
    JOIN genres AS genre2 ON songs.sub_genre_id = genre2.id
    WHERE songs.title like :title
");

$stmt->execute([
    ':title' => "%$title%",
]);

$songs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> SoundWave - listen to user published songs</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

</head>

<body class="bg-gray-50 dark:bg-gray-900">

    <header class="z-50 relative">
        <nav class="bg-white border-gray-200 px-4 lg:px-6 py-2.5 dark:bg-gray-800 fixed w-full top-0 left-0 z-20">
            <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
                <a href="index.php" class="flex items-center">
                    <img src="images/logo.svg" class="mr-3 h-6 sm:h-9" alt="Logo" />
                    <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">SoundWave</span>
                </a>
                <div class="flex items-center lg:order-2">


                    <img id="avatarButton" type="button" data-dropdown-toggle="userDropdown" data-dropdown-placement="bottom-start" class="w-10 h-10 rounded-full cursor-pointer" src="<?= $_SESSION['profile_picture'] ?>">

                    <!-- Dropdown menu -->
                    <div id="userDropdown" class="z-50 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700 dark:divide-gray-600">
                        <div class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                            <div><?= $_SESSION['username'] ?></div>
                            <div class="font-medium truncate"><?= $_SESSION['email'] ?></div>
                        </div>
                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="avatarButton">
                            <li>
                                <a href="user/profile.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Profile</a>
                            </li>
                            <li>
                                <a href="user/settings.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Settings</a>
                            </li>
                        </ul>

                        <div class="py-1">
                            <a href="login/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Sign out</a>

                        </div>
                    </div>

                    <button data-collapse-toggle="mobile-menu-2" type="button" class="inline-flex items-center p-2 ml-1 text-sm text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="mobile-menu-2" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <div class="hidden justify-between items-center w-full lg:flex lg:w-auto lg:order-1" id="mobile-menu-2">
                    <ul class="flex flex-col mt-4 font-medium lg:flex-row lg:space-x-8 lg:mt-0">
                        <li>
                            <a href="actions/randomSong.php" class="block py-2 pr-4 pl-3 text-gray-700 border-b border-gray-100 hover:bg-gray-50 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-700 lg:p-0 dark:text-gray-400 lg:dark:hover:text-white dark:hover:bg-gray-700 dark:hover:text-white lg:dark:hover:bg-transparent dark:border-gray-700">Random Song</a>
                        </li>

                        <li>
                            <a href="actions/post.php" class="block py-2 pr-4 pl-3 text-gray-700 border-b border-gray-100 hover:bg-gray-50 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-700 lg:p-0 dark:text-gray-400 lg:dark:hover:text-white dark:hover:bg-gray-700 dark:hover:text-white lg:dark:hover:bg-transparent dark:border-gray-700">Post</a>
                        </li>

                        <li>
                        </li>
                        <li>
                        </li>
                    </ul>
                    <form action="search.php" method="get" class="max-w-md mx-auto">
                        <input type="hidden" name="last_page" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                        <div class="relative">
                            <input type="search" id="title" name="title" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search song... " required />
                            <button type="submit" class="text-white absolute end-2.5 bottom-2.5  focus:ring-4 focus:outline-nonefont-medium rounded-lg text-sm px-4 py-2">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <br><br><br><br>

    <ul class="space-y-4">
        <?php foreach ($songs as $song): ?>

            <li class="flex flex-col md:flex-row items-start bg-white dark:bg-gray-800 p-4 rounded-lg shadow">

                <!-- picture -->
                <div class="w-16 h-16 md:w-24 md:h-24 flex-shrink-0 mr-3">
                    <img src="<?= $song['picture_path'] ?>"
                        alt="<?= $song['title'] ?> cover"
                        class="w-full h-full object-cover rounded-md" />
                </div>

                <div class="flex-1 mt-4 md:mt-0 md:ml-4">
                    <div class="flex items-center justify-between">
                        <!-- Title -->
                        <a href="songPage.php?id=<?= $song['id'] ?>">
                            <h3 class="mt-2 text-lg font-semibold text-gray-900 dark:text-white truncate hover:underline">
                                <span class="inline-flex items-center gap-1 hover:underline">
                                    <?= $song['title'] ?>
                                    <?php if ($song['is_explicit'] == 'Y'): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" class="bi bi-explicit" viewBox="0 0 16 16">
                                            <path d="M6.826 10.88H10.5V12h-5V4.002h5v1.12H6.826V7.4h3.457v1.073H6.826z" />
                                            <path d="M2.5 0A2.5 2.5 0 0 0 0 2.5v11A2.5 2.5 0 0 0 2.5 16h11a2.5 2.5 0 0 0 2.5-2.5v-11A2.5 2.5 0 0 0 13.5 0zM1 2.5A1.5 1.5 0 0 1 2.5 1h11A1.5 1.5 0 0 1 15 2.5v11a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 13.5z" />
                                        </svg>
                                    <?php endif; ?>
                                </span>
                            </h3>
                        </a>



                        <!-- duration -->
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            <?= gmdate("i:s", $song['duration']) ?>
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <!-- username -->
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate hover:underline">
                            <a href="userProfile.php?id=<?= $song['user_id'] ?>">
                                <?= $song['username'] ?? 'Unknown Artist' ?>
                            </a>
                        </p>

                        <!-- genres -->
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                            <?= $song['genre_name'] ?? 'no genre' ?>
                            &
                            <?= $song['sub_genre_name'] ?? 'no genre' ?>
                        </p>
                    </div>

                    <!-- audio -->
                    <div class="text-sm text-gray-500 truncate dark:text-gray-400">
                        <audio class="w-full" controls>
                            <source src="<?= $song['file_path'] ?>" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    </div>

                    <!-- stats -->
                    <?php
                    $stmt = $db->prepare("SELECT COUNT(*) as like_count FROM songs_likes WHERE song_id = :id");
                    $stmt->execute([
                        ':id' => $song['id']
                    ]);
                    $likeCount = $stmt->fetchColumn();

                    $stmt = $db->prepare("SELECT COUNT(*) as comment_count FROM songs_comments WHERE song_id = :id");
                    $stmt->execute([
                        ':id' => $song['id']
                    ]);
                    $commentCount = $stmt->fetchColumn();


                    // user like
                    $userLiked = false;
                    $stmt = $db->prepare("SELECT 1 FROM songs_likes WHERE song_id = :song_id AND user_id = :user_id LIMIT 1");
                    $stmt->execute([
                        ':song_id' => $song['id'],
                        ':user_id' => $_SESSION['user_id']
                    ]);
                    $userLiked = $stmt->fetch() !== false;

                    $likeIconColor = $userLiked ? 'text-red-400' : 'text-gray-800 dark:text-white';

                    ?>
                    <div class="mt-3 flex space-x-4 text-gray-600 dark:text-gray-300">
                        <a href="actions/like.php?id=<?= $song['id'] ?>&last_page=<?= urlencode($_SERVER['REQUEST_URI']) ?>">
                            <button class="flex items-center space-x-1">
                                <svg class="w-6 h-6 <?= $likeIconColor ?>" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="m12.75 20.66 6.184-7.098c2.677-2.884 2.559-6.506.754-8.705-.898-1.095-2.206-1.816-3.72-1.855-1.293-.034-2.652.43-3.963 1.442-1.315-1.012-2.678-1.476-3.973-1.442-1.515.04-2.825.76-3.724 1.855-1.806 2.201-1.915 5.823.772 8.706l6.183 7.097c.19.216.46.34.743.34a.985.985 0 0 0 .743-.34Z" />
                                </svg>
                                <span><?= $likeCount ?></span>
                            </button>
                        </a>

                        <button data-modal-target="commentModal-<?= $song['id'] ?>" data-modal-toggle="commentModal-<?= $song['id'] ?>" class=" flex items-center space-x-1">
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 2H4a2 2 0 0 0-2 2v16l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2Z" />
                            </svg>
                            <span><?= $commentCount ?></span>
                        </button>

                        <!-- modal -->
                        <div id="commentModal-<?= $song['id'] ?>" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50">
                            <div class="relative w-full max-w-md max-h-full mx-auto mt-24">
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                                    <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            Comment
                                        </h3>
                                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-hide="commentModal-<?= $song['id'] ?>">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <form action="actions/comment.php" method="post" class="p-4">
                                        <input type="hidden" name="song_id" value="<?= $song['id'] ?>">
                                        <input type="hidden" name="last_page" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                                        <textarea name="comment_text" rows="4" class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-yellow-500 focus:border-yellow-500"
                                            required></textarea>
                                        <div class="flex justify-end mt-4 gap-2">
                                            <button type="button" data-modal-hide="commentModal-<?= $song['id'] ?>" class="text-gray-900 hover:text-white border border-gray-800 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-gray-600 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800">
                                                Cancel
                                            </button>
                                            <button type="submit" class="text-yellow-400 hover:text-white border border-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-yellow-300 dark:text-yellow-300 dark:hover:text-white dark:hover:bg-yellow-400 dark:focus:ring-yellow-900">
                                                Comment
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>




    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

</body>