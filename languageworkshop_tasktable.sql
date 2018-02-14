-- phpMyAdmin SQL Dump
-- version 4.5.0.2
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Фев 14 2018 г., 18:46
-- Версия сервера: 5.6.26-log
-- Версия PHP: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `languageworkshop`
--

-- --------------------------------------------------------

--
-- Структура таблицы `tasktable_lessons`
--

CREATE TABLE `tasktable_lessons` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tasktable_permanent_exceptions`
--

CREATE TABLE `tasktable_permanent_exceptions` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tasktable_students`
--

CREATE TABLE `tasktable_students` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL DEFAULT '',
  `lastname` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tasktable_tasks`
--

CREATE TABLE `tasktable_tasks` (
  `id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `lesson` int(11) NOT NULL DEFAULT '0',
  `color` varchar(40) NOT NULL DEFAULT 'ffffff',
  `permanent` tinyint(2) NOT NULL DEFAULT '0',
  `permanent_id` int(11) NOT NULL DEFAULT '0',
  `start` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `permanent_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tasktable_task_students`
--

CREATE TABLE `tasktable_task_students` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL DEFAULT '0',
  `student_id` int(11) NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `tasktable_lessons`
--
ALTER TABLE `tasktable_lessons`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tasktable_permanent_exceptions`
--
ALTER TABLE `tasktable_permanent_exceptions`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tasktable_students`
--
ALTER TABLE `tasktable_students`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tasktable_tasks`
--
ALTER TABLE `tasktable_tasks`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tasktable_task_students`
--
ALTER TABLE `tasktable_task_students`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `tasktable_lessons`
--
ALTER TABLE `tasktable_lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `tasktable_permanent_exceptions`
--
ALTER TABLE `tasktable_permanent_exceptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `tasktable_students`
--
ALTER TABLE `tasktable_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `tasktable_tasks`
--
ALTER TABLE `tasktable_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `tasktable_task_students`
--
ALTER TABLE `tasktable_task_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
