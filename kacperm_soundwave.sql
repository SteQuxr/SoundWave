-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Cze 13, 2025 at 07:22 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kacperm_soundwave`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `community_comments`
--

CREATE TABLE `community_comments` (
  `id` int(16) NOT NULL,
  `post_id` int(16) NOT NULL,
  `user_id` int(16) NOT NULL,
  `text` text NOT NULL,
  `edited` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_comments`
--

INSERT INTO `community_comments` (`id`, `post_id`, `user_id`, `text`, `edited`) VALUES
(1, 1, 10, 'OK', 'N');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `community_posts`
--

CREATE TABLE `community_posts` (
  `id` int(16) NOT NULL,
  `user_id` int(16) NOT NULL,
  `type` int(16) NOT NULL DEFAULT 0,
  `title` varchar(64) NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_posts`
--

INSERT INTO `community_posts` (`id`, `user_id`, `type`, `title`, `text`) VALUES
(1, 2, 2, 'Community Rules', 'Please use you brain while making a post and be a good person :>');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `community_posts_types`
--

CREATE TABLE `community_posts_types` (
  `id` int(16) NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_posts_types`
--

INSERT INTO `community_posts_types` (`id`, `name`) VALUES
(1, 'General'),
(2, 'Updates'),
(3, 'Suggestion'),
(4, 'Question'),
(5, 'Discussion'),
(6, 'Feedback');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `genres`
--

CREATE TABLE `genres` (
  `id` int(16) NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`id`, `name`) VALUES
(2, 'Pop'),
(3, 'Rock'),
(4, 'Jazz'),
(5, 'Classical'),
(6, 'Hip-Hop'),
(7, 'R&B'),
(8, 'Country'),
(9, 'Electronic'),
(10, 'Reggae'),
(11, 'Blues'),
(12, 'Punk'),
(13, 'Metal'),
(14, 'Folk'),
(15, 'Indie'),
(16, 'Soul'),
(17, 'Latin'),
(18, 'Funk'),
(19, 'Gospel'),
(20, 'Disco'),
(21, 'Ambient');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `post_likes`
--

CREATE TABLE `post_likes` (
  `id` int(16) NOT NULL,
  `user_id` int(16) NOT NULL,
  `post_id` int(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `songs`
--

CREATE TABLE `songs` (
  `id` int(16) NOT NULL,
  `title` varchar(64) NOT NULL,
  `duration` int(11) NOT NULL,
  `artist_id` int(16) NOT NULL,
  `picture_path` varchar(64) NOT NULL DEFAULT 'images/default_song.png',
  `file_path` varchar(64) NOT NULL,
  `date_added` datetime NOT NULL,
  `is_explicit` enum('Y','N') NOT NULL,
  `genre_id` int(16) NOT NULL,
  `sub_genre_id` int(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `songs`
--

INSERT INTO `songs` (`id`, `title`, `duration`, `artist_id`, `picture_path`, `file_path`, `date_added`, `is_explicit`, `genre_id`, `sub_genre_id`) VALUES
(4, 'test', 135, 12, 'images/default_song.png', 'images/6813a6da7845a_aces to aces - memory lane.mp3', '2025-05-01 18:52:42', 'N', 10, 21),
(6, 'GOAT', 25, 12, 'images/681f62593f0d2_Zrzut ekranu 2025-01-03 195158.png', 'images/681f62593f0d5_ninjago season 5 intro.mp3', '2025-05-10 16:27:37', 'N', 9, 6),
(7, 'wakacyjny romans', 210, 12, 'images/6821dcdd1f79a_3.jpg', 'images/6821dcdd1f79e_skaner - wakacyjny romans.mp3', '2025-05-12 13:34:53', 'N', 9, 17),
(8, 'spadajaca gwiazda', 181, 12, 'images/6821dcfccd972_4.jpg', 'images/6821dcfccd976_zenek martyniuk - gwiazda.mp3', '2025-05-12 13:35:24', 'N', 20, 2),
(9, 'łoczy zielone', 265, 12, 'images/6821dd1a2503d_2.jpg', 'images/6821dd1a25040_zielone oczy.mp3', '2025-05-12 13:35:54', 'Y', 20, 2),
(10, 'golec orkiestra - sciernisko', 219, 12, 'images/6821dd342d970_5.jpg', 'images/6821dd342d973_golec orkiestra - sciernisko.mp3', '2025-05-12 13:36:20', 'Y', 3, 8),
(12, 'hot 16 challenge 2 naj', 129, 15, 'images/6821dfc1f01d7_1.jpg', 'images/6821dfc1f01db_hot16.mp3', '2025-05-12 13:47:14', 'N', 13, 20),
(13, 'red sun in the sky', 374, 15, 'images/6821dff31f469_6.jpg', 'images/6821dff31f476_red sun in the sky.mp3', '2025-05-12 13:48:03', 'Y', 8, 8),
(14, 'modjo - lady', 221, 16, 'images/682b6431d5d18_Zrzut ekranu 2025-02-28 181321.png', 'images/682b6431d5d1a_modjo - lady.mp3', '2025-05-19 19:02:42', 'N', 20, 10),
(15, 'what is love ', 267, 17, 'images/682b64ca0301b_Zrzut ekranu 2025-05-17 181121.png', 'images/682b64ca0301d_what is love (slowed + reverb).mp3', '2025-05-19 19:05:14', 'N', 5, 4),
(16, 'comic blast', 133, 17, 'images/682b65256850d_Zrzut ekranu 2025-01-03 195158.png', 'images/682b65256850f_stardust - comic blast.mp3', '2025-05-19 19:06:45', 'N', 3, 16),
(17, 'nothing breaks like a heart', 242, 18, 'images/682b65ccaf675_Zrzut ekranu 2024-12-25 165819.png', 'images/682b65ccaf678_nothing breaks like a heart (slowed).mp3', '2025-05-19 19:09:32', 'Y', 5, 2);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `songs_comments`
--

CREATE TABLE `songs_comments` (
  `id` int(16) NOT NULL,
  `user_id` int(16) NOT NULL,
  `song_id` int(16) NOT NULL,
  `comment_text` text NOT NULL,
  `edited` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `songs_comments`
--

INSERT INTO `songs_comments` (`id`, `user_id`, `song_id`, `comment_text`, `edited`) VALUES
(12, 12, 4, 'test22', 'N'),
(19, 15, 13, '我愛毛澤東和金正恩，誰跟誰笑都沒關係', 'N');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `songs_likes`
--

CREATE TABLE `songs_likes` (
  `id` int(16) NOT NULL,
  `user_id` int(16) NOT NULL,
  `song_id` int(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `songs_likes`
--

INSERT INTO `songs_likes` (`id`, `user_id`, `song_id`) VALUES
(3, 2, 4),
(33, 12, 6),
(36, 12, 9),
(37, 15, 13),
(39, 15, 9),
(43, 12, 13),
(45, 16, 13),
(46, 16, 9),
(47, 16, 6),
(48, 16, 14),
(49, 17, 14),
(50, 17, 15),
(51, 17, 16),
(52, 18, 15),
(53, 18, 14),
(54, 18, 17),
(55, 18, 9),
(56, 12, 15);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(16) NOT NULL,
  `username` varchar(32) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `date_of_birth` date NOT NULL,
  `profile_picture` varchar(64) NOT NULL DEFAULT 'images/default_pfp.avif',
  `date_joined` date NOT NULL,
  `login_tries` int(4) NOT NULL DEFAULT 3,
  `is_admin` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `date_of_birth`, `profile_picture`, `date_joined`, `login_tries`, `is_admin`) VALUES
(2, 'uzytkownik', 'kakaka@gmail.com', '$2y$10$nmpBOxecBfN8JYRxQs184.N/ndvB5u67TlRLib7fYipsgDii9J1kW', '2002-12-31', 'images/default_pfp.avif', '2025-03-26', 3, 0),
(9, 'wer', 'asdasd@gmail.com', '$2y$10$H1YnQ7P3WQX8.IWnIWdHleGHvpHra58N7xWFiYoOCtxqSyBWLNhSG', '2001-12-25', 'images/default_pfp.avif', '2025-03-27', 3, 0),
(10, 'Kacper', 'kampix@gmail.com', '$2y$10$sWDSym338hmhmMMbS52L.e1/mKmtS.wKr3qlkOvZAh.LW3GyIXzfe', '1999-06-25', 'images/default_pfp.avif', '2025-03-27', 0, 0),
(12, 'kamper', 'kacper@gmail.com', '$2y$10$C1IiBO.l4TUBlhgE8QvWUe2ie73yKFmH8MAltKb6qe8JTpeSWSSQW', '1989-05-26', 'images/default_pfp.avif', '2025-05-01', 0, 0),
(13, 'testerhasel', 'haslo@gmail.com', '$2y$10$LCGkRSpz3wmy37oeJs6iW.5L8S.eW.YlghgQdIdHASEG92mewf8sG', '1112-04-21', 'images/default_pfp.avif', '2025-05-09', 3, 0),
(14, 'admin', 'admin@gmail.com', '$2y$10$748v/BO/3yowiVWxApL/HuX4ZiqqBp5wEgI2KOU90U8I/zhldqNhG', '1234-12-12', 'images/default_pfp.avif', '2025-05-12', 3, 1),
(15, 'nowy gosc', 'nowy@gmail.com', '$2y$10$Wi4YvlUiYFb8yPMjOdb3yuXNo0NAq/A7RkaPD6mgRBtTterg9X5bC', '1996-02-16', 'images/default_pfp.avif', '2025-05-12', 3, 0),
(16, 'hee hee hee haw', 'heeheeheehaw@gmail.com', '$2y$10$BQLpRXTxUkRx8lT1t48kUe8FC6msK5CARicO9Z87tFpz9H83u8tzK', '1989-06-16', 'images/default_pfp.avif', '2025-05-19', 3, 0),
(17, 'typowy', 'gamer@gmail.com', '$2y$10$nPSDQNYFTGyB6sMph9ZcZOXPiGKdFogkm5XaQgdO83GtDUrmHkhoq', '2000-04-13', 'images/default_pfp.avif', '2025-05-19', 3, 0),
(18, 'tester', 'tester@gmail.com', '$2y$10$c2XeWP4W1LHThqbQm3XWhOabb77f4e0pMkdxy2F45113aLJSV9dSW', '1212-12-12', 'images/682b68bd63fdf_Zrzut ekranu 2025-01-03 195158.png', '2025-05-19', 2, 0),
(20, 'Asdasdasd', 'asdasdasd@gmail.com', '$2y$10$Vu0sDW7xHbraSG6iPNInXe0VN0dWPmmsICYI5lJxRCPb2O8gAC8Ri', '1122-12-12', 'images/default_pfp.avif', '2025-06-13', 3, 0);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `community_comments`
--
ALTER TABLE `community_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indeksy dla tabeli `community_posts`
--
ALTER TABLE `community_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `type` (`type`),
  ADD KEY `type_2` (`type`);

--
-- Indeksy dla tabeli `community_posts_types`
--
ALTER TABLE `community_posts_types`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `songs`
--
ALTER TABLE `songs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`artist_id`),
  ADD KEY `genre_id` (`genre_id`),
  ADD KEY `sub_genre_id` (`sub_genre_id`);

--
-- Indeksy dla tabeli `songs_comments`
--
ALTER TABLE `songs_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `track_id` (`song_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `songs_likes`
--
ALTER TABLE `songs_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `track_id` (`song_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `community_comments`
--
ALTER TABLE `community_comments`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `community_posts`
--
ALTER TABLE `community_posts`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `community_posts_types`
--
ALTER TABLE `community_posts_types`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `songs`
--
ALTER TABLE `songs`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `songs_comments`
--
ALTER TABLE `songs_comments`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `songs_likes`
--
ALTER TABLE `songs_likes`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `community_comments`
--
ALTER TABLE `community_comments`
  ADD CONSTRAINT `community_comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `community_comments_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `community_posts` (`id`);

--
-- Constraints for table `community_posts`
--
ALTER TABLE `community_posts`
  ADD CONSTRAINT `community_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `community_posts_ibfk_2` FOREIGN KEY (`type`) REFERENCES `community_posts_types` (`id`);

--
-- Constraints for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `community_posts` (`id`),
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `songs`
--
ALTER TABLE `songs`
  ADD CONSTRAINT `songs_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `songs_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`),
  ADD CONSTRAINT `songs_ibfk_3` FOREIGN KEY (`sub_genre_id`) REFERENCES `genres` (`id`);

--
-- Constraints for table `songs_comments`
--
ALTER TABLE `songs_comments`
  ADD CONSTRAINT `fk_songs_comments_song_id` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `songs_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `songs_likes`
--
ALTER TABLE `songs_likes`
  ADD CONSTRAINT `fk_songs_likes_song_id` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `songs_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
