<?php

session_start();

require_once('config\db.php');

if ($_SESSION["isLogged"] != true) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

$stmt = $db->prepare("
    SELECT 
        songs.id,
        songs.title,
        songs.artist_id,
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
    WHERE songs.id = :id
");

$stmt->execute([
    ':id' => $id
]);

$song = $stmt->fetch();

if (!$song) {
    header("Location: index.php");
    exit;
}

$stmt = $db->prepare("
    SELECT 
    songs_comments.comment_text,
    users.username,
    users.id,
    users.profile_picture,
    songs_comments.id AS comment_id,
    songs_comments.user_id,
    songs_comments.song_id,
    songs_comments.edited
FROM songs_comments, users
WHERE songs_comments.user_id = users.id AND songs_comments.song_id = :id
");

$stmt->execute([
    ':id' => $id
]);

$comments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> SoundWave - User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

</head>

<body class="bg-gray-50 dark:bg-gray-900">

    <header>
        <nav class="bg-white border-gray-200 px-4 lg:px-6 py-2.5 dark:bg-gray-800 fixed w-full top-0 left-0 z-20">
            <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
                <a href="index.php" class="flex items-center">
                    <img src="images/logo.svg" class="mr-3 h-6 sm:h-9" alt="Logo" />
                    <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">SoundWave</span>
                </a>
                <div class="flex items-center lg:order-2">


                    <img id="avatarButton" type="button" data-dropdown-toggle="userDropdown" data-dropdown-placement="bottom-start" class="w-10 h-10 rounded-full cursor-pointer" src="<?= $_SESSION['profile_picture'] ?>">

                    <!-- Dropdown menu -->
                    <div id="userDropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700 dark:divide-gray-600">
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

    <br><br><br>

    <ul class="space-y-4">

        <li class="flex flex-col md:flex-row items-start md:items-center bg-white dark:bg-gray-800 p-4 rounded-lg shadow">

            <!-- picture -->
            <div class="w-64 h-64 md:w-24 md:h-24 flex-shrink-0 mr-3">
                <img src="<?= $song['picture_path'] ?>"
                    alt="<?= $song['title'] ?> cover"
                    class="w-full h-full object-cover rounded-md" />
            </div>

            <div class="flex-1 ml-4 ">
                <div class="flex items-center justify-between">
                    <!-- Title -->
                    <a href="songPage.php?id=<?= $song['id'] ?>">
                        <h3 class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white truncate">
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
                    <span class="text-xl text-gray-500 dark:text-gray-400">
                        <?= gmdate("i:s", $song['duration']) ?>
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <!-- username -->
                    <p class="text-xl text-gray-500 dark:text-gray-400 truncate hover:underline">
                        <a href="userProfile.php?id=<?= $song['user_id'] ?>">
                            <?= $song['username'] ?? 'Unknown Artist' ?>
                        </a>
                    </p>

                    <!-- genres -->
                    <p class="text-xl text-gray-500 dark:text-gray-400 truncate">
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
                //like count
                $stmt = $db->prepare("SELECT COUNT(*) as like_count FROM songs_likes WHERE song_id = :id");
                $stmt->execute([
                    ':id' => $song['id']
                ]);
                $likeCount = $stmt->fetchColumn();

                //comment count
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
                    <button class="flex items-center space-x-1">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 2H4a2 2 0 0 0-2 2v16l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2Z" />
                        </svg>
                        <span><?= $commentCount ?></span>
                    </button>

                    <?php if ($song['artist_id'] == $_SESSION['user_id']): ?>
                        <!-- edit -->
                        <button data-modal-target="editSong-<?= $id ?>" data-modal-toggle="editSong-<?= $id ?>" class="p-2 border rounded-lg text-yellow-600 border-yellow-600 hover:bg-yellow-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-yellow-300 dark:text-yellow-300 dark:border-yellow-300 dark:hover:bg-yellow-400 dark:focus:ring-yellow-900">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m14.3 4.8 2.9 2.9M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.4-9.9a2 2 0 0 1 0 2.85l-6.84 6.84L8 14l.71-3.56 6.84-6.84a2 2 0 0 1 2.85 0Z" />
                            </svg>
                        </button>

                        <!-- edit modal -->
                        <div id="editSong-<?= $id ?>" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50">
                            <div class="relative w-full max-w-md max-h-full mx-auto mt-24">
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                                    <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            Edit Song
                                        </h3>
                                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-hide="editSong-<?= $song['id'] ?>">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <form action="actions/editSong.php" enctype="multipart/form-data" method="post" class="p-4">
                                        <input type="hidden" name="song_id" value="<?= $id ?>">
                                        <input type="hidden" name="last_page" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">

                                        <div class="grid gap-4 mb-4 sm:grid-cols-">
                                            <div>
                                                <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Title</label>
                                                <input type="text" name="title" id="title" value="<?= $song['title'] ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required="">
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-center w-full">
                                            <label for="image" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                    <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                                    </svg>
                                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF (MAX. 400x400px)</p>
                                                </div>
                                                <input id="image" type="file" name="image" class="hidden">
                                            </label>
                                        </div>

                                        <div class="grid gap-4 mb-4 sm:grid-cols-1">
                                            <div>
                                                <label for="genre_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Genre</label>
                                                <select name="genre_id" id="genre_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                                                    <?php
                                                    $stmt = $db->query("SELECT id, name FROM genres ORDER BY name ASC");
                                                    $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                    foreach ($genres as $genre) {
                                                        $selected = ($genre['name'] === $song['genre_name']) ? 'selected' : '';
                                                        echo "<option value=\"{$genre['id']}\" $selected>{$genre['name']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid gap-4 mb-4 sm:grid-cols-1">
                                            <div>
                                                <label for="sub_genre_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Sub-Genre</label>
                                                <select name="sub_genre_id" id="sub_genre_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                                                    <?php
                                                    $stmt = $db->query("SELECT id, name FROM genres ORDER BY name ASC");
                                                    $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                    foreach ($genres as $genre) {
                                                        $selected = ($genre['name'] === $song['sub_genre_name']) ? 'selected' : '';
                                                        echo "<option value=\"{$genre['id']}\" $selected>{$genre['name']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid gap-4 mb-4 sm:grid-cols-1">
                                            <div>
                                                <input type="checkbox" name="is_explicit" id="is_explicit" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" <?= ($song['is_explicit'] === 'Y') ? 'checked' : '' ?>>
                                                <label for="is_explicit" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Is the song explicit</label>
                                            </div>
                                        </div>






                                        <div class="flex justify-end mt-4 gap-2">
                                            <button type="button" data-modal-hide="editSong-<?= $id ?>" class="text-gray-900 hover:text-white border border-gray-800 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-gray-600 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800">
                                                Cancel
                                            </button>
                                            <button type="submit" class="text-yellow-400 hover:text-white border border-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-yellow-300 dark:text-yellow-300 dark:hover:text-white dark:hover:bg-yellow-400 dark:focus:ring-yellow-900">
                                                Save Changes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- delete -->
                        <form action="actions/deleteSong.php" method="post" class="inline" onsubmit="return confirm('Are you sure you want to delete this song? THIS ACTION IS IRREVERSIBLE');">
                            <input type="hidden" name="song_id" value="<?= $id ?>">
                            <button class="p-2 border rounded-lg text-red-700 border-red-700 hover:bg-red-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-300 dark:text-red-500 dark:border-red-500 dark:hover:bg-red-600 dark:focus:ring-red-900">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                </svg>
                            </button>
                        </form>

                    <?php endif; ?>

                </div>

            </div>
        </li>
    </ul>

    <br>


    <form action="actions/comment.php" method="post">
        <div class="w-full mb-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">

            <input type="hidden" id="song_id" name="song_id" value="<?= $song['id'] ?>">
            <div class="px-4 py-2 bg-white rounded-t-lg dark:bg-gray-800">
                <label for="comment_text" class="sr-only">Your comment</label>
                <textarea id="comment_text" name="comment_text" rows="4" class="w-full px-0 text-sm text-gray-900 bg-white border-0 dark:bg-gray-800 focus:ring-0 dark:text-white dark:placeholder-gray-400" placeholder="Write a comment..." required></textarea>
            </div>
            <input type="hidden" id="last_page" name="last_page" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
            <div class="flex items-center justify-between px-3 py-2 border-t dark:border-gray-600 border-gray-200">
                <button type="submit" class="inline-flex items-center py-2.5 px-4 text-xs font-medium text-center text-white bg-blue-700 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:bg-blue-800">
                    Post comment
                </button>
            </div>
        </div>
    </form>

    <br>

    <ul class="space-y-4">
        <?php foreach ($comments as $comment): ?>
            <li class="flex flex-col md:flex-row items-start md:items-center bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                <div class="flex-1 ml-4 ">
                    <div class="flex items-center justify-between">
                        <!-- username -->
                        <a href="../user/userProfile.php?id=<?= $comment['id'] ?>"
                            class="flex items-center gap-2 truncate hover:underline">
                            <img class="h-8 w-8 rounded-lg" src="<?= $comment['profile_picture'] ?>" alt="profile" />
                            <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                <?= $comment['username'] ?>
                            </span>

                            <?php if ($comment['edited'] === 'Y'): ?>
                                <span class="text-sm text-gray-400">(edited)</span>
                            <?php endif; ?>
                        </a>

                        <!-- buttons -->
                        <?php if ($_SESSION['user_id'] === $comment['user_id']): ?>
                            <div class="flex items-center gap-2 ml-4">
                                <!-- edit -->
                                <button data-modal-target="editCommentModal-<?= $comment['comment_id'] ?>" data-modal-toggle="editCommentModal-<?= $comment['comment_id'] ?>" class="p-2 border rounded-lg text-yellow-600 border-yellow-600 hover:bg-yellow-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-yellow-300 dark:text-yellow-300 dark:border-yellow-300 dark:hover:bg-yellow-400 dark:focus:ring-yellow-900">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m14.3 4.8 2.9 2.9M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.4-9.9a2 2 0 0 1 0 2.85l-6.84 6.84L8 14l.71-3.56 6.84-6.84a2 2 0 0 1 2.85 0Z" />
                                    </svg>
                                </button>

                                <!-- edit modal -->
                                <div id="editCommentModal-<?= $comment['comment_id'] ?>" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50">
                                    <div class="relative w-full max-w-md max-h-full mx-auto mt-24">
                                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                                            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                                    Edit Comment
                                                </h3>
                                                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-hide="editCommentModal-<?= $comment['comment_id'] ?>">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <form action="actions/editComment.php" method="post" class="p-4">
                                                <input type="hidden" name="comment_id" value="<?= $comment['comment_id'] ?>">
                                                <input type="hidden" name="last_page" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                                                <textarea name="comment_text" rows="4" class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-yellow-500 focus:border-yellow-500"
                                                    required><?= htmlspecialchars($comment['comment_text']) ?></textarea>
                                                <div class="flex justify-end mt-4 gap-2">
                                                    <button type="button" data-modal-hide="editCommentModal-<?= $comment['comment_id'] ?>" class="text-gray-900 hover:text-white border border-gray-800 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-gray-600 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800">
                                                        Cancel
                                                    </button>
                                                    <button type="submit" class="text-yellow-400 hover:text-white border border-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-yellow-300 dark:text-yellow-300 dark:hover:text-white dark:hover:bg-yellow-400 dark:focus:ring-yellow-900">
                                                        Save Changes
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- delete -->
                                <form action="actions/deleteComment.php" method="post" class="inline" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                    <input type="hidden" name="comment_id" value="<?= $comment['comment_id'] ?>">
                                    <input type="hidden" name="last_page" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                                    <button class="p-2 border rounded-lg text-red-700 border-red-700 hover:bg-red-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-300 dark:text-red-500 dark:border-red-500 dark:hover:bg-red-600 dark:focus:ring-red-900">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                        </svg>
                                    </button>
                                </form>
                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- text -->
                    <div class="text-sm text-gray-500 dark:text-gray-400 break-all whitespace-normal">
                        <?= $comment['comment_text'] ?>
                    </div>

                </div>
            </li>
        <?php endforeach; ?>
    </ul>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

</body>