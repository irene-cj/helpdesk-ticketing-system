-- MySQL dump 10.13  Distrib 8.2.0, for macos13 (arm64)
--
-- Host: localhost    Database: helpdesk_db
-- ------------------------------------------------------
-- Server version	8.2.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ticket_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`),
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,1,1,'Issue has been fixed and email has been sent to you with your new login information. Once you are logged in change your password. If you have any other questions do not hesitate to reach out.','2026-02-26 00:46:39');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ticket_id` int NOT NULL,
  `sent_to` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,2,'KaliU@helpdesk.com','Ticket created notification sent','2026-02-26 00:43:21'),(2,1,'KaliU@helpdesk.com','Status updated to Closed','2026-02-26 00:46:50'),(3,1,'KaliU@helpdesk.com','Status updated to Closed','2026-02-26 00:46:55'),(4,3,'KaliU@helpdesk.com','Ticket created notification sent','2026-02-26 02:06:56'),(5,9,'KaliU@helpdesk.com','Ticket created notification sent','2026-02-26 02:52:08');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `assigned_to` int DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `category` enum('Hardware','Software','Network','Account','Other') DEFAULT 'Other',
  `priority` enum('Low','Medium','High','Critical') DEFAULT 'Medium',
  `status` enum('Open','In Progress','Resolved','Closed') DEFAULT 'Open',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `assigned_to` (`assigned_to`),
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (1,2,NULL,'Login Error- Unable to Access Work Applications','Currently unable to access several work applications due to login error. When attempting to sign in, I get an authentication error message. It states that my credentials are invalid even though they are not.','Account','Medium','Closed','2026-02-25 04:35:41','2026-02-26 00:46:48'),(2,2,NULL,'Outage Preventing Business Operations','Many users are unable to access the company\'s primary production system. The application fails to load and returns a server timeout. This outage is preventing staff from doing core job functions. This issue started at 9:15 am.','Software','Critical','Open','2026-02-26 00:43:18','2026-02-26 00:43:18'),(3,2,NULL,'Password Reset','I have been locked out of my account and need to reset my password.','Account','Low','Open','2026-02-26 02:06:56','2026-02-26 02:06:56'),(4,2,NULL,'Work Laptop Battery','My work Laptop does not turn on if it isn\'t plugged in. This issue started 2/12/26','Hardware','Medium','Open','2026-02-26 02:10:17','2026-02-26 02:10:17'),(5,2,NULL,'Work Laptop Battery','My work Laptop does not turn on if it isn\'t plugged in. This issue started 2/12/26','Hardware','Medium','Open','2026-02-26 02:10:21','2026-02-26 02:10:21'),(6,2,NULL,'Work Laptop Battery','My work Laptop does not turn on if it isn\'t plugged in. This issue started 2/12/26','Hardware','Medium','Open','2026-02-26 02:11:29','2026-02-26 02:11:29'),(7,2,NULL,'Work Laptop Battery','My work Laptop does not turn on if it isn\'t plugged in. This issue started 2/12/26','Hardware','Medium','Open','2026-02-26 02:11:34','2026-02-26 02:11:34'),(8,2,NULL,'Locked out of account.','I have been locked out of my account due to many failed login attempts.','Account','Low','Open','2026-02-26 02:12:42','2026-02-26 02:12:42'),(9,2,NULL,'Printer Not Found','Printer needs resolving. I keep getting \"printer not found\" errors, as well as clearing print queues.','Hardware','Low','Open','2026-02-26 02:52:05','2026-02-26 02:52:05');
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('staff','admin') DEFAULT 'staff',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin User','admin@helpdesk.com','$2y$10$rZVXY4n4Fp5EmgZJnl4K1ONdRDQ3IE9FvGZDKPw5rn1GmE4pi4KYO','admin','2026-02-21 02:35:09'),(2,'Kali Uchis','KaliU@helpdesk.com','$2y$10$HCPMEajh6fzTGmNpFHEoiuru9AI8kF8pJ13HylPvcGp2dqQZYKi/y','staff','2026-02-25 04:29:04');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-25 20:53:32
