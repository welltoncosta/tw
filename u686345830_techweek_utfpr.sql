-- phpMyAdmin SQL Dump
-- version 5.2.2deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 06, 2025 at 07:35 PM
-- Server version: 11.8.3-MariaDB-1+b1 from Debian
-- PHP Version: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u686345830_techweek_utfpr`
--

-- --------------------------------------------------------

--
-- Table structure for table `atividades`
--

CREATE TABLE `atividades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `palestrante` varchar(1000) DEFAULT NULL,
  `singular_plural` varchar(40) DEFAULT NULL,
  `sala` varchar(100) DEFAULT NULL,
  `vagas` varchar(100) DEFAULT NULL,
  `data` varchar(100) DEFAULT NULL,
  `horario` varchar(100) DEFAULT NULL,
  `ativa` varchar(10) DEFAULT NULL,
  `hora_inicio` varchar(30) DEFAULT NULL,
  `hora_fim` varchar(30) DEFAULT NULL,
  `hash` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `atividades`
--

INSERT INTO `atividades` (`id`, `titulo`, `tipo`, `palestrante`, `singular_plural`, `sala`, `vagas`, `data`, `horario`, `ativa`, `hora_inicio`, `hora_fim`, `hash`) VALUES
(1, 'Palestra Magna de Abertura', 'credenciamento', 'Indefinido', NULL, 'A definir', '300', '2025-10-28', '19:00 - 20:00', '0', '19:00', '20:00', NULL),
(2, 'Palestra DEPEN Encerramento', 'palestra', 'Tayoná Cristina Gomes', NULL, 'Teatro Espaço da Arte', '300', '2025-10-31', NULL, '0', '19:00', '20:00', NULL),
(3, 'Robótica Bolsistas Michel', 'credenciamento', 'Manuela e Alexia', NULL, 'ACEFB - Associação Empresarial de Francisco Beltrão', '20', '2025-10-29', '19:00 - 20:00', '0', '19:00', '20:00', NULL),
(4, 'Impressão 3D e Prototipagem', 'oficina', 'Prof Lucas Bernadon', NULL, 'ACEFB', '30', '2025-10-29', NULL, '0', '19:00', '20:00', NULL),
(5, 'Interpretação de Imagem', 'oficina', 'Prof Flavio de Almeida', NULL, 'ACEFB', '30', '2025-10-29', NULL, '0', '19:00', '20:00', NULL),
(6, 'Github pra gestão de projetos', 'oficina', 'Prof Michel Albonico', NULL, 'ACEFB', '30', '2025-10-29', NULL, '0', '19:00', '20:00', NULL),
(7, 'Ruby & Rails', 'credenciamento', 'Gustavo Slomski', NULL, 'ACEFB', '30', '2025-10-29', '19:00 - 20:00', '0', '19:00', '20:00', NULL),
(8, 'Eletronica e Instrumentação', 'oficina', 'Eletronica e Instrumentação', NULL, 'ACEFB', '30', '2025-10-29', NULL, '0', '19:00', '20:00', NULL),
(9, 'Game Dev', 'oficina', 'Prof Jeconias Guimarães', NULL, 'ACEFB', '30', '2025-10-29', NULL, '0', '19:00', '19:00', NULL),
(10, 'Front-end Básico com ReactJS', 'credenciamento', 'Roger Kobs', NULL, 'UTFPR-FB', '30', '2025-10-28', '19:30 - 21:00', '0', '19:30', '21:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `backups`
--

CREATE TABLE `backups` (
  `id` int(11) NOT NULL,
  `nome_arquivo` varchar(255) NOT NULL,
  `tipo` enum('database','arquivos','completo') NOT NULL,
  `tamanho` varchar(20) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `caminho_arquivo` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categorias_transacoes`
--

CREATE TABLE `categorias_transacoes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` enum('entrada','saida') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `categorias_transacoes`
--

INSERT INTO `categorias_transacoes` (`id`, `nome`, `tipo`) VALUES
(1, 'Inscrições', 'entrada'),
(2, 'Patrocínios', 'entrada'),
(3, 'Passagens', 'saida'),
(4, 'Alimentação', 'saida'),
(5, 'Cortesias', 'saida'),
(6, 'Vouchers', 'saida'),
(7, 'Coffee Break', 'saida'),
(8, 'Outros', 'entrada'),
(9, 'Outros', 'saida');

-- --------------------------------------------------------

--
-- Table structure for table `comprovantes`
--

CREATE TABLE `comprovantes` (
  `id` int(11) NOT NULL,
  `participante_id` int(11) NOT NULL,
  `arquivo` varchar(255) NOT NULL,
  `tipo_arquivo` enum('pdf','imagem') NOT NULL,
  `status` enum('pendente','aprovado','rejeitado','excluido') DEFAULT 'pendente',
  `observacao` text DEFAULT NULL,
  `data_envio` timestamp NULL DEFAULT current_timestamp(),
  `data_avaliacao` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `comprovantes`
--

INSERT INTO `comprovantes` (`id`, `participante_id`, `arquivo`, `tipo_arquivo`, `status`, `observacao`, `data_envio`, `data_avaliacao`) VALUES
(1, 5, 'comprovantes/68b379f75aec29.12293003.png', 'imagem', 'aprovado', NULL, '2025-08-30 22:23:51', '2025-08-30 22:28:47'),
(2, 6, 'comprovantes/68b37a97e87994.24030957.jpeg', 'imagem', 'aprovado', NULL, '2025-08-30 22:26:31', '2025-08-30 22:28:42'),
(3, 7, 'comprovantes/68b37ad6da6ea6.87415999.jpg', 'imagem', 'aprovado', NULL, '2025-08-30 22:27:34', '2025-08-30 22:28:40'),
(4, 9, 'comprovantes/68b37bbc154531.87705732.jpeg', 'imagem', 'aprovado', NULL, '2025-08-30 22:31:24', '2025-08-30 22:33:37'),
(5, 10, 'comprovantes/68b37c935e4a53.42920215.pdf', 'pdf', 'aprovado', NULL, '2025-08-30 22:34:59', '2025-08-30 22:37:12'),
(6, 11, 'comprovantes/68b37cb853d7f5.54002364.pdf', 'pdf', 'aprovado', NULL, '2025-08-30 22:35:36', '2025-08-30 22:35:57'),
(7, 12, 'comprovantes/68b37d1e6a2212.34143695.pdf', 'pdf', 'aprovado', NULL, '2025-08-30 22:37:18', '2025-08-30 22:37:23'),
(8, 8, 'comprovantes/68b37d87d7b083.76795102.png', 'imagem', 'aprovado', NULL, '2025-08-30 22:39:03', '2025-08-30 22:39:36'),
(9, 14, 'comprovantes/68b37d8dbbef67.89965452.png', 'imagem', 'aprovado', NULL, '2025-08-30 22:39:09', '2025-08-30 22:39:40'),
(10, 13, 'comprovantes/68b37d9de69750.45323740.png', 'imagem', 'aprovado', NULL, '2025-08-30 22:39:25', '2025-08-30 22:41:15'),
(11, 15, 'comprovantes/68b37e27b1a6a4.89148723.pdf', 'pdf', 'aprovado', NULL, '2025-08-30 22:41:43', '2025-08-30 22:44:09'),
(12, 16, 'comprovantes/68b37ed6d5c007.35738478.jpg', 'imagem', 'aprovado', NULL, '2025-08-30 22:44:38', '2025-08-30 22:45:00'),
(13, 18, 'comprovantes/68b37f139b7642.31751676.jpg', 'imagem', 'aprovado', NULL, '2025-08-30 22:45:39', '2025-08-30 22:48:10'),
(14, 20, 'comprovantes/68b380ca7eb3b1.71160662.pdf', 'pdf', 'aprovado', NULL, '2025-08-30 22:52:58', '2025-08-30 22:54:25'),
(15, 23, 'comprovantes/68b382323cccd6.38386159.pdf', 'pdf', 'aprovado', NULL, '2025-08-30 22:58:58', '2025-08-30 23:08:01'),
(16, 21, 'comprovantes/68b3823b3ddea3.70636572.jpg', 'imagem', 'aprovado', NULL, '2025-08-30 22:59:07', '2025-08-30 23:07:36'),
(17, 24, 'comprovantes/68b3867bd958b8.70840692.pdf', 'pdf', 'aprovado', NULL, '2025-08-30 23:17:15', '2025-08-30 23:21:13'),
(18, 17, 'comprovantes/68b389a2f3be90.24460371.png', 'imagem', 'aprovado', NULL, '2025-08-30 23:30:42', '2025-08-30 23:37:29'),
(19, 27, 'comprovantes/68b38a8aa1a188.79859824.jpg', 'imagem', 'aprovado', NULL, '2025-08-30 23:34:34', '2025-08-30 23:37:27'),
(20, 26, 'comprovantes/68b39da8a98465.69893700.pdf', 'pdf', 'aprovado', NULL, '2025-08-31 00:56:08', '2025-08-31 11:24:49'),
(21, 22, 'comprovantes/68b3a0b3c84084.19363239.jpg', 'imagem', 'aprovado', NULL, '2025-08-31 01:09:07', '2025-08-31 11:21:21'),
(22, 28, 'comprovantes/68b3a75df131a0.10435600.jpg', 'imagem', 'aprovado', NULL, '2025-08-31 01:37:33', '2025-08-31 11:20:29'),
(23, 29, 'comprovantes/68b3ab465a4ee9.21485437.jpeg', 'imagem', 'aprovado', NULL, '2025-08-31 01:54:14', '2025-08-31 11:22:05'),
(24, 31, 'comprovantes/68b3b5d616c924.67471727.png', 'imagem', 'aprovado', NULL, '2025-08-31 02:39:18', '2025-08-31 11:20:16'),
(25, 32, 'comprovantes/68b3b6ee310eb2.31359172.jpeg', 'imagem', 'aprovado', NULL, '2025-08-31 02:43:58', '2025-08-31 11:20:06'),
(26, 33, 'comprovantes/68b3b786cc8e37.40022298.jpg', 'imagem', 'aprovado', NULL, '2025-08-31 02:46:30', '2025-08-31 11:22:28'),
(27, 34, 'comprovantes/68b3ca5b147684.18560674.jpg', 'imagem', 'aprovado', NULL, '2025-08-31 04:06:51', '2025-08-31 11:19:29'),
(28, 30, 'comprovantes/68b3cdfa1d5350.42524054.jpg', 'imagem', 'aprovado', NULL, '2025-08-31 04:22:18', '2025-08-31 11:19:24'),
(29, 35, 'comprovantes/68b4323856c387.22151267.pdf', 'pdf', 'aprovado', NULL, '2025-08-31 11:30:00', '2025-08-31 13:35:07'),
(30, 36, 'comprovantes/68b4352c1a7323.00633709.pdf', 'pdf', 'aprovado', NULL, '2025-08-31 11:42:36', '2025-08-31 13:35:05'),
(31, 38, 'comprovantes/68b44ecf77f3f6.17916413.jpeg', 'imagem', 'aprovado', NULL, '2025-08-31 13:31:59', '2025-08-31 13:35:03'),
(32, 39, 'comprovantes/68b45a03417bc4.53772031.jpg', 'imagem', 'aprovado', NULL, '2025-08-31 14:19:47', '2025-08-31 14:22:41'),
(33, 40, 'comprovantes/68b45cf83a8530.42352364.jpg', 'imagem', 'aprovado', NULL, '2025-08-31 14:32:24', '2025-08-31 16:18:16'),
(34, 41, 'comprovantes/68b46d2db1c323.91880878.jpg', 'imagem', 'aprovado', NULL, '2025-08-31 15:41:33', '2025-08-31 16:17:29'),
(35, 42, 'comprovantes/68b474051860e5.96845071.jpg', 'imagem', 'aprovado', NULL, '2025-08-31 16:10:45', '2025-08-31 16:17:37'),
(36, 43, 'comprovantes/68b4a14b397bc7.62200253.jpeg', 'imagem', 'aprovado', NULL, '2025-08-31 19:23:55', '2025-08-31 20:31:49'),
(37, 44, 'comprovantes/68b4c30e85ebc5.39014514.jpg', 'imagem', 'aprovado', NULL, '2025-08-31 21:47:58', '2025-08-31 23:04:14'),
(38, 46, 'comprovantes/68b4df5e125265.55790141.jpeg', 'imagem', 'aprovado', NULL, '2025-08-31 23:48:46', '2025-08-31 23:54:08'),
(39, 45, 'comprovantes/68b4df61518c86.00040004.jpeg', 'imagem', 'aprovado', NULL, '2025-08-31 23:48:49', '2025-08-31 23:54:04'),
(40, 25, 'comprovantes/68b4e185d94542.26873033.jpg', 'imagem', 'aprovado', NULL, '2025-08-31 23:57:57', '2025-08-31 23:59:36'),
(41, 47, 'comprovantes/68b5070de337c3.64421600.jpg', 'imagem', 'aprovado', NULL, '2025-09-01 02:38:05', '2025-09-01 02:49:40'),
(42, 48, 'comprovantes/68b507c6f2c9e6.14902316.jpg', 'imagem', 'aprovado', NULL, '2025-09-01 02:41:10', '2025-09-01 02:50:23'),
(43, 50, 'comprovantes/68b507e58b1423.11342069.png', 'imagem', 'aprovado', NULL, '2025-09-01 02:41:41', '2025-09-01 02:49:52'),
(44, 49, 'comprovantes/68b5090a8683a3.90213844.jpg', 'imagem', 'aprovado', NULL, '2025-09-01 02:46:34', '2025-09-01 02:49:28'),
(45, 51, 'comprovantes/68b50bd975a682.97225888.jpeg', 'imagem', 'aprovado', NULL, '2025-09-01 02:58:33', '2025-09-01 03:01:26'),
(46, 52, 'comprovantes/68b519d6db13a0.39244522.jpg', 'imagem', 'aprovado', NULL, '2025-09-01 03:58:14', '2025-09-01 04:06:31'),
(47, 53, 'comprovantes/68b57c8d97f709.67410506.jpeg', 'imagem', 'aprovado', NULL, '2025-09-01 10:59:25', '2025-09-01 12:10:06'),
(48, 54, 'comprovantes/68b58128ac0dc1.69812716.png', 'imagem', 'aprovado', NULL, '2025-09-01 11:19:04', '2025-09-01 12:10:09'),
(49, 55, 'comprovantes/68b58a43dd5c87.74976288.png', 'imagem', 'aprovado', NULL, '2025-09-01 11:57:55', '2025-09-01 12:10:33'),
(50, 57, 'comprovantes/68b59195640799.48207579.jpg', 'imagem', 'aprovado', NULL, '2025-09-01 12:29:09', '2025-09-01 12:30:39'),
(51, 62, 'comprovantes/68b594227fae83.64967679.png', 'imagem', 'aprovado', NULL, '2025-09-01 12:40:02', '2025-09-01 14:07:08'),
(52, 61, 'comprovantes/68b59473b55df2.22098571.jpg', 'imagem', 'aprovado', NULL, '2025-09-01 12:41:23', '2025-09-01 14:07:20'),
(53, 60, 'comprovantes/68b59494f0a819.84596110.pdf', 'pdf', 'aprovado', NULL, '2025-09-01 12:41:56', '2025-09-01 14:07:34'),
(54, 64, 'comprovantes/68b595ba6a2243.98914865.pdf', 'pdf', 'aprovado', NULL, '2025-09-01 12:46:50', '2025-09-01 14:07:43'),
(55, 65, 'comprovantes/68b595e8707888.60448936.png', 'imagem', 'aprovado', NULL, '2025-09-01 12:47:36', '2025-09-01 14:07:56'),
(56, 59, 'comprovantes/68b596266b1a90.35279693.jpeg', 'imagem', 'aprovado', NULL, '2025-09-01 12:48:38', '2025-09-01 14:08:06'),
(57, 66, 'comprovantes/68b597164d7262.82299266.pdf', 'pdf', 'aprovado', NULL, '2025-09-01 12:52:38', '2025-09-01 14:08:17'),
(58, 58, 'comprovantes/68b5985c8cfe14.14452812.jpg', 'imagem', 'aprovado', NULL, '2025-09-01 12:58:04', '2025-09-01 14:08:20'),
(59, 1, 'comprovantes/68b59cdb6858b5.89468125.png', 'imagem', 'rejeitado', NULL, '2025-09-01 13:17:15', '2025-09-01 13:40:38'),
(60, 70, 'comprovantes/68b59e7f211542.69535280.pdf', 'pdf', 'aprovado', NULL, '2025-09-01 13:24:15', '2025-09-01 14:10:38'),
(61, 69, 'comprovantes/68b59f047d2b58.18358847.png', 'imagem', 'rejeitado', NULL, '2025-09-01 13:26:28', '2025-09-01 13:40:42'),
(62, 71, 'comprovantes/68b5a004c5f360.02982510.png', 'imagem', 'aprovado', NULL, '2025-09-01 13:30:44', '2025-09-01 14:09:34'),
(63, 73, 'comprovantes/68b5a226545347.72898346.pdf', 'pdf', 'aprovado', NULL, '2025-09-01 13:39:50', '2025-09-01 14:09:45'),
(64, 68, 'comprovantes/68b5a412e72b96.41558478.pdf', 'pdf', 'aprovado', NULL, '2025-09-01 13:48:02', '2025-09-01 14:10:01'),
(65, 74, 'comprovantes/68b5a8bde99f58.85572558.pdf', 'pdf', 'aprovado', NULL, '2025-09-01 14:07:57', '2025-09-01 14:10:44'),
(66, 75, 'comprovantes/68b5aeecc95d23.58592191.pdf', 'pdf', 'aprovado', NULL, '2025-09-01 14:34:20', '2025-09-01 17:08:42'),
(67, 76, 'comprovantes/68b5b3f4cc0976.35115750.jpg', 'imagem', 'aprovado', NULL, '2025-09-01 14:55:48', '2025-09-01 17:08:55'),
(68, 69, 'comprovantes/68b5c3371c6696.98560422.png', 'imagem', 'excluido', NULL, '2025-09-01 16:00:55', '2025-09-01 16:01:14'),
(69, 78, 'comprovantes/68b5c5e77d0a12.36804600.pdf', 'pdf', 'aprovado', NULL, '2025-09-01 16:12:23', '2025-09-01 17:09:37'),
(70, 82, 'comprovantes/68b5ca24b8a909.96843979.pdf', 'pdf', 'aprovado', NULL, '2025-09-01 16:30:28', '2025-09-01 17:11:03'),
(71, 80, 'comprovantes/68b5ca507eeac8.98260870.jpg', 'imagem', 'aprovado', NULL, '2025-09-01 16:31:12', '2025-09-01 17:10:16'),
(72, 81, 'comprovantes/68b5cac3227789.50148017.jpeg', 'imagem', 'aprovado', NULL, '2025-09-01 16:33:07', '2025-09-01 17:09:45'),
(73, 83, 'comprovantes/68b5cdd92bb157.92712176.jpeg', 'imagem', 'aprovado', NULL, '2025-09-01 16:46:17', '2025-09-01 17:09:53'),
(74, 81, 'comprovantes/68b5d147566625.15288178.jpeg', 'imagem', 'aprovado', NULL, '2025-09-01 17:00:55', '2025-09-01 17:10:12'),
(75, 84, 'comprovantes/68b5d1a77caa52.86441583.jpeg', 'imagem', 'aprovado', NULL, '2025-09-01 17:02:31', '2025-09-01 17:10:00'),
(76, 85, 'comprovantes/68b5f928c1e7a6.97885665.pdf', 'pdf', 'aprovado', NULL, '2025-09-01 19:51:04', '2025-09-01 19:53:11'),
(77, 86, 'comprovantes/68b61bff3f3288.21570655.jpeg', 'imagem', 'aprovado', NULL, '2025-09-01 22:19:43', '2025-09-01 23:57:28'),
(78, 88, 'comprovantes/68b61ea378f989.14659383.jpg', 'imagem', 'aprovado', NULL, '2025-09-01 22:30:59', '2025-09-01 23:56:41'),
(79, 87, 'comprovantes/68b61ea53ea059.81152341.pdf', 'pdf', 'aprovado', NULL, '2025-09-01 22:31:01', '2025-09-01 23:56:50'),
(80, 89, 'comprovantes/68b61ef6a985e9.71535793.png', 'imagem', 'aprovado', NULL, '2025-09-01 22:32:22', '2025-09-02 02:40:49'),
(81, 90, 'comprovantes/68b61f739961a9.16220814.pdf', 'pdf', 'aprovado', NULL, '2025-09-01 22:34:27', '2025-09-01 23:56:33'),
(82, 91, 'comprovantes/68b61fd881d4f8.63763275.jpeg', 'imagem', 'aprovado', NULL, '2025-09-01 22:36:08', '2025-09-01 23:56:29'),
(83, 94, 'comprovantes/68b61fdb56c387.42336963.pdf', 'pdf', 'aprovado', NULL, '2025-09-01 22:36:11', '2025-09-01 23:56:25'),
(84, 96, 'comprovantes/68b62139c59ef1.50311920.png', 'imagem', 'aprovado', NULL, '2025-09-01 22:42:01', '2025-09-01 23:56:21'),
(85, 93, 'comprovantes/68b623b457bbd0.68006127.pdf', 'pdf', 'aprovado', NULL, '2025-09-01 22:52:36', '2025-09-01 23:56:09'),
(86, 98, 'comprovantes/68b632898efff2.62381931.png', 'imagem', 'aprovado', NULL, '2025-09-01 23:55:53', '2025-09-01 23:57:52'),
(87, 99, 'comprovantes/68b633e94af9e7.59288524.pdf', 'pdf', 'aprovado', NULL, '2025-09-02 00:01:45', '2025-09-02 02:41:25'),
(88, 100, 'comprovantes/68b6368e68c931.50926136.pdf', 'pdf', 'aprovado', NULL, '2025-09-02 00:13:02', '2025-09-02 02:41:19'),
(89, 101, 'comprovantes/68b637e5cbf829.58059588.jpeg', 'imagem', 'aprovado', NULL, '2025-09-02 00:18:45', '2025-09-02 02:41:14'),
(90, 102, 'comprovantes/68b64f03dc8210.78950168.jpg', 'imagem', 'aprovado', NULL, '2025-09-02 01:57:23', '2025-09-02 02:43:38'),
(91, 1, 'comprovantes/68b6e7b94a2645.09247898.jpeg', 'imagem', 'excluido', NULL, '2025-09-02 12:48:57', '2025-09-02 12:53:54'),
(92, 103, 'comprovantes/68b78c0a2c6a44.67507186.jpg', 'imagem', 'pendente', NULL, '2025-09-03 00:30:02', NULL),
(93, 104, 'comprovantes/68b7912235ab99.98843166.jpg', 'imagem', 'pendente', NULL, '2025-09-03 00:51:46', NULL),
(94, 105, 'comprovantes/68b7967c633329.98855753.pdf', 'pdf', 'aprovado', NULL, '2025-09-03 01:14:36', '2025-09-04 14:57:27'),
(95, 106, 'comprovantes/68b7b4c6248679.12414708.pdf', 'pdf', 'aprovado', NULL, '2025-09-03 03:23:50', '2025-09-04 14:57:46'),
(96, 107, 'comprovantes/68b84f607f3498.63517267.pdf', 'pdf', 'aprovado', NULL, '2025-09-03 14:23:28', '2025-09-04 14:57:51'),
(97, 108, 'comprovantes/68b8b0809515c3.44024507.pdf', 'pdf', 'aprovado', NULL, '2025-09-03 21:17:52', '2025-09-04 14:57:57'),
(98, 112, 'comprovantes/68b8bc69a527d8.62492109.jpg', 'imagem', 'aprovado', NULL, '2025-09-03 22:08:41', '2025-09-04 14:58:04'),
(99, 110, 'comprovantes/68b8bca9113c57.29783753.jpg', 'imagem', 'aprovado', NULL, '2025-09-03 22:09:45', '2025-09-04 14:58:18'),
(100, 111, 'comprovantes/68b8bdbac20ba6.14729570.jpg', 'imagem', 'aprovado', NULL, '2025-09-03 22:14:18', '2025-09-04 14:58:24'),
(101, 77, 'comprovantes/68b8be5f602cb2.81107305.jpg', 'imagem', 'aprovado', NULL, '2025-09-03 22:17:03', '2025-09-04 14:58:29'),
(102, 114, 'comprovantes/68b8c12a30cd37.53052392.pdf', 'pdf', 'aprovado', NULL, '2025-09-03 22:28:58', '2025-09-04 14:58:32'),
(103, 116, 'comprovantes/68b8cc4cb71c26.80386692.jpg', 'imagem', 'aprovado', NULL, '2025-09-03 23:16:28', '2025-09-04 14:58:38'),
(104, 117, 'comprovantes/68b8d17e448555.64931593.jpg', 'imagem', 'aprovado', NULL, '2025-09-03 23:38:38', '2025-09-04 14:58:47'),
(105, 118, 'comprovantes/68b8d19137ee80.86504015.pdf', 'pdf', 'aprovado', NULL, '2025-09-03 23:38:57', '2025-09-04 14:58:58'),
(106, 119, 'comprovantes/68b8d20f8238e1.12996839.jpg', 'imagem', 'aprovado', NULL, '2025-09-03 23:41:03', '2025-09-04 14:59:02'),
(107, 120, 'comprovantes/68b8db0156bea9.60638966.png', 'imagem', 'aprovado', NULL, '2025-09-04 00:19:13', '2025-09-04 14:59:07'),
(108, 121, 'comprovantes/68b9a5e45a8721.13394074.png', 'imagem', 'aprovado', NULL, '2025-09-04 14:44:52', '2025-09-04 14:59:12'),
(109, 122, 'comprovantes/68b9aa1ce38ca9.56727087.png', 'imagem', 'aprovado', NULL, '2025-09-04 15:02:52', '2025-09-04 15:05:02'),
(110, 123, 'comprovantes/68b9d4b1114cf4.13248877.pdf', 'pdf', 'aprovado', NULL, '2025-09-04 18:04:33', '2025-09-05 00:01:07'),
(111, 113, 'comprovantes/68ba0cb05f5af8.53430049.jpg', 'imagem', 'aprovado', NULL, '2025-09-04 22:03:28', '2025-09-05 00:00:58'),
(112, 124, 'comprovantes/68bacbf29fa121.30076721.jpeg', 'imagem', 'pendente', NULL, '2025-09-05 11:39:30', NULL),
(113, 125, 'comprovantes/68bb2191a068c1.45069815.jpg', 'imagem', 'pendente', NULL, '2025-09-05 17:44:49', NULL),
(114, 126, 'comprovantes/68bb2a32577b75.66556083.jpg', 'imagem', 'pendente', NULL, '2025-09-05 18:21:38', NULL),
(115, 127, 'comprovantes/68bb47d663e902.82969387.png', 'imagem', 'pendente', NULL, '2025-09-05 20:28:06', NULL),
(116, 127, 'comprovantes/68bb47db680207.35944844.png', 'imagem', 'pendente', NULL, '2025-09-05 20:28:11', NULL),
(117, 129, 'comprovantes/68bc81a7173929.62849735.pdf', 'pdf', 'pendente', NULL, '2025-09-06 18:47:03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `informacoes_evento`
--

CREATE TABLE `informacoes_evento` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `subtitulo` varchar(500) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `local` text DEFAULT NULL,
  `publico_alvo` text DEFAULT NULL,
  `objetivo` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `informacoes_evento`
--

INSERT INTO `informacoes_evento` (`id`, `titulo`, `subtitulo`, `descricao`, `data_inicio`, `data_fim`, `local`, `publico_alvo`, `objetivo`, `updated_at`) VALUES
(1, '1ª TechWeek', 'Semana Acadêmica de Tecnologia e Inovação', 'A 1ª TechWeek é um marco na história tecnológica da região. Este evento promete reunir as mentes mais brilhantes do setor para discutir inovações, tendências e desafios do mundo digital.\n                    Durante quatro dias intensos, participantes terão acesso a palestras de especialistas renomados, workshops práticos, competições de programação, oportunidades de networking e muito mais. O evento será realizado em diversos locais estratégicos, transformando a cidade em um polo de inovação.\n                    Temas como Inteligência Artificial, Desenvolvimento Web Moderno, Segurança Cibernética, Ciência de Dados e Empreendedorismo Tecnológico estarão no centro das discussões, proporcionando uma imersão completa no universo da tecnologia da informação.', '2025-10-28', '2025-10-31', 'UTFPR, CESUL, ACEFB e Teatro Municipal', 'Estudantes, profissionais e entusiastas de TI', 'Fomentar a inovação e o conhecimento tecnológico', '2025-09-06 19:27:28');

-- --------------------------------------------------------

--
-- Table structure for table `inscricoes_atividades`
--

CREATE TABLE `inscricoes_atividades` (
  `id` int(11) NOT NULL,
  `participante_id` int(11) NOT NULL,
  `atividade_id` int(11) NOT NULL,
  `data_inscricao` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organizadores`
--

CREATE TABLE `organizadores` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `tipo` enum('realizacao','apoio') NOT NULL,
  `logo_url` varchar(500) DEFAULT NULL,
  `site_url` varchar(500) DEFAULT NULL,
  `ordem` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `organizadores`
--

INSERT INTO `organizadores` (`id`, `nome`, `tipo`, `logo_url`, `site_url`, `ordem`) VALUES
(1, 'UTFPR', 'realizacao', 'imagens/utfpr.png', NULL, 1),
(2, 'Curso de Bacharelado em Sistemas de Informação da UTFPR/FB', 'realizacao', 'imagens/bsi.png', NULL, 2),
(3, 'CASIS', 'realizacao', 'imagens/casis.png', NULL, 3),
(4, 'Nubetec', 'realizacao', 'imagens/nubetec.png', NULL, 4),
(5, 'TypeX', 'realizacao', 'imagens/typex.png', NULL, 5),
(6, 'CESUL', 'realizacao', 'imagens/cesul.png', NULL, 6),
(7, 'Prefeitura de Francisco Beltrão', 'apoio', 'imagens/prefeiturafb.png', NULL, 1),
(8, 'Aiqfome', 'apoio', 'imagens/aiqfome.png', NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `participantes`
--

CREATE TABLE `participantes` (
  `id` int(11) NOT NULL,
  `administrador` int(1) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `tipo_inscricao` varchar(50) DEFAULT NULL,
  `lote_inscricao` varchar(50) DEFAULT NULL,
  `valor_pago` decimal(10,2) DEFAULT NULL,
  `hash` varchar(100) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(100) DEFAULT NULL,
  `cpf` varchar(14) NOT NULL,
  `codigo_barra` varchar(20) DEFAULT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `instituicao` varchar(255) NOT NULL,
  `preco_inscricao` decimal(10,2) DEFAULT NULL,
  `voucher` varchar(50) DEFAULT NULL,
  `isento_pagamento` tinyint(1) DEFAULT 0,
  `data_cadastro` timestamp NULL DEFAULT current_timestamp(),
  `token_recuperacao` varchar(64) DEFAULT NULL,
  `expiracao_token` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `participantes`
--

INSERT INTO `participantes` (`id`, `administrador`, `tipo`, `tipo_inscricao`, `lote_inscricao`, `valor_pago`, `hash`, `nome`, `email`, `senha`, `cpf`, `codigo_barra`, `telefone`, `instituicao`, `preco_inscricao`, `voucher`, `isento_pagamento`, `data_cadastro`, `token_recuperacao`, `expiracao_token`) VALUES
(1, 1, 'administrador', 'universitario_ti', '2', 25.00, '0fba6bd9bc288bfa147aa283a54b40ec', 'Wellton Costa de Oliveira', 'professorwelltoncosta@gmail.com', '$2y$10$Gc207JvRWBSkWHmpRbn4hOyyMv4ckbI8bRXkLNjDg8DlpbwmjEvLK', '857.922.682-15', 'TW20250001', '(46) 99105-7348', 'UTFPR Francisco Beltrão', 35.00, NULL, 0, '2025-08-29 17:11:42', 'aabb82fdaa5ce13af95553dd98bc13a0fc342fa3feace539530ff625fe8fe851', '2025-09-05 12:48:44'),
(2, 1, 'administrador', 'universitario_ti', '2', 25.00, '6e5722f0fbb2b6d1c36cd9a6645c19bf', 'Marcos Mincov Ten&oacute;rio', 'marcostenorio@utfpr.edu.br', '$2y$12$0wPDjrIRLgjiyU7tFvBY8.JcJ7Dg4dzW0.uHBznS9Yz3BF73y9oTG', '066.178.719-28', 'TW20250002', '', '', 35.00, NULL, 0, '2025-08-29 17:14:05', NULL, NULL),
(3, 1, 'administrador', 'universitario_ti', '1', NULL, 'e1caebf68f8a2fc5d5ece1db10b25dc4', 'Vitor Ferreira Viana', 'vitorferreiraviana@alunos.utfpr.edu.br', '$2y$10$jAdySZZjH8ZKsgjkdlyr5OfPXzClvi1rh1dzFHSccfYZ7wTfrz8fS', '557.040.638-70', 'TW20250014', '(12) 98142-4603', 'UTFPR-FB', 25.00, NULL, 0, '2025-08-30 14:24:57', NULL, NULL),
(4, 1, 'administrador', 'universitario_ti', '2', NULL, '92fa1bbda0b9ecd1fa7631a808feceed', 'David Lopes Araujo Junior ', 'david.junior211204@gmail.com', '$2y$10$du/K1qum7TrFkxX4gVYnDucYoqmPoZnBGl.4SiaA6fSJ2cipfiGPO', '528.643.558-09', 'TW20250016', '(11) 96164-9701', 'UTFPR ', 35.00, NULL, 0, '2025-08-30 15:17:53', NULL, NULL),
(5, 0, 'participante', 'universitario_ti', '1', 25.00, '66d517992e33f821cc1a8e97e2b894e5', 'INGRID MAYARA SPISS ANDRADE', 'ingridandrade@alunos.utfpr.edu.br', '$2y$10$SWyNqZxOHtqSZmuo.peqxeZtKUIaOQE8gr3K1oDHcDRVJCY4CI48W', '355.948.358-14', 'TW20250005', '(46) 99933-5836', '', 25.00, NULL, 0, '2025-08-30 22:22:31', NULL, NULL),
(6, 0, 'participante', 'universitario_ti', '1', 25.00, '673bb8f37faeb604d335f140e1b9a7a0', 'Maikon Icaro dos Santos', 'maikonicaro@alunos.utfpr.edu.br', '$2y$10$01eI7y/MAno0jofl9.oKGujb6qUm5APH4xZAt.Hte/Bul7ZMe1kuS', '119.743.919-65', 'TW20250006', '(46) 99971-3665', 'UTFPR-FB', 25.00, NULL, 0, '2025-08-30 22:24:30', NULL, NULL),
(7, 0, 'participante', 'universitario_ti', '1', 25.00, 'eba8e1ce949229ae086cafc3849ec871', 'Camila Queli Sockenski Thome ', 'camilathomest@gmail.com', '$2y$10$bZyFs/vjCt5buBP3QMMwQOWDRcxS2A7oQawJUqgjd7yIHTAuqAgxu', '112.650.189-10', 'TW20250007', '(46) 99901-3535', 'UTFPR ', 25.00, NULL, 0, '2025-08-30 22:25:06', NULL, NULL),
(8, 0, 'participante', 'universitario_ti', '1', 25.00, 'e8ad657b5431ed34be5681b5c1157340', 'Marcos Antonio Reolon Peters', 'marcosantonioreolon@hotmail.com', '$2y$10$wbs2YekrBRaoyX4wEm7tJ.wYCPO.d.vNlKYAka2Zuq3HIjKCq9Hj2', '077.790.219-20', 'TW20250008', '(46) 99916-2126', '', 25.00, NULL, 0, '2025-08-30 22:29:40', NULL, NULL),
(9, 0, 'participante', 'universitario_ti', '1', 25.00, 'dc179c61e4ec00dc6052c286de7c7d94', 'Jo&atilde;o Vitor Reolon ', 'joaovitorreolon087@gmail.com', '$2y$10$L3NfE1Xo5kYigyBCc7Y72ekBQwgteB2nc3gvmFTaWPOsBiBdFuSba', '106.949.169-19', 'TW20250009', '(46) 98828-0152', 'Cesul ', 25.00, NULL, 0, '2025-08-30 22:30:16', NULL, NULL),
(10, 0, 'participante', 'universitario_ti', '1', 25.00, 'fb93c76ee74a188a754ff6b4d44bb90d', 'Gabriel Luiz Barcelos ', 'gbarcelos232@gmail.com', '$2y$10$AnVLxNbB9iYL8IJtbWUA5eNuB5XEENwWDCDd8144OItcoy.irqGAy', '106.538.969-80', 'TW20250010', '(46) 99930-2664', 'Cesul ', 25.00, NULL, 0, '2025-08-30 22:31:01', NULL, NULL),
(11, 0, 'participante', 'universitario_ti', '1', 25.00, '415958c3bd94275d9576bfb73b1a85dc', 'Emanuel Henrique Stecanella', 'emanuel1stecanella@gmail.com', '$2y$10$GgfIDnUkyIS6h73YsvGy1.bNFZ8NphWqgin/gn0xw.aBI1LYsb.Eq', '086.891.359-60', 'TW20250011', '(46) 99983-1279', 'UTFPR-FB', 25.00, NULL, 0, '2025-08-30 22:34:03', NULL, NULL),
(12, 0, 'participante', 'universitario_ti', '1', 25.00, '71ed0ca9460c02aa6f0e71cb53c7c457', 'Laura Poloni Bell&eacute;', 'laurabelle@alunos.utfpr.edu.br', '$2y$10$R7S5gCPpZWp.FazufJM0h.9ugxDyk7l0YcLXKz1dc1iwQUr1Btu7K', '074.164.529-70', 'TW20250012', '(46) 99920-4214', 'UTFPR', 25.00, NULL, 0, '2025-08-30 22:35:05', NULL, NULL),
(13, 0, 'participante', 'universitario_ti', '1', 25.00, '72598e6b74f2c32a3ff7b24f1d4703c5', 'Victor Matheus Paim Cales Cura', 'vitinmtxs@gmail.com', '$2y$10$opOiUghgFoVofnUoej8MM.xTgTZvNhHT/UXv23YRYW8.5bhKzYwLi', '116.392.509-80', 'TW20250013', '(46) 98820-3373', 'CESUL', 25.00, NULL, 0, '2025-08-30 22:36:14', NULL, NULL),
(14, 0, 'participante', 'universitario_ti', '1', 25.00, '96ec9de785d9d708cfbafc3e715977c4', 'Emily Gabrielle Sousa Pia', 'emilysousa9103@gmail.com', '$2y$10$1c53mAl1p.OeKCLLb6ddbu6jChEsOft1zFoCj13zLwVfEG4JWsO5i', '041.964.352-46', 'TW20250014', '(93) 99652-4950', 'UTFPR- FB', 25.00, NULL, 0, '2025-08-30 22:37:21', NULL, NULL),
(15, 0, 'participante', 'universitario_ti', '1', 25.00, '34550cc468d461fab1af906f367ffd36', 'RICARDO CAPISTRANO', 'ricardoprbr17@gmail.com', '$2y$10$neekvFgt1.1m0Rozubg/ZOCCm/rm9J1qmhRmRuSBzCCBQvfpb/fSy', '105.738.979-05', 'TW20250015', '(47) 99726-3207', 'Universidade federal do Paran&aacute; Francisco Beltr&atilde;o ', 25.00, NULL, 0, '2025-08-30 22:37:35', NULL, NULL),
(16, 0, 'participante', 'universitario_ti', '1', 25.00, '2489f5e8acd789d512f56e2bb9106e13', 'Luiz Gustavo Gomes da Silva ', 'gustavogomesmmm@gmail.com', '$2y$10$42oxtc5OytqDzNhLiNr5w.q5isUicHbV8cdh3AyZx5Y6tVw.vRIEG', '147.623.769-73', 'TW20250016', '(46) 98803-4552', 'UTFPR', 25.00, NULL, 0, '2025-08-30 22:37:39', NULL, NULL),
(17, 0, 'participante', 'universitario_ti', '1', 25.00, 'c9bffb624f963c2f9c2e229fcdfc7ced', 'Mariana dos Santos Siqueira', 'marisantossiqueira29@gmail.com', '$2y$10$RPdcuR1iOK6zVOwtyeGoHO7AtOLoyCFfyqbGl1o7Ml459G.5zndg.', '141.611.429-78', 'TW20250017', '(46) 99920-5541', 'UTFPR', 25.00, NULL, 0, '2025-08-30 22:40:40', NULL, NULL),
(18, 0, 'participante', 'universitario_ti', '1', 25.00, 'b8b229d42ff232ffaaed4dbbf6a2a31e', 'Eduardo Conte', 'eduardo.2024014337@alunocesul.com', '$2y$10$VbpVaF0HewD/941hZQkBeuBG.QVV2JCLs5TDdC/1kdDagnrEhYrEG', '123.249.479-86', 'TW20250018', '(46) 99135-4935', 'Cesul', 25.00, NULL, 0, '2025-08-30 22:41:15', NULL, NULL),
(19, 0, 'participante', 'universitario_ti', '1', NULL, '7352cdb311cc0f7370e4b0b27b803f25', 'Jos&eacute; Vitor Bueno Braga', 'jvbb0122@gmail.com', '$2y$10$lSMf9CuU1eZ9n7P1lT6ndu3IlSZSI21uW2pjX4126Ry7JZ3N.oQ/C', '076.833.013-02', 'TW20250019', '(85) 98726-1008', 'Cesul', 25.00, NULL, 0, '2025-08-30 22:41:55', NULL, NULL),
(20, 0, 'participante', 'universitario_ti', '1', 25.00, 'ae1543b8bf7e390790102f84c7250d1b', 'Adriel Shiguemi Cikoski Kiyota ', 'adrielshiguemi@alunos.utfpr.edu.br', '$2y$10$SkfZ5xXO3zwJO3QkX5DizO9Qz/WdJwZYcbimcWSOWOvUsRVtyZHLK', '337.840.718-21', 'TW20250020', '(46) 99987-1716', 'UTFPR', 25.00, NULL, 0, '2025-08-30 22:48:09', NULL, NULL),
(21, 0, 'participante', 'universitario_ti', '1', 25.00, '9b3978b094795ece6ed17ca45b5dea35', 'Sulamita Sarom Peixoto Gomes ', 'sulgomes08@gmail.comgmail.com', '$2y$10$LLbKbgkABpk2op/1I5RIKeh4OP/vNzA5hS89jf0rSGxZHCALL3gNC', '177.880.186-27', 'TW20250021', '(31) 99507-5536', 'UTFPR ', 25.00, NULL, 0, '2025-08-30 22:48:28', NULL, NULL),
(22, 0, 'participante', 'universitario_ti', '1', 25.00, 'a8c930ca6f1a41d65989244b9f60d67e', 'Julia Caroline de Ramos da Silva', 'juliacarolinesilva@alunos.utfpr.edu.br', '$2y$10$rMqKfKK6AokejrSeP0xZOOCAEgmToXkdkNHg1I4P9NOVXVH1YpD3a', '031.308.200-60', 'TW20250022', '(46) 98803-0686', 'UTFPR- Francisco Beltr&atilde;o ', 25.00, NULL, 0, '2025-08-30 22:50:34', NULL, NULL),
(23, 0, 'participante', 'universitario_ti', '1', 25.00, '33bb9149af10f87f49a5b80aacccbfa7', 'Welington Felipe Blasius', 'welington1br@gmail.com', '$2y$10$xcjtK5NszmRL95E9c6hSkeWC12HOgvzGqnl43jlyN.7.Lwk81wPL2', '105.373.249-09', 'TW20250023', '(46) 99110-9121', 'UTFPR', 25.00, NULL, 0, '2025-08-30 22:51:29', NULL, NULL),
(24, 0, 'participante', 'universitario_ti', '1', 25.00, '62d186e3c8d1f1978aceca6e1b8d7e64', 'Camile Vitoria Paz', 'camilepaz@alunos.utfpr.edu.br', '$2y$10$xx6si0gHzQsyF7vV1tW7Xuj4n/g/B0McqKES9.l5z6PtJJUON70gO', '110.544.229-23', 'TW20250024', '(46) 98800-0078', 'UTFPR - FB', 25.00, NULL, 0, '2025-08-30 23:10:49', NULL, NULL),
(25, 0, 'participante', 'universitario_ti', '1', 25.00, '7ec448554b3170037680a601ae1ec9a1', 'Alana Yasmin zauza ', 'smoalanayz@gmail.com', '$2y$10$6e.UxWwM1Dtsb.dfVMIxGOXgEx6AFstYiTBC3pEgHaehbzE.x3l5u', '088.727.129-47', 'TW20250025', '(46) 99918-6240', 'Cesul', 25.00, NULL, 0, '2025-08-30 23:12:25', NULL, NULL),
(26, 0, 'participante', 'universitario_ti', '1', 25.00, '6240e9ab7e1f88f68577ad0ce1d73256', 'Eduardo de Miranda', 'demiranda.du@gmail.com', '$2y$10$m3tDJMzZrbgfXNfC4lXvlOri00RV3SiZRiE18cl55RIjJhGdCzqQa', '111.308.039-61', 'TW20250026', '(46) 99930-9633', 'UTFPR-Campus Francisco Beltr&atilde;o', 25.00, NULL, 0, '2025-08-30 23:29:42', NULL, NULL),
(27, 0, 'participante', 'universitario_ti', '1', 25.00, 'fb072ea6156559ad8a718915f42a420c', 'Myguel Henryque Dachery do Prado ', 'myguelhenry05@gmail.com', '$2y$10$IkGiSpXDmxd2iVseQ0pPNe4QeT.MK/B2lQvfxmDCul3jjH7nrJx7K', '121.119.149-40', 'TW20250027', '(46) 99914-7573', 'Utpfr - Francisco Beltr&atilde;o ', 25.00, NULL, 0, '2025-08-30 23:30:55', NULL, NULL),
(28, 0, 'participante', 'universitario_ti', '1', 25.00, 'e80c383c118640647c27095db03012ed', 'Jo&atilde;o Aloisio Battisti', 'joaoabattisti851@gmail.com', '$2y$10$qEKz23NlX5x9k8dtf9ruduP1w4VBhLP30PURpDpaArI8JLN1xn4AG', '075.370.559-12', 'TW20250028', '(46) 98828-6844', 'Universidade Tecnol&oacute;gica Federal do Paran&aacute;', 25.00, NULL, 0, '2025-08-31 01:35:44', NULL, NULL),
(29, 0, 'participante', 'universitario_ti', '1', 25.00, '135951e249fb4793ec148d14ef487995', 'Vinicius Raitz Pilati', 'vini1718a@gmail.com', '$2y$10$LYyOmH7qVbRu4j.ZjnYOIe8p3dKiBbdgPK5wwJpRUZrQ0ejnpVYjK', '104.560.649-90', 'TW20250029', '(46) 99980-2449', '', 25.00, NULL, 0, '2025-08-31 01:52:41', NULL, NULL),
(30, 0, 'participante', 'universitario_ti', '1', 25.00, '1969e3a1b083b7fb5b1a5232049b1d69', 'Lucas Furtado Souza ', 'lucasfurtado256@gmail.com', '$2y$10$JCbm93ilUEV61VKIgzF55.qAlBg0rYhRvk742Z/7q7FZVLaku1vja', '027.765.812-89', 'TW20250030', '(69) 98408-1391', 'TypeX', 25.00, NULL, 0, '2025-08-31 02:17:06', NULL, NULL),
(31, 0, 'participante', 'universitario_ti', '1', 25.00, '3d8cd954cd7ac6d797e57d92b533f7b8', 'Pedro Henrique Peruzzo', 'pedrocperuzzo@gmail.com', '$2y$10$9YYoEbjljY59Po0/RXZZ1.v6q0z5LZt8TqLEgmEXcTQI20RYVDWiO', '083.461.459-69', 'TW20250031', '(46) 98802-9000', 'UTFPR', 25.00, NULL, 0, '2025-08-31 02:37:48', NULL, NULL),
(32, 0, 'participante', 'universitario_ti', '1', 25.00, 'cf9cf926d6ebc0a307ee7fce3417be45', 'Vitor Gabriel dos Santos Conte', 'vitorgabrielconte77@gmail.com', '$2y$10$l/azY2vdBjzS.U5T13ZNgemU.CXzq5Gg3pHSEw2FxlST9HgG0vvY2', '112.226.259-03', 'TW20250032', '(46) 99127-9326', 'CESUL', 25.00, NULL, 0, '2025-08-31 02:41:02', NULL, NULL),
(33, 0, 'participante', 'universitario_ti', '1', 25.00, '5f3be6fa42d1882eee4708640c0af5e3', 'GABRIELI MOURA NARDI', 'gabrielin.2025@alunos.utfpr.edu.br', '$2y$10$grWVKtBr.uxskmF/86fR9emCApcpLfkss6j5mBip8IGRRkM0sP2eK', '109.978.669-00', 'TW20250033', '(46) 99140-8829', '', 25.00, NULL, 0, '2025-08-31 02:43:23', NULL, NULL),
(34, 0, 'participante', 'universitario_ti', '2', 25.00, 'b94dbcdaef2c121e91c237b8ede4e096', 'André Buriola Trevisan ', 'andre.buriola@gmail.com', '$2y$10$ICXlo.L1oJNk67ZI6IBX8eJJ.DE0K0Y4zgKR8rewL6l/18iteGbkG', '363.245.048-08', 'TW20250034', '(46) 99941-5520', 'UTFPR ', 35.00, NULL, 0, '2025-08-31 04:02:27', NULL, NULL),
(35, 0, 'participante', 'universitario_ti', '1', 25.00, '2912ed2f4c71a1b82455b4238622c11f', 'Ana Carolina de Morais Benedetti', 'benedetti@alunos.utfpr.edu.br', '$2y$10$blkh/F4jyB5Q.TzVb9J03O.0UATw.o2QeISK2Fx/nQhG6rTlytTuW', '076.786.549-95', 'TW20250035', '(46) 99920-1629', 'UTFPR', 25.00, NULL, 0, '2025-08-31 11:28:28', NULL, NULL),
(36, 0, 'participante', 'universitario_ti', '1', 25.00, 'a38f3bde0531660f1d39a7c107a5aa58', 'Igor Kussumoto do Nascimento ', 'igor.kussumoto@gmail.com', '$2y$10$MBhhZWGT2pSDYj2U8DzoBeJjAibJS5TV8I3xwCKdinz9o9O5KAe7K', '424.841.828-21', 'TW20250036', '(11) 94468-6762', 'UTFPR', 25.00, NULL, 0, '2025-08-31 11:36:04', NULL, NULL),
(37, 0, 'participante', 'universitario_ti', '1', NULL, 'ad1fdec6210f2aa5b5bf7bb19fb962b5', 'Gabriela Kuhnen Marcello', 'gabikmarcello@gmail.com', '$2y$10$hc49pI0JZGijea.JzO8cuO5gQfoGuB3VcXo9Wq.lL.9IwdC76/mUO', '084.531.009-77', 'TW20250037', '(46) 9102-6002', 'UTFPR', 25.00, NULL, 0, '2025-08-31 13:18:45', NULL, NULL),
(38, 0, 'participante', 'universitario_ti', '1', 25.00, '5a223f5c2fc4339d67b40e4444e58eb3', 'Viviane Aparecida Hreneczen', 'hreneczenviviane@gmail.com', '$2y$10$06tluLGAvZj5sIY.i4inDuMJEqS7ziVMgHMnd.c/X0ZygJG63AlaC', '146.078.469-37', 'TW20250038', '(46) 98816-7963', '', 25.00, NULL, 0, '2025-08-31 13:27:39', NULL, NULL),
(39, 0, 'participante', 'universitario_ti', '1', 25.00, '7f236fba8674e94cda95fa055892f917', 'Gabrielle Dall Alba Pozzebon ', 'gabriellepozzebon@gmail.com', '$2y$10$rxsjDzSxDcndlNnX2c/amOKd83OngduuGHDR1xLyCJ/Ki8uTdI8Jy', '097.785.579-18', 'TW20250039', '(46) 9129-8083', 'UTFPR - Francisco Beltr&atilde;o ', 25.00, NULL, 0, '2025-08-31 14:16:53', NULL, NULL),
(40, 0, 'participante', 'universitario_ti', '1', 25.00, '59338a9e0b0960c5e0a0b8cdf4946db8', 'Kauan Mendon&ccedil;a ', 'kauanquiodeli2006@gmail.com', '$2y$10$Dbonl5YLf0dxwooK8HRGpOrOUrYyC/.AsLd3MXOORZYj02TY2.oz6', '123.253.639-37', 'TW20250040', '(46) 98822-1085', 'UTFPR - FB', 25.00, NULL, 0, '2025-08-31 14:29:49', NULL, NULL),
(41, 0, 'participante', 'universitario_ti', '1', 25.00, 'c5266da669d92a596f7f88fe7bd6c798', 'Lucas Gabriel Garcia da Silva ', 'lucsil.2023@alunos.utfpr.edu.br', '$2y$10$t0xXfCxf4UxB1DVNsmUTw.4efST8W4Sp/e2pPREY6VI0mWfFCaThe', '106.331.329-50', 'TW20250041', '(46) 99982-6510', 'UTFPR-FB ', 25.00, NULL, 0, '2025-08-31 15:33:02', NULL, NULL),
(42, 0, 'participante', 'universitario_ti', '2', 25.00, '2b7d282fe5350f7ae5e6d78e36e8feba', 'Eduardo Gabriel', 'duduedu2378@gmail.com', '$2y$10$GrKvifYL/kDYKVoaFoxhL.bNRW7QC3Q2A1q4O4GSGVjbB5D4Airc2', '113.261.809-67', 'TW20250042', '(55) 46988-0028', 'Cesul ', 35.00, NULL, 0, '2025-08-31 16:07:31', NULL, NULL),
(43, 0, 'participante', 'universitario_ti', '1', 25.00, 'ecd8743fecbe481c4034e0b10e2eeeca', 'Samira Laura Talau do Nascimento ', 'samiralaura@alunos.utfpr.edu.br', '$2y$10$YTHl6AAGoEXjfLvggZ.3wOP1I1.8jHgu35o90AqfSokGNtuYlxDpy', '106.077.669-30', 'TW20250043', '(46) 99906-4210', '', 25.00, NULL, 0, '2025-08-31 18:34:18', NULL, NULL),
(44, 0, 'participante', 'universitario_ti', '1', 25.00, '8e7483c5f99542d994fa7547e2f50b80', 'Roger Gabriel Schneider Kobs', 'rogerkobs@alunos.utfpr.edu.br', '$2y$10$yn5zSBaWCdND7UaKeu4SkOWwzrbpKQhttBTYk.9zey8XSgr7wcPCu', '098.450.349-85', 'TW20250044', '', 'Utfpr', 25.00, NULL, 0, '2025-08-31 21:45:09', NULL, NULL),
(45, 0, 'participante', 'universitario_ti', '1', 25.00, '04cbd733267eb14eaa2f00fa263fd936', 'Emanuel Henrique Choldys Biava', 'emanuelcholdys@gmail.com', '$2y$10$qSV2bRqJGGqiopjbuCG57OfL861uJP5Wt/5K6R1AJmDjotCaHwkze', '119.876.549-60', 'TW20250045', '(46) 99911-4104', 'UTFPR', 25.00, NULL, 0, '2025-08-31 23:46:29', NULL, NULL),
(46, 0, 'participante', 'universitario_ti', '1', 25.00, '7e5668ba2f7b029706159d43d365c747', 'Giovana Estrela Godinho ', 'giovanaestrelagodinho02@gmail.com', '$2y$10$VKa0QIqVHh/u7gqmkrZ57e4MvzLC0R73.0TrYZIX03IhiqZt3BPRS', '113.750.809-46', 'TW20250046', '(46) 99980-6500', 'UTFPR FB', 25.00, NULL, 0, '2025-08-31 23:46:35', NULL, NULL),
(47, 0, 'participante', 'universitario_ti', '1', 25.00, 'bfc6e43be43daffa061a3d9777ecb424', 'Ruan Kowalski ', 'ruan.2023014206@alunocesul.com', '$2y$10$g/um3HduDG5/.za6jPH2ZuA1tAVBjMVbj7jfm/OfaxeSFbESeP.sO', '131.540.059-60', 'TW20250047', '(46) 99976-6168', 'CESUL', 25.00, NULL, 0, '2025-09-01 02:34:55', NULL, NULL),
(48, 0, 'participante', 'universitario_ti', '2', 25.00, '3f8627cb1d900772772cc303352e8b4d', 'Lucas Scotti', 'lucas.2023014216@alunocesul.com', '$2y$10$oAwMl1gxdrNJTo8yodVYcevUDhY0ZNNNS6ZG0/1oPXTLZbof23Pge', '088.982.939-09', 'TW20250048', '(46) 98834-8816', 'Cesul', 35.00, NULL, 0, '2025-09-01 02:38:22', NULL, NULL),
(49, 0, 'participante', 'universitario_ti', '2', 25.00, 'ed5773bb69ed09daf89ef77334035db2', 'Guilherme Sartori ', 'guilherme.2023014204@alunocesul.com', '$2y$10$XOjk5/3chFzt8sJEi.ZEO.pnEvIkW75kEfvs1UZNInKYxsuruGlbi', '139.679.729-76', 'TW20250049', '(46) 99980-0217', 'Cesul', 35.00, NULL, 0, '2025-09-01 02:38:41', NULL, NULL),
(50, 0, 'participante', 'universitario_ti', '1', 25.00, '6d0ad36e0a0dd5268addc63caadc0693', 'henrique dos santos junkes', 'rickjunkes@hotmail.com', '$2y$10$czpwTFbN3vGEZLKb/YDldu0xLSfJO7eaM/hxK9VErBc/bWBghjqpe', '106.807.329-26', 'TW20250050', '(46) 99913-1034', 'cesul', 25.00, NULL, 0, '2025-09-01 02:38:42', NULL, NULL),
(51, 0, 'participante', 'universitario_ti', '1', 25.00, 'ec49b5376e509534f5f3b93449dfe5db', 'Karine Guedes ', 'karine.guedes@escola.pr.gov.br', '$2y$10$tlT7nQ9zOMcJQTiQxly9p.KhlE.dUa5VSGKNWUsCb/GoXOqzmDnr6', '123.285.429-80', 'TW20250051', '(46) 98822-7187', 'UTFPR', 25.00, NULL, 0, '2025-09-01 02:56:44', NULL, NULL),
(52, 0, 'participante', 'universitario_ti', '2', 25.00, '2b4412e5bd7af04817cabace3a416da3', 'Alisson Koerich ', 'alissonkoerich5@gmail.com', '$2y$10$KWMMFPIl4GD4ZQZNijbeDuCQZk/PMJxEE0kDdHrUASYS1SR2g7X72', '117.711.529-82', 'TW20250052', '(46) 99978-1227', 'Cesul', 35.00, NULL, 0, '2025-09-01 03:05:00', NULL, NULL),
(53, 0, 'participante', 'universitario_ti', '1', 25.00, '1812daf57a9b4be204c069e2072c9d5e', 'Jo&atilde;o Ezequiel Hansen Silva', 'joaoehansen@gmail.com', '$2y$10$9tS/P6QBDRAQr7HbxyUbz.NRiMDr.rQRcxWdPutjx6Q4pjEhUio.u', '012.965.559-76', 'TW20250053', '(38) 99812-9542', 'UTFPR', 25.00, NULL, 0, '2025-09-01 10:52:11', NULL, NULL),
(54, 0, 'participante', 'universitario_ti', '1', 25.00, 'd181446e4946ee8b7786181e45b1e3b1', 'Gabriel Biankati de Souza ', 'gab.biankati@gmail.com', '$2y$10$kxmwtn7J/Kja7.qvKncFIubLmGm067dsp2C79kZQbsDDmnjhyQuSi', '095.428.499-23', 'TW20250054', '(46) 93505-1856', 'Cesul ', 25.00, NULL, 0, '2025-09-01 11:13:00', NULL, NULL),
(55, 0, 'participante', 'universitario_ti', '1', 25.00, '367e83bba20033f3b0b2b19e2f81aaf8', 'Jo&atilde;o Vitor Pires de Souza', 'joaovitorpiresdesouza01@gmail.com', '$2y$10$XRpPXv8Ji1jRYVNmbCpREuUoLfUxoclu8ulb34pWsjsHE4wFiz3eO', '105.643.019-22', 'TW20250055', '(46) 99922-0828', 'CESUL', 25.00, NULL, 0, '2025-09-01 11:56:02', NULL, NULL),
(56, 0, 'participante', 'universitario_ti', '1', NULL, '5efdfdea589a154dfb7852ea759a05dc', 'Teste', 'teste@gmail.com', '$2y$10$0P6q9S5SCMS/ZiKhrGe4nO0hv9/895J6D9j3p1H63Yz7RgLUUBBla', '605.409.580-34', 'TW20250056', '', '', 25.00, NULL, 0, '2025-09-01 12:16:00', NULL, NULL),
(57, 0, 'participante', 'universitario_ti', '1', 25.00, 'a4bd120d46f7b2f3e6ae6c06e16d4959', 'JEAN CARLOS BRUNHERA DE LIZ', 'jeanliz@alunos.utfpr.edu.br', '$2y$10$3p3ul.6lVGFmfVSo18ibbOSddRV6yAR6sxB8/5UPOo7u7HB0Dm882', '080.198.839-08', 'TW20250057', '(46) 99908-7047', 'UTFPR', 25.00, NULL, 0, '2025-09-01 12:24:01', NULL, NULL),
(58, 0, 'participante', 'universitario_ti', '2', 35.00, '1bb7bb940a10199675b20c49ea8dcf03', 'Monica Dinkel Baggio ', 'monicabaggio@alunos.utfpr.edu.br', '$2y$10$orQUFjLk/8jBbxljWxh.a.KC7zi3vYFpCF1blt81Dj9USo5XF.qXe', '110.334.559-11', 'TW20250058', '(46) 99921-1829', 'UTFPR ', 25.00, NULL, 0, '2025-09-01 12:26:33', NULL, NULL),
(59, 0, 'participante', 'universitario_ti', '2', 35.00, '241e9876aa202f0bdf61ca8dac25d83f', 'Gabriel Augusto Perin', 'gabriel.2015012222@alunocesul.com', '$2y$10$T.BxXfXG46mMlUessiL/gusxTDOlCORPOM0ZD0b.TE/3Jow5RDHQm', '062.165.339-08', 'TW20250059', '(46) 99136-7575', 'CESUL', 35.00, NULL, 0, '2025-09-01 12:35:31', NULL, NULL),
(60, 0, 'participante', 'universitario_ti', '2', 25.00, '5b0ca40de50bcb19975ef72d3ddf2f47', 'JOSE RENATO PERIN JUNIOR', 'joserenatoperin11@gmail.com', '$2y$10$Owz/4sev0iNtkpv3C9kFFOTMco6QGx3mfZ8mdOAAMwrhVLbsOf1aK', '119.385.249-85', 'TW20250060', '(49) 99180-0613', 'Cesul', 35.00, NULL, 0, '2025-09-01 12:36:15', NULL, NULL),
(61, 0, 'participante', 'universitario_ti', '1', 25.00, '16934913f66d6c0dc99819babc6f92fd', 'MOISES LUIZ MATHIAS ZANDONAI', 'moiseszandonai1234@gmail.com', '$2y$10$r72c9N9PT/Ije0L19W/zS..i0kseK9m.oxP5VkKAuy/9M0oQzCj36', '099.798.969-65', 'TW20250061', '(46) 99919-3909', 'Cesul', 35.00, NULL, 0, '2025-09-01 12:36:59', NULL, NULL),
(62, 0, 'participante', 'ensino_medio', 'regular', NULL, '9850e41c6baa5ebb1783c0e7115f5e0c', 'Lu&iacute;s Felipe Busatto Reichett', 'luisfeliperei2006@gmail.com', '$2y$10$.moYTrlpRojm.ewVc9bsh.kTg.6Bx.H3.3GhNnm/wA41fGzZSQVya', '104.800.949-18', 'TW20250062', '(46) 99936-0886', 'Cesul ', 0.00, NULL, 0, '2025-09-01 12:38:19', NULL, NULL),
(63, 0, 'participante', 'universitario_ti', '2', NULL, 'eeabf42b03a62e3b031848ae3a239a36', 'Jo&atilde;o Lucas de Souza ', 'joao.2023014171@alunocesul.com', '$2y$10$b04xGk/ErfckMmVmZsd4AOU.YMqGkXJ/OL97HDWWsenJ5J8OlCqw2', '111.248.459-09', 'TW20250063', '(46) 98800-0374', 'Cesul', 35.00, NULL, 0, '2025-09-01 12:40:12', NULL, NULL),
(64, 0, 'participante', 'universitario_ti', '2', 35.00, 'f60b9c5cf6f151e1536bfe73c25ad4ba', 'ROBERT JEAN DA ROSA', 'omegados69@gmail.com', '$2y$10$2.7JaOGOxnHiyqnLLVtUS.o91hG9VsrXXgoukiUO.x9F5VeAaINKG', '123.391.829-05', 'TW20250064', '(46) 98823-1050', 'Cesul', 35.00, NULL, 0, '2025-09-01 12:40:21', NULL, NULL),
(65, 0, 'participante', 'universitario_ti', '2', 35.00, '64717d187819c13e45c44e4ed1a1d9f8', 'PEDRO HENRIQUE DOS SANTOS PAES', 'firedep04@gmail.com', '$2y$10$PZTg2G8KcFdk5a2OC0PLPe0b0/AENR7gFkBwUFe.bmYaTKALVzJqe', '099.967.229-02', 'TW20250065', '(46) 99115-1509', 'CESUL', 25.00, NULL, 0, '2025-09-01 12:44:36', NULL, NULL),
(66, 0, 'participante', 'universitario_ti', '2', 35.00, '5a33502ef962f2d2c853ac078ae59f0d', 'Luiz Feix', 'luizfeix1108@gmail.com', '$2y$10$1bWEbcFJTHh/u1QACQTV4Oa9.VZTnpvHcqQTjWZT9wqdxi1AzmPfe', '117.288.069-76', 'TW20250066', '(49) 99202-7055', 'Cesul', 25.00, NULL, 0, '2025-09-01 12:50:46', NULL, NULL),
(67, 0, 'participante', 'universitario_ti', '1', NULL, 'b2e842b8bb5207350c6914efe0be7170', 'Lucas Bernardon', 'lucasbernardon94@gmail.com', '$2y$10$sndrVoCXbFAxmJQo8bdYiueXt0/1gPtfbiZahZt3ETUfXW3VZCGbK', '063.253.429-09', 'TW20250067', '(46) 99923-3812', 'Cesul', 25.00, NULL, 0, '2025-09-01 13:07:00', NULL, NULL),
(68, 0, 'participante', 'universitario_ti', '2', 35.00, 'af343a7834745246751444e3c2348c81', 'VICTOR AUGUSTO DE SOUZA DA SILVA', 'victoraugustosouzasilva4232@gmail.com', '$2y$10$GKOqS5U3RhunVabbnz2V4OdiwOCi48VOQwlgX4w6Rhr0mFtLtxTNO', '147.031.079-10', 'TW20250068', '(46) 99130-7611', 'Cesul', 25.00, NULL, 0, '2025-09-01 13:11:40', NULL, NULL),
(69, 0, 'participante', 'universitario_ti', '2', 25.00, '8df523f0c771562848ff6a1e07206264', 'teste', 'teste@teste.com', '$2y$10$YdnmXjGsBXUdJMMQwI5nheH21OKfygT60bGop7t8o/WbnHqvwlLha', '485.572.452-11', 'TW20250069', '', '', 35.00, NULL, 0, '2025-09-01 13:22:14', NULL, NULL),
(70, 0, 'participante', 'universitario_ti', '2', 35.00, '9d198ceb020d08ea4e4466021613a8ab', 'Gustavo Jabornik', 'gustavojabornik123@gmail.com', '$2y$10$5W8shOE7503eXxU8aFll0uplI5QhAD21owWiXM6jeZcuue95.EsBu', '119.370.639-41', 'TW20250070', '(49) 99101-1237', 'Cesul', 25.00, NULL, 0, '2025-09-01 13:22:44', NULL, NULL),
(71, 0, 'participante', 'universitario_ti', '2', 35.00, '48538884e5a95dc6e077a2ddc638acbb', 'carlos henrique badia lazarin', 'carlos.badia31@gmail.com', '$2y$10$3zhaV7.YCctc.biTPsK11eUvPjkbqi9N0DPDB8B/uKvXZf6tVp5p2', '127.323.479-06', 'TW20250071', '(46) 99975-6130', 'Cesul', 25.00, NULL, 0, '2025-09-01 13:23:55', NULL, NULL),
(72, 0, 'participante', 'universitario_ti', '2', NULL, '5d82ea1576aeedee3524b710e9e52bb6', 'teste', 'teste2@teste.com', '$2y$10$FgIqjtczg1AN.dR7Y2LZKONhHO/pM285Hy7PDD0ITvsnIBOd29xYm', '979.004.725-84', 'TW20250072', '', '', 35.00, NULL, 0, '2025-09-01 13:36:26', NULL, NULL),
(73, 0, 'participante', 'universitario_ti', '2', 25.00, 'e8447800326c841250e7181b704feefe', 'Gabriel Faganello Bonassi', 'exegabriel086@gmail.com', '$2y$10$GxWNec2s9KoZQWz3/uNTyunWfDu7sCtug8Qi4OC90vZE/dnODiqri', '158.972.379-13', 'TW20250073', '(46) 99975-2206', 'UTFPR-FB', 35.00, NULL, 0, '2025-09-01 13:36:37', NULL, NULL),
(74, 0, 'participante', 'universitario_ti', '2', 35.00, '36aca00b80734ed202ebe428466738ca', 'Andreia de Almeida Teixeira', 'andreiateixeira@alunos.utfpr.edu.br', '$2y$10$VWw3wrEtkYsH5oSDFR8is.jiEpJfBPX3/.gUXWy286VXDBvETZQo2', '103.061.689-28', 'TW20250074', '(46) 99924-2765', 'UTFPR-FB', 35.00, NULL, 0, '2025-09-01 14:03:47', NULL, NULL),
(75, 0, 'participante', 'universitario_ti', '1', 25.00, 'de1710fa3dd7f2eb7f453c3354c4ed7c', 'Ang&eacute;lica Luiza Pagani', 'angelicapagani05@gmail.com', '$2y$10$3Pte5HnnkrDE0.zaJlfQvex/rEnb9jfW01q1l2DcjnGXJX83jbKzO', '123.508.749-23', 'TW20250075', '(46) 99908-8440', 'UTFPR', 35.00, NULL, 0, '2025-09-01 14:32:23', NULL, NULL),
(76, 0, 'participante', 'universitario_ti', '1', 25.00, '5a33dbffd10f9ca875d114f0344cb290', 'Ana Paula Ragievicz', 'anaragievicz@alunos.utfpr.edu.br', '$2y$10$8Gl2tMyc//50dFWyrsMy1.hVosCE9DNXzaDrPGhcH9D7qKh56vqq6', '116.064.159-56', 'TW20250076', '(46) 99984-8853', 'UTFPR', 35.00, NULL, 0, '2025-09-01 14:44:59', NULL, NULL),
(77, 0, 'participante', 'universitario_ti', '2', NULL, 'f5fac8ce3dfbdffb35266927c65cff5b', 'DANIEL MARTINS CASSARO FILHO', 'danielf.cassaro@gmail.com', '$2y$10$K/rYmr1a8KdRNOIJ.uf21uZkEaz0xUUczYXuFK.Qb48LW0x/KyKxS', '098.404.019-63', 'TW20250077', '(46) 92000-7398', 'Cesul ', 35.00, NULL, 0, '2025-09-01 14:54:48', NULL, NULL),
(78, 0, 'participante', 'universitario_ti', '2', 35.00, '800dadf4dc9f64ba90f0275a3df02e21', 'DIEGO FELIPE SCOPEL', 'diegoscopel10@gmail.com', '$2y$10$nREOsA5mQNzF0mcgEmf6NOO5Lzg2FdGJvZLAf7M2AvsSh6550s1G2', '122.933.499-85', 'TW20250078', '(46) 99125-8579', 'Cesul', 35.00, NULL, 0, '2025-09-01 16:05:38', NULL, NULL),
(79, 0, 'participante', 'universitario_ti', '2', NULL, '11033db9bdf716da750f71aee21bbf6e', 'Marco Antonio Raspini da Silva ', 'marco.raspini@cresol.com.br', '$2y$10$QTo5hnZsZ4ozkkt9zdQRDOzh3T7ix/yKe1MILvvC/2HPeeaZvMJle', '087.544.439-38', 'TW20250079', '(46) 93618-8832', 'Unipar', 35.00, NULL, 0, '2025-09-01 16:06:00', NULL, NULL),
(80, 0, 'participante', 'universitario_ti', '2', 35.00, '016835ff265f4d4cf5a6d4e17f4d025d', 'Emanuel Luan da Silva', 'emanuel.2025014656@alunocesul.com', '$2y$10$Q4x8b2RjSMFDvb3tlm.xTutOdqSzzLyrJvNfTWFtJBp7t3f/lz0Iy', '129.662.949-06', 'TW20250080', '(46) 99982-5044', 'Cesul', 35.00, NULL, 0, '2025-09-01 16:24:41', NULL, NULL),
(81, 0, 'participante', 'universitario_ti', '2', 35.00, '8c8b13f0048e3d01358ef980b795429a', 'Jo&atilde;o Biankati de Souza', 'joaobiankati0164@gmail.com', '$2y$10$koI17RbhAa/eAlgLksKaNe7rtDqGAHDxQh9JioD4HPaLp4siu/pvy', '095.428.559-07', 'TW20250081', '(46) 99919-3148', 'CESUL', 35.00, NULL, 0, '2025-09-01 16:27:12', NULL, NULL),
(82, 0, 'participante', 'universitario_ti', '2', 35.00, 'd6940dd8f9b9585cc1133ff5b56a51ba', 'Gabriel Tres', 'gabriel.2025014556@alunocesul.com', '$2y$10$KrDXqALjjXqGutNB3IiH0euKcLjoP1bXDLaQcGJKsEDu2YEM8LOBK', '080.144.409-86', 'TW20250082', '(46) 99977-1021', 'Cesul', 35.00, NULL, 0, '2025-09-01 16:27:23', NULL, NULL),
(83, 0, 'participante', 'universitario_ti', '2', 35.00, '87cebe67f93f60a9f9dc063ef90c855e', 'Edson Rafael Pavanelo ', 'rafaelpavanelo37@gmail.com', '$2y$10$b4qxBZFug8iAvUwXRVgEXerXERPBYKzmhsEQmbjwPiYYnSYvpvqBC', '099.661.499-02', 'TW20250083', '(46) 99140-2714', 'Utfpr', 35.00, NULL, 0, '2025-09-01 16:41:58', NULL, NULL),
(84, 0, 'participante', 'universitario_ti', '2', 35.00, '954f862bbc8fced7806caf1d940df14c', 'Gabriel Sim&atilde;o de Miranda Siminihuk', 'gsimao1108@gmail.com', '$2y$10$L0EKYvEIssdmnJt5VW8FYupkqKPSULou8X8SBO1SNOB9Q2Olf65Lu', '107.982.109-07', 'TW20250084', '(46) 99986-1679', '', 35.00, NULL, 0, '2025-09-01 16:42:51', NULL, NULL),
(85, 0, 'participante', 'universitario_ti', '1', 25.00, 'd9c99f653b05fa3f605e98eab772eb8f', 'Caetano Cesar Pavan', 'caetanopavan0@gmail.com', '$2y$10$EeTci0qyKqN8EBWPsuqjZuuusXZsxtpV9FYW48RP1xL/JjsD8xpy2', '133.478.559-73', 'TW20250085', '(46) 99975-9237', 'UTFPR', 35.00, NULL, 0, '2025-09-01 19:49:33', NULL, NULL),
(86, 0, 'participante', 'universitario_ti', '2', NULL, '09e8c5e0491f422a7db1388fadc48af8', 'Pedro Moura', 'pedrozanonimoura@gmail.com', '$2y$10$C18igYxsmpHoP7w8Oj3bjOk7FeopXHU.ar4lQNXauXaoyi8OuItte', '118.003.739-10', 'TW20250086', '(49) 99122-7928', 'CESUL', 35.00, NULL, 0, '2025-09-01 22:17:39', NULL, NULL),
(87, 0, 'participante', 'universitario_ti', '2', NULL, 'a4925c6a1c5c8ea4259e9eb32be620bc', 'ANTONIO GABRIEL ZANATTA RIZZOTTO', 'bananareidelass@gmail.com', '$2y$10$MB0bF6mW0m3438SPaQGkHuYq57U1dRPkw8oimo8hJYMGLLmS0qPzi', '084.363.859-18', 'TW20250087', '(49) 99166-4967', 'CESUL', 35.00, NULL, 0, '2025-09-01 22:28:30', NULL, NULL),
(88, 0, 'participante', 'universitario_ti', '2', NULL, '4b5ec5caccc9f2cb4582d9f2f483b39e', 'EMANUEL BERNARDON MACHADO', 'emanuel.bernardon.machado@escola.pr.gov.br', '$2y$10$uCDqAyrFbAxaDoeZSuq2ouvd/vDMiwG0K2Ym/MBOwzuh4TMiOB.Ry', '108.716.549-03', 'TW20250088', '(46) 99973-9831', 'CESUL', 35.00, NULL, 0, '2025-09-01 22:28:42', NULL, NULL),
(89, 0, 'participante', 'universitario_ti', '2', NULL, '89c5561b42ea81b9450dfaddd2f8c8fc', 'Gabriel Ghisi', 'gabriel.2025014621@alunocesul.com', '$2y$10$4uFABwtb2Slgl5eshpW2o.jpUZ4LVPgj1mZD9dSDFWvNwXAFi6L0G', '126.517.809-79', 'TW20250089', '(46) 99906-1861', 'Cesul', 35.00, NULL, 0, '2025-09-01 22:28:43', NULL, NULL),
(90, 0, 'participante', 'universitario_ti', '2', NULL, '531f214327081ce416a785285757ea25', 'Arthur Jos&eacute; Bandeira ', 'arthur.2024014360@alunocesul.com', '$2y$10$veIYx5VlMUNjm.TxcSSLVuVYmoFjrzGw76/Us7vEv1Tkh8DtG0vK.', '102.933.469-25', 'TW20250090', '(46) 99988-1679', 'Cesul', 35.00, NULL, 0, '2025-09-01 22:28:58', NULL, NULL),
(91, 0, 'participante', 'universitario_ti', '2', NULL, 'bfa2c589f2c35c0ee0c3f67bf9d29d41', 'Marco Antonio Muller', 'marcomullerantonio@gmail.com', '$2y$10$vuMuAJtOeGgmh4DcRQI.xur.vh7128ffYgm4nL0zLUus/mzSNtT9O', '098.761.589-02', 'TW20250091', '(48) 9100-4744', 'Cesul', 35.00, NULL, 0, '2025-09-01 22:29:36', NULL, NULL),
(92, 0, 'participante', 'universitario_ti', '2', NULL, '28706ebde38028650ef773d1eced0e54', 'JOAO JOSE OLBERMANN DICKEL', 'dickeljoao@gmail.com', '$2y$10$ZIW00vwFY5pZIB0LPHqQI.KaZ.N43qp4Yb9rOgtWn2NLIE0R2Kzz6', '107.295.519-97', 'TW20250092', '(49) 99171-1644', 'Cesul', 35.00, NULL, 0, '2025-09-01 22:30:12', NULL, NULL),
(93, 0, 'participante', 'universitario_ti', '2', NULL, '79d0695099043d3967f9beb4343e5760', 'Matheus Antunes perondi', 'mathsap40@outlook.com', '$2y$10$bktVlpod3pIqlUIHc1NngOX3QrNTmSLRaO4LjcnKWjzHn3x.WkExS', '101.380.209-81', 'TW20250093', '(46) 98803-7234', 'Cesul', 35.00, NULL, 0, '2025-09-01 22:32:13', NULL, NULL),
(94, 0, 'participante', 'universitario_ti', '2', NULL, '5f579824d4f479f57c6600b1364e9b87', 'paola machado', 'paolamachado2056@gmail.com', '$2y$10$a9FqP5mhbfpmZ7f3p8SdLuyIXrEy16bx1AL8aRn3QfAX1ZXOQOsVy', '141.109.269-47', 'TW20250094', '(49) 99827-6145', 'cesul', 35.00, NULL, 0, '2025-09-01 22:32:57', NULL, NULL),
(95, 0, 'participante', 'universitario_ti', '2', NULL, '424a39eec36a3ad895f19996e0f688ce', 'Gabriela S de Gois', 'gabriela.2023014224@alunocesul.com', '$2y$10$OQ308bJygl7nQHDfo2uPAOGOYZH/of2UXYTeXDYHHnm5YlO3o8sdW', '094.745.929-42', 'TW20250095', '(46) 9912-6962', 'Cesul', 35.00, NULL, 0, '2025-09-01 22:35:08', NULL, NULL),
(96, 0, 'participante', 'universitario_ti', '2', NULL, 'b5bcf813848ede02bfd969bdb900db03', 'Mateus Rigon Link', 'mateuslink0606@gmail.com', '$2y$10$jDTCtp0H1GchGdnoW8Y8uO6VoKXZ/Vt0m9MNP44QfF8c4euX04kUC', '137.137.009-58', 'TW20250096', '(46) 99134-6807', 'UTFPR', 35.00, NULL, 0, '2025-09-01 22:39:47', NULL, NULL),
(97, 0, 'participante', 'universitario_ti', '2', NULL, 'eb2f1d7a931e9fa2945a4544aa29e89c', 'Gustavo Maur&iacute;cio Budant', 'gustavobudant@gmail.com', '$2y$10$c929dL8WvQL4FKWcoVy1zOrtFN8Nw5LUCQ4iqimY80UWdLcJ3udLO', '098.257.869-57', 'TW20250097', '(41) 99925-3895', 'Utfpr', 35.00, NULL, 0, '2025-09-01 22:48:19', NULL, NULL),
(98, 0, 'participante', 'universitario_ti', '2', NULL, '67b5730e7c61d93156ce2f2fc21a3cbd', 'Guilherme Cazella', 'gui.cazella@gmail.com', '$2y$10$x/HGxDpvoWquem7zLj9R6Owhuf/REdLCaxtVhNgJb6O4uR5G7rMHS', '127.671.309-62', 'TW20250098', '(46) 99906-0122', 'Cesul', 35.00, NULL, 0, '2025-09-01 23:53:52', NULL, NULL),
(99, 0, 'participante', 'universitario_ti', '2', NULL, 'cc50cc02239f2eac4045029c721efac9', 'Kaua Barcelos', 'kauabarcelosmorais@gmail.com', '$2y$10$Ah55jpkDHx4TmhoBy5AeW.vDzTEsKiuWxW6/Znk7cY5AIe0NEstOm', '137.545.979-14', 'TW20250099', '(46) 98800-0972', 'Cesul', 35.00, NULL, 0, '2025-09-01 23:59:38', NULL, NULL),
(100, 0, 'participante', 'universitario_ti', '2', NULL, '380a370804ecf42e276a8e0d7f392826', 'Carmen Elena ', 'carmenburimyt@gmail.com', '$2y$10$79XujG49plZRQgrExfR7ee7Kzvuznerk1QPYlJIVdNzsg9cCQWism', '080.686.529-65', 'TW20250100', '(46) 99915-2006', '', 35.00, NULL, 0, '2025-09-02 00:11:04', NULL, NULL),
(101, 0, 'participante', 'universitario_ti', '2', NULL, '7828c4a3d400bee8a5faffa2c8617a25', 'Valenttin Motter', 'valenttinmotter@gmail.com', '$2y$10$kNe/4L0sLx.wg3akQzzb9.9Wp3fgMtBpyHupRYvL0dV/RAz2PEAuS', '118.066.529-51', 'TW20250101', '(46) 99984-1493', 'Cesul', 35.00, NULL, 0, '2025-09-02 00:12:48', NULL, NULL),
(102, 0, 'participante', 'universitario_ti', '2', NULL, '418a092dbdcc792d37c2fb7faa6aa976', 'João Vitor Candioto Nezzi', 'joaonezzi5@gmail.com', '$2y$10$Q7h7LRg4VFNoJdgTVk8yp.FH.fbnl/1IMoBr1B2kXnagneQZ9q5rS', '131.184.939-41', 'TW20250102', '(46) 99105-3808', 'CESUL', 35.00, NULL, 0, '2025-09-02 01:52:22', NULL, NULL),
(103, 0, 'participante', 'universitario_ti', '2', NULL, 'a018c6e4cda82ebc6ca80cc745017a71', 'Ketlin Vitória Santos das Neves ', 'ketlinneves@alunos.utfpr.edu.br', '$2y$10$VQivN9vCmCj5zh9TAVCaLOSNBjm5nQ0VZPlOI4NOEOrvzCEoxIAZq', '114.212.489-48', 'TW20250103', '(46) 98807-5778', 'Utfpr - fb', 35.00, NULL, 0, '2025-09-03 00:29:08', NULL, NULL),
(104, 0, 'participante', 'universitario_ti', '2', NULL, 'abe0e15acf78bf075f0b385c522374ed', 'Larissa Reiss Redivo ', 'redivolarissa6@gmail.com', '$2y$10$KU0x6d/cYOMpeioA4rsfE.DRtbxgculPUqVVPbZBRBKDlaAARKpRS', '082.861.899-22', 'TW20250104', '(46) 98834-5525', 'UTFPR', 35.00, NULL, 0, '2025-09-03 00:47:17', NULL, NULL),
(105, 0, 'participante', 'universitario_ti', '2', NULL, 'ab6fe44a381166a111ced57b5bd6bdee', 'Naiara da SIlva Aretz', 'naiaradasilva.aretz@gmail.com', '$2y$10$5cHYvAp9oQTB325KteNHseEtB4E6cSnv.Rq2gEmdTaA/4Ko71SI2e', '115.685.339-77', 'TW20250105', '(46) 99935-9974', 'UTFPR', 35.00, NULL, 0, '2025-09-03 01:12:55', NULL, NULL),
(106, 0, 'participante', 'universitario_ti', '2', NULL, '633c23cfcbcb27b3b20a7b8fc3d1e064', 'Rafael Ribeiro', 'rafael.2023014116@alunocesul.com', '$2y$10$S.p1qcoyOEZK9NTb0gar4O2oovA9Kj0vpVOrNxh0soHDJLG6B.HOi', '071.344.769-95', 'TW20250106', '(46) 93300-2103', 'Cesul', 35.00, NULL, 0, '2025-09-03 03:20:54', NULL, NULL),
(107, 0, 'participante', 'universitario_ti', '2', NULL, '7acf6e5de7f77e169f4d53d5ee667f4d', 'JOSE FERNANDO ROEHRS DOS SANTOS', 'josefernando@alunos.utfpr.edu.br', '$2y$10$F3aPJec268y4dS9NTuZPzOTQcb/qRaB4HorA59YZ0m/IGmBeqtv1.', '123.827.259-23', 'TW20250107', '(46) 99937-5514', '', 35.00, NULL, 0, '2025-09-03 14:22:09', NULL, NULL),
(108, 0, 'participante', 'universitario_ti', '2', NULL, '6892aca5930a7d10a939ef39de2c9308', 'Igor Bogoni Rathier', 'igorrathier@alunos.utfpr.edu.br', '$2y$10$xH.qpl9Z2c5oWTCyeR6BlubII2UXg4kUNQSXrhvOfwUvuz35fGj82', '154.632.409-73', 'TW20250108', '(49) 99914-8740', 'UTFPR-FB', 35.00, NULL, 0, '2025-09-03 21:15:48', NULL, NULL),
(109, 0, 'participante', 'universitario_ti', '2', NULL, '7a5d46e767b4157ff87e0669922fcc6f', 'Alexandre Gabriel Hammerschmidt ', 'alexandrehammerschmidt06@gmail.com', '$2y$10$EPrbzI/Me0NSyw8wchBPRu79ttO7P19CXqv6iB58DY0Crd9zpnRHO', '072.093.509-18', 'TW20250109', '(46) 99942-4644', 'Cesul', 35.00, NULL, 0, '2025-09-03 21:46:21', NULL, NULL),
(110, 0, 'participante', 'universitario_ti', '2', NULL, '5d9876148c589ebcfd59d6ef1304bb83', 'IVAN ROBERTO STEIN', 'ivan.stein@gmail.com', '$2y$10$UodNDMexnWOH7t62QhuKoOUdupwh9Ifw3EBHR5uL7itZhyJ3eiSIC', '025.732.759-22', 'TW20250110', '(46) 98802-0951', 'Cesul', 35.00, NULL, 0, '2025-09-03 21:57:28', NULL, NULL),
(111, 0, 'participante', 'universitario_ti', '2', NULL, '808e11f61efb0424b50c7b1bd027600b', 'Lucas Fagundes', 'lucasfagundesice@gmail.com', '$2y$10$WPt5mhYs9/pmLLcsdDcoW.kdAEDuJHJPx4qC6jnY7twCrfLB1jZQO', '094.974.279-13', 'TW20250111', '(46) 99926-3578', 'CESUL', 35.00, NULL, 0, '2025-09-03 22:06:45', NULL, NULL),
(112, 0, 'participante', 'universitario_ti', '2', NULL, 'b72bba95cf4e31f7b919c166a3d6e05e', 'Gustavo Henrique Scatola', 'scatolagustavo@gmail.com', '$2y$10$1OpsKQhMG/ZtMYGDPpm9eeInuKH2jL86LLllLDifXRHJ2SugBUADq', '115.167.709-43', 'TW20250112', '(46) 98401-4025', 'CESUL', 35.00, NULL, 0, '2025-09-03 22:06:57', NULL, NULL),
(113, 0, 'participante', 'universitario_ti', '2', NULL, 'f6d484eff8d473f49235c5b6f630a26f', 'Saimon Luiz Sebbe', 'saimonlsebben@gmail.com', '$2y$10$9cxSNDwmjmqZKqyjfXoowOfGaXOirrefoS4RV.ZUzJ5UyFhU4gJsC', '138.612.099-50', 'TW20250113', '(46) 99127-4488', 'Cesul', 35.00, NULL, 0, '2025-09-03 22:16:07', NULL, NULL),
(114, 0, 'participante', 'universitario_ti', '2', NULL, '08e2bb1b7010bb3013dd2c1eafa14b69', 'Roger Raspini', 'roger.2023014088@alunocesul.com', '$2y$10$1l3tZgKnB3aNbvQmpNtoueCLr9PQqO/rC7YPcPvz90Ac3dDCUgcIm', '124.134.089-75', 'TW20250114', '(46) 99910-9541', 'Cesul', 35.00, NULL, 0, '2025-09-03 22:26:16', NULL, NULL),
(115, 0, 'participante', 'universitario_ti', '2', NULL, 'd49f47f2f5eb619c1dba1fb798c1261a', 'Yohan Ara&uacute;jo De Abreu Franceschini ', 'yohanfranceschini1234@gmail.com', '$2y$10$xJzS1U6.k82t.wbTn5kcZO8/tYmNFEe.NOfoiUrgpgEBWdf/8z8Jm', '127.679.229-82', 'TW20250115', '(46) 99122-9903', 'UTFPR', 35.00, NULL, 0, '2025-09-03 22:34:35', NULL, NULL),
(116, 0, 'participante', 'universitario_ti', '2', NULL, '607e9f6c491655e0d645b6678ef9017f', 'Lourenco Daniel Balbinotti Picoli', 'lourencobalbinoti@gmail.com', '$2y$10$vSFvGmqMKC/zKCs6.WgVBuAgk73Mow0WQNmTuB3kGSq9KpI0WxrY2', '102.333.279-59', 'TW20250116', '(46) 99935-5076', 'UTFPR ', 35.00, NULL, 0, '2025-09-03 23:10:39', NULL, NULL),
(117, 0, 'participante', 'universitario_ti', '2', NULL, '253c974cadb4fa66436044790010067b', 'Alisson Rafael Siliprandi Haubert ', 'alissonhaubert@hotmail.com', '$2y$10$OO7xM2/0wNkADIgtY6aiz.9GcjSzvenf5gkMaUkkcTMUuNbzAKEca', '082.870.759-63', 'TW20250117', '', '', 35.00, NULL, 0, '2025-09-03 23:36:23', NULL, NULL),
(118, 0, 'participante', 'universitario_ti', '2', NULL, '2e12abbe43f44528aaae8c12ac35ce2c', 'Gabriel Felixtrowich Betiolo', 'gabrielbetiolo2020@gmail.com', '$2y$10$cv0HS1w5SDPGBKmFGozC2eJe35TE6qwbCrl9nOVw15bVMY57RzPmq', '097.184.209-40', 'TW20250118', '(46) 99901-0832', 'Utfpr', 35.00, NULL, 0, '2025-09-03 23:37:16', NULL, NULL),
(119, 0, 'participante', 'universitario_ti', '2', NULL, '7d4051eb27a28446f03d47e1ea5cf6c0', 'Marcos Paulo Macedo ', 'marcos.storchio@gmail.com', '$2y$10$kG9L7NWXdqm7V5yku4dBw.0L5HEfylZIpXwFsw25dDmImhiAZMEba', '090.537.119-45', 'TW20250119', '(46) 98834-3477', 'Unipar', 35.00, NULL, 0, '2025-09-03 23:38:38', NULL, NULL),
(120, 0, 'participante', 'universitario_ti', '2', NULL, 'f9d190c865dd7facd008c5dec25aa09c', 'Felix Barbosa da Silva Filho ', 'felinho@msn.com', '$2y$10$QRzd1xsK5VzppjJ3D7yf8OmDgfe9WihowuB9KMquo/Mbd3b610wli', '033.592.509-07', 'TW20250120', '(46) 99901-1696', 'Universidade Tecnologia Federal do Paran&aacute; ', 35.00, NULL, 0, '2025-09-04 00:16:11', NULL, NULL),
(121, 0, 'participante', 'universitario_ti', '2', NULL, 'e9c5cd4769939206c38f4e97a4886c8d', 'Guilherme Cadore Bert&eacute;', 'guilhermeberte370@gmail.com', '$2y$10$S1rWzYuPgCdAtZI/lPB38O49tyEO7BAMZn5aiaVwGgynO3nvfI3Qy', '099.263.029-04', 'TW20250121', '(46) 98820-0322', 'Cesul ', 35.00, NULL, 0, '2025-09-04 14:43:27', NULL, NULL),
(122, 0, 'participante', 'universitario_ti', '2', NULL, '6a71b47ffba9f4dc0a7ec3b69060e3b0', 'Luis Henrique da Silva ternoski ', 'ternoskiluis@gmail.com', '$2y$10$vpBAgWd3f9D0BwZQ46U1yOg2ibJ4gwaX7coIIPYWc.GqF8VwRfuxm', '118.844.739-46', 'TW20250122', '(46) 99141-8315', 'UTFPR ', 35.00, NULL, 0, '2025-09-04 15:00:06', NULL, NULL),
(123, 0, 'participante', 'universitario_ti', '2', NULL, '12103aeffe7029c9100b4d52d1cae0ae', 'Jo&atilde;o Felipe Freitas Carneiro ', 'joaofelipefreitas2007@gmail.com', '$2y$10$kZ7ulfPPowa/OJhU3Afj2eLeX6A49ad46bVKt3l4RMdD6e2hyriG.', '085.384.289-26', 'TW20250123', '(46) 98814-6971', 'Cesul', 35.00, NULL, 0, '2025-09-04 17:59:01', NULL, NULL),
(124, 0, 'participante', 'universitario_ti', '2', NULL, '0b8f733f95a27d37f033e53fc9e2709f', 'Jo&atilde;o Vitor migon krug ', 'vitormigon2018@gmail.com', '$2y$10$02SWz/qarSUaND8Y.NGwneW0eZgUvUyz5bI8VdF90f.Oped8pREry', '138.666.259-36', 'TW20250124', '(46) 99930-3168', 'UTFPR-FB', 35.00, NULL, 0, '2025-09-05 11:31:26', NULL, NULL),
(125, 0, 'participante', 'universitario_ti', '2', NULL, '783d58adc5b177cc2e7fe4a1cda7078b', 'Thiago Gusm&atilde;o Moreira', 'thiagomoreira.2020@alunos.utfpr.edu.br', '$2y$10$Nt9Rk4L3hS0Q/6jDGLqxPe7UhHVy2KBOQocd65ZVPn7XypxnJeOmO', '075.074.189-95', 'TW20250125', '(46) 98803-4018', 'UTFPR Francisco Beltr&atilde;o ', 35.00, NULL, 0, '2025-09-05 17:43:20', NULL, NULL),
(126, 0, 'participante', 'universitario_ti', '2', NULL, '8547fb88ed9b31e4fb986c45b8bc72b5', 'Bruno Giovanny Santana Felix ', 'brunofelixgsf@gmail.com', '$2y$10$TsQo1tsA2ynlhJKD86xGmuQNoUavpYNZhRqYhMyZWEKLWVQoH0ZrS', '451.541.898-09', 'TW20250126', '(14) 14991-7131', 'UTFPR', 35.00, NULL, 0, '2025-09-05 18:17:59', NULL, NULL),
(127, 0, 'participante', 'universitario_ti', '2', NULL, 'ab18d10bddf3d708eb1e647cd0d512af', 'Henrique Pit Zanella ', 'hpitt991@gmail.com', '$2y$10$5FHdk2CDieYAjiR7WDcDHuVkXDin38xtNbF5/jokXFE8MDq1QqptK', '090.912.249-08', 'TW20250127', '(46) 99135-9048', 'Cesul ', 35.00, NULL, 0, '2025-09-05 19:58:34', NULL, NULL),
(128, 0, 'participante', 'universitario_ti', '2', NULL, '75878065300345f343158d090aa89e61', 'Gustavo da Silva', 'gustavosilva.2006@alunos.utfpr.edu.br', '$2y$10$SkyykulqSTwZZx8IlTHN8.7o1oXCKxn3WpoJ1nbbmGmpC3Sw.k5IS', '118.092.929-21', 'TW20250128', '(46) 98821-9091', 'UTFPR-FB', 35.00, NULL, 0, '2025-09-06 00:45:49', NULL, NULL),
(129, 0, 'participante', 'universitario_ti', '2', NULL, '183fa4312ce6ed7f8c71906597e70b53', 'JOAO PEDRO MARTINS CAZUNI', 'frigideira90@gmail.com', '$2y$10$pRAkcBvSlwfYTuBmJQeFZ.Qoah9gXeejmFoNwtSou5bJX4la8i.wS', '107.746.039-25', 'TW20250129', '(49) 99101-0754', 'cesul', 35.00, NULL, 0, '2025-09-06 18:44:59', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `precos_inscricao`
--

CREATE TABLE `precos_inscricao` (
  `id` int(11) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `lote` varchar(50) DEFAULT 'regular',
  `ativo` tinyint(1) DEFAULT 1,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `precos_inscricao`
--

INSERT INTO `precos_inscricao` (`id`, `categoria`, `descricao`, `valor`, `lote`, `ativo`, `data_inicio`, `data_fim`) VALUES
(1, 'universitario_ti', 'Universitário de TI - 1° Lote', 25.00, '1', 1, NULL, NULL),
(2, 'universitario_ti', 'Universitário de TI - 2° Lote', 35.00, '2', 1, NULL, NULL),
(3, 'ensino_medio', 'Ensino Médio - 1° Lote', 15.00, '1', 1, NULL, NULL),
(4, 'publico_geral', 'Público Geral - 1° Lote', 50.00, '1', 1, NULL, NULL),
(5, 'hackathon_inscrito', 'Hackathon (Inscritos no evento)', 15.00, 'regular', 1, NULL, NULL),
(6, 'hackathon_nao_inscrito', 'Hackathon (Não inscritos no evento)', 50.00, 'regular', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `presencas`
--

CREATE TABLE `presencas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_atividade` int(3) NOT NULL,
  `id_participante` int(3) NOT NULL,
  `data_hora` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transacoes`
--

CREATE TABLE `transacoes` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `data` varchar(50) NOT NULL,
  `tipo` enum('entrada','saida') NOT NULL,
  `comprovante_id` int(11) DEFAULT NULL,
  `participante_id` int(11) DEFAULT NULL,
  `data_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `transacoes`
--

INSERT INTO `transacoes` (`id`, `categoria_id`, `descricao`, `valor`, `data`, `tipo`, `comprovante_id`, `participante_id`, `data_registro`) VALUES
(1, 1, 'Inscrição - INGRID MAYARA SPISS ANDRADE', 25.00, '2025-08-30', 'entrada', 1, 5, '2025-09-01 21:23:06'),
(2, 1, 'Inscrição - Maikon Icaro dos Santos', 25.00, '2025-08-30', 'entrada', 2, 6, '2025-09-01 21:23:06'),
(3, 1, 'Inscrição - Camila Queli Sockenski Thome ', 25.00, '2025-08-30', 'entrada', 3, 7, '2025-09-01 21:23:06'),
(4, 1, 'Inscrição - Jo&atilde;o Vitor Reolon ', 25.00, '2025-08-30', 'entrada', 4, 9, '2025-09-01 21:23:06'),
(5, 1, 'Inscrição - Gabriel Luiz Barcelos ', 25.00, '2025-08-30', 'entrada', 5, 10, '2025-09-01 21:23:06'),
(6, 1, 'Inscrição - Emanuel Henrique Stecanella', 25.00, '2025-08-30', 'entrada', 6, 11, '2025-09-01 21:23:06'),
(7, 1, 'Inscrição - Laura Poloni Bell&eacute;', 25.00, '2025-08-30', 'entrada', 7, 12, '2025-09-01 21:23:06'),
(8, 1, 'Inscrição - Marcos Antonio Reolon Peters', 25.00, '2025-08-30', 'entrada', 8, 8, '2025-09-01 21:23:06'),
(9, 1, 'Inscrição - Emily Gabrielle Sousa Pia', 25.00, '2025-08-30', 'entrada', 9, 14, '2025-09-01 21:23:06'),
(10, 1, 'Inscrição - Victor Matheus Paim Cales Cura', 25.00, '2025-08-30', 'entrada', 10, 13, '2025-09-01 21:23:06'),
(11, 1, 'Inscrição - RICARDO CAPISTRANO', 25.00, '2025-08-30', 'entrada', 11, 15, '2025-09-01 21:23:06'),
(12, 1, 'Inscrição - Luiz Gustavo Gomes da Silva ', 25.00, '2025-08-30', 'entrada', 12, 16, '2025-09-01 21:23:06'),
(13, 1, 'Inscrição - Eduardo Conte', 25.00, '2025-08-30', 'entrada', 13, 18, '2025-09-01 21:23:06'),
(14, 1, 'Inscrição - Adriel Shiguemi Cikoski Kiyota ', 25.00, '2025-08-30', 'entrada', 14, 20, '2025-09-01 21:23:06'),
(15, 1, 'Inscrição - Welington Felipe Blasius', 25.00, '2025-08-30', 'entrada', 15, 23, '2025-09-01 21:23:06'),
(16, 1, 'Inscrição - Sulamita Sarom Peixoto Gomes ', 25.00, '2025-08-30', 'entrada', 16, 21, '2025-09-01 21:23:06'),
(17, 1, 'Inscrição - Camile Vitoria Paz', 25.00, '2025-08-30', 'entrada', 17, 24, '2025-09-01 21:23:06'),
(18, 1, 'Inscrição - Mariana dos Santos Siqueira', 25.00, '2025-08-30', 'entrada', 18, 17, '2025-09-01 21:23:06'),
(19, 1, 'Inscrição - Myguel Henryque Dachery do Prado ', 25.00, '2025-08-30', 'entrada', 19, 27, '2025-09-01 21:23:06'),
(20, 1, 'Inscrição - Eduardo de Miranda', 25.00, '2025-08-31', 'entrada', 20, 26, '2025-09-01 21:23:06'),
(21, 1, 'Inscrição - Julia Caroline de Ramos da Silva', 25.00, '2025-08-31', 'entrada', 21, 22, '2025-09-01 21:23:06'),
(22, 1, 'Inscrição - Jo&atilde;o Aloisio Battisti', 25.00, '2025-08-31', 'entrada', 22, 28, '2025-09-01 21:23:06'),
(23, 1, 'Inscrição - Vinicius Raitz Pilati', 25.00, '2025-08-31', 'entrada', 23, 29, '2025-09-01 21:23:06'),
(24, 1, 'Inscrição - Pedro Henrique Peruzzo', 25.00, '2025-08-31', 'entrada', 24, 31, '2025-09-01 21:23:06'),
(25, 1, 'Inscrição - Vitor Gabriel dos Santos Conte', 25.00, '2025-08-31', 'entrada', 25, 32, '2025-09-01 21:23:06'),
(26, 1, 'Inscrição - GABRIELI MOURA NARDI', 25.00, '2025-08-31', 'entrada', 26, 33, '2025-09-01 21:23:06'),
(27, 1, 'Inscrição - André Buriola Trevisan ', 25.00, '2025-08-31', 'entrada', 27, 34, '2025-09-01 21:23:06'),
(28, 1, 'Inscrição - Lucas Furtado Souza ', 25.00, '2025-08-31', 'entrada', 28, 30, '2025-09-01 21:23:06'),
(29, 1, 'Inscrição - Ana Carolina de Morais Benedetti', 25.00, '2025-08-31', 'entrada', 29, 35, '2025-09-01 21:23:06'),
(30, 1, 'Inscrição - Igor Kussumoto do Nascimento ', 25.00, '2025-08-31', 'entrada', 30, 36, '2025-09-01 21:23:06'),
(31, 1, 'Inscrição - Viviane Aparecida Hreneczen', 25.00, '2025-08-31', 'entrada', 31, 38, '2025-09-01 21:23:06'),
(32, 1, 'Inscrição - Gabrielle Dall Alba Pozzebon ', 25.00, '2025-08-31', 'entrada', 32, 39, '2025-09-01 21:23:06'),
(33, 1, 'Inscrição - Kauan Mendon&ccedil;a ', 25.00, '2025-08-31', 'entrada', 33, 40, '2025-09-01 21:23:06'),
(34, 1, 'Inscrição - Lucas Gabriel Garcia da Silva ', 25.00, '2025-08-31', 'entrada', 34, 41, '2025-09-01 21:23:06'),
(35, 1, 'Inscrição - Eduardo Gabriel', 25.00, '2025-08-31', 'entrada', 35, 42, '2025-09-01 21:23:06'),
(36, 1, 'Inscrição - Samira Laura Talau do Nascimento ', 25.00, '2025-08-31', 'entrada', 36, 43, '2025-09-01 21:23:06'),
(37, 1, 'Inscrição - Roger Gabriel Schneider Kobs', 25.00, '2025-08-31', 'entrada', 37, 44, '2025-09-01 21:23:06'),
(38, 1, 'Inscrição - Giovana Estrela Godinho ', 25.00, '2025-08-31', 'entrada', 38, 46, '2025-09-01 21:23:06'),
(39, 1, 'Inscrição - Emanuel Henrique Choldys Biava', 25.00, '2025-08-31', 'entrada', 39, 45, '2025-09-01 21:23:06'),
(40, 1, 'Inscrição - Alana Yasmin zauza ', 25.00, '2025-08-31', 'entrada', 40, 25, '2025-09-01 21:23:06'),
(41, 1, 'Inscrição - Ruan Kowalski ', 25.00, '2025-09-01', 'entrada', 41, 47, '2025-09-01 21:23:06'),
(42, 1, 'Inscrição - Lucas Scotti', 25.00, '2025-09-01', 'entrada', 42, 48, '2025-09-01 21:23:06'),
(43, 1, 'Inscrição - henrique dos santos junkes', 25.00, '2025-09-01', 'entrada', 43, 50, '2025-09-01 21:23:06'),
(44, 1, 'Inscrição - Guilherme Sartori ', 25.00, '2025-09-01', 'entrada', 44, 49, '2025-09-01 21:23:06'),
(45, 1, 'Inscrição - Karine Guedes ', 25.00, '2025-09-01', 'entrada', 45, 51, '2025-09-01 21:23:06'),
(46, 1, 'Inscrição - Alisson Koerich ', 25.00, '2025-09-01', 'entrada', 46, 52, '2025-09-01 21:23:06'),
(47, 1, 'Inscrição - Jo&atilde;o Ezequiel Hansen Silva', 25.00, '2025-09-01', 'entrada', 47, 53, '2025-09-01 21:23:06'),
(48, 1, 'Inscrição - Gabriel Biankati de Souza ', 25.00, '2025-09-01', 'entrada', 48, 54, '2025-09-01 21:23:06'),
(49, 1, 'Inscrição - Jo&atilde;o Vitor Pires de Souza', 25.00, '2025-09-01', 'entrada', 49, 55, '2025-09-01 21:23:06'),
(50, 1, 'Inscrição - JEAN CARLOS BRUNHERA DE LIZ', 25.00, '2025-09-01', 'entrada', 50, 57, '2025-09-01 21:23:06'),
(51, 1, 'Inscrição - Lu&iacute;s Felipe Busatto Reichett', NULL, '2025-09-01', 'entrada', 51, 62, '2025-09-01 21:27:12'),
(52, 1, 'Inscrição - MOISES LUIZ MATHIAS ZANDONAI', 25.00, '2025-09-01', 'entrada', 52, 61, '2025-09-01 21:27:12'),
(53, 1, 'Inscrição - JOSE RENATO PERIN JUNIOR', 25.00, '2025-09-01', 'entrada', 53, 60, '2025-09-01 21:27:12'),
(54, 1, 'Inscrição - ROBERT JEAN DA ROSA', 35.00, '2025-09-01', 'entrada', 54, 64, '2025-09-01 21:27:12'),
(55, 1, 'Inscrição - PEDRO HENRIQUE DOS SANTOS PAES', 35.00, '2025-09-01', 'entrada', 55, 65, '2025-09-01 21:27:12'),
(56, 1, 'Inscrição - Gabriel Augusto Perin', 35.00, '2025-09-01', 'entrada', 56, 59, '2025-09-01 21:27:12'),
(57, 1, 'Inscrição - Luiz Feix', 35.00, '2025-09-01', 'entrada', 57, 66, '2025-09-01 21:27:12'),
(58, 1, 'Inscrição - Monica Dinkel Baggio ', 35.00, '2025-09-01', 'entrada', 58, 58, '2025-09-01 21:27:12'),
(59, 1, 'Inscrição - Gustavo Jabornik', 35.00, '2025-09-01', 'entrada', 60, 70, '2025-09-01 21:27:12'),
(60, 1, 'Inscrição - carlos henrique badia lazarin', 35.00, '2025-09-01', 'entrada', 62, 71, '2025-09-01 21:27:12'),
(61, 1, 'Inscrição - Gabriel Faganello Bonassi', 25.00, '2025-09-01', 'entrada', 63, 73, '2025-09-01 21:27:12'),
(62, 1, 'Inscrição - VICTOR AUGUSTO DE SOUZA DA SILVA', 35.00, '2025-09-01', 'entrada', 64, 68, '2025-09-01 21:27:12'),
(63, 1, 'Inscrição - Andreia de Almeida Teixeira', 35.00, '2025-09-01', 'entrada', 65, 74, '2025-09-01 21:27:12'),
(64, 1, 'Inscrição - Ang&eacute;lica Luiza Pagani', 25.00, '2025-09-01', 'entrada', 66, 75, '2025-09-01 21:27:12'),
(65, 1, 'Inscrição - Ana Paula Ragievicz', 25.00, '2025-09-01', 'entrada', 67, 76, '2025-09-01 21:27:12'),
(67, 1, 'Inscrição - DIEGO FELIPE SCOPEL', 35.00, '2025-09-01', 'entrada', 69, 78, '2025-09-01 21:27:12'),
(68, 1, 'Inscrição - Gabriel Tres', 35.00, '2025-09-01', 'entrada', 70, 82, '2025-09-01 21:27:12'),
(69, 1, 'Inscrição - Emanuel Luan da Silva', 35.00, '2025-09-01', 'entrada', 71, 80, '2025-09-01 21:27:12'),
(70, 1, 'Inscrição - Jo&atilde;o Biankati de Souza', 35.00, '2025-09-01', 'entrada', 72, 81, '2025-09-01 21:27:12'),
(71, 1, 'Inscrição - Edson Rafael Pavanelo ', 35.00, '2025-09-01', 'entrada', 73, 83, '2025-09-01 21:27:12'),
(72, 1, 'Inscrição - Jo&atilde;o Biankati de Souza', 35.00, '2025-09-01', 'entrada', 74, 81, '2025-09-01 21:27:12'),
(73, 1, 'Inscrição - Gabriel Sim&atilde;o de Miranda Siminihuk', 35.00, '2025-09-01', 'entrada', 75, 84, '2025-09-01 21:27:12'),
(74, 1, 'Inscrição - Caetano Cesar Pavan', 25.00, '2025-09-01', 'entrada', 76, 85, '2025-09-01 21:27:12'),
(75, 1, 'Inscrição - Matheus Antunes perondi', NULL, '2025-09-01', 'entrada', 85, 93, '2025-09-01 23:56:09'),
(76, 1, 'Inscrição - Mateus Rigon Link', NULL, '2025-09-01', 'entrada', 84, 96, '2025-09-01 23:56:21'),
(77, 1, 'Inscrição - paola machado', NULL, '2025-09-01', 'entrada', 83, 94, '2025-09-01 23:56:25'),
(78, 1, 'Inscrição - Marco Antonio Muller', NULL, '2025-09-01', 'entrada', 82, 91, '2025-09-01 23:56:29'),
(79, 1, 'Inscrição - Arthur Jos&eacute; Bandeira ', NULL, '2025-09-01', 'entrada', 81, 90, '2025-09-01 23:56:33'),
(80, 1, 'Inscrição - EMANUEL BERNARDON MACHADO', NULL, '2025-09-01', 'entrada', 78, 88, '2025-09-01 23:56:41'),
(81, 1, 'Inscrição - ANTONIO GABRIEL ZANATTA RIZZOTTO', NULL, '2025-09-01', 'entrada', 79, 87, '2025-09-01 23:56:50'),
(82, 1, 'Inscrição - Pedro Moura', NULL, '2025-09-01', 'entrada', 77, 86, '2025-09-01 23:57:28'),
(83, 1, 'Inscrição - Guilherme Cazella', NULL, '2025-09-01', 'entrada', 86, 98, '2025-09-01 23:57:52'),
(84, 1, 'Inscrição - Gabriel Ghisi', NULL, '2025-09-01', 'entrada', 80, 89, '2025-09-02 02:40:45'),
(85, 1, 'Inscrição - Valenttin Motter', NULL, '2025-09-01', 'entrada', 89, 101, '2025-09-02 02:41:14'),
(86, 1, 'Inscrição - Carmen Elena ', NULL, '2025-09-01', 'entrada', 88, 100, '2025-09-02 02:41:19'),
(87, 1, 'Inscrição - Kaua Barcelos', NULL, '2025-09-01', 'entrada', 87, 99, '2025-09-02 02:41:25'),
(88, 1, 'Inscrição - João Vitor Candioto Nezzi', NULL, '2025-09-01', 'entrada', 90, 102, '2025-09-02 02:43:38'),
(90, 1, 'Inscrição - Naiara da SIlva Aretz', NULL, '2025-09-04', 'entrada', 94, 105, '2025-09-04 14:57:27'),
(91, 1, 'Inscrição - Rafael Ribeiro', NULL, '2025-09-04', 'entrada', 95, 106, '2025-09-04 14:57:46'),
(92, 1, 'Inscrição - JOSE FERNANDO ROEHRS DOS SANTOS', NULL, '2025-09-04', 'entrada', 96, 107, '2025-09-04 14:57:51'),
(93, 1, 'Inscrição - Igor Bogoni Rathier', NULL, '2025-09-04', 'entrada', 97, 108, '2025-09-04 14:57:57'),
(94, 1, 'Inscrição - Gustavo Henrique Scatola', NULL, '2025-09-04', 'entrada', 98, 112, '2025-09-04 14:58:04'),
(95, 1, 'Inscrição - IVAN ROBERTO STEIN', NULL, '2025-09-04', 'entrada', 99, 110, '2025-09-04 14:58:18'),
(96, 1, 'Inscrição - Lucas Fagundes', NULL, '2025-09-04', 'entrada', 100, 111, '2025-09-04 14:58:24'),
(97, 1, 'Inscrição - DANIEL MARTINS CASSARO FILHO', NULL, '2025-09-04', 'entrada', 101, 77, '2025-09-04 14:58:29'),
(98, 1, 'Inscrição - Roger Raspini', NULL, '2025-09-04', 'entrada', 102, 114, '2025-09-04 14:58:32'),
(99, 1, 'Inscrição - Lourenco Daniel Balbinotti Picoli', NULL, '2025-09-04', 'entrada', 103, 116, '2025-09-04 14:58:38'),
(100, 1, 'Inscrição - Alisson Rafael Siliprandi Haubert ', NULL, '2025-09-04', 'entrada', 104, 117, '2025-09-04 14:58:47'),
(101, 1, 'Inscrição - Gabriel Felixtrowich Betiolo', NULL, '2025-09-04', 'entrada', 105, 118, '2025-09-04 14:58:58'),
(102, 1, 'Inscrição - Marcos Paulo Macedo ', NULL, '2025-09-04', 'entrada', 106, 119, '2025-09-04 14:59:02'),
(103, 1, 'Inscrição - Felix Barbosa da Silva Filho ', NULL, '2025-09-04', 'entrada', 107, 120, '2025-09-04 14:59:07'),
(104, 1, 'Inscrição - Guilherme Cadore Bert&eacute;', NULL, '2025-09-04', 'entrada', 108, 121, '2025-09-04 14:59:12'),
(105, 1, 'Inscrição - Luis Henrique da Silva ternoski ', NULL, '2025-09-04', 'entrada', 109, 122, '2025-09-04 15:05:02'),
(106, 1, 'Inscrição - Saimon Luiz Sebbe', NULL, '2025-09-04', 'entrada', 111, 113, '2025-09-05 00:00:58'),
(107, 1, 'Inscrição - Jo&atilde;o Felipe Freitas Carneiro ', NULL, '2025-09-04', 'entrada', 110, 123, '2025-09-05 00:01:07'),
(108, 1, 'Saimon Luiz Sebbe', 35.00, '04/09/2025', 'entrada', NULL, NULL, '2025-09-05 00:05:52'),
(109, 1, 'João Felipe Freitas Carneiro', 35.00, '04/09/2025', 'entrada', NULL, NULL, '2025-09-05 00:06:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `atividades`
--
ALTER TABLE `atividades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `backups`
--
ALTER TABLE `backups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categorias_transacoes`
--
ALTER TABLE `categorias_transacoes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comprovantes`
--
ALTER TABLE `comprovantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `participante_id` (`participante_id`);

--
-- Indexes for table `informacoes_evento`
--
ALTER TABLE `informacoes_evento`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inscricoes_atividades`
--
ALTER TABLE `inscricoes_atividades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_inscricao` (`participante_id`,`atividade_id`),
  ADD KEY `atividade_id` (`atividade_id`);

--
-- Indexes for table `organizadores`
--
ALTER TABLE `organizadores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `participantes`
--
ALTER TABLE `participantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Indexes for table `precos_inscricao`
--
ALTER TABLE `precos_inscricao`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transacoes`
--
ALTER TABLE `transacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `comprovante_id` (`comprovante_id`),
  ADD KEY `participante_id` (`participante_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `atividades`
--
ALTER TABLE `atividades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `backups`
--
ALTER TABLE `backups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categorias_transacoes`
--
ALTER TABLE `categorias_transacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `comprovantes`
--
ALTER TABLE `comprovantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `informacoes_evento`
--
ALTER TABLE `informacoes_evento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inscricoes_atividades`
--
ALTER TABLE `inscricoes_atividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `organizadores`
--
ALTER TABLE `organizadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `participantes`
--
ALTER TABLE `participantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `precos_inscricao`
--
ALTER TABLE `precos_inscricao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `transacoes`
--
ALTER TABLE `transacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comprovantes`
--
ALTER TABLE `comprovantes`
  ADD CONSTRAINT `comprovantes_ibfk_1` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inscricoes_atividades`
--
ALTER TABLE `inscricoes_atividades`
  ADD CONSTRAINT `inscricoes_atividades_ibfk_1` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transacoes`
--
ALTER TABLE `transacoes`
  ADD CONSTRAINT `transacoes_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_transacoes` (`id`),
  ADD CONSTRAINT `transacoes_ibfk_2` FOREIGN KEY (`comprovante_id`) REFERENCES `comprovantes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transacoes_ibfk_3` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
