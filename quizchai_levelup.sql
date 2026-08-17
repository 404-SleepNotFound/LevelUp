-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 15, 2026 at 01:17 AM
-- Server version: 10.11.14-MariaDB-0+deb12u2-log
-- PHP Version: 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quizchai_levelup`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_type` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_recommendations`
--

CREATE TABLE `ai_recommendations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recommendation_type` enum('summary','course') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `career`
--

CREATE TABLE `career` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'เชื่อมโยงกับแผนอาชีพ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `career`
--

INSERT INTO `career` (`id`, `course_id`, `name`) VALUES
(15, 38, 'Full Stack Developer'),
(17, 40, 'Full Stack Developer'),
(18, 41, 'Full Stack Developer'),
(19, 42, 'Full Stack Developer'),
(20, 43, 'Full Stack Developer'),
(21, 44, 'Full Stack Developer'),
(22, 45, 'Full Stack Developer'),
(24, 47, 'Full Stack Developer');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'หมวดหมู่ คอส',
  `icon` varchar(255) DEFAULT NULL,
  `color` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0 = ปิด | 1 = เปิด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `icon`, `color`, `status`) VALUES
(1, 'Dev', 'bi-code-slash', '#0d6efd', 1),
(2, 'การตลาดดิจิทัล', 'bi-megaphone', '#ffc107', 1),
(3, 'ดีไซน์ & กราฟิก', 'bi-palette', '#d63384', 1),
(4, 'ธุรกิจ & การจัดการ', 'bi-briefcase', '#198754', 1),
(5, 'ภาษาต่างประเทศ', 'bi-translate', '#0dcaf0', 1),
(6, 'ถ่ายภาพ & ตัดต่อวิดีโอ', 'bi-camera-reels', '#fd7e14', 1),
(7, 'วิทยาศาสตร์ข้อมูล & AI', 'bi-cpu', '#6f42c1', 1),
(8, 'การเงิน & การลงทุน', 'bi-graph-up-arrow', '#20c997', 1),
(9, 'พัฒนาตนเอง', 'bi-journal-bookmark-fill', '#6c757d', 1),
(10, 'ดนตรี & เสียง', 'bi-music-note-beamed', '#e83e8c', 1),
(11, 'เขียนโปรแกรม & พัฒนาเว็บ', 'bi-code-slash', '#0d6efd', 1);

-- --------------------------------------------------------

--
-- Table structure for table `chapters`
--

CREATE TABLE `chapters` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL COMMENT 'ชื่อ Chapter',
  `chapter_order` int(11) NOT NULL DEFAULT 1 COMMENT 'ลำดับ Chapter',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL COMMENT 'ชื่อคอส',
  `subtitle` varchar(255) DEFAULT NULL COMMENT 'ชื่อคอส ย่อ',
  `language` varchar(255) NOT NULL,
  `short_description` text DEFAULT NULL,
  `full_description` text DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL COMMENT 'รูป',
  `category_id` int(11) NOT NULL COMMENT 'หมวดหมู่',
  `difficulty` varchar(255) NOT NULL COMMENT 'ความยาก',
  `video` varchar(255) DEFAULT NULL COMMENT 'วิดิโอแนะนำ',
  `time_total` int(11) DEFAULT NULL COMMENT 'ชม. เรียนรวม',
  `chapter_total` int(11) DEFAULT NULL COMMENT 'จำนวน บท รวม',
  `time_week` int(11) DEFAULT NULL COMMENT 'ชม. เรียนต่อ week',
  `target` varchar(255) DEFAULT NULL,
  `type` int(11) NOT NULL COMMENT 'ประเภทคอร์สและการคิดเงิน',
  `visibility` varchar(255) NOT NULL COMMENT 'สถานะเปิดรับชม',
  `is_strict_flow` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'บังคับเรียนตามลำดับ (1=เปิด, 0=ปิด)',
  `certificate_type` varchar(255) NOT NULL COMMENT 'รูปการได้รับ ใบรับรอง',
  `seo_slug` varchar(255) DEFAULT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `icon_class` varchar(100) DEFAULT 'bi-filetype-html'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`id`, `user_id`, `title`, `subtitle`, `language`, `short_description`, `full_description`, `img`, `category_id`, `difficulty`, `video`, `time_total`, `chapter_total`, `time_week`, `target`, `type`, `visibility`, `is_strict_flow`, `certificate_type`, `seo_slug`, `seo_title`, `seo_description`, `price`, `created_at`, `updated_at`, `icon_class`) VALUES
(38, 24, 'สอนเเฮ็กเว็ป ม กรุงเทพ', 'สอนเเฮ็กเว็ป ม', 'th', 'จะสอนเเฮ็กเว็ป ม กรุงเทพ ยังไงให้ ม ไม่รู้', 'จะสอนเเฮ็กเว็ป ม กรุงเทพ ยังไงให้ ม ไม่รู้โดย เราจะทำการระเบิดเว็ป ม เเม่ง หล่อเท่ๆตึงๆโหดๆ ราชาเเห่งความมืด', 'uploads/covers/media_6a74a88c598fd8.02213793.jpeg', 1, 'Beginner', NULL, 200000000, 400000, 50000, 'ผู้เริ่มต้น / นักเรียน นักศึกษา', 0, 'Draft', 1, 'quiz', '', NULL, '', NULL, '2026-08-06 15:30:20', '2026-08-06 15:30:20', 'bi-filetype-html'),
(40, 25, 'ทดสอบ', 'ทดสอบ', 'th', 'คอร์สเร่งรัดและเจาะลึกวิชา ทดสอบ มุ่งเน้นการลงมือปฏิบัติจริงเพื่อเสริมทักษะระดับมืออาชีพ', 'คอร์สเร่งรัดและเจาะลึกวิชา ทดสอบ มุ่งเน้นการลงมือปฏิบัติจริงเพื่อเสริมทักษะระดับมืออาชีพ', NULL, 1, 'Beginner', NULL, 10, 5, 2, 'ผู้เริ่มต้น / นักเรียน นักศึกษา', 0, 'Draft', 1, 'quiz', 'คอร์สเร่งรัดและเจาะลึกวิชา ทดสอบ มุ่งเน้นการลงมือปฏิบัติจริงเพื่อเสริมทักษะระดับมืออาชีพ', NULL, 'คอร์สเร่งรัดและเจาะลึกวิชา ทดสอบ มุ่งเน้นการลงมือปฏิบัติจริงเพื่อเสริมทักษะระดับมืออาชีพ', NULL, '2026-08-06 15:43:14', '2026-08-06 15:43:14', 'bi-filetype-html'),
(41, 24, 'test', 'test', 'en', 'test', 'test', 'uploads/covers/media_6a74ace5ca5a39.12952628.png', 3, 'Beginner', NULL, 10, 5, 2, 'ผู้เริ่มต้น / นักเรียน นักศึกษา', 0, 'Draft', 1, 'quiz', 'test', NULL, 'test', NULL, '2026-08-06 15:48:53', '2026-08-06 17:04:16', 'bi-filetype-html'),
(42, 24, 'test', 'test', 'en', 'test', 'test', 'uploads/covers/media_6a74ae5ba85ff2.60876285.jpg', 1, 'Beginner', NULL, 5, 1, 5, 'ผู้เริ่มต้น / นักเรียน นักศึกษา', 0, 'Draft', 1, 'quiz', 'test', NULL, 'test', NULL, '2026-08-06 15:55:07', '2026-08-06 15:55:07', 'bi-filetype-html'),
(43, 24, 'test', 'test', 'th', 'test', 'test', 'uploads/covers/media_6a74b0475eef69.14151811.jpg', 1, 'Beginner', NULL, 50, 5, 10, 'ผู้เริ่มต้น / นักเรียน นักศึกษา', 0, 'Draft', 1, 'quiz', 'test', NULL, 'test', NULL, '2026-08-06 16:03:19', '2026-08-06 16:03:19', 'bi-filetype-html'),
(44, 25, 'Test Course', 'Test Course', 'th', 'คอร์สเร่งรัดและเจาะลึกวิชา Test Course มุ่งเน้นการลงมือปฏิบัติจริงเพื่อเสริมทักษะระดับมืออาชีพ', 'Test Course', 'uploads/covers/media_6a74b1b7cd7af9.40722417.png', 1, 'Beginner', NULL, 10, 5, 2, 'ผู้เริ่มต้น / นักเรียน นักศึกษา', 0, 'Draft', 1, 'quiz', 'Test Course', NULL, 'Test Course', NULL, '2026-08-06 16:09:27', '2026-08-06 16:09:27', 'bi-filetype-html'),
(45, 25, 'Test Course', 'Test Course', 'th', 'คอร์สเร่งรัดและเจาะลึกวิชา  Test Course มุ่งเน้นการลงมือปฏิบัติจริงเพื่อเสริมทักษะระดับมืออาชีพ', 'คอร์สเร่งรัดและเจาะลึกวิชา  Test Course มุ่งเน้นการลงมือปฏิบัติจริงเพื่อเสริมทักษะระดับมืออาชีพ', 'uploads/covers/media_6a74ba30c52736.22270559.png', 9, 'Beginner', 'https://youtu.be/dQw4w9WgXcQ?si=Dt9dBZRYZtfdk4o7', 10, 5, 2, 'ผู้เริ่มต้น / นักเรียน นักศึกษา', 0, 'Draft', 1, 'quiz', 'Test Course', NULL, 'คอร์สเร่งรัดและเจาะลึกวิชา ทดสอบ มุ่งเน้นการลงมือปฏิบัติจริงเพื่อเสริมทักษะระดับมืออาชีพ', '1500', '2026-08-06 16:45:36', '2026-08-06 16:55:39', 'bi-filetype-html'),
(47, 25, 'หายใจยังไงไม่ให้ตายห่า (The Art of Breathing 101)', 'หายใจยังไงไม่ให้ตายห่า', 'th', 'คอร์สสำหรับคนที่เกิดมาตั้งนาน แต่เสือกเพิ่งรู้ตัวว่าตัวเองหายใจแบบงูๆ ปลาๆ มาตลอด!', 'มึงเคยเป็นไหม? นั่งเหม่อๆ โง่ๆ อยู่หน้าคอมแล้วจู่ๆ ก็ \"เอ๊ะ กูหายใจยังวะ?\" หรือเวลาโดนทักว่า “ระวังนะ ตอนนี้มึงกำลังหายใจแบบแมนนวลอยู่” แล้วระบบประสาทมึงก็รวน รูจมูกพัง ลืมวิธีสูดลมหายใจไปดื้อๆ จนแทบจะขิตคาเก้าอี้!', 'uploads/covers/media_6a75a0514804b4.14302435.png', 9, 'Beginner', 'https://youtu.be/OT2CneeCmpw?si=MuGy7V36CknNNdF3', 5, 5, 1, 'ผู้เริ่มต้น / นักเรียน นักศึกษา', 0, 'Draft', 1, 'quiz', '?หายใจยังไงไม่ให้ตายห่า', NULL, 'คอร์ส \"หายใจยังไงไม่ให้ตายห่า\" สำหรับคนลืมวิธีสูดออกซิเจนจนเกือบขิต! สอนตั้งแต่สลับรูจมูกยันกลั้นหายใจหนีตดแฟน เรียนฟรีนะไอ้เวร รีบมาลงก่อนหมดลม!', NULL, '2026-08-07 09:07:29', '2026-08-07 09:24:48', 'bi-filetype-html');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `status` enum('in-progress','completed') NOT NULL DEFAULT 'in-progress',
  `progress` int(11) NOT NULL DEFAULT 0,
  `last_accessed` timestamp NULL DEFAULT current_timestamp(),
  `current_lesson_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_paths`
--

CREATE TABLE `learning_paths` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `learning_paths`
--

INSERT INTO `learning_paths` (`id`, `title`, `description`) VALUES
(1, 'Backend Developer Path', 'The complete path to becoming a backend developer.');

-- --------------------------------------------------------

--
-- Table structure for table `learning_path_steps`
--

CREATE TABLE `learning_path_steps` (
  `id` int(11) NOT NULL,
  `path_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `step_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `chapter_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` text DEFAULT NULL COMMENT 'คำอธิบายรายวิชาย่อย',
  `lesson_type` varchar(100) NOT NULL DEFAULT 'theory' COMMENT 'ประเภทบทเรียน (Theory Lecture, Lab Practical, ฯลฯ)',
  `duration_minutes` int(11) NOT NULL DEFAULT 15 COMMENT 'เวลาเรียนของบทนี้ (นาที)',
  `lesson_order` int(11) NOT NULL,
  `status` enum('draft','published','unpublished') NOT NULL DEFAULT 'draft' COMMENT 'สถานะบทเรียน',
  `content` longtext DEFAULT NULL COMMENT 'เนื้อหาหลักแบบ Rich Text / HTML',
  `video_url` varchar(255) DEFAULT NULL COMMENT 'ลิงก์ Embed วิดีโอภายนอก (YouTube/Vimeo)',
  `video_file` varchar(255) DEFAULT NULL COMMENT 'พาธไฟล์วิดีโอที่อัปโหลด',
  `thumbnail` varchar(255) DEFAULT NULL COMMENT 'รูปภาพหน้าปกวิดีโอ',
  `subtitle_file` varchar(255) DEFAULT NULL COMMENT 'ไฟล์คำบรรยาย (SRT/VTT)',
  `quiz_id` int(11) DEFAULT NULL COMMENT 'ID ชุดข้อสอบที่เชื่อมโยง',
  `lab_id` int(11) DEFAULT NULL COMMENT 'ID Coding Lab ที่เชื่อมโยง',
  `virtual_lab_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'การตั้งค่า Virtual Lab Sandbox (Docker, Linux, SQL, ฯลฯ)' CHECK (json_valid(`virtual_lab_config`)),
  `assignment_type` varchar(100) DEFAULT NULL COMMENT 'ประเภทการบ้านที่มอบหมาย',
  `assignment_instructions` text DEFAULT NULL COMMENT 'คำสั่งและเกณฑ์คะแนนการบ้าน',
  `learning_objectives` text DEFAULT NULL COMMENT 'เป้าหมายการเรียนรู้ (Learning Objectives)',
  `prerequisites` text DEFAULT NULL COMMENT 'บทเรียนตั้งต้นที่ต้องเรียนก่อน',
  `estimated_time` varchar(255) DEFAULT NULL COMMENT 'รายละเอียดเวลาเรียนรวม',
  `resources` text DEFAULT NULL COMMENT 'ลิงก์แนบภายนอก (GitHub / Docs)',
  `seo_slug` varchar(255) DEFAULT NULL COMMENT 'URL Slug',
  `seo_title` varchar(255) DEFAULT NULL COMMENT 'SEO Meta Title',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lesson_attachments`
--

CREATE TABLE `lesson_attachments` (
  `id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL COMMENT 'ชื่อไฟล์ดั้งเดิม',
  `file_path` varchar(255) NOT NULL COMMENT 'พาธจัดเก็บไฟล์ในเซิร์ฟเวอร์',
  `file_type` varchar(50) DEFAULT NULL COMMENT 'นามสกุล/ประเภทไฟล์ (PDF, ZIP, CODE, ฯลฯ)',
  `file_size` int(11) DEFAULT NULL COMMENT 'ขนาดไฟล์ (Bytes)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lesson_blocks`
--

CREATE TABLE `lesson_blocks` (
  `id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `block_type` varchar(50) NOT NULL COMMENT 'ประเภทบล็อก (text, video, code, quiz, lab)',
  `block_data` longtext DEFAULT NULL COMMENT 'เนื้อหาหรือการตั้งค่าของแต่ละบล็อก',
  `block_order` int(11) NOT NULL DEFAULT 1 COMMENT 'ลำดับการแสดงผลของบล็อก',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lesson_versions`
--

CREATE TABLE `lesson_versions` (
  `id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `version_number` varchar(20) NOT NULL DEFAULT 'v1.0',
  `snapshot_data` longtext NOT NULL COMMENT 'สแนปชอตข้อมูลบทเรียนทั้งหมดในรูปแบบ JSON',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meta_title`
--

CREATE TABLE `meta_title` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'SEO Meta Title'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `missions`
--

CREATE TABLE `missions` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `xp_reward` int(11) NOT NULL DEFAULT 0,
  `is_daily` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `missions`
--

INSERT INTO `missions` (`id`, `title`, `description`, `xp_reward`, `is_daily`) VALUES
(1, 'เรียนจบ 1 บทเรียน', NULL, 50, 1),
(2, 'ทำ Quiz ให้ผ่าน 80%', NULL, 75, 1),
(3, 'ทำ Coding Lab 1 ครั้ง', NULL, 100, 1);

-- --------------------------------------------------------

--
-- Table structure for table `outcomes`
--

CREATE TABLE `outcomes` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'สิ่งที่จะได้เรียนรู้ '
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `outcomes`
--

INSERT INTO `outcomes` (`id`, `course_id`, `name`) VALUES
(13, 38, 'เข้าใจแนวคิดและหลักการทำงานเบื้องต้นอย่างรายละเอียด'),
(15, 40, 'เข้าใจแนวคิดและหลักการทำงานเบื้องต้นอย่างรายละเอียด'),
(16, 41, 'เข้าใจแนวคิดและหลักการทำงานเบื้องต้นอย่างรายละเอียด'),
(17, 42, 'เข้าใจแนวคิดและหลักการทำงานเบื้องต้นอย่างรายละเอียด'),
(18, 43, 'เข้าใจแนวคิดและหลักการทำงานเบื้องต้นอย่างละเอียด'),
(19, 43, 'สร้างและพัฒนาโปรเจกต์จริงพร้อม Deploy ขึ้นระบบ Cloud ได้'),
(20, 44, 'เข้าใจแนวคิดและหลักการทำงานเบื้องต้นอย่างรายละเอียด'),
(21, 45, 'เข้าใจแนวคิดและหลักการทำงานเบื้องต้นอย่างรายละเอียด'),
(23, 47, 'เข้าใจแนวคิดและหลักการทำงานเบื้องต้นอย่างรายละเอียด');

-- --------------------------------------------------------

--
-- Table structure for table `prereq`
--

CREATE TABLE `prereq` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `name` int(255) NOT NULL COMMENT 'ความรู้พื้นฐานที่ต้องมีก่อน '
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `prereq`
--

INSERT INTO `prereq` (`id`, `course_id`, `name`) VALUES
(10, 38, 0),
(12, 40, 0),
(13, 41, 0),
(14, 42, 0),
(15, 43, 0),
(16, 44, 0),
(17, 45, 0),
(19, 47, 0);

-- --------------------------------------------------------

--
-- Table structure for table `saved_courses`
--

CREATE TABLE `saved_courses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saved_courses`
--

INSERT INTO `saved_courses` (`id`, `user_id`, `course_id`, `created_at`) VALUES
(1, 25, 45, '2026-08-14 14:55:49'),
(7, 26, 41, '2026-08-14 15:57:53');

-- --------------------------------------------------------

--
-- Table structure for table `status_users`
--

CREATE TABLE `status_users` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `level` int(11) NOT NULL DEFAULT 1,
  `exp` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `status_users`
--

INSERT INTO `status_users` (`id`, `user_id`, `level`, `exp`) VALUES
(20, 25, 1, 998),
(21, 26, 1, 0),
(22, 24, 1, 0),
(23, 27, 1, 0),
(24, 28, 1, 999);

--
-- Triggers `status_users`
--
DELIMITER $$
CREATE TRIGGER `levelup` BEFORE UPDATE ON `status_users` FOR EACH ROW BEGIN
    -- เช็กก่อนว่า EXP รวมแล้วเกิน 1000 หรือไม่
    IF NEW.exp >= 1000 THEN
        -- เพิ่ม Level ตามจำนวนรอบของ 1000 EXP ที่เกินมา
        SET NEW.level = NEW.level + FLOOR(NEW.exp / 1000);
        -- หัก EXP ออกทีละ 1000 ตามเลเวลที่เพิ่มขึ้น (เก็บเศษที่เหลือไว้)
        SET NEW.exp = NEW.exp % 1000;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Tags คอร์ส (ระบุข้อความแล้วกด Enter เพื่อเพิ่มคีย์เวิร์ด)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `google_id` varchar(255) NOT NULL,
  `github_id` varchar(255) DEFAULT NULL,
  `user_role` varchar(255) NOT NULL DEFAULT 'student',
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `img` text NOT NULL,
  `bio` varchar(255) DEFAULT NULL,
  `career_goal` varchar(255) DEFAULT 'Not Set',
  `create_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `google_id`, `github_id`, `user_role`, `name`, `email`, `img`, `bio`, `career_goal`, `create_at`) VALUES
(24, '114303844792512954505', NULL, 'student', 'Rahat Buranabenya', 'rahatburanabenya@gmail.com', 'https://lh3.googleusercontent.com/a/ACg8ocIrAKd7H4-v1OU101VpAUNhrNykfacdHHu-vqD-JB0LiMwjZqg=s96-c', NULL, 'Not Set', '2026-08-06 15:18:38'),
(25, '101185003714788298710', NULL, 'student', 'จิรายุส ติ๊บปะสอน', 'jirayostippason@gmail.com', 'https://lh3.googleusercontent.com/a/ACg8ocIalWg2fdEE1tejIFl3QEJhRj243ULxqeXgwuxJi_0zNmticXw=s96-c', '123', 'Not Set', '2026-08-06 15:21:29'),
(26, '105509850423976750043', NULL, 'student', 'JIRAYOS TIPPASON', 'jirayos.tipp@bumail.net', 'https://lh3.googleusercontent.com/a/ACg8ocK8UqE0AORIJvFUbNsXcdaarm_3OkhtQv_ibqScAyCvOaZIFg=s96-c', NULL, 'Not Set', '2026-08-06 17:23:56'),
(27, '104975818076476602777', NULL, 'student', 'RAHART BURANABENYA', 'rahart.bura@bumail.net', 'https://lh3.googleusercontent.com/a/ACg8ocLL-tM2SJcRNx9HgbzC8OgnJmPHSNCmL49kEQftfvqd7z450Q=s96-c', NULL, 'Not Set', '2026-08-07 01:42:16'),
(28, '102094635692835714222', '190042953', 'student', 'Jirayos Tippason', 'fritlovefrie0843788093@gmail.com', 'https://avatars.githubusercontent.com/u/190042953?v=4', NULL, 'Not Set', '2026-08-11 08:48:17');

-- --------------------------------------------------------

--
-- Table structure for table `user_learning_path`
--

CREATE TABLE `user_learning_path` (
  `user_id` int(11) NOT NULL,
  `path_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_missions`
--

CREATE TABLE `user_missions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mission_id` int(11) NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `progress_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ai_recommendations`
--
ALTER TABLE `ai_recommendations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `career`
--
ALTER TABLE `career`
  ADD PRIMARY KEY (`id`),
  ADD KEY `career_cours` (`course_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_chapters_course` (`course_id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cours_users` (`user_id`),
  ADD KEY `cours_category` (`category_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_course_enrollment` (`user_id`,`course_id`),
  ADD KEY `current_lesson_id` (`current_lesson_id`),
  ADD KEY `enrollments_ibfk_2` (`course_id`);

--
-- Indexes for table `learning_paths`
--
ALTER TABLE `learning_paths`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `learning_path_steps`
--
ALTER TABLE `learning_path_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `path_id` (`path_id`),
  ADD KEY `learning_path_steps_ibfk_2` (`course_id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lessons_ibfk_1` (`course_id`),
  ADD KEY `fk_lessons_chapter` (`chapter_id`);

--
-- Indexes for table `lesson_attachments`
--
ALTER TABLE `lesson_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_attachments_lesson` (`lesson_id`);

--
-- Indexes for table `lesson_blocks`
--
ALTER TABLE `lesson_blocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_blocks_lesson` (`lesson_id`);

--
-- Indexes for table `lesson_versions`
--
ALTER TABLE `lesson_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_versions_lesson` (`lesson_id`);

--
-- Indexes for table `meta_title`
--
ALTER TABLE `meta_title`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meta_title_cours` (`course_id`);

--
-- Indexes for table `missions`
--
ALTER TABLE `missions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `outcomes`
--
ALTER TABLE `outcomes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `outcomes_course` (`course_id`);

--
-- Indexes for table `prereq`
--
ALTER TABLE `prereq`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prereq_course_id` (`course_id`);

--
-- Indexes for table `saved_courses`
--
ALTER TABLE `saved_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_course` (`user_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `status_users`
--
ALTER TABLE `status_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status_users_users` (`user_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tags_cours` (`course_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_learning_path`
--
ALTER TABLE `user_learning_path`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `path_id` (`path_id`);

--
-- Indexes for table `user_missions`
--
ALTER TABLE `user_missions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_daily_mission` (`user_id`,`mission_id`,`progress_date`),
  ADD KEY `mission_id` (`mission_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_recommendations`
--
ALTER TABLE `ai_recommendations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `career`
--
ALTER TABLE `career`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `chapters`
--
ALTER TABLE `chapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `learning_paths`
--
ALTER TABLE `learning_paths`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `learning_path_steps`
--
ALTER TABLE `learning_path_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `lesson_attachments`
--
ALTER TABLE `lesson_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lesson_blocks`
--
ALTER TABLE `lesson_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lesson_versions`
--
ALTER TABLE `lesson_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meta_title`
--
ALTER TABLE `meta_title`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `missions`
--
ALTER TABLE `missions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `outcomes`
--
ALTER TABLE `outcomes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `prereq`
--
ALTER TABLE `prereq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `saved_courses`
--
ALTER TABLE `saved_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `status_users`
--
ALTER TABLE `status_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `user_missions`
--
ALTER TABLE `user_missions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_recommendations`
--
ALTER TABLE `ai_recommendations`
  ADD CONSTRAINT `ai_recommendations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chapters`
--
ALTER TABLE `chapters`
  ADD CONSTRAINT `fk_chapters_course` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `cours_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cours_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_3` FOREIGN KEY (`current_lesson_id`) REFERENCES `lessons` (`id`);

--
-- Constraints for table `learning_path_steps`
--
ALTER TABLE `learning_path_steps`
  ADD CONSTRAINT `learning_path_steps_ibfk_1` FOREIGN KEY (`path_id`) REFERENCES `learning_paths` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `learning_path_steps_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `fk_lessons_chapter` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson_attachments`
--
ALTER TABLE `lesson_attachments`
  ADD CONSTRAINT `fk_attachments_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lesson_blocks`
--
ALTER TABLE `lesson_blocks`
  ADD CONSTRAINT `fk_blocks_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lesson_versions`
--
ALTER TABLE `lesson_versions`
  ADD CONSTRAINT `fk_versions_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `saved_courses`
--
ALTER TABLE `saved_courses`
  ADD CONSTRAINT `saved_courses_course_fk` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_courses_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `status_users`
--
ALTER TABLE `status_users`
  ADD CONSTRAINT `status_users_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_learning_path`
--
ALTER TABLE `user_learning_path`
  ADD CONSTRAINT `user_learning_path_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_learning_path_ibfk_2` FOREIGN KEY (`path_id`) REFERENCES `learning_paths` (`id`);

--
-- Constraints for table `user_missions`
--
ALTER TABLE `user_missions`
  ADD CONSTRAINT `user_missions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_missions_ibfk_2` FOREIGN KEY (`mission_id`) REFERENCES `missions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
