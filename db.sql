-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 29, 2010 at 05:27 PM
-- Server version: 5.1.40
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `task`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_user_id` int(11) NOT NULL,
  `comment_file_id` int(11) NOT NULL,
  `comment_text` varchar(512) NOT NULL,
  `comment_date` datetime NOT NULL,
  `comment_left_key` int(11) NOT NULL,
  `comment_right_key` int(11) NOT NULL,
  `comment_level` int(11) NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `comment_left_key` (`comment_left_key`,`comment_right_key`,`comment_level`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `comments`
--


-- --------------------------------------------------------

--
-- Table structure for table `entry`
--

CREATE TABLE IF NOT EXISTS `entry` (
  `entry_id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_user_id` int(11) NOT NULL,
  `entry_date` datetime NOT NULL,
  `entry_ip` varchar(15) NOT NULL,
  `entry_agent` varchar(256) NOT NULL,
  `entry_hash` varchar(32) NOT NULL,
  PRIMARY KEY (`entry_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `entry`
--


-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_user_id` int(11) NOT NULL,
  `file_name` varchar(32) NOT NULL,
  `file_path` varchar(12) NOT NULL,
  `file_type` varchar(32) NOT NULL,
  `file_size` varchar(16) NOT NULL,
  `file_date` datetime NOT NULL,
  `file_access` enum('open','close') NOT NULL DEFAULT 'open',
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `files`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_email` varchar(128) NOT NULL,
  `user_password` varchar(32) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `users`
--

