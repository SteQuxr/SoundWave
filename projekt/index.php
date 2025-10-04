<?php

session_start();

require_once('config\db.php');

$_SESSION["isLogged"] ?? false;

if ($_SESSION["isLogged"] != true) {
    header("Location: login/login.php");
    exit;
}

if ($_SESSION["is_admin"] == true) {
    header("Location: admin/users.php");
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
    genre2.name AS sub_genre_name,
    COUNT(songs_likes.song_id) AS like_count
FROM songs
JOIN users ON songs.artist_id = users.id
JOIN genres AS genre1 ON songs.genre_id = genre1.id
JOIN genres AS genre2 ON songs.sub_genre_id = genre2.id
LEFT JOIN songs_likes ON songs.id = songs_likes.song_id
GROUP BY songs.id 
ORDER BY `like_count` DESC LIMIT 5
");

$stmt->execute();

$songs = $stmt->fetchAll();

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
ORDER BY songs.date_added DESC
LIMIT 5
");

$stmt->execute();

$songs2 = $stmt->fetchAll();

$stmt = $db->prepare("
SELECT 
    users.id AS user_id,
    users.username,
    users.date_joined,
    users.profile_picture,
    COUNT(songs_likes.song_id) AS total_likes
FROM users
JOIN songs ON songs.artist_id = users.id
LEFT JOIN songs_likes ON songs.id = songs_likes.song_id
GROUP BY users.id
ORDER BY total_likes DESC
LIMIT 5
");

$stmt->execute();

$users = $stmt->fetchAll();

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


    <div class="ml-6">

        <p class="text-5xl font-bold text-gray-900 dark:text-white">Most Popular</p>
        <div id="default-carousel" class="relative mt-6" data-carousel="static">
            <!-- Carousel wrapper -->
            <div class="relative h-56 overflow-hidden rounded-lg md:h-96">
                <?php foreach ($songs as $song): ?>
                    <a href="songPage.php?id=<?= $song['id'] ?>">
                        <div onclick="location.href='songPage.php?id=<?= $song['id'] ?>'" class=" duration-700 ease-in-out" data-carousel-item>

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

                            <section class="bg-center bg-no-repeat bg-gray-700 bg-blend-multiply">
                                <div class="px-4 mx-auto max-w-screen-xl text-center py-12 lg:py-28">
                                    <img src="<?= $song['picture_path'] ?>" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                                    <div class="relative z-10 text-white">


                                        <div class="relative max-w-screen-xl mx-auto px-6 py-12 md:py-24 text-white flex flex-col md:flex-row items-start gap-6">

                                            <div class="flex-1 bg-black/60 p-6">
                                                <div class="flex items-center justify-between mb-4">
                                                    <!-- Title + Explicit -->
                                                    <a href="songPage.php?id=<?= $song['id'] ?>" class="hover:underline">
                                                        <h3 style="text-shadow: 2px 2px 2px rgba(0,0,0,1);" class=" text-2xl font-bold truncate flex items-center gap-2">
                                                            <?= $song['title'] ?>
                                                            <?php if ($song['is_explicit'] == 'Y'): ?>
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" class="bi bi-explicit" viewBox="0 0 16 16">
                                                                    <path d="M6.826 10.88H10.5V12h-5V4.002h5v1.12H6.826V7.4h3.457v1.073H6.826z" />
                                                                    <path d="M2.5 0A2.5 2.5 0 0 0 0 2.5v11A2.5 2.5 0 0 0 2.5 16h11a2.5 2.5 0 0 0 2.5-2.5v-11A2.5 2.5 0 0 0 13.5 0zM1 2.5A1.5 1.5 0 0 1 2.5 1h11A1.5 1.5 0 0 1 15 2.5v11a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 13.5z" />
                                                                </svg>
                                                            <?php endif; ?>
                                                        </h3>
                                                    </a>

                                                    <!-- Duration -->
                                                    <span style="text-shadow: 2px 2px 2px rgba(0,0,0,1);" class="text-sm text-gray-300">
                                                        <?= gmdate("i:s", $song['duration']) ?>
                                                    </span>
                                                </div>

                                                <div class="flex items-center justify-between mb-4">
                                                    <!-- Username -->
                                                    <p style="text-shadow: 2px 2px 2px rgba(0,0,0,1);" class="text-sm truncate">
                                                        <a href="userProfile.php?id=<?= $song['user_id'] ?>" class="hover:underline">
                                                            <?= $song['username'] ?? 'Unknown Artist' ?>
                                                        </a>
                                                    </p>

                                                    <!-- Genres -->
                                                    <p style="text-shadow: 2px 2px 2px rgba(0,0,0,1);" class="text-sm truncate">
                                                        <?= $song['genre_name'] ?? 'no genre' ?>
                                                        &nbsp;&amp;&nbsp;
                                                        <?= $song['sub_genre_name'] ?? 'no genre' ?>
                                                    </p>
                                                </div>

                                                <!-- Audio player -->
                                                <div class="mb-4">
                                                    <audio class="w-full" controls>
                                                        <source src="<?= $song['file_path'] ?>" type="audio/mpeg" />
                                                        Your browser does not support the audio element.
                                                    </audio>
                                                </div>

                                                <!-- Stats -->
                                                <div class="flex space-x-6 text-gray-300">
                                                    <a href="actions/like.php?id=<?= $song['id'] ?>&last_page=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="flex items-center space-x-2 hover:text-red-400 transition">
                                                        <svg class="w-6 h-6 <?= $likeIconColor ?>" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="m12.75 20.66 6.184-7.098c2.677-2.884 2.559-6.506.754-8.705-.898-1.095-2.206-1.816-3.72-1.855-1.293-.034-2.652.43-3.963 1.442-1.315-1.012-2.678-1.476-3.973-1.442-1.515.04-2.825.76-3.724 1.855-1.806 2.201-1.915 5.823.772 8.706l6.183 7.097c.19.216.46.34.743.34a.985.985 0 0 0 .743-.34Z" />
                                                        </svg>
                                                        <span style="text-shadow: 2px 2px 2px rgba(0,0,0,1);"><?= $likeCount ?></span>
                                                    </a>

                                                    <button class="flex items-center space-x-2 hover:text-yellow-400 transition">
                                                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M20 2H4a2 2 0 0 0-2 2v16l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2Z" />
                                                        </svg>
                                                        <span style="text-shadow: 2px 2px 2px rgba(0,0,0,1);"><?= $commentCount ?></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </section>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Slider indicators -->
            <div class="absolute z-30 flex -translate-x-1/2 bottom-5 left-1/2 space-x-3 rtl:space-x-reverse">
                <button type="button" class="w-3 h-3 rounded-full" aria-current="true" aria-label="Slide 1" data-carousel-slide-to="0"></button>
                <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 2" data-carousel-slide-to="1"></button>
                <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 3" data-carousel-slide-to="2"></button>
                <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 4" data-carousel-slide-to="3"></button>
                <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 5" data-carousel-slide-to="4"></button>
            </div>
            <!-- Slider controls -->
            <button type="button" class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                    <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4" />
                    </svg>
                    <span class="sr-only">Previous</span>
                </span>
            </button>
            <button type="button" class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                    <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                    </svg>
                    <span class="sr-only">Next</span>
                </span>
            </button>
        </div>
    </div>

    <br><br>

    <div class="ml-6">

        <p class="text-5xl font-bold text-gray-900 dark:text-white">Recently Added</p>
        <div id="default-carousel" class="relative mt-6" data-carousel="static">
            <!-- Carousel wrapper -->
            <div class="relative h-56 overflow-hidden rounded-lg md:h-96">
                <?php foreach ($songs2 as $song): ?>
                    <a href="songPage.php?id=<?= $song['id'] ?>">
                        <div onclick="location.href='songPage.php?id=<?= $song['id'] ?>'" class=" duration-700 ease-in-out" data-carousel-item>

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

                            <section class="bg-center bg-no-repeat bg-gray-700 bg-blend-multiply">
                                <div class="px-4 mx-auto max-w-screen-xl text-center py-12 lg:py-28">
                                    <img src="<?= $song['picture_path'] ?>" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                                    <div class="relative z-10 text-white">


                                        <div class="relative max-w-screen-xl mx-auto px-6 py-12 md:py-24 text-white flex flex-col md:flex-row items-start gap-6">

                                            <div class="flex-1 bg-black/60 p-6">
                                                <div class="flex items-center justify-between mb-4">
                                                    <!-- Title + Explicit -->
                                                    <a href="songPage.php?id=<?= $song['id'] ?>" class="hover:underline">
                                                        <h3 style="text-shadow: 2px 2px 2px rgba(0,0,0,1);" class=" text-2xl font-bold truncate flex items-center gap-2">
                                                            <?= $song['title'] ?>
                                                            <?php if ($song['is_explicit'] == 'Y'): ?>
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" class="bi bi-explicit" viewBox="0 0 16 16">
                                                                    <path d="M6.826 10.88H10.5V12h-5V4.002h5v1.12H6.826V7.4h3.457v1.073H6.826z" />
                                                                    <path d="M2.5 0A2.5 2.5 0 0 0 0 2.5v11A2.5 2.5 0 0 0 2.5 16h11a2.5 2.5 0 0 0 2.5-2.5v-11A2.5 2.5 0 0 0 13.5 0zM1 2.5A1.5 1.5 0 0 1 2.5 1h11A1.5 1.5 0 0 1 15 2.5v11a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 13.5z" />
                                                                </svg>
                                                            <?php endif; ?>
                                                        </h3>
                                                    </a>

                                                    <!-- Duration -->
                                                    <span style="text-shadow: 2px 2px 2px rgba(0,0,0,1);" class="text-sm text-gray-300">
                                                        <?= gmdate("i:s", $song['duration']) ?>
                                                    </span>
                                                </div>

                                                <div class="flex items-center justify-between mb-4">
                                                    <!-- Username -->
                                                    <p style="text-shadow: 2px 2px 2px rgba(0,0,0,1);" class="text-sm truncate">
                                                        <a href="userProfile.php?id=<?= $song['user_id'] ?>" class="hover:underline">
                                                            <?= $song['username'] ?? 'Unknown Artist' ?>
                                                        </a>
                                                    </p>

                                                    <!-- Genres -->
                                                    <p style="text-shadow: 2px 2px 2px rgba(0,0,0,1);" class="text-sm truncate">
                                                        <?= $song['genre_name'] ?? 'no genre' ?>
                                                        &nbsp;&amp;&nbsp;
                                                        <?= $song['sub_genre_name'] ?? 'no genre' ?>
                                                    </p>
                                                </div>

                                                <!-- Audio player -->
                                                <div class="mb-4">
                                                    <audio class="w-full" controls>
                                                        <source src="<?= $song['file_path'] ?>" type="audio/mpeg" />
                                                        Your browser does not support the audio element.
                                                    </audio>
                                                </div>

                                                <!-- Stats -->
                                                <div class="flex space-x-6 text-gray-300">
                                                    <a href="actions/like.php?id=<?= $song['id'] ?>&last_page=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="flex items-center space-x-2 hover:text-red-400 transition">
                                                        <svg class="w-6 h-6 <?= $likeIconColor ?>" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="m12.75 20.66 6.184-7.098c2.677-2.884 2.559-6.506.754-8.705-.898-1.095-2.206-1.816-3.72-1.855-1.293-.034-2.652.43-3.963 1.442-1.315-1.012-2.678-1.476-3.973-1.442-1.515.04-2.825.76-3.724 1.855-1.806 2.201-1.915 5.823.772 8.706l6.183 7.097c.19.216.46.34.743.34a.985.985 0 0 0 .743-.34Z" />
                                                        </svg>
                                                        <span style="text-shadow: 2px 2px 2px rgba(0,0,0,1);"><?= $likeCount ?></span>
                                                    </a>

                                                    <button class="flex items-center space-x-2 hover:text-yellow-400 transition">
                                                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M20 2H4a2 2 0 0 0-2 2v16l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2Z" />
                                                        </svg>
                                                        <span style="text-shadow: 2px 2px 2px rgba(0,0,0,1);"><?= $commentCount ?></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </section>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Slider indicators -->
            <div class="absolute z-30 flex -translate-x-1/2 bottom-5 left-1/2 space-x-3 rtl:space-x-reverse">
                <button type="button" class="w-3 h-3 rounded-full" aria-current="true" aria-label="Slide 1" data-carousel-slide-to="0"></button>
                <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 2" data-carousel-slide-to="1"></button>
                <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 3" data-carousel-slide-to="2"></button>
                <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 4" data-carousel-slide-to="3"></button>
                <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 5" data-carousel-slide-to="4"></button>
            </div>
            <!-- Slider controls -->
            <button type="button" class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                    <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4" />
                    </svg>
                    <span class="sr-only">Previous</span>
                </span>
            </button>
            <button type="button" class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                    <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                    </svg>
                    <span class="sr-only">Next</span>
                </span>
            </button>
        </div>
    </div>

    <br><br>

    <div class="ml-6">

        <p class="text-5xl font-bold text-gray-900 dark:text-white">Most Popular Artists</p>
        <div id="default-carousel" class="relative mt-6" data-carousel="static">
            <!-- Carousel wrapper -->
            <div class="relative h-56 overflow-hidden rounded-lg md:h-96">
                <?php foreach ($users as $user): ?>
                    <div onclick="location.href='userProfile.php?id=<?= $user['user_id'] ?>'" class=" duration-700 ease-in-out" data-carousel-item>

                        <section class="bg-center bg-no-repeat bg-gray-700 bg-blend-multiply">
                            <div class="px-4 mx-auto max-w-screen-xl text-center py-12 lg:py-28">
                                <img src="<?= $user['profile_picture'] ?>" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                                <div class="relative z-10 text-white">


                                    <div class="relative max-w-screen-xl mx-auto px-6 py-3 md:py-24 text-white flex flex-col md:flex-row items-start gap-6">
                                        <div class="flex-1 bg-black/60 p-6 flex items-center justify-center">
                                            <div>
                                                <a href="userProfile.php?id=<?= $user['user_id'] ?>" class="hover:underline">
                                                    <h2 style="text-shadow: 2px 2px 2px rgba(0,0,0,1);" class="text-8xl font-bold truncate flex items-center gap-2 justify-center">
                                                        <?= $user['username'] ?>
                                                    </h2>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="relative max-w-screen-xl mx-auto px-6 py-3 md:py-12 text-white flex flex-col md:flex-row items-start gap-6">
                                        <div class="flex-1 bg-black/60 p-6 flex items-center justify-center">
                                            <div>
                                                <h2 style="text-shadow: 2px 2px 2px rgba(0,0,0,1);" class="text-xl font-bold truncate flex items-center gap-2 justify-center">
                                                    Date joined SoundWave: <?= $user['date_joined'] ?>
                                                </h2>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </section>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Slider indicators -->
            <div class="absolute z-30 flex -translate-x-1/2 bottom-5 left-1/2 space-x-3 rtl:space-x-reverse">
                <button type="button" class="w-3 h-3 rounded-full" aria-current="true" aria-label="Slide 1" data-carousel-slide-to="0"></button>
                <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 2" data-carousel-slide-to="1"></button>
                <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 3" data-carousel-slide-to="2"></button>
                <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 4" data-carousel-slide-to="3"></button>
                <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 5" data-carousel-slide-to="4"></button>
            </div>
            <!-- Slider controls -->
            <button type="button" class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                    <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4" />
                    </svg>
                    <span class="sr-only">Previous</span>
                </span>
            </button>
            <button type="button" class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                    <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                    </svg>
                    <span class="sr-only">Next</span>
                </span>
            </button>
        </div>
    </div>

    <br><br>



    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

</body>
