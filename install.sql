
-- --------------------------------------------------------

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
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: 临时文件（临时文件有效期为24小时）， 1： 持久化文件',
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `fullname` (`fullname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
