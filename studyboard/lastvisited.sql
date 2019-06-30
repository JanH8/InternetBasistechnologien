-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 27. Jun 2019 um 21:10
-- Server-Version: 10.3.16-MariaDB
-- PHP-Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `studyboard_db`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lastvisited`
--

CREATE TABLE `lastvisited` (
  `tstmp` int(10) NOT NULL,
  `forumId` int(10) UNSIGNED NOT NULL,
  `student` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `lastvisited`
--
ALTER TABLE `lastvisited`
  ADD PRIMARY KEY (`forumId`,`student`);

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `lastvisited`
--
ALTER TABLE `lastvisited`
  ADD CONSTRAINT `lastvisited_ibfk_1` FOREIGN KEY (`forumId`) REFERENCES `forum` (`forumId`),
  ADD CONSTRAINT `lastvisited_ibfk_2` FOREIGN KEY (`student`) REFERENCES `student` (`studentId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
