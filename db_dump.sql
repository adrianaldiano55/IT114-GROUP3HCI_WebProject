-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 28, 2026 at 02:15 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fayds`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `usertype` enum('admin','staff','customer') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `login_at` timestamp NULL DEFAULT NULL,
  `logout_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `usertype`, `created_at`, `login_at`, `logout_at`) VALUES
(4, 'Adrian', 'adrianaldiano55@gmail.com', '$2y$10$vRC97NmbjOQsiE1WvxMo4.KBzJUJryQqEpzqz/Wyz.AtJffjncsGC', 'customer', '2026-03-09 08:18:47', NULL, NULL),
(6, 'Steve', 'steve123@gmail.com', '$2y$10$jUthebMR7urkofk49zHz5uDTH0uLZ.ZIvRNrScTH3fyTlwGU1I2kO', 'customer', '2026-03-15 00:05:59', NULL, NULL),
(7, 'Karkat', 'karkat413@gmail.com', '$2y$10$tWEy1MYwmSsa2W5juiP0fuBK6VTl.ars1VFPft7qlPFyQP8UxJ7EC', 'customer', '2026-03-15 00:07:00', NULL, NULL),
(8, 'William', 'william@gmail.com', '$2y$10$u8e0gQSVh9iJJhmXyoGGSe9y/azo/6XArlp7jgd8nx6H0dlslUMs.', 'staff', '2026-03-15 00:11:14', NULL, NULL),
(9, 'Robert', 'robert@gmail.com', '$2y$10$Ohjsy88z05PgDAiVlSNPL.VbDOA1UsN1E0F/zSbFZcBG0I/zx5izO', 'staff', '2026-03-15 00:12:00', NULL, NULL),
(10, 'John', 'john@gmail.com', '$2y$10$pQVgOLPXwF6na0hnzPuOx.oPH0vx5c9ufLx5HBn42HssUkPzyIVBK', 'customer', '2026-03-28 00:26:39', NULL, NULL),
(11, 'Dave', 'dave@gmail.com', '$2y$10$6kD4LSVSoXDKh685nruzkOnS0YNcygzFSKI/RuYj/bwsCsTeBiF.G', 'customer', '2026-03-28 00:26:58', NULL, NULL),
(12, 'Rose', 'rose@gmail.com', '$2y$10$ty0ltitsTzJmWjM37Ed7/uHEkftkNGTH/v6wacqM8mqbElFNRRXxK', 'customer', '2026-03-28 00:27:15', NULL, NULL),
(13, 'Jade', 'jade@gmail.com', '$2y$10$uWFjIbUU0amYIQ2CSbza..tjqkt9HBSvGJfaBGvdvt0hAREkgqobC', 'customer', '2026-03-28 00:27:33', NULL, NULL),
(15, 'Aradia', 'aradia@gmail.com', '$2y$10$P.QwfU.mZ7GCOlkDHVBw5.wpKJvBPJdsc75wcpqWSGPuVKmOiWqjS', 'customer', '2026-03-28 00:29:47', NULL, NULL),
(16, 'Tavros', 'tavros@gmail.com', '$2y$10$xwDESgDc/xqBhH/ROgaea.tQgu4FDH.uBxO6XLCzVYQtyPWbm7DAa', 'customer', '2026-03-28 00:30:02', NULL, NULL),
(17, 'Sollux', 'sollux@gmail.com', '$2y$10$VvpMRNjGNszQHfXlsUOZTOlmooHsHc0drG959ZPrTRnKSifq7I8dm', 'customer', '2026-03-28 00:30:21', NULL, NULL),
(18, 'Nepeta', 'nepeta@gmail.com', '$2y$10$lcDVpyyAVelkgxsi.jL3/OP8NFrxoF5yIV8LNLMFR1.6MnQZ1RjJ6', 'customer', '2026-03-28 00:30:39', NULL, NULL),
(19, 'Kanaya', 'kanaya@gmail.com', '$2y$10$vMiKAQe9011BN0OhC3SSw.2Zgex3szPYiy0Nq5eHKN7nqNTZBlise', 'customer', '2026-03-28 00:30:57', NULL, NULL),
(20, 'Terezi', 'terezi@gmail.com', '$2y$10$Mx5Dq.aPHA6RbSu94LKE0.C0NtzSHOnvv71hXjT3XeAdSU104bY2i', 'customer', '2026-03-28 00:31:13', NULL, NULL),
(21, 'Vriska', 'vriska@gmail.com', '$2y$10$Ji0cwnZQ7C/MPhimnBntDOGcinpn5kqUdxw8Bg03UNI5/uzJ1n8X.', 'customer', '2026-03-28 00:31:30', NULL, NULL),
(22, 'Equius', 'equius@gmail.com', '$2y$10$TZ5S//bdA66pp.OtZHLW7.AG.v11ayeKnLFupAtBxeLIsS3pdT8EK', 'customer', '2026-03-28 00:31:52', NULL, NULL),
(23, 'Gamzee', 'gamzee@gmail.com', '$2y$10$291Uaf3H6e3HsQFyXXdcWu.Oexq1juy6u1LJszf8w623HaZKH4uq6', 'customer', '2026-03-28 00:32:14', NULL, NULL),
(24, 'Eridan', 'eridan@gmail.com', '$2y$10$bb4DUc3VF0R7efv8u8wwDeoDVwB9AgnC6R8TxMDvlenHDHorpfQdC', 'customer', '2026-03-28 00:32:29', NULL, NULL),
(25, 'Feferi', 'feferi@gmail.com', '$2y$10$QgkoP5L7GGN4BLzpUZEF0O.Ias4kKF2FhyKyFrHklJHP7S.kRbWBu', 'customer', '2026-03-28 00:33:02', NULL, NULL),
(26, 'Kankri', 'kankri@gmail.com', '$2y$10$dElHyqAyPa1p7FCbR4p5SO9pGwWOv5q7COnOoSf9fEgfVJFInUiYm', 'customer', '2026-03-28 00:33:58', NULL, NULL),
(27, 'Damara', 'damara@gmail.com', '$2y$10$p5qb0P0GbayatC1CYM.NlOJ3FgxWAYzNyVez5m/Gx6AmmSW/nciP.', 'customer', '2026-03-28 00:34:13', NULL, NULL),
(28, 'Rufioh', 'rufioh@gmail.com', '$2y$10$Qm.V3GjA9rPM0Fc3rlG4m.CIrQceQbvcH/XxUD9b0C9TUcWk/fihi', 'customer', '2026-03-28 00:34:30', NULL, NULL),
(29, 'Mituna', 'mituna@gmail.com', '$2y$10$U7bpzNPsDf8Mpk9A5TPAAO/SkPl0kJ34IiOzgduY23kuL9ptY2nK.', 'customer', '2026-03-28 00:34:46', NULL, NULL),
(30, 'Meulin', 'meulin@gmail.com', '$2y$10$zHWI1BDEX3nKCp87B8KPF.23lhL0/QwNr1bZ0.C7tsdl2rfGtYU1u', 'customer', '2026-03-28 00:35:06', NULL, NULL),
(31, 'Porrim', 'porrim@gmail.com', '$2y$10$VpiGkDvYR9z4nS3JHawz/umHDn5vQgkD9UkCYC0NjZJgpxCI5aM5i', 'customer', '2026-03-28 00:35:22', NULL, NULL),
(32, 'Latula', 'latula@gmail.com', '$2y$10$dN710VA4.QWoV1JbCpLQH.LW4p9WNaXatsGLjID6B7ei4P/FVOgTW', 'customer', '2026-03-28 00:35:43', NULL, NULL),
(33, 'Aranea', 'aranea@gmail.com', '$2y$10$dnbOzV7TROsjksg63hjgNOwl2gFdJpdlWyxH82JdG.6RzUcxZ7.PG', 'customer', '2026-03-28 00:36:24', NULL, NULL),
(34, 'Horuss', 'horuss@gmail.com', '$2y$10$p2be22iW5d71b1Od2N1Ceu9fTnZu6MtGoN8z/asoNNcHiATxEAp9u', 'customer', '2026-03-28 00:36:46', NULL, NULL),
(35, 'Kurloz', 'kurloz@gmail.com', '$2y$10$g66NoPEFB4GRTmC/YJTlheUtovtgxd498ObfW74/5anBtJb1g/EKG', 'customer', '2026-03-28 00:37:07', NULL, NULL),
(36, 'Cronus', 'cronus@gmail.com', '$2y$10$y14qD52JvRxGcwAfdzgZ9.KXS2/R2//TFUtvtb3.BXJbYmcBswI12', 'customer', '2026-03-28 00:37:32', NULL, NULL),
(37, 'Meenah', 'meenah@gmail.com', '$2y$10$rMJAXvMpwxxH80/tKyLz4ueFI29GrPRwAwYfYJHi5uaQ9zCR2fpaW', 'customer', '2026-03-28 00:37:48', NULL, NULL),
(38, 'Jane', 'jane@gmail.com', '$2y$10$ycCaF.XdjVRe3cYCpkl8mebbIGV1sqSZ8NK5.jmNARCHcD63XuyS2', 'customer', '2026-03-28 00:38:53', NULL, NULL),
(39, 'Roxy', 'roxy@gmail.com', '$2y$10$JS3Ptlkv9dxt/Bz5seob1e9XqCdSCAtvluZirKjp04GN4XxqHekwS', 'customer', '2026-03-28 00:39:11', NULL, NULL),
(40, 'Dirk', 'dirk@gmail.com', '$2y$10$6zOpZEMVLUYq45dcXtI20O/a0xA/TpHmNK3ZHPDxM4WDEzl1qNNkO', 'customer', '2026-03-28 00:39:24', NULL, NULL),
(41, 'Jake', 'jake@gmail.com', '$2y$10$vK/TaclQrNL.Ownm93Pcpe2DilMlgOvo1gDDu1yeTmp/M2bU5W01y', 'customer', '2026-03-28 00:39:40', NULL, NULL),
(42, 'Spades', 'spades@gmail.com', '$2y$10$5oVgE1DpLMW2j5HLVhExKe9oMJ7rYWqKgAkDms/yLHHbdJCKY9sMa', 'customer', '2026-03-28 00:41:27', NULL, NULL),
(43, 'Diamond', 'diamond@gmail.com', '$2y$10$vu6iMilUYOHE0URvvv3mbuy/br7gkbMCU2h9ju.qzy3Rkzx.MyxT6', 'customer', '2026-03-28 00:41:43', NULL, NULL),
(44, 'Hearts', 'hearts@gmail.com', '$2y$10$eINV2/N/8fztU3mmf1k63e6J6lUIlmZFODykr197fukWSLQAelS86', 'customer', '2026-03-28 00:42:07', NULL, NULL),
(45, 'Clubs', 'clubs@gmail.com', '$2y$10$llwl0LE4DrKsZ6RXKLE3x.GMMqfbcRXNTPDkGoqoplic/qlcnGjgK', 'customer', '2026-03-28 00:42:30', NULL, NULL),
(46, 'Snowman', 'snowman@gmail.com', '$2y$10$hRq8.79IP6je9Q7k34IJp.qcG43MRDlaIndXOxecc.fQPRa48BULi', 'customer', '2026-03-28 00:42:42', NULL, NULL),
(47, 'AR', 'AR@gmail.com', '$2y$10$7oNA/XYHjiQABzkAwmakZOAJblQyXpeVFfykzvVXGchEUGQi1TG5m', 'customer', '2026-03-28 00:43:22', NULL, NULL),
(48, 'WV', 'WV@gmail.com', '$2y$10$HwzxtDjASBdxyd9Xz8Guquy5l.wm9oPOUk/QgLMqOQDBzPQA6pSs.', 'customer', '2026-03-28 00:43:41', NULL, NULL),
(49, 'PM', 'PM@gmail.com', '$2y$10$a5T2.LCuW67WZBhIAjr5xeI9NuqU3XH/W4dor9JGEshoC8CfKLsCW', 'customer', '2026-03-28 00:44:11', NULL, NULL),
(50, 'WS', 'WS@gmail.com', '$2y$10$VIY8uidR4DKy2D7FPmtCXOtOIh/BVomBQQxRDbBggm/FN0KqNw1Y.', 'customer', '2026-03-28 00:44:55', NULL, NULL),
(51, 'Calliope', 'Calliope@gmail.com', '$2y$10$WwR7RZ7slnX1iIGt.tOrg..AgMa0T5RkbkzaJmdWPu0Ny/bHd8u4K', 'customer', '2026-03-28 00:48:17', NULL, NULL),
(52, 'Caliborn', 'Caliborn@gmail.com', '$2y$10$9ecLQEroLafpQGF337XTcu5Bul7qHatX.z9BucFwzFoHyKHbK5Ps.', 'customer', '2026-03-28 00:48:33', NULL, NULL),
(53, 'LilCal', 'LilCal@gmail.com', '$2y$10$L3GuYZ5RC5CkRcdi/Ktj9.QPVgWWZaUNDVYMX6WXTCn7xv/R9AtUu', 'customer', '2026-03-28 00:49:17', NULL, NULL),
(54, 'Davepeta', 'Davepeta@gmail.com', '$2y$10$cJQeh79VwYEyLiMiU/3WhOMmBTkeEXVWN.D9cA7CkMWcwCwVH5X8.', 'customer', '2026-03-28 00:51:07', NULL, NULL),
(55, 'Fefeta', 'Fefeta@gmail.com', '$2y$10$l7Jq7JHz5gOLo2qLB//.8O98V65.NwtJbf6V5Of8NHBVUktZHpape', 'customer', '2026-03-28 00:51:32', NULL, NULL),
(56, 'Rosesprite', 'Rosesprite@gmail.com', '$2y$10$LJyVPW6H5l2wwDp3bunjVuTJzINO9MHzVmYWtnaKOq1aj1SHrOr/a', 'customer', '2026-03-28 00:51:49', NULL, NULL),
(57, 'Erisol', 'Erisol@gmail.com', '$2y$10$Y1UFJTgTvkmddkA994n0HuQSeSTqFOqX8MTr3bQyPfWoLf2Kqgk/G', 'customer', '2026-03-28 00:52:27', NULL, NULL),
(58, 'Nanna1', 'Nanna1@gmail.com', '$2y$10$KS4G/pYJoiDVun70vUDpt.X/3jIrDeF8Zr6IkDfVz.HGQmGLjnlsu', 'customer', '2026-03-28 00:52:55', NULL, NULL),
(59, 'Nanna2', 'Nanna2@gmail.com', '$2y$10$sz535tLfMLskatGPKxyL7u0Zcjv95bpR9E3BVFU6wRBeZzP7uIdDe', 'customer', '2026-03-28 00:53:06', NULL, NULL),
(60, 'Crow', 'Crow@gmail.com', '$2y$10$JAfzPboP4prKpS3pxx88ReA.TCXI7F6L1Dr8cqlSUliW1jba2utjW', 'customer', '2026-03-28 00:53:36', NULL, NULL),
(61, 'June', 'June@gmail.com', '$2y$10$xL4mLOQP7R68oRfQoUSgPOXk7pyr3/jOOIAw8szU4L2Q.IMWt6jsu', 'customer', '2026-03-28 00:53:51', NULL, NULL),
(62, 'TheCondensce', 'TheCondensce@gmail.com', '$2y$10$o5BSqHgOMEaKNXB2l/WuTurYsUmYdtIsyZNKONs1jiyFc0s5NjpBm', 'customer', '2026-03-28 00:54:31', NULL, NULL),
(63, 'LordEnglish', 'LordEnglish413@gmail.com', '$2y$10$UPbM1bO3xH7C2KGoAAyH5uVxaK1yujCIDWX9KfyMUne7PEIrZw3y6', 'customer', '2026-03-28 00:58:21', NULL, NULL),
(64, 'DocScratch', 'docscratch@gmail.com', '$2y$10$iwGBMWsSWwbpvMt6.ZUGyuw2MX9WZKy09z7fVgdNYPx7O67zmMTMu', 'customer', '2026-03-28 00:58:53', NULL, NULL),
(65, 'Skitter', 'taylor@gmail.com', '$2y$10$1JVApQ7pkYqg00P/DF4vY.ehOBinQoRl1p2KpQL.EfQ5sm7v0Yj1y', 'customer', '2026-03-28 01:01:29', NULL, NULL),
(66, 'TattleTale', 'tattletale@gmail.com', '$2y$10$ZbXI6fq/oD3aQ9agdNMl0.CHoxTYAjgN2/XXEDIVszY8OUnMUGGB6', 'customer', '2026-03-28 01:02:10', NULL, NULL),
(67, 'Grue', 'grue@gmail.com', '$2y$10$deqAa3a2gnbo.QLbVZ6auOgPy.ng0Mk2xXU18xbUfv5cSsbDI.khe', 'customer', '2026-03-28 01:02:29', NULL, NULL),
(68, 'Hellhound', 'hellhound@gmail.com', '$2y$10$/PxvjEOiEqCETA4CNionIeBrWfERNscMmBWAaj0l0Zq6uotRDTpUO', 'customer', '2026-03-28 01:03:02', NULL, NULL),
(69, 'Regent', 'regent@gmail.com', '$2y$10$2so2dYjqkIXr1FdsZ5d/WO45ihhwZ1vZgwc8fLqOL.kzXZWOyB6eG', 'customer', '2026-03-28 01:03:22', NULL, NULL),
(70, 'Imp', 'imp@gmail.com', '$2y$10$P53sEqDwK1OIG7bMvvT1/.Df6mI4yMRFX7n8fWovv2iURSaSWm5gS', 'customer', '2026-03-28 01:03:40', NULL, NULL),
(71, 'Parian', 'parian@gmail.com', '$2y$10$7qtXfbDIWbtL7sv0kGqCCeSwPsaMYMbVyMTu6Y2W43IaJo.H.9NW6', 'customer', '2026-03-28 01:04:02', NULL, NULL),
(72, 'MissMilitia', 'missmilitia@gmail.com', '$2y$10$RsQL.gdhq2LLtA.3vFRngOKK1T8m3VjYQ0jS2Lx0E7xIlW9KZDRae', 'customer', '2026-03-28 01:04:30', NULL, NULL),
(73, 'Assault', 'assault@gmail.com', '$2y$10$IHjTdA4LDGHaySRJpAmLXewvDbui81tGJ9ECBc280ZN//U8Dbtlie', 'customer', '2026-03-28 01:04:50', NULL, NULL),
(74, 'Triumph', 'triumph@gmail.com', '$2y$10$.7p.pUvzHDAazc0kW6oEruc9UaCu.e.1gqiy2/5Mi3xWRFS84O5/y', 'customer', '2026-03-28 01:05:12', NULL, NULL),
(75, 'Adamant', 'adamant@gmail.com', '$2y$10$61w6X1./iYuHLlXc7DUXz.U5HjiNaopUB/bo9DudgA6H7GNQaQIHe', 'customer', '2026-03-28 01:05:30', NULL, NULL),
(76, 'Sere', 'sere@gmail.com', '$2y$10$ElTpKJkppZynYEoAB7P2NOtFTcL/Z/UzkLcsjszzyVJHSiaZlIntu', 'customer', '2026-03-28 01:05:38', NULL, NULL),
(77, 'Armsmaster', 'armsmaster@gmail.com', '$2y$10$sITkmr04hmuzrdv.PknWfupHEVe9e2pjDifAByujKav0qAbSNo4bW', 'customer', '2026-03-28 01:05:51', NULL, NULL),
(78, 'Dragon', 'dragon@gmail.com', '$2y$10$iv5DDMXvseq/Xijh2Q2wrurbWqunK5PknPB2lMj3HBvUD4bXtwV4O', 'customer', '2026-03-28 01:05:58', NULL, NULL),
(79, 'Battery', 'battery@gmail.com', '$2y$10$WD7oAIro3lUqHjouLCCmnOT/snYyT69SpF3Zuq5AhsQXf9W7k9RwS', 'customer', '2026-03-28 01:06:08', NULL, NULL),
(80, 'Dauntless', 'dauntless@gmail.com', '$2y$10$X5rmG7n6cuTabJ19UhIS/OHoszA9oUvfbw8Y56WyllYwmVT6thZNC', 'customer', '2026-03-28 01:06:18', NULL, NULL),
(81, 'Velocity', 'velocity@gmail.com', '$2y$10$zfHqPp5WfQyqGINL9im9ieWPOF0YpWuZxht35RwoZ/0TyJtL1CQWS', 'customer', '2026-03-28 01:06:27', NULL, NULL),
(82, 'Clockblocker', 'clockblocker@gmail.com', '$2y$10$I1O0xqLWR48HlDSleRcq4OxDvLGxWq4uTuq3BbefkrlSp2nTKhLbi', 'customer', '2026-03-28 01:06:40', NULL, NULL),
(83, 'Vista', 'vista@gmail.com', '$2y$10$K6ehkSxxKPIdWi7oKvuRpefX.cmx8TDY0CLXMboaSDX3yx27Jl9.K', 'customer', '2026-03-28 01:06:49', NULL, NULL),
(84, 'KidWin', 'kidwin@gmail.com', '$2y$10$O2pRZgs32O4BVJSL8cQIBO7Vjf4hK5KtpYUipYn.PsqcHppevoocy', 'customer', '2026-03-28 01:06:59', NULL, NULL),
(85, 'Flechette', 'flechette@gmail.com', '$2y$10$3XTdSKdmERTURgVCGW8SQe.vL/GtshVIOukqXfccJ.65ZiREs6WJ2', 'customer', '2026-03-28 01:07:10', NULL, NULL),
(86, 'ShadowStalker', 'shadowstalker@gmail.com', '$2y$10$HzwfhuNbEwoQPZ2T41Iw4OyVhB2vITveq4d3N0ircIgBsyAVR2482', 'customer', '2026-03-28 01:07:23', NULL, NULL),
(87, 'Chariot', 'chariot@gmail.com', '$2y$10$tQHEV5MjRtfeJnXrj8F1cOogd7RBt00lfOmPZUlyxN/0OvrvqBVey', 'customer', '2026-03-28 01:07:35', NULL, NULL),
(88, 'Weld', 'weld@gmail.com', '$2y$10$M6TY/hHeKKzzbiJPrlYklumjE2S6xGQ24snkGbCxa3l.U23SfzJli', 'customer', '2026-03-28 01:07:44', NULL, NULL),
(89, 'Aegis', 'aegis@gmail.com', '$2y$10$lN4mBvnRr8ieBSSUqIjCc.hEDoMEVnT0SBeLpm3fi4joGWOhYQb7u', 'customer', '2026-03-28 01:07:53', NULL, NULL),
(90, 'Browbeat', 'browbeat@gmail.com', '$2y$10$hHaG.BtgBHo84NkxPlzxfOcaAKLSTIHAwUaYxwpbn03TYygiCFwSu', 'customer', '2026-03-28 01:08:04', NULL, NULL),
(91, 'Gallant', 'gallant@gmail.com', '$2y$10$v4jUBGGzY7mZ7MAAFVmh2eJP0lzL3LTkWa3kxpAbyoIHR/C3hwCAe', 'customer', '2026-03-28 01:08:15', NULL, NULL),
(92, 'LadyPhoton', 'ladyphoton@gmail.com', '$2y$10$ehuzuChg.W3t90RtnUKv/.Qbn4vma3zeQFwIjRN2cZtZA9GMTq3t2', 'customer', '2026-03-28 01:09:06', NULL, NULL),
(93, 'Manpower', 'manpower@gmail.com', '$2y$10$cfylbAfiK0wBVqg4MhaSWu1m9QYB83m32inwEayZ3himA3h.kg5iW', 'customer', '2026-03-28 01:09:29', NULL, NULL),
(94, 'Flashbang', 'flashbang@gmail.com', '$2y$10$ud1WcgtZe4HNVoeKZlx35.7uliMp3/.qBV/s8uN9OfSX4Mj/8Ra7O', 'customer', '2026-03-28 01:09:49', NULL, NULL),
(95, 'Brandish', 'brandish@gmail.com', '$2y$10$hG3o/3Z2A8eTwQV01CVki.Puy5woltxChgeJ9ks2bNZqOCnmjHnOe', 'customer', '2026-03-28 01:10:19', NULL, NULL),
(96, 'GloryGirl', 'glorygirl@gmail.com', '$2y$10$jgoW/fu7pEA4UZaaMjia7.z4BSjcVntUrmceKoKuEVTx5xTiNk2jm', 'customer', '2026-03-28 01:10:40', NULL, NULL),
(97, 'Panacea', 'panacea@gmail.com', '$2y$10$wkWzjZsoh6ts0TQ6IHK8MONm/NiLgX4Bos7kFJKkQFeMGWNTvEERG', 'customer', '2026-03-28 01:11:10', NULL, NULL),
(98, 'JackSlash', 'jackslash@gmail.com', '$2y$10$71Vel2oWg0lWVu6IOc9tAuEc4X4b52ni3UCT6.EW4NP/MIDxjukPC', 'customer', '2026-03-28 01:12:34', NULL, NULL),
(99, 'Bonesaw', 'bonesaw@gmail.com', '$2y$10$XtUQJ5rKJkmS1qcCzuEOIuOq/CCU6ZeHsoHZMSpYzKc.kpYoteyDS', 'customer', '2026-03-28 01:12:43', NULL, NULL),
(100, 'Crawler', 'crawler@gmail.com', '$2y$10$j4DPt2uxGwgA9cybOl4HHOLfpuUNV1jKpCOtemNLteJr1Os27NqVm', 'customer', '2026-03-28 01:12:57', NULL, NULL),
(101, 'Mannequin', 'mannequin@gmail.com', '$2y$10$pv.mz64qKqKiUDiu5FedpebSDunMYaUdHNKkK.fhEIwdt/RI5AURK', 'customer', '2026-03-28 01:13:06', NULL, NULL),
(102, 'Shatterbird', 'shatterbird@gmail.com', '$2y$10$b3kPECkMatFLxykCFSxK5.6CaiRhQBL884rYrtekIhKU3QlwWnqo6', 'customer', '2026-03-28 01:13:17', NULL, NULL),
(103, 'TheSiberian', 'thesiberian@gmail.com', '$2y$10$tpdYLQ2W6d09A7YYudqKEuUt72lkLjOjtfcUQd0zP25G8U8lwJz.a', 'customer', '2026-03-28 01:13:29', NULL, NULL),
(104, 'Burnscar', 'burnscar@gmail.com', '$2y$10$J0Vz.kYWSeVMYEBM.RuGDuCGbuofQm.HzqDnHXEgbVwuisaGsVmTm', 'customer', '2026-03-28 01:13:40', NULL, NULL),
(105, 'Cherish', 'cherish@gmail.com', '$2y$10$a1Y9dE0x2eL2I7QReTMszOkj5lWsNZni.GFms0toCM8p441LtGFCu', 'customer', '2026-03-28 01:13:51', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
