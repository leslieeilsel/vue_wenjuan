-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2021-04-01 12:37:06
-- 服务器版本： 5.7.26
-- PHP 版本： 7.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `testdb`
--

-- --------------------------------------------------------

--
-- 表的结构 `fb_options`
--

CREATE TABLE `fb_options` (
  `id` int(11) NOT NULL COMMENT '选项id',
  `title` varchar(900) NOT NULL COMMENT '选项标题',
  `personCount` smallint(6) NOT NULL DEFAULT '0' COMMENT '选项选择人数',
  `content` text COMMENT '简答内容(&$%分割)',
  `w_order` tinyint(4) NOT NULL DEFAULT '0' COMMENT '选项顺序(1234...)',
  `status` varchar(1) NOT NULL DEFAULT '1' COMMENT '选项状态(1 启用 0禁用)',
  `qid` text NOT NULL COMMENT '所属问题id',
  `user_id` text NOT NULL COMMENT '选项所属用户'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `fb_options`
--

INSERT INTO `fb_options` (`id`, `title`, `personCount`, `content`, `w_order`, `status`, `qid`, `user_id`) VALUES
(20, '1111111111111111111', 0, NULL, 0, '1', '630', '44'),
(19, '111', 0, NULL, 0, '1', '629', '44'),
(18, '111', 0, '', 0, '1', '628', '44'),
(17, '111', 0, '', 0, '1', '628', '44'),
(21, '1111111', 0, '', 0, '0', '631', '44'),
(22, '111111111111111', 0, '', 0, '0', '631', '44'),
(23, '111111111111111111', 0, NULL, 0, '1', '632', '44'),
(24, '', 0, NULL, 0, '1', '633', '44'),
(25, '1-10', 0, '', 0, '0', '634', '44'),
(26, '10-20', 0, '', 0, '0', '634', '44'),
(27, '10-20', 1, '', 0, '1', '634', '44'),
(28, '30-50', 1, '', 0, '1', '634', '44'),
(29, '50+', 0, '', 0, '0', '634', '44'),
(30, '骑车', 1, '', 0, '1', '635', '44'),
(31, '30-50', 0, '', 0, '0', '635', '44'),
(32, '20-30', 0, '', 0, '0', '635', '44'),
(33, '10-20', 0, '', 0, '0', '635', '44'),
(34, '1-10', 0, '', 0, '0', '635', '44'),
(35, '新加', 0, '', 0, '0', '635', '44'),
(36, '新加2', 0, '', 0, '0', '635', '44'),
(37, '1111111111', 0, '', 0, '1', '636', '44'),
(38, '1111', 0, '', 0, '1', '637', '44'),
(39, '11111', 0, '', 0, '1', '637', '44'),
(40, '1111', 0, '', 0, '0', '626', '44'),
(41, '22222', 0, '', 0, '1', '626', '44'),
(42, '', 0, NULL, 0, '1', '638', '44'),
(43, '打球', 2, '', 0, '1', '635', '44'),
(44, '游泳', 1, '', 0, '1', '635', '44');

-- --------------------------------------------------------

--
-- 表的结构 `fb_questions`
--

CREATE TABLE `fb_questions` (
  `id` int(11) NOT NULL COMMENT '问题id',
  `wenjuan_id` text NOT NULL COMMENT '从属问卷加密id',
  `qtype` varchar(1) NOT NULL COMMENT '问题类型(1 单选 2 多选 3简答)',
  `qtitle` varchar(900) NOT NULL COMMENT '问题标题(<=255)',
  `status` varchar(1) NOT NULL DEFAULT '1' COMMENT '问题状态(1 启用 0 禁用)',
  `w_order` smallint(6) DEFAULT '0' COMMENT '问题顺序(1234...)',
  `user_id` text NOT NULL COMMENT '问题所属用户',
  `mustbe` varchar(1) NOT NULL DEFAULT '1' COMMENT '问题必选(1 必选 0可选)',
  `personCount` smallint(6) NOT NULL DEFAULT '0' COMMENT '问题填报人数',
  `content` mediumtext COMMENT '简答内容(&$%分割)'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `fb_questions`
--

INSERT INTO `fb_questions` (`id`, `wenjuan_id`, `qtype`, `qtitle`, `status`, `w_order`, `user_id`, `mustbe`, `personCount`, `content`) VALUES
(617, '66e5cb562721d32b8594ee2a161c9184', '1', '问题标题', '1', 0, '41', '1', 0, NULL),
(618, '', '1', '11111111', '1', 0, '44', '1', 0, NULL),
(619, '', '2', '222', '1', 0, '44', '1', 0, NULL),
(620, '', '2', '11', '1', 0, '44', '1', 0, NULL),
(621, '', '2', '111', '1', 0, '44', '1', 0, NULL),
(622, '', '2', '111', '1', 0, '44', '1', 0, NULL),
(623, '', '2', '111', '1', 0, '44', '1', 0, NULL),
(624, '', '2', '1', '1', 0, '44', '1', 0, NULL),
(625, '', '2', '1', '1', 0, '44', '1', 0, NULL),
(626, 'd8c790f19ecfa273e3b4ecb2da0669cd', '2', '1', '1', 0, '44', '1', 0, ''),
(627, 'd8c790f19ecfa273e3b4ecb2da0669cd', '2', '1', '1', 0, '44', '1', 0, ''),
(628, 'd8c790f19ecfa273e3b4ecb2da0669cd', '2', '1', '1', 0, '44', '1', 0, ''),
(629, '6f0266a1920a043ce35c51b5c22f8d30', '2', '11', '1', 0, '44', '1', 0, NULL),
(630, '6f0266a1920a043ce35c51b5c22f8d30', '2', '11111', '1', 0, '44', '1', 0, NULL),
(631, '2166f80524d9b51ac5aa69e204d5ac3d', '1', '1111', '0', 0, '44', '1', 0, ''),
(632, '0f41f93cf21182a88081791fad2176b9', '2', '1', '1', 0, '44', '1', 0, NULL),
(633, '6f0266a1920a043ce35c51b5c22f8d30', '3', 'made', '1', 0, '44', '1', 0, NULL),
(634, '2166f80524d9b51ac5aa69e204d5ac3d', '1', '年龄', '1', 0, '44', '0', 2, ''),
(635, '2166f80524d9b51ac5aa69e204d5ac3d', '2', '爱好', '1', 0, '44', '1', 2, ''),
(636, '2166f80524d9b51ac5aa69e204d5ac3d', '1', '1', '0', 0, '44', '1', 0, ''),
(637, '2166f80524d9b51ac5aa69e204d5ac3d', '3', '个人简介', '1', 0, '44', '1', 2, '杨凌云&$%王洋&$%'),
(638, '8171423ec945cd249e56ff953a62667a', '3', '问卷删除(1 正常 0删除)', '1', 0, '44', '1', 0, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `fb_users`
--

CREATE TABLE `fb_users` (
  `id` int(11) NOT NULL COMMENT '用户id',
  `username` varchar(20) NOT NULL COMMENT '用户账号(<=20)',
  `password` char(32) NOT NULL COMMENT '用户密码(md5加密)',
  `displayname` varchar(20) NOT NULL COMMENT '用户昵称(<=20)',
  `tel` varchar(20) NOT NULL COMMENT '用户电话(<=20)',
  `last_login_ip` char(15) DEFAULT NULL COMMENT '用户最后登录ip',
  `last_login_time` varchar(30) DEFAULT NULL COMMENT '用户最后登录时间',
  `login_times` smallint(11) NOT NULL DEFAULT '0' COMMENT '用户登录总次数',
  `status` varchar(1) NOT NULL DEFAULT '1' COMMENT '用户状态(1 启用，0 禁用)',
  `role` varchar(1) NOT NULL DEFAULT '0' COMMENT '用户角色(1 管理员，0 普通用户)',
  `addate` varchar(30) NOT NULL COMMENT '用户加入时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `fb_users`
--

INSERT INTO `fb_users` (`id`, `username`, `password`, `displayname`, `tel`, `last_login_ip`, `last_login_time`, `login_times`, `status`, `role`, `addate`) VALUES
(41, '12345678', '25d55ad283aa400af464c76d713c07ad', '问卷用户12345678', '0', '127.0.0.1', '1617083673', 6, '1', '0', '1616679672'),
(42, '12345678911111', '25d55ad283aa400af464c76d713c07ad', 'test123456', '0', '127.0.0.1', '1616679878', 0, '1', '0', '1616679878'),
(43, '123456789111111', '4781b44e99f1bcf00ca3b3911361d3fe', '问卷用户123456789111111', '0', '127.0.0.1', '1616679883', 0, '1', '0', '1616679883'),
(44, '123456789', '25f9e794323b453885f5181f1b624d0b', '问卷用户123456789', '0', '127.0.0.1', '1617065082', 16, '1', '0', '1616846311');

-- --------------------------------------------------------

--
-- 表的结构 `fb_wenjuans`
--

CREATE TABLE `fb_wenjuans` (
  `id` int(11) NOT NULL COMMENT '问卷真实id',
  `user_id` text NOT NULL COMMENT '问卷发布者id',
  `wenjuan_id` text NOT NULL COMMENT '问卷加密id(防止恶意填写)',
  `intro` varchar(900) NOT NULL COMMENT '问卷标题(<=255汉字)',
  `title` varchar(600) NOT NULL COMMENT '问卷标题(<=140汉字)',
  `timeCreated` varchar(30) NOT NULL COMMENT '问卷创建时间(秒)',
  `timeEnd` varchar(30) NOT NULL COMMENT '问卷结束时间(秒)',
  `personCount` int(11) NOT NULL DEFAULT '0' COMMENT '问卷有效填报人数',
  `totalPerson` int(11) NOT NULL DEFAULT '100' COMMENT '问卷期望填报人数',
  `status` varchar(1) NOT NULL DEFAULT '0' COMMENT '问卷启用状态(1 启用 0禁用)',
  `code` char(4) NOT NULL COMMENT '私密问卷提取码(4位数字+字母)',
  `provided` text NOT NULL COMMENT '已经填报userid(,分割)',
  `mustbe` varchar(1) NOT NULL DEFAULT '1' COMMENT '是否必选(1 必选，0 可选)',
  `enabled` varchar(1) DEFAULT '1' COMMENT '问卷删除(1 正常 0删除)',
  `mustLogin` varchar(1) NOT NULL DEFAULT '1' COMMENT '是否必须登录才填报(1 是 0 否)'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='问卷标';

--
-- 转存表中的数据 `fb_wenjuans`
--

INSERT INTO `fb_wenjuans` (`id`, `user_id`, `wenjuan_id`, `intro`, `title`, `timeCreated`, `timeEnd`, `personCount`, `totalPerson`, `status`, `code`, `provided`, `mustbe`, `enabled`, `mustLogin`) VALUES
(196, '41', '66e5cb562721d32b8594ee2a161c9184', '简介', '关于问卷的问卷', '1616749521', '1577583758', 0, 100, '1', '简介', '41,', '1', '1', '1'),
(197, '41', '66e5cb562721d32b8594ee2a161c9184', '简介', '关于问卷的问卷', '1616749521', '1577583758', 0, 100, '1', '简介', '41,', '1', '1', '1'),
(198, '44', '0f41f93cf21182a88081791fad2176b9', ' 健康几乎已经成为我们最关心的话题，自然而然，食品安全也就成为我们最关注的热点之一了。在此，十分感谢您在百忙之中抽出时间来填写这份关于食品安全的调查问卷，做这份调查问卷是为了了解您对于食品安全信息的需求程度和遇到的问题，从而了解您的需要，\r\n\r\n为写实践报告提供数据。希望您如实填写，非常感谢您的合作！', '关于的问卷', '1616909908', '1617255507343', 0, 50, '0', '1000', '44,', '1', '1', '1'),
(199, '44', '6f0266a1920a043ce35c51b5c22f8d30', '2166f80524d9b51ac5aa69e204d5ac3d\", 3: \"感谢您能抽时间参与本次问卷，您的意见和建', '去1111111111111', '1616910104', '1617023186700', 0, 5000, '1', '1005', '44,', '1', '1', '1'),
(200, '44', 'd8c790f19ecfa273e3b4ecb2da0669cd', '感谢您能抽时间参与本次问卷，您的意见和建议就是我们前行的动力！', 'questions', '1616910134', '1617082902471', 0, 52, '1', '4865', '44,', '1', '1', '1'),
(201, '44', '2166f80524d9b51ac5aa69e204d5ac3d', '感谢您能抽时间参与本次问卷，您的意见和建议就是我们前行的动力！', '关于个人信息填写', '1616910585', '1617291557277', 2, 5555, '1', '0', '44,41,,', '1', '1', '0'),
(202, '44', '8171423ec945cd249e56ff953a62667a', '无密码无密码111', '无密1111111111', '1616933358', '1617282307925', 0, 504, '1', '0', '44,', '1', '1', '0'),
(203, '44', 'ba3c49cd29db163bbcaffd178bd481f4', 'testtest', 'test', '1617076638162', '1617422233621', 0, 5, '0', '0', '44,', '1', '1', '1');

--
-- 转储表的索引
--

--
-- 表的索引 `fb_options`
--
ALTER TABLE `fb_options`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `fb_questions`
--
ALTER TABLE `fb_questions`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `fb_users`
--
ALTER TABLE `fb_users`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `fb_wenjuans`
--
ALTER TABLE `fb_wenjuans`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `fb_options`
--
ALTER TABLE `fb_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '选项id', AUTO_INCREMENT=45;

--
-- 使用表AUTO_INCREMENT `fb_questions`
--
ALTER TABLE `fb_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '问题id', AUTO_INCREMENT=639;

--
-- 使用表AUTO_INCREMENT `fb_users`
--
ALTER TABLE `fb_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id', AUTO_INCREMENT=45;

--
-- 使用表AUTO_INCREMENT `fb_wenjuans`
--
ALTER TABLE `fb_wenjuans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '问卷真实id', AUTO_INCREMENT=204;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
