CREATE DATABASE IF NOT EXISTS `ws_storage` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
-- --------------------------------------------------------
USE ws_storage;
--
-- Table structure for table `app`
--

CREATE TABLE `app` (
  `id` char(8) NOT NULL,
  `secret` char(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE `file` (
  `id` char(36) NOT NULL,
  `app_id` char(8) NOT NULL,
  `path` varchar(255) NOT NULL,
  `size` bigint(20) NOT NULL,
  `ext` varchar(10) NOT NULL,
  `md5` char(32) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `app_id` (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `file_thumb`
--

CREATE TABLE `file_thumb` (
  `id` char(36) NOT NULL,
  `fullname` varchar(500) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
