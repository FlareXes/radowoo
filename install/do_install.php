<?php
ini_set('max_execution_time', 300); //300 seconds

if (isset($_POST)) {
    $host = $_POST['host'];
    $dbuser = $_POST['dbuser'];
    $dbpassword = $_POST['dbpassword'];
    $dbname = $_POST['dbname'];

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];

    $login_password = $_POST['password'] ? $_POST['password'] : '';

    //check required fields
    if (!($host && $dbuser && $dbname && $first_name && $last_name && $email && $login_password)) {
        echo json_encode(['success' => false, 'message' => 'Please input all fields.']);
        exit();
    }

    //check for valid email
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        echo json_encode(['success' => false, 'message' => 'Please input a valid email.']);
        exit();
    }

    if (!preg_match('/^[a-zA-Z0-9]{5,}$/', $username)) {
        echo json_encode(['success' => false, 'message' => 'Please input a valid username.']);
        exit();
    }

    //check for valid database connection
    $mysqli = @new mysqli($host, $dbuser, $dbpassword, $dbname);

    if (mysqli_connect_errno()) {
        echo json_encode(['success' => false, 'message' => $mysqli->connect_error]);
        exit();
    }

    /*
     * check the db config file
     * if db already configured, we'll assume that the installation has completed
     */
    $db_file_path = '../application/config/database.php';
    $db_file = file_get_contents($db_file_path);
    $is_installed = strpos($db_file, 'install_enter_hostname');

    if (!$is_installed) {
        echo json_encode(['success' => false, 'message' => "Seems this app is already installed! You can't reinstall it again."]);
        exit();
    }

    //start installation
    $sql = "# ************************************************************
    # Sequel Pro SQL dump
    # Version 5446
    #
    # https://www.sequelpro.com/
    # https://github.com/sequelpro/sequelpro
    #
    # Host: 127.0.0.1 (MySQL 8.0.26)
    # Database: qz_dev
    # Generation Time: 2021-11-14 06:57:03 +0000
    # ************************************************************
    
    
    /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
    /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
    /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
    /*!40101 SET NAMES utf8 */;
    SET NAMES utf8mb4;
    /*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
    /*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
    /*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
    
    
    # Dump of table advertisment
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `advertisment`;
    
    CREATE TABLE `advertisment` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
      `url` text,
      `image` text,
      `position` varchar(255) DEFAULT NULL,
      `status` tinyint DEFAULT '0',
      `added` datetime DEFAULT NULL,
      `updated` datetime DEFAULT NULL,
      `is_goole_adsense` tinyint DEFAULT '0',
      `google_ad_code` longtext,
      `ad_order` int DEFAULT '0',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
    
    
    
    # Dump of table blog_category
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `blog_category`;
    
    CREATE TABLE `blog_category` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `parent_category` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `display_on_home` tinyint DEFAULT '0' COMMENT '0=not display,1= display ',
      `category_is_delete` tinyint DEFAULT '0' COMMENT '0=notdelete,1=softdelete',
      `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table blog_post
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `blog_post`;
    
    CREATE TABLE `blog_post` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `blog_category_id` int unsigned DEFAULT NULL,
      `post_title` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `post_slug` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `post_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `post_image` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `post_status` tinyint DEFAULT '1' COMMENT '0=unpublished,1=published',
      `post_author_id` int unsigned DEFAULT NULL,
      `published` timestamp NULL DEFAULT NULL,
      `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `meta_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `meta_keywords` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `meta_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table captcha
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `captcha`;
    
    CREATE TABLE `captcha` (
      `captcha_id` bigint unsigned NOT NULL AUTO_INCREMENT,
      `captcha_time` int unsigned DEFAULT NULL,
      `ip_address` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0',
      `word` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      PRIMARY KEY (`captcha_id`),
      KEY `word` (`word`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table category
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `category`;
    
    CREATE TABLE `category` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `category_title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `category_slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `parent_category` int DEFAULT NULL,
      `category_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `display_on_home` tinyint DEFAULT '0',
      `category_icon` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `category_image` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `category_status` tinyint DEFAULT '1' COMMENT '1=active,0=inactive',
      `category_is_delete` tinyint DEFAULT '0' COMMENT '0=notdelete,1=softdelete',
      `category_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `category_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `order` int DEFAULT '0',
      PRIMARY KEY (`id`),
      UNIQUE KEY `category_slug` (`category_slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table ci_sessions
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `ci_sessions`;
    
    CREATE TABLE `ci_sessions` (
      `id` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `ip_address` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `timestamp` int unsigned DEFAULT '0',
      `data` mediumblob,
      PRIMARY KEY (`id`),
      KEY `ci_sessions_timestamp` (`timestamp`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table coupon
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `coupon`;
    
    CREATE TABLE `coupon` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `category_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `coupon_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `coupon_for` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `coupon_discount_type` enum('Percent','Value') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `discount_value` decimal(10,2) DEFAULT NULL,
      `no_of_times_can_be_used` int DEFAULT NULL,
      `expiry_date` datetime DEFAULT NULL,
      `status` enum('Active','Inactive') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'Active',
      `is_all` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `created_at` datetime DEFAULT NULL,
      `updated_at` datetime DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table coupon_relation_data
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `coupon_relation_data`;
    
    CREATE TABLE `coupon_relation_data` (
      `coupon_id` int unsigned DEFAULT NULL,
      `relation_data_id` int unsigned DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table courses
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `courses`;
    
    CREATE TABLE `courses` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `title` varchar(255) DEFAULT '',
      `description` longtext,
      `added` datetime DEFAULT NULL,
      `updated` datetime DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
    
    
    
    # Dump of table currency
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `currency`;
    
    CREATE TABLE `currency` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `code` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `symbol` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    LOCK TABLES `currency` WRITE;
    /*!40000 ALTER TABLE `currency` DISABLE KEYS */;
    
    INSERT INTO `currency` (`id`, `name`, `code`, `symbol`)
    VALUES
      (1,'Leke','ALL','Lek'),
      (2,'Dollars','USD','$'),
      (3,'Afghanis','AFN','؋'),
      (4,'Pesos','ARS','$'),
      (5,'Guilders','AWG','ƒ'),
      (6,'Dollars','AUD','$'),
      (7,'New Manats','AZN','ман'),
      (8,'Dollars','BSD','$'),
      (9,'Dollars','BBD','$'),
      (10,'Rubles','BYR','p.'),
      (11,'Euro','EUR','€'),
      (12,'Dollars','BZD','BZ$'),
      (13,'Dollars','BMD','$'),
      (14,'Bolivianos','BOB','$b'),
      (15,'Convertible Marka','BAM','KM'),
      (16,'Pula','BWP','P'),
      (17,'Leva','BGN','лв'),
      (18,'Reais','BRL','R$'),
      (19,'Pounds','GBP','£'),
      (20,'Dollars','BND','$'),
      (21,'Riels','KHR','៛'),
      (22,'Dollars','CAD','$'),
      (23,'Dollars','KYD','$'),
      (24,'Pesos','CLP','$'),
      (25,'Yuan Renminbi','CNY','¥'),
      (26,'Pesos','COP','$'),
      (27,'Colón','CRC','₡'),
      (28,'Kuna','HRK','kn'),
      (29,'Pesos','CUP','₱'),
      (30,'Koruny','CZK','Kč'),
      (31,'Kroner','DKK','kr'),
      (32,'Pesos','DOP ','RD$'),
      (33,'Dollars','XCD','$'),
      (34,'Pounds','EGP','£'),
      (35,'Colones','SVC','$'),
      (36,'Pounds','FKP','£'),
      (37,'Dollars','FJD','$'),
      (38,'Cedis','GHC','¢'),
      (39,'Pounds','GIP','£'),
      (40,'Quetzales','GTQ','Q'),
      (41,'Pounds','GGP','£'),
      (42,'Dollars','GYD','$'),
      (43,'Lempiras','HNL','L'),
      (44,'Dollars','HKD','$'),
      (45,'Forint','HUF','Ft'),
      (46,'Kronur','ISK','kr'),
      (47,'Rupees','INR','Rp'),
      (48,'Rupiahs','IDR','Rp'),
      (49,'Rials','IRR','﷼'),
      (50,'Pounds','IMP','£'),
      (51,'New Shekels','ILS','₪'),
      (52,'Dollars','JMD','J$'),
      (53,'Yen','JPY','¥'),
      (54,'Pounds','JEP','£'),
      (55,'Tenge','KZT','лв'),
      (56,'Won','KPW','₩'),
      (57,'Won','KRW','₩'),
      (58,'Soms','KGS','лв'),
      (59,'Kips','LAK','₭'),
      (60,'Lati','LVL','Ls'),
      (61,'Pounds','LBP','£'),
      (62,'Dollars','LRD','$'),
      (63,'Switzerland Francs','CHF','CHF'),
      (64,'Litai','LTL','Lt'),
      (65,'Denars','MKD','ден'),
      (66,'Ringgits','MYR','RM'),
      (67,'Rupees','MUR','₨'),
      (68,'Pesos','MXN','$'),
      (69,'Tugriks','MNT','₮'),
      (70,'Meticais','MZN','MT'),
      (71,'Dollars','NAD','$'),
      (72,'Rupees','NPR','₨'),
      (73,'Guilders','ANG','ƒ'),
      (74,'Dollars','NZD','$'),
      (75,'Cordobas','NIO','C$'),
      (76,'Nairas','NGN','₦'),
      (77,'Krone','NOK','kr'),
      (78,'Rials','OMR','﷼'),
      (79,'Rupees','PKR','₨'),
      (80,'Balboa','PAB','B/.'),
      (81,'Guarani','PYG','Gs'),
      (82,'Nuevos Soles','PEN','S/.'),
      (83,'Pesos','PHP','Php'),
      (84,'Zlotych','PLN','zł'),
      (85,'Rials','QAR','﷼'),
      (86,'New Lei','RON','lei'),
      (87,'Rubles','RUB','руб'),
      (88,'Pounds','SHP','£'),
      (89,'Riyals','SAR','﷼'),
      (90,'Dinars','RSD','Дин.'),
      (91,'Rupees','SCR','₨'),
      (92,'Dollars','SGD','$'),
      (93,'Dollars','SBD','$'),
      (94,'Shillings','SOS','S'),
      (95,'Rand','ZAR','R'),
      (96,'Rupees','LKR','₨'),
      (97,'Kronor','SEK','kr'),
      (98,'Dollars','SRD','$'),
      (99,'Pounds','SYP','£'),
      (100,'New Dollars','TWD','NT$'),
      (101,'Baht','THB','฿'),
      (102,'Dollars','TTD','TT$'),
      (103,'Lira','TRY','₺'),
      (104,'Liras','TRL','£'),
      (105,'Dollars','TVD','$'),
      (106,'Hryvnia','UAH','₴'),
      (107,'Pesos','UYU','$U'),
      (108,'Sums','UZS','лв'),
      (109,'Bolivares Fuertes','VEF','Bs'),
      (110,'Dong','VND','₫'),
      (111,'Rials','YER','﷼'),
      (112,'Zimbabwe Dollars','ZWD','Z$'),
      (113,'Rupees','INR','₹'),
      (116,'Turkish','TRY','₺');
    
    /*!40000 ALTER TABLE `currency` ENABLE KEYS */;
    UNLOCK TABLES;
    
    
    # Dump of table custom_field_values
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `custom_field_values`;
    
    CREATE TABLE `custom_field_values` (
      `id` int NOT NULL AUTO_INCREMENT,
      `field_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `rel_id` int DEFAULT NULL,
      `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `form` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table custom_fields
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `custom_fields`;
    
    CREATE TABLE `custom_fields` (
      `id` int NOT NULL AUTO_INCREMENT,
      `form` enum('registration','contact') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'registration' COMMENT '(registration/contact)',
      `field_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `field_label` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `is_required` tinyint DEFAULT '0' COMMENT '(toggle)',
      `field_help_text` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `field_placeholder` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `field_type` enum('input','email','phone','date','textarea','select','checkbox','file') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'input' COMMENT '(input, email, phone, date, textarea, select, checkbox, file)',
      `field_options` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `width` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '(full, half, 1/3rd, 1/4th),',
      `field_order` int DEFAULT NULL,
      `status` tinyint DEFAULT NULL,
      `added` timestamp NULL DEFAULT NULL,
      `updated` timestamp NULL DEFAULT NULL,
      `deleted` tinyint DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table email_template
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `email_template`;
    
    CREATE TABLE `email_template` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `subject` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `email_tokens` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    LOCK TABLES `email_template` WRITE;
    /*!40000 ALTER TABLE `email_template` DISABLE KEYS */;
    
    INSERT INTO `email_template` (`id`, `subject`, `slug`, `description`, `email_tokens`)
    VALUES
      (1,'Reset Password','reset-password','<p>Hello {account_holder_name}</p><p>Click the below link to reset your password</p><p>{password_reset_link_is_here}</p><p>{your_site_name} {site_name_with_link}</p><p>Best Regards</p>','{account_holder_name},{password_reset_link_is_here},{your_site_name} {site_name_with_link}'),
      (2,'Verify Account','verify_account','<p xss=\"removed\">Hello.</p><p xss=\"removed\">{user_full_name} Please Verify And Activate Your Account With Below Link</p><p xss=\"removed\">{account_verify_link}</p><p xss=\"removed\">{your_site_name} {site_name_with_url}</p><p xss=\"removed\">Best Regards</p>','{user_full_name},{account_verify_link},{your_site_name},{site_name_with_url}'),
      (4,'Register New Account','register_new_account','<p xss=\"removed\">Hello.<br><br>{new_customer_name} You Are Registered Successfully :<br><span xss=\"removed\">Firstname:</span> {contact_firstname}<br><span xss=\"removed\">Lastname:</span> {contact_lastname}<br><span xss=\"removed\">Email:</span> {contact_email}<br><span xss=\"removed\"><br>Verify Account With This Link</span><span xss=\"removed\">:</span> {account_verify_link}</p><p xss=\"removed\">{site_name_with_url} {your_site_name}<br>Best Regards</p>','{new_customer_name},{contact_firstname},{contact_lastname},{contact_email},{account_verify_link},{site_name_with_url},{your_site_name}'),
      (5,'Payment Success','payment_success','<p xss=\"removed\">Hello.<br><br>{customer_full_name} Your Payment Is Successful For {purchaged_item_name}<br></p><p xss=\"removed\">Thank you,<br></p><p xss=\"removed\">{your_site_name} {site_name_with_url}<br></p><p xss=\"removed\">Best Regards</p>','{customer_full_name}, {purchaged_item_name}, {your_site_name}, {site_name_with_url}'),
      (6,'Item Purchased','item-purchased','<p xss=\"removed\">Hello.<br><br>{customer_full_name} Your Payment Is Successful For {purchaged_item_name}<br></p><p xss=\"removed\">Thank you,<br></p><p xss=\"removed\">{your_site_name} {site_name_with_url}<br></p><p xss=\"removed\">Best Regards</p>','{customer_full_name}, {purchaged_item_name}, {your_site_name}, {site_name_with_url}'),
      (12,'Register New Tutor Account','register_new_tutor_account','<p xss=\"removed\">Hello.<br><br>{new_customer_name} You Are Registered Successfully :<br><span xss=\"removed\">Firstname:</span> {contact_firstname}<br><span xss=\"removed\">Lastname:</span> {contact_lastname}<br><span xss=\"removed\">Email:</span> {contact_email}<br><span xss=\"removed\"><br>Admin will activate your account shortly. </span></p><p xss=\"removed\">{site_name_with_url} {your_site_name}<br>Best Regards</p>','{new_customer_name},{contact_firstname},{contact_lastname},{contact_email},{account_verify_link},{site_name_with_url},{your_site_name}'),
      (13,'Congratulations! Tutor Account Approved','admin_approve_tutor_account','<p xss=\"removed\">Hello.<br><br>{new_customer_name} Your account is activated successfully.</p><p xss=\"removed\"><br></p><p xss=\"removed\">{site_name_with_url} {your_site_name}<br></p><p xss=\"removed\">Best Regards</p>','{new_customer_name},{contact_firstname},{contact_lastname},{contact_email},{account_verify_link},{site_name_with_url},{your_site_name}'),
      (14,'Admin Decline Tutor Account','admin_decline_tutor_account','<p xss=\"removed\">Hello.<br><br>{new_customer_name} Your account is Decline by  {your_site_name}.</p><p xss=\"removed\"><br></p><p xss=\"removed\">{site_name_with_url} {your_site_name}<br></p><p xss=\"removed\">Best Regards</p>','{new_customer_name},{contact_firstname},{contact_lastname},{contact_email},{account_verify_link},{site_name_with_url},{your_site_name}');
    
    /*!40000 ALTER TABLE `email_template` ENABLE KEYS */;
    UNLOCK TABLES;
    
    
    # Dump of table emails
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `emails`;
    
    CREATE TABLE `emails` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `email` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `message` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `created` datetime DEFAULT NULL,
      `read` datetime DEFAULT NULL,
      `read_by` int unsigned DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `name` (`name`),
      KEY `title` (`title`),
      KEY `created` (`created`),
      KEY `read` (`read`),
      KEY `read_by` (`read_by`),
      KEY `email` (`email`(78))
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table footer_links
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `footer_links`;
    
    CREATE TABLE `footer_links` (
      `id` int NOT NULL AUTO_INCREMENT,
      `link_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `page_slug` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `is_newtab` tinyint DEFAULT '0' COMMENT '0 = not new tab, 1 = new tab',
      `added` timestamp NULL DEFAULT NULL,
      `updated` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table footer_sections
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `footer_sections`;
    
    CREATE TABLE `footer_sections` (
      `id` int NOT NULL AUTO_INCREMENT,
      `section_number` enum('1','2','3','4') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `type` enum('image','editor','link','text') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `value` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `position` int DEFAULT NULL,
      `updated` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    LOCK TABLES `footer_sections` WRITE;
    /*!40000 ALTER TABLE `footer_sections` DISABLE KEYS */;
    
    INSERT INTO `footer_sections` (`id`, `section_number`, `type`, `title`, `value`, `position`, `updated`)
    VALUES
      (1,'1','image','','footer-logo.png',1,'2020-07-26 19:21:46'),
      (2,'1','text','','Suspendisse hendrerit tellus laoreet luctus pharetra. Aliquam porttitor vitae orci nec ultricies. Curabitur vehicula, libero eget faucibus faucibus, purus erat eleifend enim, porta pellentesque ex mi ut sem.',0,'2020-07-26 19:21:46'),
      (3,'2','text','Categories','',0,'2020-07-26 19:21:46'),
      (4,'2','link','Govt. Exams','#',0,'2020-07-26 19:21:46'),
      (5,'2','link','Medical Enterance','#',1,'2020-07-26 19:21:46'),
      (6,'2','link','Engineering Enterance','#',2,'2020-07-26 19:21:46'),
      (7,'2','link','Civil Services','#',3,'2020-07-26 19:21:46'),
      (8,'2','link','Bank Exams','#',4,'2020-07-26 19:21:46'),
      (9,'3','text','Other Pages','',0,'2020-07-26 19:21:46'),
      (10,'3','link','Terms and Conditions','#',0,'2020-07-26 19:21:46'),
      (11,'3','link','Privacy Policy','#',1,'2020-07-26 19:21:46'),
      (12,'3','link','Help and Support','#',2,'2020-07-26 19:21:46'),
      (13,'4','text','Follow Us','',0,'2020-07-26 19:21:46'),
      (14,'4','editor','','<ul class=\"social-network social-circle\">\r\n<li><a href=\"#\" title=\"Facebook\"><i class=\"fab fa-facebook-square\"></i></a></li>\r\n<li><a href=\"#\" title=\"Linkedin\"><i class=\"fab fa-linkedin\"></i></a></li>\r\n</ul>',0,'2020-07-26 19:21:46');
    
    /*!40000 ALTER TABLE `footer_sections` ENABLE KEYS */;
    UNLOCK TABLES;
    
    
    # Dump of table front_menu_items
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `front_menu_items`;
    
    CREATE TABLE `front_menu_items` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `order` int DEFAULT NULL,
      `status` tinyint DEFAULT '1',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table institutions
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `institutions`;
    
    CREATE TABLE `institutions` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `title` varchar(255) DEFAULT NULL,
      `description` longtext,
      `logo` varchar(255) DEFAULT NULL,
      `address` text CHARACTER SET utf8 COLLATE utf8_general_ci,
      `added` datetime DEFAULT NULL,
      `updated` datetime DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
    
    
    
    # Dump of table instute_courses
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `instute_courses`;
    
    CREATE TABLE `instute_courses` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `course_id` int DEFAULT NULL,
      `instute_id` int DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table lang_token
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `lang_token`;
    
    CREATE TABLE `lang_token` (
      `id` int NOT NULL AUTO_INCREMENT,
      `language_id` int DEFAULT '1',
      `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `group_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'excel-data',
      `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    LOCK TABLES `lang_token` WRITE;
    /*!40000 ALTER TABLE `lang_token` DISABLE KEYS */;
    
    INSERT INTO `lang_token` (`id`, `language_id`, `token`, `description`, `group_name`, `added`, `updated`)
    VALUES
      (1,1,'language_group','Group','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (2,1,'token_name','Name','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (3,1,'description','Description','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (4,1,'core_button_save','Save','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (5,1,'core_button_cancel','Cancel','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (6,1,'language_list','Language List','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (7,1,'admin_record_added_successfully','Record Added Successfully','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (8,1,'admin_error_adding_record','Error During Adding Record','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (9,1,'admin_add_language','Add Language','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (10,1,'invalid_url','Invalid Url','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (11,1,'admin_invalid_id','Invalid Id','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (12,1,'admin_record_updated_successfully','Record Update Successfully','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (13,1,'admin_error_during_update_record','Error During Update Record','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (14,1,'admin_update_language','Edit Language','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (15,1,'admin_you_cannot_update_this_language','You can not update this language','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (16,1,'admin_you_cannot_delete_this_language','You can not delete this record','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (17,1,'admin_record_delete_successfully','Record Deleted Successfully','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (18,1,'admin_error_during_delete_record','Error during delete record','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (19,1,'admin_edit_record','Record edit','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (20,1,'admin_translation_record','Record traslation','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (21,1,'admin_delete_record','Record delete','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (22,1,'admin_token_or_category_exist','Category and Language token already exist','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (23,1,'add_token','Add Token','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (24,1,'admin_language_is_rtl','Is Rtl','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (25,1,'admin_language_is_rtl_msg','Language Is Rtl','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (26,1,'core_button_update','Update','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (27,1,'admin_table_no','Number','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (28,1,'admin_table_action','Action','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (29,1,'are_you_sure','Are you sure','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (30,1,'it_will_permanently_deleted','Delete permanent','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (31,1,'yes_delere_it','Delete','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (32,1,'table_search','Search','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (33,1,'table_show','Show','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (34,1,'table_entries','Entries','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (35,1,'table_showing','Showing','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (36,1,'table_to','To','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (37,1,'table_of','Of','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (38,1,'java_error_msg','Error','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (39,1,'table_previous','Previous','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (40,1,'table_next','Next','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (41,1,'you_need_to_update_also','Update also','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (42,1,'yes_add_more_field','Add More','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (43,1,'it_will_remove_from_quiz_also','Delete from quiz also','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (44,1,'core_button_logout','Logout','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (45,1,'admin_dashboard','Dashboard','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (46,1,'admin_category','Category','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (47,1,'category_list','Category List','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (48,1,'add_category','Add Category','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (49,1,'dashboard_quiz','Quiz','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (50,1,'quiz_list','Quiz List','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (51,1,'admin_add_quiz','Add Quiz','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (52,1,'dashboard_question','Question','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (53,1,'admin_questions_list','Question List','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (54,1,'admin_add_question','Add Question','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (55,1,'dashboard_pages','Page','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (56,1,'admin_page_list','Page List','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (57,1,'admin_add_page','Add Page','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (58,1,'admin_link','Link','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (59,1,'list_link','Link List','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (60,1,'add_link','Add Link','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (61,1,'admin_settings','Settings','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (62,1,'dashboard_user','User','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (63,1,'users_list','User List','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (64,1,'add_user','Add User','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (65,1,'core_text_page_rendered','Page rendered in <strong>0.0925</strong> seconds','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (66,1,'category_title_category_edit','Edit Category','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (67,1,'admin_category_status_updated_successfully','Category status updated successfully','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (68,1,'admin_title','Title','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (69,1,'admin_parent_category','Parent category','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (70,1,'admin_select_one','Select one','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (71,1,'admin_icon','Icon','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (72,1,'admin_category_display_on_home_or_menu','Display-on Home Or Menu','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (73,1,'admin_category_display_on_home_or_menu_msg','If you check then show on home and menu','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (74,1,'admin_upload_image','Upload','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (75,1,'admin_image','Image','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (76,1,'select_category','Select category','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (77,1,'update_quiz','Edit Quiz','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (78,1,'record_copied_successfully','Record copied successfully','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (79,1,'admin_error_during_copying_record','Error during copying record','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (80,1,'thumbnail_resize_success','Thumbnail Resize Success','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (81,1,'thumbnail_resize_error','Thumbnail Resize Errors','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (82,1,'small_resize_errors','Small Resize Errors','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (83,1,'small_resize_success','Small Resize Success','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (84,1,'medium_resize_success','Medium Resize Success','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (85,1,'medium_resize_errors','Medium Resize Errors','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (86,1,'upload_success','Upload successfully','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (87,1,'admin_copy_record','Record copy','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (88,1,'admin_all_quiz_questions','All Quiz Questions','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (89,1,'admin_import_quiz_question','Import Quiz Question','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (90,1,'file_copy_success','File Copying Successfull','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (91,1,'resize_errors','Resize Errors','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (92,1,'quiz_category_name','Category Name','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (93,1,'admin_quiz_number_of_questions','Number of question','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (94,1,'quiz_price','Price','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (95,1,'quiz_duration_in_minute','Quiz Duration In Minute','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (96,1,'display_on_leaderboard','Display On Leaderboard','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (97,1,'question_is_random','Is Random Question','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (98,1,'instruction','Instruction','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (99,1,'admin_question_per_test','No of questions','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (100,1,'admin_test_duration','Duration','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (101,1,'back','Back','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (102,1,'invalid_file_formate','Invalid file format','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (103,1,'select_quiz','Select quiz','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (104,1,'admin_update_question','Edit question','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (105,1,'quiz_name','Quiz Name','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (106,1,'multiple_choies','Is Multiple Choices','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (107,1,'question_choices','Question Choices','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (108,1,'admin_is_correct','Is Correct','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (109,1,'admin_add_more','Add More','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (110,1,'admin_record_remove','Record Remove','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (111,1,'admin_page_update','Edit page','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (112,1,'admin_content','Content','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (113,1,'admin_slug','Slug','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (114,1,'admin_update_footer_link','Edit Link','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (115,1,'admin_link_type','Link type','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (116,1,'admin_header','Header','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (117,1,'admin_footer','Footer','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (118,1,'admin_page_check_msg','If You Check Then Show Page List','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (119,1,'admin_link_url','Url','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (120,1,'admin_page_select','Select Page List','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (121,1,'admin_new_tab','New Tab','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (122,1,'admin_new_tab_msg','If You Check Then Page Open In New Tab','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (123,1,'admin_link_title','Link Title','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (124,1,'admin_input_select','select...','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (125,1,'site_name','Site Name','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (126,1,'admin_edit_user','Edit user','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (127,1,'user_not_exist','This user is not exist','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (128,1,'user_id_required','User Id is required','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (129,1,'admin_inactive','Inactive','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (130,1,'admin_active','Active','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (131,1,'core_error_no_results','No Result Found','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (132,1,'username_exist','Username Already Exist','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (133,1,'user_email_exist','User email already exist','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (134,1,'admin_username','Username','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (135,1,'admin_first_name','First Name','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (136,1,'admin_last_name','Last Name','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (137,1,'admin_user_language','Language','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (138,1,'email','Email','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (139,1,'admin_status','Status','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (140,1,'user_is_admin','Is Admin','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (141,1,'user_password','Password','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (142,1,'user_help_passwords','Only enter passwords if you want to change it.','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (143,1,'user_password_repeat','Repeat Password','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (144,1,'core_button_reset','Reset','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (145,1,'core_button_filter','Filter','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (146,1,'admin_csv_export','CSV Export','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (147,1,'admin_input_items_per_page','Items Per Page','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (148,1,'messages','Messages','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (149,1,'contact_msg_updated','Message updated','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (150,1,'contact_error_update_failed','Contact update failed','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (151,1,'message_id','Id','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (152,1,'name','Name','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (153,1,'received','Received','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (154,1,'read_message','Read Message','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (155,1,'record_import_successfully','Questions imported success','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (156,1,'row_skip_during_import','Questions skipped','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (157,1,'data_import_error','Record imported error','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (158,1,'admin_import_quiz_questions_excel','Quid question import','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (159,1,'unable_to_read_this_file_formate','Unable To Read This File Format','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (160,1,'upload_excel_file','Upload Excel FIle','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (161,1,'quiz_excel_question_over_write','Overwrite Last Quiz Questions','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (162,1,'downlod_sample_file','Download Sample File','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (163,1,'meta_keywords','Meta Keywords','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (164,1,'meta_description','Meta Description','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (165,1,'contact_msg_send_success','Thanks for contacting us, %s! Your message has been sent.','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (166,1,'contact_error_send_failed','Sorry, %s. There was a problem sending your message. Please try again.','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (167,1,'contact_title','Contact Us','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (168,1,'error_captcha','The CAPTCHA text did not match.','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (169,1,'title','Title','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (170,1,'message','Message','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (171,1,'captcha','Captch text','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (172,1,'core_error_direct_access_forbidden','Direct accesss is forbidden!','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (173,1,'core_error_page_not_found','Page Not Found','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (174,1,'core_text_oops','OOPS!','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (175,1,'core_text_error','You requested a page that does not exist.','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (176,1,'core_return_home','Return Home','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (177,1,'quiz_history','Quiz History','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (178,1,'quiz_leader_board','Quiz Leader Board','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (179,1,'category_quiz','Quiz Category','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (180,1,'free','Free','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (181,1,'resume_test','Resume Test','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (182,1,'start_quiz','Start Quiz','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (183,1,'front_questions','Front Question','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (184,1,'duration','Duration','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (185,1,'minutes','Minute','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (186,1,'leader_board','Leader Board','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (187,1,'home','Home','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (188,1,'welcome_to_online_quiz','Welcome to Online Quiz','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (189,1,'front_record_edited_successfully','Record Edited Successfully','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (190,1,'front_record_edited_during_error','Error during record edited','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (191,1,'user_profile','Profile','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (192,1,'username_exists','The username <strong>%s</strong> already exists!','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (193,1,'email_exists','Email Already Exist','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (194,1,'front_email','Email','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (195,1,'user_reset_password','Your Reset Password','front','2020-10-04 02:22:24','2020-10-04 02:22:24'),
      (196,1,'user_username_email','Username or Email','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (197,1,'password','Password','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (198,1,'user_link_register_account','Register for an account.','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (199,1,'username','Username','front','2020-08-17 17:32:40','2020-08-17 23:02:40'),
      (200,1,'front_first_name','First Name','front','2020-08-17 17:36:24','2020-08-17 23:06:24'),
      (201,1,'front_last_name','Last Name','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (202,1,'front_language','Language','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (203,1,'front_password_repeat','Repeat Password','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (204,1,'help_passwords','Only enter passwords if you want to change it.','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (205,1,'front_upload_image','User Image','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (206,1,'users_register','Create Account','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (207,1,'front_quiz_instruction','Quiz Instruction','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (208,1,'proceed','Proceed','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (209,1,'there_no_question_in_this_quiz','There is no Questions in This Quiz','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (210,1,'invalid_id','Invalid Id','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (211,1,'schedule_quiz_first','Shedule quiz first','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (212,1,'test_result','Test Result','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (213,1,'user_msg_email_new_account','<p>Thank you for creating an account at %s. Click the link below to validate your email address  and activate your account.<br /><br />','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (214,1,'core_email_end','</body></html>','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (215,1,'user_email_new_account','New Account for','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (216,1,'user_msg_register_success','Thanks for registering, %s! Check your email for a confirmation message. Once your account has been verified, you will be able to log in with the credentials you provided.','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (217,1,'user_error_register_failed','Your account could not be created at this time. Please try again.','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (218,1,'front_register','Register','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (219,1,'user_msg_validate_success','Your account has been verified. You may now log in to your account.','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (220,1,'user_error_validate_failed','There was a problem validating your account. Please try again.','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (221,1,'user_msg_email_password_reset','<p>Your password for %s Can be reset By This Link. <br /><br /><strong>%s</strong><br /><br />','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (222,1,'user_msg_email_password_reset_title','Password Reset for','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (223,1,'user_error_password_reset_failed','There was a problem resetting your password Or Your is not Active.','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (224,1,'front_forgot','Forgot Password','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (225,1,'user_error_invalid_login','Invalid username or password','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (226,1,'user_error_too_many_login_attempts','You\'ve made too many attempts to log in too quickly. Please wait %s seconds and try again.','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (227,1,'user_error_email_not_exists','That email does not exists!','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (228,1,'read_instructions_carefully','Please read the instructions carefully','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (229,1,'question_no','Question Number','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (230,1,'previous_btn','Previous','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (231,1,'next_btn','Next','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (232,1,'mark_for_review_and_next','Mark for Review and Next','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (233,1,'save_and_next','Save And Next','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (234,1,'submit_test','Submit Test','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (235,1,'count_down','Count Down','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (236,1,'question_palette','Question Palette','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (237,1,'answered_tag','Answered','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (238,1,'not_answered_tag','Not Answered','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (239,1,'marked_tag','Marked','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (240,1,'not_visited_tag','Not Visited','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (241,1,'answered_marked_for_review_tag','Answered & Marked for Review','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (242,1,'resume_quiz','Resume Quiz','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (243,1,'core_button_login','Login','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (244,1,'user_forgot_password','Forgot your password?','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (245,1,'your_total_attemp_is','Your total attemp is','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (246,1,'quiz_already_running','Quiz already running','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (247,1,'plz_complete_or_stop_running_quiz','Please complete quiz or Stop running quiz','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (248,1,'stop_quiz','Stop Quiz','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (249,1,'footer_text','Footer Text','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (250,1,'copyright','Copyright','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (251,1,'copyright_all_rights_reserved','All rights reserved','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (252,1,'admin_settings_msg_save_success','Settings have been successfully saved.','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (253,1,'admin_settings_error_save_failed','There was a problem saving settings. Please try ag...','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (254,1,'admin_excel_quiz_name','Excel quiz name','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (255,1,'admin_excel_file','Excel file','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (256,1,'admin_page_title','Title','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (257,1,'admin_page_content','Content','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (258,1,'admin_page_image','Image','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (259,1,'admin_testimonial_list','Testimonial List','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (260,1,'admin_testimonial_name','Name','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (261,1,'admin_testimonial_message','Testimonial Message','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (262,1,'admin_testimonial_image','Image','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (263,1,'admin_testimonial_add','Add Testimonial','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (264,1,'admin_testimonial_update','Update Testimonial','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (265,1,'admin_testimonial_edit','Edit Testimonial','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (266,1,'admin_testimonial_delete','Delete Testimonial','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (267,1,'admin_testimonial_name_form','Testimonial Name','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (268,1,'admin_testimonial_form_profile','Testimonial Profile','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (269,1,'admin_sponsors_list','Sponsors List','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (270,1,'admin_sponser_name','Name','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (271,1,'admin_sponser_link','Sponsors Link','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (272,1,'admin_sponsors_logo','Sponsors Logo','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (273,1,'admin_sponsors_update','Update Sponsors','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (274,1,'admin_sponsors_delete','Delete Sponsors','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (275,1,'admin_sponser_add','Add Sponsors','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (276,1,'admin_add_sponsors','Add Sponsors','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (277,1,'admin_sponser_form_name','Sponsors Name','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (278,1,'admin_sponsors_form_link','Sponsors Link','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (279,1,'admin_footer_input_type','Input Type','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (280,1,'admin_footer_section','Footer Section','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (281,1,'admin_footer_section_first','Footer Section First','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (282,1,'admin_footer_section_second','Footer Section Second','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (283,1,'admin_footer_section_third','Footer Section Third','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (284,1,'admin_footer_section_fourth','Footer Section Fourth','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (285,1,'admin_footer_text','Text','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (286,1,'admin_footer_link','Link','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (287,1,'admin_footer_content','Content','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (288,1,'admin_footer_image','Image','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (289,1,'admin_footer_add_more','Add More','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (290,1,'admin_footer_field_title','Title','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (291,1,'admin_footer_field_link_url','Link Url','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (292,1,'admin_setting_site_name','Setting Site Name','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (293,1,'setting_meta_key','Setting Meta Keywords','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (294,1,'setting_meta_desc','Setting Meta Description','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (295,1,'setting_meta_key_help','Setting Comma-seperated list of site keywords','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (296,1,'setting_meta_desc_help','Setting Short description describing your site.','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (297,1,'setting_site_email','Setting Site Email','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (298,1,'setting_site_email_help','Setting Email address all emails will be sent from.','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (299,1,'setting_upload_site_logo','Setting Upload Site Logo','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (300,1,'setting_upload_site_favicon','Setting Upload Site Favicon','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (301,1,'set_first_slide','Setting Home First Slide','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (302,1,'fb_url','Site Facebook Url','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (303,1,'twiter_url','Setting Site Twitter Url','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (304,1,'g_plus_url','Setting Site Google Plus Url','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (305,1,'lin_in_url','Setting Site Linkedin Url','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (306,1,'pin_url','Setting Site Pininterest Url','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (307,1,'insta_url','Setting Site Instagram Url','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (308,1,'site_ph_nbr','Setting Site Phone Number','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (309,1,'site_sec_slide','Setting Home Second Slide','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (310,1,'site_trd_slide','Setting Home Third Slide','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (311,1,'site_footer_text_head','Setting Footer Text Heading','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (312,1,'sel_curency_code','Setting Setting Select Currency Code','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (313,1,'smto_protocol','SMTP protocol','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (314,1,'smtp_host','SMTP Host','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (315,1,'smtp_user','SMTP USER','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (316,1,'smtp_pass','SMTP PASSWORD','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (317,1,'smtp_port','SMTP PORT','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (318,1,'smtp_crypt','SMTP CRYPTO','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (319,1,'smtp_crypt_help','SMTP CRYPTO(\'tls\' or \'ssl\')','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (320,1,'select_default_lang','Select Site Default Language','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (321,1,'quz_instructions','Quiz General Instructions','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (322,1,'email_active_status','User Email Account Activation','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (323,1,'admin_lang_token','Admin Token','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (324,1,'front_lang_token','Front Token','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (325,1,'other_lang_token','Other Token','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (326,1,'add_new_token','Add New Token','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (327,1,'admin_sponsors','Sponsors','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (328,1,'admin_testimonial','Testimonials','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (329,1,'admin_profile','Profile','admin','2021-01-09 18:02:16','2021-01-09 23:32:16'),
      (330,1,'admin_admin_settings','Settings','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (331,1,'close','Close','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (332,1,'model_image_preview','Image preview','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (333,1,'welcome','Welcome','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (334,1,'no_data','No Data Yet','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (335,1,'model_box','Modal Box','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (336,1,'view_more','View more','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (337,1,'latest_quizes','Latest Quizes','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (338,1,'pay_now','Pay Now','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (339,1,'no_quiz_found','No Quiz Found In','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (340,1,'popular_quizes','Popular Quizes','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (341,1,'our_partners','Our Partners','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (342,1,'user_required','User Name Is Necessary So Plz Start Quiz With Your Name','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (343,1,'most_recent','Most Recent','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (344,1,'most_liked','Most Liked','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (345,1,'most_attended','Most Attended','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (346,1,'attended','Attended','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (347,1,'correct','Correct','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (348,1,'date','Date','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (349,1,'score','Score','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (350,1,'rank','Rank','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (351,1,'no_quiz_given','No Test Given In This Quiz','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (352,1,'questions','Questions','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (353,1,'goven_answer','Given Answer','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (354,1,'correct_answer','Correct Answer','front','2020-09-11 22:33:47','2020-08-13 21:22:52'),
      (355,1,'status','Status','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (356,1,'wrong','Wrong','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (357,1,'not_attempted','Not Attempted','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (358,1,'question_status','Question Status','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (359,1,'not_attemp_question','Not Attemp Question','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (360,1,'wrong_answer','Wrong Answer','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (361,1,'not_attempted_this_questions','Not Attempted This Questions','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (362,1,'right_answer','Right Answer','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (363,1,'question_detail','Question Detail','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (364,1,'quiz_summary','Quiz Summary','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (365,1,'total_atemp_ques','Total Attempted Questions','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (366,1,'your_score','Your Score','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (367,1,'time_spend','Time Spend','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (368,1,'total_question','Total Questions','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (369,1,'total_attem_ques','Total Attempted Questions','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (370,1,'incorrect_answered','Incorrect Answered','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (371,1,'login_or_view_history','Plz Login To View Quiz History','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (372,1,'quiz_date','Quiz Date','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (373,1,'like','Like','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (374,1,'total_attempt','Total Attempt','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (375,1,'dashboard_categories','Categories','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (376,1,'sign_out','Sign out','other','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (377,1,'login','Login','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (378,1,'front_testimonial','What our customers are saying?','front','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (379,1,'quiz_result','Quiz Result','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (380,1,'check_quiz_result','Time Over Check Quiz Result','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (381,1,'contacts','Contacts','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (382,1,'footers_section','Footer Section','admin','2020-08-13 15:52:52','2020-08-13 21:22:52'),
      (383,1,'admin_import','Import','admin','2020-08-15 01:36:27','2020-08-14 20:06:27'),
      (384,1,'admin_report','Reports','admin','2020-08-16 04:44:35','2020-08-15 23:14:35'),
      (385,1,'is_random_option','Is Random Question Choice','admin','2020-08-20 17:24:54','2020-08-20 17:24:54'),
      (386,1,'translate_quiz_btn','Translate Quiz','admin','2020-08-20 17:33:26','2020-08-20 17:33:26'),
      (387,1,'translate','Translate','admin','2020-08-20 17:35:31','2020-08-20 17:35:31'),
      (388,1,'quiz_translation_success','Quiz Translation Is added Successfully ! ','admin','2020-08-20 17:37:18','2020-08-20 17:37:18'),
      (389,1,'quiz_translation_invalid_form_data','Sorry Invalid Form Data Plz Try Again... !','admin','2020-08-20 17:38:23','2020-08-20 17:38:23'),
      (390,1,'question_translation_success','Questions Translation Is added Successfully ! ','admin','2020-08-20 17:48:06','2020-08-20 17:48:06'),
      (391,1,'question_translation_invalid_form_data','Sorry Invalid Form Data Plz Try Again.. !','admin','2020-08-20 17:48:54','2020-08-20 17:48:54'),
      (392,1,'quiz_language','Quiz Language','admin','2020-08-20 18:03:19','2020-08-20 18:03:19'),
      (393,1,'admin_blog','Blog','admin','2020-08-21 16:48:40','2020-08-21 11:18:40'),
      (394,1,'admin_blog_category_list','Blog Category','admin','2020-08-21 16:49:24','2020-08-21 11:19:24'),
      (395,1,'admin_post_list','Posts','admin','2020-08-21 16:49:47','2020-08-21 11:19:47'),
      (396,1,'admin_add_blog_category','Add Blog Category','admin','2020-08-21 16:51:14','2020-08-21 11:21:14'),
      (397,1,'admin_edit_blog_category','Edit Blog Category','admin','2020-08-21 16:54:19','2020-08-21 11:24:19'),
      (398,1,'admin_blog_post_list','Post','admin','2020-08-21 16:55:18','2020-08-21 11:25:18'),
      (399,1,'admin_add_blog_post','Add Post','admin','2020-08-21 16:55:45','2020-08-21 11:25:45'),
      (400,1,'admin_author','Author','admin','2020-08-21 16:57:15','2020-08-21 11:27:15'),
      (401,1,'disable_right_click','Disable Right Click On Browser','admin','2020-08-25 00:47:05','2020-08-25 00:47:05'),
      (402,1,'disable_print_screen','Disable Screenshot','admin','2020-08-25 00:49:40','2020-08-25 00:49:40'),
      (403,1,'quiz_pay_now','Pay Now','admin','2020-08-25 18:19:06','2020-08-25 18:19:06'),
      (404,1,'facebook_app_id','Facebook App Id','admin','2020-08-26 01:45:38','2020-08-26 01:45:38'),
      (405,1,'facebook_app_secret','Facebook App Secret Key','admin','2020-08-26 01:46:59','2020-08-26 01:46:59'),
      (406,1,'google_key','Google Key','admin','2020-08-26 18:22:42','2020-08-26 18:22:42'),
      (407,1,'google_secret','Google Secret Key','admin','2020-08-26 18:23:38','2020-08-26 18:23:38'),
      (408,1,'paypal_mode','Paypal Mode','admin','2020-08-26 18:26:35','2020-08-26 18:26:35'),
      (409,1,'paypal_key','Paypal Key','admin','2020-08-26 18:27:30','2020-08-26 18:27:30'),
      (410,1,'paypal_secret_key','Paypal Secret Key','admin','2020-08-26 18:28:20','2020-08-26 18:28:20'),
      (411,1,'stripe_key','Stripe Key','admin','2020-08-26 18:29:12','2020-08-26 18:29:12'),
      (412,1,'stripe_secret_key','Stripe Secret Key','admin','2020-08-26 18:29:54','2020-08-26 18:29:54'),
      (413,1,'paid_currency','Paid Currencies','admin','2020-08-26 23:54:45','2020-08-26 23:54:45'),
      (414,1,'multiple_choice_question','Multiple Choice','front','2020-08-27 00:13:28','2020-08-27 00:13:28'),
      (415,1,'is_registered','Is Registered','admin','2020-08-27 22:48:27','2020-08-27 22:48:27'),
      (416,1,'admin_rating','Rating','admin','2020-08-28 23:44:34','2020-08-28 23:44:34'),
      (417,1,'razorpay_key','Razorpay Key','admin','2020-08-30 00:26:34','2020-08-30 00:26:34'),
      (418,1,'razorpay_secret_key','Razorpay Secret Key','admin','2020-08-30 00:26:58','2020-08-30 00:26:58'),
      (419,1,'solution','Solution','admin','2020-08-31 23:12:25','2020-08-31 23:12:25'),
      (420,1,'payment_not_done','Your payment is pending','front','2020-09-01 01:43:16','2020-09-01 01:43:16'),
      (421,1,'seo_heading','Seo','admin','2020-09-01 17:46:41','2020-09-01 17:46:41'),
      (422,1,'meta_title','Meta Title','admin','2020-09-01 17:56:06','2020-09-01 17:56:06'),
      (423,1,'meta_kewords','Meta Keywords','admin','2020-09-01 18:05:27','2020-09-01 18:05:27'),
      (424,1,'front_quiz_detail','Quiz Detail','front','2020-09-01 20:31:28','2020-09-01 20:31:28'),
      (425,1,'like_post','Like Post','front','2020-09-03 18:16:41','2020-09-03 18:16:10'),
      (426,1,'dashboard_blog_post','Blog Post','admin','2020-09-03 19:43:36','2020-09-03 19:43:36'),
      (427,1,'no_data_found','Data Not Found','front','2020-09-03 19:52:57','2020-09-03 19:52:57'),
      (428,1,'profile_user','Profile User','front','2020-09-04 00:33:03','2020-09-04 00:33:03'),
      (429,1,'purchased_quiz','Purchased Quiz','front','2020-09-04 00:33:47','2020-09-04 00:33:47'),
      (430,1,'like_quiz','Like Quiz','front','2020-09-04 00:34:12','2020-09-04 00:34:12'),
      (431,1,'all_recent_blogs','All Recent Blogs','front','2020-09-04 01:31:09','2020-09-04 01:31:09'),
      (432,1,'admin_setting_site_name_help','Admin Site Name Help','admin','2020-09-04 22:29:26','2020-09-04 22:28:58'),
      (433,1,'smto_protocol_help','SMTP protocol Help','admin','2020-09-04 22:42:49','2020-09-04 22:42:49'),
      (434,1,'smtp_host_help','SMTP Host Help','admin','2020-09-04 22:43:27','2020-09-04 22:43:27'),
      (435,1,'smtp_user_help','SMTP USER Help','admin','2020-09-04 22:44:08','2020-09-04 22:44:08'),
      (436,1,'smtp_pass_help','SMTP PASSWORD Help','admin','2020-09-04 22:45:09','2020-09-04 22:45:09'),
      (437,1,'smtp_port_help','SMTP PORT Help','admin','2020-09-04 22:45:48','2020-09-04 22:45:48'),
      (438,1,'quz_instructions_help','Quiz General Instructions Help','admin','2020-09-04 22:46:48','2020-09-04 22:46:48'),
      (439,1,'email_active_status_help','User Email Account Activation Help','admin','2020-09-04 22:47:49','2020-09-04 22:47:49'),
      (440,1,'disable_right_click_help','Disable Right Click On Browser Help','admin','2020-09-04 22:48:18','2020-09-04 22:48:18'),
      (441,1,'disable_print_screen_help','Disable Screenshot Help','admin','2020-09-04 22:48:54','2020-09-04 22:48:54'),
      (442,1,'facebook_app_id_help','Facebook App Id Help','admin','2020-09-04 22:49:41','2020-09-04 22:49:41'),
      (443,1,'facebook_app_secret_help','Facebook App Secret Key Help','admin','2020-09-04 22:50:13','2020-09-04 22:50:13'),
      (444,1,'google_key_help','Google Key Help','admin','2020-09-04 22:50:40','2020-09-04 22:50:40'),
      (445,1,'google_secret_help','Google Secret Key Help','admin','2020-09-04 22:51:09','2020-09-04 22:51:09'),
      (446,1,'paypal_mode_help','Paypal Mode Help','admin','2020-09-04 22:51:50','2020-09-04 22:51:50'),
      (447,1,'paypal_key_help','Paypal Key Help','admin','2020-09-04 22:52:23','2020-09-04 22:52:23'),
      (448,1,'paypal_secret_key_help','Paypal Secret Key Help','admin','2020-09-04 22:53:02','2020-09-04 22:53:02'),
      (449,1,'stripe_key_help','Stripe Key Help','admin','2020-09-04 22:53:25','2020-09-04 22:53:25'),
      (450,1,'stripe_secret_key_help','Stripe Secret Key Help','admin','2020-09-04 22:53:56','2020-09-04 22:53:56'),
      (451,1,'paid_currency_help','Paid Currencies Help','admin','2020-09-04 22:54:51','2020-09-04 22:54:51'),
      (452,1,'razorpay_key_help','Razorpay Key Help','admin','2020-09-04 22:55:28','2020-09-04 22:55:28'),
      (453,1,'razorpay_secret_key_help','Razorpay Secret Key Help','admin','2020-09-04 22:55:59','2020-09-04 22:55:59'),
      (454,1,'setting_upload_site_logo_help','Setting Upload Site Logo Help','admin','2020-09-04 23:01:07','2020-09-04 23:01:07'),
      (455,1,'setting_upload_site_favicon_help','Setting Upload Site Favicon Help','admin','2020-09-04 23:01:33','2020-09-04 23:01:33'),
      (456,1,'fb_url_help','Site Facebook Url Help','admin','2020-09-04 23:02:06','2020-09-04 23:02:06'),
      (457,1,'twiter_url_help','Setting Site Twitter Url Help','admin','2020-09-04 23:02:36','2020-09-04 23:02:36'),
      (458,1,'g_plus_url_help','Setting Site Google Plus Url Help','admin','2020-09-04 23:03:22','2020-09-04 23:03:22'),
      (459,1,'lin_in_url_help','Setting Site Linkedin Url Help','admin','2020-09-04 23:04:10','2020-09-04 23:04:10'),
      (460,1,'pin_url_help','Setting Site Pininterest Url Help','admin','2020-09-04 23:04:36','2020-09-04 23:04:36'),
      (461,1,'insta_url_help','Setting Site Instagram Url Help','admin','2020-09-04 23:05:02','2020-09-04 23:05:02'),
      (462,1,'site_ph_nbr_help','Setting Site Phone Number Help','admin','2020-09-04 23:05:42','2020-09-04 23:05:42'),
      (463,1,'sel_curency_code_help','Setting Setting Select Currency Code Help','admin','2020-09-04 23:09:01','2020-09-04 23:09:01'),
      (464,1,'select_default_lang_help','Select Site Default Language Help','admin','2020-09-04 23:09:40','2020-09-04 23:09:40'),
      (465,1,'bank_transfer','Bank Transfer','admin','2020-09-04 23:48:12','2020-09-04 23:48:12'),
      (466,1,'bank_transfer_help','Bank Transfer Help','admin','2020-09-04 23:48:42','2020-09-04 23:48:42'),
      (467,1,'payment_list','Payment List','admin','2020-09-05 00:02:54','2020-09-05 00:02:54'),
      (468,1,'dashboard_payment','Payment','admin','2020-09-05 00:03:28','2020-09-05 00:03:28'),
      (469,1,'amount','Amount','admin','2020-09-05 00:17:32','2020-09-05 00:17:32'),
      (470,1,'success','Success','admin','2020-09-05 02:04:28','2020-09-05 02:04:28'),
      (471,1,'bank_transfer_added_successfully','Transaction Added Successfully','front','2020-09-07 22:04:58','2020-09-07 22:04:58'),
      (472,1,'please_add_transaction_no','Please add transaction or reference number','front','2020-09-07 22:08:19','2020-09-07 22:08:19'),
      (473,1,'bank_transfer_updated_successfully','Transaction Updated Successfully','front','2020-09-07 23:07:20','2020-09-07 23:07:20'),
      (474,1,'currency','Currency','admin','2020-09-08 17:28:53','2020-09-08 17:28:53'),
      (475,1,'payment_method','Payment Method','admin','2020-09-08 17:36:21','2020-09-08 17:36:21'),
      (476,1,'tax_name','Tax Name','admin','2020-09-08 18:43:44','2020-09-08 18:43:44'),
      (477,1,'tax_percentage','Tax Percentage','admin','2020-09-08 18:44:20','2020-09-08 18:44:20'),
      (478,1,'tax_name_help','Tax Name Help','admin','2020-09-08 18:44:43','2020-09-08 18:44:43'),
      (479,1,'tax_percentage_help','Tax Percentage Help','admin','2020-09-08 18:45:09','2020-09-08 18:45:09'),
      (480,1,'invoice','Invoice','admin','2020-09-08 20:18:31','2020-09-08 20:18:31'),
      (481,1,'invoice_header_text','Invoice Header Text','admin','2020-09-08 21:25:27','2020-09-08 21:25:27'),
      (482,1,'invoice_header_text_help','Invoice Header Text Help','admin','2020-09-08 21:25:49','2020-09-08 21:25:49'),
      (483,1,'invoice_footer_text_help','Invoice Footer Text Help','admin','2020-09-08 21:26:13','2020-09-08 21:26:13'),
      (484,1,'invoice_footer_text','Invoice Footer Text','admin','2020-09-08 21:26:34','2020-09-08 21:26:34'),
      (485,1,'invoice_start_number_help','Invoice Start Number Help','admin','2020-09-08 21:27:07','2020-09-08 21:27:07'),
      (486,1,'invoice_start_number','Invoice Start Number','admin','2020-09-08 21:27:33','2020-09-08 21:27:33'),
      (487,1,'is_once','Is Once','admin','2020-09-09 00:01:55','2020-09-09 00:01:55'),
      (488,1,'test_already_given','Test Already Given','front','2020-09-09 01:00:07','2020-09-09 00:44:09'),
      (489,1,'no_payment_found','No Payment Found','front','2020-09-10 23:16:01','2020-09-10 23:16:01'),
      (490,1,'attempt','Attempts','admin','2020-09-11 17:56:44','2020-09-11 17:56:44'),
      (491,1,'page_view','Page View','admin','2020-09-11 19:33:03','2020-09-11 19:33:03'),
      (492,1,'view_answer','View Answer','front','2020-09-11 22:26:06','2020-09-11 22:26:06'),
      (493,1,'detail','Detail','front','2020-09-11 22:34:50','2020-09-11 22:34:50'),
      (494,1,'question_answer','Answer','admin','2020-09-13 00:06:35','2020-09-13 00:06:35'),
      (495,1,'question_solution','Solution','front','2020-09-14 22:24:48','2020-09-14 22:24:48'),
      (496,1,'admin_backup_restore','Backup And Restore','admin','2020-09-14 23:29:44','2020-09-14 23:29:44'),
      (497,1,'admin_backup_restore_list','Backup And Restore List','admin','2020-09-14 23:30:20','2020-09-14 23:30:20'),
      (498,1,'admin_backup_now','Backup Now','admin','2020-09-14 23:44:01','2020-09-14 23:44:01'),
      (499,1,'admin_backup_successfully','Backup Successfully','admin','2020-09-15 16:54:56','2020-09-15 16:54:56'),
      (500,1,'admin_backup_error','Backup Error','admin','2020-09-15 16:55:52','2020-09-15 16:55:52'),
      (501,1,'admin_datetime','Datetime','admin','2020-09-15 17:28:12','2020-09-15 17:28:12'),
      (502,1,'admin_record_restore','Record Restore','admin','2020-09-15 18:05:28','2020-09-15 18:05:28'),
      (503,1,'admin_record_download','Record Download','admin','2020-09-15 18:06:01','2020-09-15 18:06:01'),
      (504,1,'no_record','No Record','admin','2020-09-16 01:43:37','2020-09-16 01:43:37'),
      (505,1,'admin_package','Package','admin','2020-09-16 17:07:59','2020-09-16 17:07:59'),
      (506,1,'admin_quiz_package_list','Quiz Package','admin','2020-09-16 17:08:27','2020-09-16 17:08:27'),
      (507,1,'admin_add_quiz_package','Add Quiz Package','admin','2020-09-16 17:17:12','2020-09-16 17:17:12'),
      (508,1,'admin_edit_quiz_package','Edit Package Quiz','admin','2020-09-17 20:42:32','2020-09-17 20:42:32'),
      (509,1,'plz_login_first','Please Login First save rating','front','2020-09-18 16:42:46','2020-09-18 16:42:46'),
      (510,1,'admin_package_quiz','Package Quiz','admin','2020-09-18 19:37:36','2020-09-18 19:37:36'),
      (511,1,'admin_add_now','Add Now','admin','2020-09-18 20:20:51','2020-09-18 20:20:51'),
      (512,1,'admin_add_keypad','Add Keypad','admin','2020-09-18 22:45:31','2020-09-18 22:45:31'),
      (513,1,'question','Question','admin','2020-09-20 00:28:05','2020-09-20 00:28:05'),
      (514,1,'admin_upload_solution_image','Solution Image','admin','2020-09-21 17:53:54','2020-09-21 17:53:54'),
      (515,1,'package_quiz_order_save','Quiz Order Save','admin','2020-09-21 19:52:47','2020-09-21 19:52:47'),
      (516,1,'admin_study_material','Study Material','admin','2020-09-23 00:15:20','2020-09-23 00:15:20'),
      (517,1,'admin_study_material_list','Study Material','admin','2020-09-23 00:13:53','2020-09-23 00:13:53'),
      (518,1,'admin_quiz_study_material_list','Quiz Study Material','admin','2020-09-23 00:15:58','2020-09-23 00:15:58'),
      (519,1,'admin_add_study_material','Add Study Material','admin','2020-09-23 00:55:44','2020-09-23 00:55:44'),
      (520,1,'asign_study_material_to_user','Assign Study Material To User','admin','2020-09-23 17:49:01','2020-09-23 17:49:01'),
      (521,1,'admin_edit_study_material','Edit Study Material','admin','2020-09-23 20:07:37','2020-09-23 20:07:37'),
      (522,1,'admin_material_file','Material File','admin','2020-09-23 22:06:58','2020-09-23 22:06:58'),
      (523,1,'admin_add_material_file','Add Material File','admin','2020-09-23 22:13:58','2020-09-23 22:13:58'),
      (524,1,'admin_study_material_file_list','Study Material File','admin','2020-09-23 22:15:04','2020-09-23 22:15:04'),
      (525,1,'admin_add_study_material_file','Add Material File','admin','2020-09-23 22:41:58','2020-09-23 22:41:58'),
      (526,1,'admin_file_type','Type','admin','2020-09-23 23:43:59','2020-09-23 23:43:59'),
      (527,1,'material_order','Order','admin','2020-09-23 23:48:35','2020-09-23 23:48:35'),
      (528,1,'upload_max_file_size','Upload max file size','admin','2020-09-24 00:08:15','2020-09-24 00:08:15'),
      (529,1,'short_description','Short Description','admin','2020-09-24 00:11:36','2020-09-24 00:11:36'),
      (530,1,'admin_type','Type','admin','2020-09-24 20:19:27','2020-09-24 20:19:27'),
      (531,1,'admin_edit_study_material_file','Edit Material File','admin','2020-09-24 20:26:51','2020-09-24 20:26:51'),
      (532,1,'admin_package_study_material','Package Study Material','admin','2020-09-25 19:44:49','2020-09-25 19:44:49'),
      (533,1,'study_material_name','Study Material Name','admin','2020-09-25 20:10:57','2020-09-25 20:10:57'),
      (534,1,'select_study_material','Select Study Material','admin','2020-09-25 21:19:09','2020-09-25 21:19:09'),
      (535,1,'admin_no_of_files','No Of Files','admin','2020-09-25 23:04:11','2020-09-25 23:04:11'),
      (536,1,'package_study_order_save','Study material order save','admin','2020-09-25 23:19:51','2020-09-25 23:19:51'),
      (537,1,'disable_copy_paste_click','Disable Copy Paste On Browser','admin','2020-09-26 00:06:02','2020-09-26 00:06:02'),
      (538,1,'disable_copy_paste_click_help','Disable Copy Paste On Browser Help','admin','2020-09-26 00:06:52','2020-09-26 00:06:52'),
      (539,1,'top_text_left_help','Top Text Left Help','admin','2020-09-26 02:27:36','2020-09-26 02:27:36'),
      (540,1,'top_text_left','Top Text Left','admin','2020-09-26 02:28:00','2020-09-26 02:28:00'),
      (541,1,'top_text_right_help','Top Text Right Help','admin','2020-09-26 02:28:20','2020-09-26 02:28:20'),
      (542,1,'top_text_right','Top Text Right','admin','2020-09-26 02:28:39','2020-09-26 02:28:39'),
      (543,1,'please_fill_all_required_field','Fill all require fields','admin','2020-09-27 01:14:56','2020-09-27 01:14:56'),
      (544,1,'latest_study_material','Latest Study Material','admin','2020-09-28 17:44:51','2020-09-28 17:44:51'),
      (545,1,'study_material_show','Show Study Material','admin','2020-09-28 17:55:18','2020-09-28 17:55:18'),
      (546,1,'files','Files','front','2020-09-28 22:35:55','2020-09-28 22:35:55'),
      (547,1,'no_study_material_found','No Study Material Found','front','2020-09-29 18:36:57','2020-09-29 18:36:57'),
      (548,1,'front_study_material_detail','Study Material Detail','front','2020-09-29 18:59:28','2020-09-29 18:59:28'),
      (549,1,'front_page_duration','Page No / Duration','front','2020-09-29 20:03:25','2020-09-29 20:03:25'),
      (550,1,'front_file_name','File Name','front','2020-09-29 20:04:26','2020-09-29 20:04:26'),
      (551,1,'popular_study_material','Popular Study Material','front','2020-09-30 17:12:41','2020-09-30 17:12:41'),
      (552,1,'go_to_study_detail','Study Detail','front','2020-09-30 18:27:58','2020-09-30 18:27:58'),
      (553,1,'choices_required','Choices Required','admin','2020-09-30 22:27:42','2020-09-30 22:27:42'),
      (554,1,'no_answer_given_yet','No answer given yet','front','2020-09-30 22:47:26','2020-09-30 22:47:26'),
      (555,1,'admin_label_rows','Rows','admin','2020-09-30 23:44:02','2020-09-30 23:44:02'),
      (556,1,'disable_quiz_history','Disable Quiz History','admin','2020-10-01 00:21:15','2020-10-01 00:21:15'),
      (557,1,'disable_quiz_history_help','Disable Quiz History Help','admin','2020-10-01 00:21:39','2020-10-01 00:21:39'),
      (558,1,'nav_language','Language','admin','2020-10-01 00:22:42','2020-10-01 00:22:42'),
      (559,1,'disable_blog','Disable Blog','admin','2020-10-01 00:33:37','2020-10-01 00:33:37'),
      (560,1,'disable_blog_help','Disable Blog Help','admin','2020-10-01 00:33:56','2020-10-01 00:33:56'),
      (561,1,'testimonial_background','Set Testimonial Background','admin','2020-10-01 00:45:30','2020-10-01 00:45:30'),
      (562,1,'testimonial_background_help','Testimonial Background Help','admin','2020-10-01 00:45:56','2020-10-01 00:45:56'),
      (563,1,'user_last_access','Last Access','admin','2020-10-02 17:04:08','2020-10-02 17:04:08'),
      (564,1,'admin_user_delete_confirm','Are you sure to delete','admin','2020-10-02 17:24:29','2020-10-02 17:24:29'),
      (565,1,'users_quiz_history_list','User wise quiz history','admin','2020-10-02 17:47:36','2020-10-02 17:47:36'),
      (566,1,'passing_marks','Passing','admin','2020-10-02 23:08:21','2020-10-02 23:08:21'),
      (567,1,'enter_no_limit','Enter 1 To 100 Number','admin','2020-10-02 23:25:38','2020-10-02 23:25:38'),
      (568,1,'pass','Pass','admin','2020-10-02 23:52:08','2020-10-02 23:52:08'),
      (569,1,'fail','Fail','admin','2020-10-02 23:52:24','2020-10-02 23:52:24'),
      (570,1,'email_encountered_an_error','Password not reset','front','2020-10-04 01:42:08','2020-10-04 01:42:08'),
      (571,1,'forgot_email_send','Your Password Change Link Send To Your Mail Address. ! ','front','2020-10-04 01:45:18','2020-10-04 01:45:18'),
      (572,1,'click_here','click Here','front','2020-10-04 02:22:44','2020-10-04 02:22:44'),
      (573,1,'admin_custom_fields_title','Custom  Fields Title','admin','2020-09-23 17:57:54','2020-09-23 17:57:54'),
      (574,1,'admin_field_name','Field Name','admin','2020-09-23 19:25:49','2020-09-23 19:25:49'),
      (575,1,'admin_field_type','Field Type','admin','2020-09-23 21:34:36','2020-09-23 21:34:36'),
      (576,1,'admin_field_form','Custom Field For Form','admin','2020-09-23 19:58:03','2020-09-23 19:58:03'),
      (577,1,'admin_is_required','Is Required','admin','2020-09-23 20:12:25','2020-09-23 20:12:25'),
      (578,1,'admin_field_help_text','Field Help Text','admin','2020-09-23 20:14:51','2020-09-23 20:14:51'),
      (579,1,'admin_field_placeholder','Field Placeholder','admin','2020-09-23 20:15:36','2020-09-23 20:15:36'),
      (580,1,'admin_field_width','Field Width','admin','2020-09-23 20:20:37','2020-09-23 20:20:37'),
      (581,1,'admin_field_options','Field Options','admin','2020-09-23 20:21:17','2020-09-23 20:21:17'),
      (582,1,'admin_field_order','Field Order','admin','2020-09-23 20:27:18','2020-09-23 20:27:18'),
      (583,1,'add_more','Add More','admin','2020-09-23 22:08:46','2020-09-23 22:08:46'),
      (584,1,'remove_option','Remove Option','admin','2020-09-23 22:09:14','2020-09-23 22:09:14'),
      (585,1,'admin_custom_fields_update','Update Custom Fields','admin','2020-09-23 22:49:13','2020-09-23 22:49:13'),
      (586,1,'custom_fields','Custom Fields','admin','2020-10-05 17:28:22','2020-10-05 17:28:22'),
      (587,1,'admin_custom_fields_list','Custom Fields List','admin','2020-10-05 17:28:50','2020-10-05 17:28:50'),
      (588,1,'admin_add_custom_fields','Add Custom Fields','admin','2020-10-05 17:29:10','2020-10-05 17:29:10'),
      (589,1,'admin_field_label','Field Label','admin','2020-10-05 17:30:27','2020-10-05 17:30:27'),
      (590,1,'audio_file','Audio ','front','2020-10-06 19:21:29','2020-10-06 19:21:29'),
      (591,1,'video_file','Video','front','2020-10-06 19:21:59','2020-10-06 19:21:59'),
      (592,1,'correct_on_answer','Correct on answer','admin','2020-10-06 22:34:54','2020-10-06 22:34:54'),
      (593,1,'quiz_level','Quiz level','admin','2020-10-06 23:03:12','2020-10-06 23:03:12'),
      (594,1,'easy','Easy','admin','2020-10-06 23:04:55','2020-10-06 23:04:55'),
      (595,1,'medium','Medium','admin','2020-10-06 23:05:40','2020-10-06 23:05:40'),
      (596,1,'hard','Hard','admin','2020-10-06 23:05:51','2020-10-06 23:05:51'),
      (597,1,'difficulty_level','Difficulty Level','admin','2020-10-07 00:47:30','2020-10-07 00:47:30'),
      (598,1,'points_on_correct','Points on correct','admin','2020-10-07 00:52:39','2020-10-07 00:52:39'),
      (599,1,'bonus_points','Bonus Points','admin','2020-10-07 01:16:11','2020-10-07 01:16:11'),
      (600,1,'is_premium','Is Premium','admin','2020-10-07 01:19:23','2020-10-07 01:19:23'),
      (601,1,'is_unlimited_time','Is Unlimited Time','admin','2020-10-07 01:20:18','2020-10-07 01:20:18'),
      (602,1,'admin_membership','Membership','admin','2020-10-07 18:52:55','2020-10-07 18:52:55'),
      (603,1,'membership_list','Membership List','admin','2020-10-07 18:54:33','2020-10-07 18:54:33'),
      (604,1,'admin_add_membership','Add Membership','admin','2020-10-07 19:12:18','2020-10-07 19:12:18'),
      (605,1,'no_of_days','Enter no of days','admin','2020-10-07 19:29:07','2020-10-07 19:29:07'),
      (606,1,'admin_edit_membership','Edit Membership','admin','2020-10-07 20:26:05','2020-10-07 20:26:05'),
      (607,1,'admin_level','Level','admin','2020-10-07 22:27:10','2020-10-07 22:27:10'),
      (608,1,'level_list','Level List','admin','2020-10-07 22:28:15','2020-10-07 22:28:15'),
      (609,1,'admin_add_level','Add Level','admin','2020-10-07 22:53:06','2020-10-07 22:53:06'),
      (610,1,'min_points','Minimum Point','admin','2020-10-07 23:23:22','2020-10-07 23:23:22'),
      (611,1,'admin_edit_level','Edit Level','admin','2020-10-07 23:59:55','2020-10-07 23:59:55'),
      (612,1,'invalid_api_keys_error_during_authentication','Invalid api keys error during authentication !','front','2020-10-08 17:41:33','2020-10-08 17:41:33'),
      (613,1,'buy_now','Buy Now','front','2020-10-08 19:38:13','2020-10-08 19:38:13'),
      (614,1,'view_content','View Content','front','2020-10-08 23:29:32','2020-10-08 23:29:32'),
      (615,1,'document','Docs','front','2020-10-09 02:09:20','2020-10-09 02:09:20'),
      (616,1,'audio','Audios','admin','2020-10-09 02:10:51','2020-10-09 02:10:51'),
      (617,1,'video','Videos','front','2020-10-09 02:11:16','2020-10-09 02:11:16'),
      (618,1,'general_setting','Gneral Setting','admin','2020-10-10 01:40:04','2020-10-10 01:40:04'),
      (619,1,'social_setting','Social link setting','admin','2020-10-10 01:41:22','2020-10-10 01:41:22'),
      (620,1,'mail_setting','Mail setting','admin','2020-10-10 01:42:11','2020-10-10 01:42:11'),
      (621,1,'social_login_setting','Social login setting','admin','2020-10-10 01:43:05','2020-10-10 01:43:05'),
      (622,1,'payment_setting','Payment setting','admin','2020-10-10 01:45:48','2020-10-10 01:45:48'),
      (623,1,'invoice_setting','Invoice setting','admin','2020-10-10 01:46:51','2020-10-10 01:46:51'),
      (624,1,'header_setting','Header setting','admin','2020-10-10 19:27:00','2020-10-10 19:27:00'),
      (625,1,'testimonial_setting','Testimonial setting','admin','2020-10-10 19:28:12','2020-10-10 19:28:12'),
      (626,1,'ratting_added_successfully','Ratting Added Successfully','front','2020-10-11 01:19:09','2020-10-11 01:19:09'),
      (627,1,'eroor_during_ratting_added','Error during add ratting','front','2020-10-11 01:19:40','2020-10-11 01:19:40'),
      (628,1,'form_validation_required','The {field} field is required.','admin','2020-10-12 16:54:04','2020-10-12 16:54:04'),
      (629,1,'form_validation_isset','The {field} field must have a value.','admin','2020-10-12 16:54:21','2020-10-12 16:54:21'),
      (630,1,'form_validation_valid_email','The {field} field must contain a valid email address.','admin','2020-10-12 16:54:36','2020-10-12 16:54:36'),
      (631,1,'form_validation_valid_emails','The {field} field must contain all valid email addresses.','admin','2020-10-12 16:54:56','2020-10-12 16:54:56'),
      (632,1,'form_validation_valid_url','The {field} field must contain a valid URL.','admin','2020-10-12 16:57:37','2020-10-12 16:57:37'),
      (633,1,'form_validation_valid_ip','The {field} field must contain a valid IP.','admin','2020-10-12 16:57:54','2020-10-12 16:57:54'),
      (634,1,'form_validation_min_length','The {field} field must be at least {param} characters in length.','admin','2020-10-12 16:58:26','2020-10-12 16:58:26'),
      (635,1,'form_validation_max_length','The {field} field cannot exceed {param} characters in length.','admin','2020-10-12 16:58:43','2020-10-12 16:58:43'),
      (636,1,'form_validation_exact_length','The {field} field must be exactly {param} characters in length.','admin','2020-10-12 16:59:03','2020-10-12 16:59:03'),
      (637,1,'form_validation_alpha','The {field} field may only contain alphabetical characters.','admin','2020-10-12 16:59:19','2020-10-12 16:59:19'),
      (638,1,'form_validation_alpha_numeric','The {field} field may only contain alpha-numeric characters.','admin','2020-10-12 16:59:39','2020-10-12 16:59:39'),
      (639,1,'form_validation_alpha_numeric_spaces','The {field} field may only contain alpha-numeric characters and spaces.','admin','2020-10-12 17:00:56','2020-10-12 17:00:56'),
      (640,1,'form_validation_alpha_dash','The {field} field may only contain alpha-numeric characters, underscores, and dashes.','admin','2020-10-12 17:01:18','2020-10-12 17:01:18'),
      (641,1,'form_validation_numeric','The {field} field must contain only numbers.','admin','2020-10-12 17:01:37','2020-10-12 17:01:37'),
      (642,1,'form_validation_is_numeric','The {field} field must contain only numeric characters.','admin','2020-10-12 17:01:55','2020-10-12 17:01:55'),
      (643,1,'form_validation_integer','The {field} field must contain an integer.','admin','2020-10-12 17:02:12','2020-10-12 17:02:12'),
      (644,1,'form_validation_regex_match','The {field} field is not in the correct format.','admin','2020-10-12 17:02:27','2020-10-12 17:02:27'),
      (645,1,'form_validation_matches','The {field} field does not match the {param} field.','admin','2020-10-12 17:02:44','2020-10-12 17:02:44'),
      (646,1,'form_validation_differs','The {field} field must differ from the {param} field.','admin','2020-10-12 17:03:01','2020-10-12 17:03:01'),
      (647,1,'form_validation_is_unique','The {field} field must contain a unique value.','admin','2020-10-12 17:03:17','2020-10-12 17:03:17'),
      (648,1,'form_validation_is_natural','The {field} field must only contain digits.','admin','2020-10-12 17:03:34','2020-10-12 17:03:34'),
      (649,1,'form_validation_is_natural_no_zero','The {field} field must only contain digits and must be greater than zero.','admin','2020-10-12 17:04:12','2020-10-12 17:04:12'),
      (650,1,'form_validation_decimal','The {field} field must contain a decimal number.','admin','2020-10-12 17:04:33','2020-10-12 17:04:33'),
      (651,1,'form_validation_less_than','The {field} field must contain a number less than {param}.','admin','2020-10-12 17:04:52','2020-10-12 17:04:52'),
      (652,1,'form_validation_less_than_equal_to','The {field} field must contain a number less than or equal to {param}.','admin','2020-10-12 17:05:08','2020-10-12 17:05:08'),
      (653,1,'form_validation_greater_than','The {field} field must contain a number greater than {param}.','admin','2020-10-12 17:05:24','2020-10-12 17:05:24'),
      (654,1,'form_validation_greater_than_equal_to','The {field} field must contain a number greater than or equal to {param}.','admin','2020-10-12 17:05:38','2020-10-12 17:05:38'),
      (655,1,'form_validation_error_message_not_set','Unable to access an error message corresponding to your field name {field}.','admin','2020-10-12 17:05:56','2020-10-12 17:05:56'),
      (656,1,'form_validation_in_list','The {field} field must be one of: {param}.','admin','2020-10-12 17:06:11','2020-10-12 17:06:11'),
      (657,1,'category_related_quiz_clear','Pass in all related quizes for next quiz level','front','2020-10-13 19:53:19','2020-10-13 19:53:20'),
      (658,1,'paypal_payment_getway','Paypal','admin','2020-10-23 23:44:59','2020-10-23 23:44:59'),
      (659,1,'stripe_payment_getway','Stripe','admin','2020-10-23 23:45:56','2020-10-23 23:45:56'),
      (660,1,'razorpay_payment_getway','Razorpay','admin','2020-10-23 23:47:32','2020-10-23 23:47:32'),
      (661,1,'instamojo_payment_getway','Instamojo','admin','2020-10-23 23:48:04','2020-10-23 23:48:04'),
      (662,1,'pay_by_bank_transfer','Bank Transfer','admin','2020-10-23 23:49:28','2020-10-23 23:49:28'),
      (663,1,'email_template_list','Email Template List','admin','2021-01-09 23:14:17','2021-01-09 17:44:17'),
      (664,1,'admin_name','Name','admin','2021-01-09 23:32:01','2021-01-09 18:02:01'),
      (665,1,'admin_published','Published','admin','2021-01-09 23:49:31','2021-01-09 18:19:31'),
      (666,1,'admin_unpublished','Unpublished','admin','2021-01-09 23:49:52','2021-01-09 18:19:52'),
      (667,1,'admin_email_template','Email Template','admin','2021-01-09 23:53:52','2021-01-09 18:23:52'),
      (668,1,'admin_subject','Subject','admin','2021-01-09 23:56:15','2021-01-09 18:26:15'),
      (669,1,'site_update_token','Update Token','admin','2021-01-09 23:57:09','2021-01-09 18:27:09'),
      (670,1,'date_or_time_formate','Date or time formate','admin','2021-01-09 23:57:43','2021-01-09 18:27:43'),
      (671,1,'admin_date_formate','Date Formate','admin','2021-01-09 23:58:06','2021-01-09 18:28:06'),
      (672,1,'recaptcha_site_key','Recaptcha Site Key','admin','2021-01-10 00:01:13','2021-01-09 18:31:13'),
      (673,1,'recaptcha_secret_key','Recaptha Secret Key','admin','2021-01-10 00:03:34','2021-01-09 18:33:34'),
      (674,1,'enable_captch_code_login','Enable captcha code login','admin','2021-01-10 00:04:00','2021-01-09 18:34:00'),
      (675,1,'custom_css_help','Custom Css Help','admin','2021-01-10 00:05:08','2021-01-09 18:35:08'),
      (676,1,'custom_css','Custom Css','admin','2021-01-10 00:05:22','2021-01-09 18:35:22'),
      (677,1,'footer_javascript_help','Footer Javascript Help','admin','2021-01-09 18:36:04','2021-01-10 00:06:04'),
      (678,1,'footer_javascript','Footer Javascript','admin','2021-01-10 00:05:53','2021-01-09 18:35:53'),
      (679,1,'header_javascript','Header Javascript','admin','2021-01-10 00:06:38','2021-01-09 18:36:38'),
      (680,1,'header_javascript_help','Header Javascript Help','admin','2021-01-10 00:06:49','2021-01-09 18:36:49'),
      (681,1,'user_admin','Admin','admin','2021-01-10 00:07:32','2021-01-09 18:37:32'),
      (682,1,'core_text_yes','Yes','admin','2021-01-10 00:08:01','2021-01-09 18:38:01'),
      (683,1,'common_leader_board','Common Leader Board','admin','2021-01-10 00:14:21','2021-01-09 18:44:21'),
      (684,1,'number_of_questions_require_for_quiz','Number of question','admin','2021-01-10 00:15:18','2021-01-09 18:45:18'),
      (685,1,'enter_zero_for_unlitimited_time','Enter Zero for unlimited time','admin','2021-01-10 00:15:37','2021-01-09 18:45:37'),
      (686,1,'require_quiz_passing_percentage','Passing marks','admin','2021-01-10 00:16:07','2021-01-09 18:46:07'),
      (687,1,'allow_registered_user_only','Allow registered user','admin','2021-01-10 00:16:33','2021-01-09 18:46:33'),
      (688,1,'allow_number_of_attempt_on_quiz','Allow no of attemp in quiz','admin','2021-01-10 00:16:54','2021-01-09 18:46:54'),
      (689,1,'quiz_upload_featured_images','Upload','admin','2021-01-10 00:17:09','2021-01-09 18:47:09'),
      (690,1,'overall_performance_summary','Overall Performance Summary','admin','2021-01-10 01:35:45','2021-01-09 20:05:45'),
      (691,1,'Update Profile','Update Profile','other','2021-01-14 01:12:36','2021-01-14 01:12:36'),
      (692,1,'Stop This Quiz','Stop This Quiz','other','2021-01-14 01:13:38','2021-01-14 01:13:38'),
      (693,1,'active_account','Active account','other','2021-01-15 17:25:32','2021-01-15 17:25:32'),
      (694,1,'otp_login','Otp login','other','2021-01-15 17:25:32','2021-01-15 17:25:32'),
      (695,1,'core error session_language','Core error session language','other','2021-01-15 17:25:38','2021-01-15 17:25:38'),
      (696,1,'Updates','Updates','other','2021-01-15 17:25:38','2021-01-15 17:25:38'),
      (697,1,'general setting','General setting','other','2021-01-15 19:41:05','2021-01-15 19:41:05'),
      (698,1,'payment setting','Payment setting','other','2021-01-15 19:41:05','2021-01-15 19:41:05'),
      (699,1,'social setting','Social setting','other','2021-01-15 19:41:05','2021-01-15 19:41:05'),
      (700,1,'mail setting','Mail setting','other','2021-01-15 19:41:05','2021-01-15 19:41:05'),
      (701,1,'api keys','Api keys','other','2021-01-15 19:41:05','2021-01-15 19:41:05'),
      (702,1,'invoice setting','Invoice setting','other','2021-01-15 19:41:05','2021-01-15 19:41:05'),
      (703,1,'social login setting','Social login setting','other','2021-01-15 19:41:05','2021-01-15 19:41:05'),
      (704,1,'testimonial setting','Testimonial setting','other','2021-01-15 19:41:05','2021-01-15 19:41:05'),
      (705,1,'header setting','Header setting','other','2021-01-15 19:41:05','2021-01-15 19:41:05'),
      (706,1,'instamojo_token','Instamojo token','other','2021-01-15 19:41:05','2021-01-15 19:41:05'),
      (707,1,'instamojo_token_help','Instamojo token help','other','2021-01-15 19:41:05','2021-01-15 19:41:05'),
      (708,1,'instamojo_apikey','Instamojo apikey','other','2021-01-15 19:41:05','2021-01-15 19:41:05'),
      (709,1,'instamojo_apikey_help','Instamojo apikey help','other','2021-01-15 19:41:05','2021-01-15 19:41:05'),
      (710,1,'instamojo_mode','Instamojo mode','other','2021-01-15 19:41:05','2021-01-15 19:41:05'),
      (711,1,'instamojo_mode_help','Instamojo mode help','other','2021-01-15 19:41:05','2021-01-15 19:41:05'),
      (712,1,'Evaluation Test','Evaluation Test','other','2021-01-15 19:41:10','2021-01-15 19:41:10'),
      (713,1,'YES','YES','other','2021-01-15 19:41:10','2021-01-15 19:41:10'),
      (714,1,'NO','NO','other','2021-01-15 19:41:10','2021-01-15 19:41:10'),
      (715,1,' Payment Not Found ... !',' Payment Not Found ... !','other','2021-01-16 05:54:18','2021-01-16 05:54:18'),
      (716,1,'payment_for','Payment for','other','2021-01-16 06:08:39','2021-01-16 06:08:39'),
      (717,1,'item_name','Item name','other','2021-01-16 06:08:39','2021-01-16 06:08:39'),
      (718,1,'Your Earn Points ','Your Earn Points ','other','2021-01-16 17:56:50','2021-01-16 17:56:50'),
      (719,1,'Next Level Points ','Next Level Points ','other','2021-01-16 17:56:50','2021-01-16 17:56:50'),
      (720,1,'Go To Home','Go To Home','other','2021-01-16 17:56:50','2021-01-16 17:56:50'),
      (721,1,'Payment Mode','Payment Mode','other','2021-01-17 21:35:41','2021-01-17 21:35:41'),
      (722,1,'Payment Mode','Payment Mode','other','2021-01-17 21:35:41','2021-01-17 21:35:41'),
      (723,1,'Payment Mode','Payment Mode','other','2021-01-17 21:35:41','2021-01-17 21:35:41'),
      (724,1,'header_logo_height','Header logo height','other','2021-01-19 17:33:14','2021-01-19 17:33:14'),
      (725,1,'header_logo_height','Header logo height','other','2021-01-19 17:33:14','2021-01-19 17:33:14'),
      (726,1,'Rozorpay Payment','Rozorpay Payment','other','2021-01-19 18:35:55','2021-01-19 18:35:55'),
      (727,1,'Rozorpay Payment','Rozorpay Payment','other','2021-01-19 18:35:55','2021-01-19 18:35:55'),
      (728,1,'Sorry You Already Pass This Quiz So Multiple Attemp Is Not Allowed ..!','Sorry You Already Pass This Quiz So Multiple Attemp Is Not Allowed ..!','other','2021-01-19 19:16:35','2021-01-19 19:16:35'),
      (729,1,'Stripe Payment','Stripe Payment','other','2021-01-19 19:25:27','2021-01-19 19:25:27'),
      (730,1,'Instamojo Payment','Instamojo Payment','other','2021-01-19 19:43:27','2021-01-19 19:43:27'),
      (731,1,'Instamojo Payment','Instamojo Payment','other','2021-01-19 19:43:27','2021-01-19 19:43:27'),
      (732,1,'Update','Update','other','2021-01-20 20:39:27','2021-01-20 20:39:27'),
      (733,1,'Update Name','Update Name','other','2021-01-20 20:39:27','2021-01-20 20:39:27'),
      (734,1,'Payment For','Payment For','other','2021-01-20 22:51:43','2021-01-20 22:51:43'),
      (735,1,'Item name','Item name','other','2021-01-20 22:51:43','2021-01-20 22:51:43'),
      (736,1,'credit_card_number','Credit card number','other','2021-01-20 22:57:22','2021-01-20 22:57:22'),
      (737,1,'goto_membership_page','Goto membership page','other','2021-01-20 22:58:13','2021-01-20 22:58:13'),
      (738,1,'get_membership','Get membership','other','2021-01-21 18:55:11','2021-01-21 18:55:11'),
      (739,1,'get_membership','Get membership','other','2021-01-21 18:55:11','2021-01-21 18:55:11'),
      (740,1,'all_categories','All categories','other','2021-01-21 20:54:48','2021-01-21 20:54:48'),
      (741,1,'all_categories','All categories','other','2021-01-21 20:54:48','2021-01-21 20:54:48'),
      (742,1,'all_categories','All categories','other','2021-01-21 20:54:48','2021-01-21 20:54:48'),
      (743,1,'login_please','Login please','other','2021-01-26 00:04:02','2021-01-26 00:04:02'),
      (744,1,'login_please','Login please','other','2021-01-26 00:04:02','2021-01-26 00:04:02'),
      (745,1,'admin_edit_email_template','Admin edit email template','other','2021-01-26 00:31:56','2021-01-26 00:31:56'),
      (746,1,'Update Downloaded And Verify Successfully','Update Downloaded And Verify Successfully','other','2021-01-26 02:00:44','2021-01-26 02:00:44'),
      (747,1,'Quiz Start Date Or Time','Quiz Start Date Or Time','other','2021-01-28 20:00:24','2021-01-28 20:00:24'),
      (748,1,'Quiz End Date Or Time','Quiz End Date Or Time','other','2021-01-28 20:00:24','2021-01-28 20:00:24'),
      (749,1,'core_text_no','Core text no','other','2021-01-28 20:06:14','2021-01-28 20:06:14'),
      (750,1,'admin_user_delete','Admin user delete','other','2021-01-28 20:06:14','2021-01-28 20:06:14'),
      (751,1,'admin_user_delete','Admin user delete','other','2021-01-28 20:06:14','2021-01-28 20:06:14'),
      (752,1,'users input username','Users input username','other','2021-01-28 20:06:19','2021-01-28 20:06:19'),
      (753,1,'users input first_name','Users input first name','other','2021-01-28 20:06:19','2021-01-28 20:06:19'),
      (754,1,'users input last_name','Users input last name','other','2021-01-28 20:06:19','2021-01-28 20:06:19'),
      (755,1,'users input email','Users input email','other','2021-01-28 20:06:19','2021-01-28 20:06:19'),
      (756,1,'users input language','Users input language','other','2021-01-28 20:06:19','2021-01-28 20:06:19'),
      (757,1,'users input status','Users input status','other','2021-01-28 20:06:19','2021-01-28 20:06:19'),
      (758,1,'users input is_admin','Users input is admin','other','2021-01-28 20:06:19','2021-01-28 20:06:19'),
      (759,1,'users input password','Users input password','other','2021-01-28 20:06:19','2021-01-28 20:06:19'),
      (760,1,'users input password_repeat','Users input password repeat','other','2021-01-28 20:06:19','2021-01-28 20:06:19'),
      (761,1,'is_sheduled_quiz','Is sheduled quiz','other','2021-01-28 23:09:33','2021-01-28 23:09:33'),
      (762,1,'Quiz Start Date Time','Quiz Start Date Time','other','2021-01-29 00:13:30','2021-01-29 00:13:30'),
      (763,1,'Quiz End Date Time','Quiz End Date Time','other','2021-01-29 00:13:30','2021-01-29 00:13:30'),
      (764,1,'Quiz Start Date Must Less Then From Quiz End Date','Quiz Start Date Must Less Then From Quiz End Date','other','2021-01-29 00:13:30','2021-01-29 00:13:30'),
      (765,1,'Quiz Start Date Must Less Then From Quiz End Date','Quiz Start Date Must Less Then From Quiz End Date','other','2021-01-29 00:13:30','2021-01-29 00:13:30'),
      (766,1,'renew_your_membership','Renew your membership','other','2021-01-29 02:32:41','2021-01-29 02:32:41'),
      (767,1,'Email','Email','other','2021-01-29 03:49:21','2021-01-29 03:49:21'),
      (768,1,'Quiz Status Change Successfully !','Quiz Status Change Successfully !','other','2021-01-29 06:00:36','2021-01-29 06:00:36'),
      (769,1,'select_default_time_zone','Select default time zone','other','2021-01-29 16:32:47','2021-01-29 16:32:47'),
      (770,1,'select_default_time_zone','Select default time zone','other','2021-01-29 16:32:47','2021-01-29 16:32:47'),
      (771,1,'mainteness_mode_contant','Mainteness mode contant','other','2021-02-05 22:33:01','2021-02-05 22:33:01'),
      (772,1,'mainteness_mode_contant','Mainteness mode contant','other','2021-02-05 22:33:01','2021-02-05 22:33:01'),
      (773,1,'mainteness_mode_on','Mainteness mode on','other','2021-02-05 22:33:01','2021-02-05 22:33:01'),
      (774,1,'mainteness_mode_on','Mainteness mode on','other','2021-02-05 22:33:01','2021-02-05 22:33:01'),
      (775,1,'home page contant','Home page contant','other','2021-02-06 23:47:29','2021-02-06 23:47:29'),
      (776,1,'hide_language_menu','Hide language menu','other','2021-02-06 23:47:29','2021-02-06 23:47:29'),
      (777,1,'hide_language_menu','Hide language menu','other','2021-02-06 23:47:29','2021-02-06 23:47:29'),
      (778,1,'popular_quiz_show_on_home','Popular quiz show on home','other','2021-02-06 23:47:29','2021-02-06 23:47:29'),
      (779,1,'popular_quiz_show_on_home','Popular quiz show on home','other','2021-02-06 23:47:29','2021-02-06 23:47:29'),
      (780,1,'latest_quiz_show_on_home','Latest quiz show on home','other','2021-02-06 23:47:29','2021-02-06 23:47:29'),
      (781,1,'latest_quiz_show_on_home','Latest quiz show on home','other','2021-02-06 23:47:29','2021-02-06 23:47:29'),
      (782,1,'show_home_page_latest_study_material','Show home page latest study material','other','2021-02-06 23:47:29','2021-02-06 23:47:29'),
      (783,1,'show_home_page_latest_study_material','Show home page latest study material','other','2021-02-06 23:47:29','2021-02-06 23:47:29'),
      (784,1,'show_home_page_popular_study_material','Show home page popular study material','other','2021-02-06 23:47:29','2021-02-06 23:47:29'),
      (785,1,'show_home_page_popular_study_material','Show home page popular study material','other','2021-02-06 23:47:29','2021-02-06 23:47:29'),
      (786,1,'show_home_page_testimonial','Show home page testimonial','other','2021-02-06 23:47:29','2021-02-06 23:47:29'),
      (787,1,'show_home_page_testimonial','Show home page testimonial','other','2021-02-06 23:47:29','2021-02-06 23:47:29'),
      (788,1,'show_home_page_sponser','Show home page sponser','other','2021-02-06 23:47:29','2021-02-06 23:47:29'),
      (789,1,'show_home_page_sponser','Show home page sponser','other','2021-02-06 23:47:29','2021-02-06 23:47:29'),
      (790,1,'is_special_user','Is special user','other','2021-02-07 00:14:25','2021-02-07 00:14:25'),
      (791,1,'special_user_more_duration_in_minute','Special user more duration in minute','other','2021-02-07 00:31:10','2021-02-07 00:31:10'),
      (792,1,'enter_zero_for_no_more_time','Enter zero for no more time','other','2021-02-07 00:32:01','2021-02-07 00:32:01'),
      (793,1,'special_user_more_time','Special user more time','other','2021-02-07 00:39:41','2021-02-07 00:39:41'),
      (794,1,'dashboard','Dashboard','other','2021-02-11 01:38:16','2021-02-11 01:38:16'),
      (795,1,'access_denide','Access denide','other','2021-02-11 02:02:40','2021-02-11 02:02:40'),
      (796,1,'view','View','other','2021-02-11 03:03:44','2021-02-11 03:03:44'),
      (797,1,'view','View','other','2021-02-11 03:03:44','2021-02-11 03:03:44'),
      (798,1,'user role','User role','other','2021-02-11 04:25:25','2021-02-11 04:25:25'),
      (799,1,'USER DASHBOARD','USER DASHBOARD','other','2021-02-11 19:22:42','2021-02-11 19:22:42'),
      (800,1,'time_accommodation','Time accommodation','other','2021-02-11 19:26:05','2021-02-11 19:26:05'),
      (801,1,'users input role','Users input role','other','2021-02-11 19:30:22','2021-02-11 19:30:22'),
      (802,1,'users role','Users role','other','2021-02-11 19:35:37','2021-02-11 19:35:37'),
      (803,1,'please_take_membership','Please take membership','other','2021-02-15 21:07:35','2021-02-15 21:07:35'),
      (804,1,'external_contant_link','External contant link','other','2021-02-23 00:21:11','2021-02-23 00:21:11'),
      (805,1,'external_contant_link','External contant link','other','2021-02-23 00:21:11','2021-02-23 00:21:11'),
      (806,1,'external_contant_link','External contant link','other','2021-02-23 00:21:11','2021-02-23 00:21:11'),
      (807,1,'admin_question_addon_type','Admin question addon type','other','2021-02-23 00:41:32','2021-02-23 00:41:32'),
      (808,1,'admin_question_addon_value','Admin question addon value','other','2021-02-23 00:41:32','2021-02-23 00:41:32'),
      (809,1,'total_points','Total points','other','2021-02-25 00:17:56','2021-02-25 00:17:56'),
      (810,1,'accuray','Accuray','other','2021-02-25 01:03:30','2021-02-25 01:03:30'),
      (811,1,'Mark for Review and Next','Mark for Review and Next','other','2021-02-25 01:15:39','2021-02-25 01:15:39'),
      (812,1,'Mark for Answer and Next','Mark for Answer and Next','other','2021-02-25 01:15:39','2021-02-25 01:15:39'),
      (813,1,'mark_for_answer_and_next','Mark for answer and next','other','2021-02-25 01:15:39','2021-02-25 01:15:39'),
      (814,1,'Time Taken','Time Taken','other','2021-02-25 01:58:54','2021-02-25 01:58:54'),
      (815,1,'Time ','Time ','other','2021-02-25 01:59:30','2021-02-25 01:59:30'),
      (816,1,'mark_for_review','Mark for review','other','2021-02-25 20:01:33','2021-02-25 20:01:33'),
      (817,1,'marked_for_answer','Marked for answer','other','2021-02-25 20:01:33','2021-02-25 20:01:33'),
      (818,1,'mark_for_answer','Mark for answer','other','2021-02-25 20:02:49','2021-02-25 20:02:49'),
      (819,1,'Payment Success ','Payment Success ','other','2021-02-26 00:08:33','2021-02-26 00:08:33'),
      (820,1,'Payment Success ','Payment Success ','other','2021-02-26 00:08:33','2021-02-26 00:08:33'),
      (821,1,'Payment Success','Payment Success','other','2021-02-26 00:08:33','2021-02-26 00:08:33'),
      (822,1,'hidden','Hidden','other','2021-02-26 00:25:10','2021-02-26 00:25:10'),
      (823,1,'update_info','Update info','other','2021-02-26 00:25:10','2021-02-26 00:25:10'),
      (824,1,'update_info','Update info','other','2021-02-26 00:25:10','2021-02-26 00:25:10'),
      (825,1,'Not Verify Yet','Not Verify Yet','other','2021-02-26 05:54:55','2021-02-26 05:54:55'),
      (826,1,'Purchage Code Not Verify Yet ... !','Purchage Code Not Verify Yet ... !','other','2021-02-26 06:08:12','2021-02-26 06:08:12'),
      (827,1,'Purchase code not verified!','Purchase code not verified!','other','2021-02-26 19:21:18','2021-02-26 19:21:18'),
      (828,1,'front_error_no_results','Front error no results','other','2021-02-26 22:28:53','2021-02-26 22:28:53'),
      (829,1,'front_active','Front active','other','2021-02-26 22:30:15','2021-02-26 22:30:15'),
      (830,1,'front_active','Front active','other','2021-02-26 22:30:15','2021-02-26 22:30:15'),
      (831,1,'You are On Latest Version !','You are On Latest Version !','other','2021-03-01 03:24:19','2021-03-01 03:24:19'),
      (832,1,'advertisment','Advertisment','other','2021-03-02 18:07:07','2021-03-02 18:07:07'),
      (833,1,'advertisment_list','Advertisment list','other','2021-03-02 18:30:52','2021-03-02 18:30:52'),
      (834,1,'advertisment_update','Advertisment update','other','2021-03-02 18:31:52','2021-03-02 18:31:52'),
      (835,1,'advertisment_delete','Advertisment delete','other','2021-03-02 18:31:52','2021-03-02 18:31:52'),
      (836,1,'advertisment_update','Advertisment update','other','2021-03-02 18:31:52','2021-03-02 18:31:52'),
      (837,1,'advertisment_delete','Advertisment delete','other','2021-03-02 18:31:52','2021-03-02 18:31:52'),
      (838,1,'advertisment_update','Advertisment update','other','2021-03-02 18:31:52','2021-03-02 18:31:52'),
      (839,1,'advertisment_delete','Advertisment delete','other','2021-03-02 18:31:52','2021-03-02 18:31:52'),
      (840,1,'advertisment_update','Advertisment update','other','2021-03-02 18:31:52','2021-03-02 18:31:52'),
      (841,1,'advertisment_delete','Advertisment delete','other','2021-03-02 18:31:52','2021-03-02 18:31:52'),
      (842,1,'advertisment_update','Advertisment update','other','2021-03-02 18:31:52','2021-03-02 18:31:52'),
      (843,1,'advertisment_delete','Advertisment delete','other','2021-03-02 18:31:52','2021-03-02 18:31:52'),
      (844,1,'advertisment_update','Advertisment update','other','2021-03-02 18:31:52','2021-03-02 18:31:52'),
      (845,1,'advertisment_delete','Advertisment delete','other','2021-03-02 18:31:52','2021-03-02 18:31:52'),
      (846,1,'advertisment_update','Advertisment update','other','2021-03-02 18:31:52','2021-03-02 18:31:52'),
      (847,1,'advertisment_delete','Advertisment delete','other','2021-03-02 18:31:52','2021-03-02 18:31:52'),
      (848,1,'advertisment_update','Advertisment update','other','2021-03-02 18:31:53','2021-03-02 18:31:53'),
      (849,1,'advertisment_delete','Advertisment delete','other','2021-03-02 18:31:53','2021-03-02 18:31:53'),
      (850,1,'add_advertisment','Add advertisment','other','2021-03-03 00:01:33','2021-03-03 00:01:33'),
      (851,1,'url','Url','other','2021-03-03 00:01:33','2021-03-03 00:01:33'),
      (852,1,'image','Image','other','2021-03-03 00:01:33','2021-03-03 00:01:33'),
      (853,1,'position','Position','other','2021-03-03 00:01:33','2021-03-03 00:01:33'),
      (854,1,'action','Action','other','2021-03-03 00:01:33','2021-03-03 00:01:33'),
      (855,1,'link','Link','other','2021-03-03 00:03:38','2021-03-03 00:03:38'),
      (856,1,'advertisment_logo','Advertisment logo','other','2021-03-03 00:03:38','2021-03-03 00:03:38'),
      (857,1,'advertisment_add','Advertisment add','other','2021-03-03 00:03:38','2021-03-03 00:03:38'),
      (858,1,'logo','Logo','other','2021-03-03 00:05:26','2021-03-03 00:05:26'),
      (859,1,'home_page_under_menubar','Home page under menubar','other','2021-03-03 00:45:20','2021-03-03 00:45:20'),
      (860,1,'home_page_before_footer','Home page before footer','other','2021-03-03 00:45:20','2021-03-03 00:45:20'),
      (861,1,'home_page_before_footer','Home page before footer','other','2021-03-03 00:45:20','2021-03-03 00:45:20'),
      (862,1,'home_page_before_footer','Home page before footer','other','2021-03-03 00:45:20','2021-03-03 00:45:20'),
      (863,1,'home_page_before_footer','Home page before footer','other','2021-03-03 00:45:20','2021-03-03 00:45:20'),
      (864,1,'blog_list_page_on_sidebar','Blog list page on sidebar','other','2021-03-03 00:45:20','2021-03-03 00:45:20'),
      (865,1,'admin_advertisment_logo','Admin advertisment logo','other','2021-03-03 01:10:51','2021-03-03 01:10:51'),
      (866,1,'blog_detail_below_sidebar','Blog detail below sidebar','other','2021-03-03 01:21:18','2021-03-03 01:21:18'),
      (867,1,'common_under_menu','Common under menu','other','2021-03-03 01:21:25','2021-03-03 01:21:25'),
      (868,1,'common_before_footer','Common before footer','other','2021-03-03 01:21:25','2021-03-03 01:21:25'),
      (869,1,'home_page_below_category','Home page below category','other','2021-03-03 01:21:25','2021-03-03 01:21:25'),
      (870,1,'home_page_below_testimonials','Home page below testimonials','other','2021-03-03 01:21:25','2021-03-03 01:21:25'),
      (871,1,'blog_list_page_below_sidebar','Blog list page below sidebar','other','2021-03-03 01:21:25','2021-03-03 01:21:25'),
      (872,1,'blog_detail_page_below_sidebar','Blog detail page below sidebar','other','2021-03-03 01:21:25','2021-03-03 01:21:25'),
      (873,1,'relation_tree','Relation tree','other','2021-03-03 02:55:33','2021-03-03 02:55:33'),
      (874,1,'available_for_registered_only','Available for registered only','other','2021-03-03 23:18:49','2021-03-03 23:18:49'),
      (875,1,'price','Price','other','2021-03-03 23:30:19','2021-03-03 23:30:19'),
      (876,1,'study_pay_now','Study pay now','other','2021-03-04 00:18:23','2021-03-04 00:18:23'),
      (877,1,'study_pay_now','Study pay now','other','2021-03-04 00:18:23','2021-03-04 00:18:23'),
      (878,1,'please_take_membership_plan_first','Please take membership plan first','other','2021-03-04 02:48:49','2021-03-04 02:48:49'),
      (879,1,'something_went_wrong','Something went wrong','other','2021-03-04 19:19:37','2021-03-04 19:19:37'),
      (880,1,'unlike','Unlike','other','2021-03-04 19:21:43','2021-03-04 19:21:43'),
      (881,1,'admin Rating Status Updated Successfully','Admin Rating Status Updated Successfully','other','2021-03-04 21:04:22','2021-03-04 21:04:22'),
      (882,1,'Congratulation Register Successfully','Congratulation Register Successfully','other','2021-03-04 21:21:16','2021-03-04 21:21:16'),
      (883,1,'please_login_first','Please login first','other','2021-03-04 21:35:32','2021-03-04 21:35:32'),
      (884,1,'request_for_tutor','Request for tutor','other','2021-03-04 23:34:41','2021-03-04 23:34:41'),
      (885,1,'requested_for_tutor','Requested for tutor','other','2021-03-04 23:52:28','2021-03-04 23:52:28'),
      (886,1,'approve_now','Approve now','other','2021-03-04 23:52:28','2021-03-04 23:52:28'),
      (887,1,'requested_for_tutor_approval','Requested for tutor approval','other','2021-03-04 23:53:14','2021-03-04 23:53:14'),
      (888,1,'Account Activation','Account Activation','other','2021-03-05 00:08:35','2021-03-05 00:08:35'),
      (889,1,'Email Address','Email Address','other','2021-03-05 00:08:35','2021-03-05 00:08:35'),
      (890,1,'Activate Account','Activate Account','other','2021-03-05 00:08:35','2021-03-05 00:08:35'),
      (891,1,'invalid_uri_arguments','Invalid uri arguments','other','2021-03-05 01:16:41','2021-03-05 01:16:41'),
      (892,1,'tutor_list','Tutor list','other','2021-03-05 01:28:43','2021-03-05 01:28:43'),
      (893,1,'no','No','other','2021-03-05 01:52:46','2021-03-05 01:52:46'),
      (894,1,'update','Update','other','2021-03-05 01:52:47','2021-03-05 01:52:47'),
      (895,1,'change_status','Change status','other','2021-03-05 02:05:40','2021-03-05 02:05:40'),
      (896,1,'tutor_requests','Tutor requests','other','2021-03-05 02:18:26','2021-03-05 02:18:26'),
      (897,1,'Account Activate Successfully','Account Activate Successfully','other','2021-03-05 02:38:14','2021-03-05 02:38:14'),
      (898,1,'language language name','Language language name','other','2021-03-05 12:54:10','2021-03-05 12:54:10'),
      (899,1,'no_category_found_in','No category found in','other','2021-03-05 17:29:37','2021-03-05 17:29:37'),
      (900,1,'view_all_categories','View all categories','other','2021-03-05 14:17:40','2021-03-05 14:17:40'),
      (901,1,'quiz_running','Quiz running','other','2021-03-06 21:29:09','2021-03-06 21:29:09'),
      (902,1,'start_now','Start now','other','2021-03-06 21:29:09','2021-03-06 21:29:09'),
      (903,1,'reviews','Reviews','other','2021-03-06 21:29:09','2021-03-06 21:29:09'),
      (904,1,'view_contant','View contant','other','2021-03-06 21:29:24','2021-03-06 21:29:24'),
      (905,1,'section_or_contant','Section or contant','other','2021-03-09 21:33:29','2021-03-09 21:33:29'),
      (906,1,'add_section','Add section','other','2021-03-09 21:33:35','2021-03-09 21:33:35'),
      (907,1,'please_add_section','Please add section','other','2021-03-09 21:33:35','2021-03-09 21:33:35'),
      (908,1,'add_secton','Add secton','other','2021-03-09 21:33:35','2021-03-09 21:33:35'),
      (909,1,'update_secton','Update secton','other','2021-03-09 21:33:35','2021-03-09 21:33:35'),
      (910,1,'contant_type','Contant type','other','2021-03-09 21:33:35','2021-03-09 21:33:35'),
      (911,1,'please_add_Section_first','Please add Section first','other','2021-03-09 21:34:06','2021-03-09 21:34:06'),
      (912,1,'section','Section','other','2021-03-09 21:34:34','2021-03-09 21:34:34'),
      (913,1,'section_name','Section name','other','2021-03-09 21:34:59','2021-03-09 21:34:59'),
      (914,1,'content_duration_in_minutes','Content duration in minutes','other','2021-03-10 16:12:40','2021-03-10 16:12:40'),
      (915,1,'VIDEO','VIDEO','other','2021-03-12 19:15:18','2021-03-12 19:15:18'),
      (916,1,'AUDIO','AUDIO','other','2021-03-12 19:15:18','2021-03-12 19:15:18'),
      (917,1,'IMAGES','IMAGES','other','2021-03-12 19:15:18','2021-03-12 19:15:18'),
      (918,1,'PDF','PDF','other','2021-03-12 19:15:18','2021-03-12 19:15:18'),
      (919,1,'DOC','DOC','other','2021-03-12 19:15:18','2021-03-12 19:15:18'),
      (920,1,'OTHER','OTHER','other','2021-03-12 19:15:18','2021-03-12 19:15:18'),
      (921,1,'View Content','View Content','other','2021-03-12 19:15:18','2021-03-12 19:15:18'),
      (922,1,'yes','Yes','other','2021-03-13 03:20:07','2021-03-13 03:20:07'),
      (923,1,'required_fields_missing','Required fields missing','other','2021-03-13 14:27:10','2021-03-13 14:27:10'),
      (924,1,'youtube_embed_code','Youtube embed code','other','2021-03-13 15:11:31','2021-03-13 15:11:31'),
      (925,1,'embed_code_or_contant','Embed code or contant','other','2021-03-13 15:13:35','2021-03-13 15:13:35'),
      (926,1,'min','Min','other','2021-03-13 19:21:49','2021-03-13 19:21:49'),
      (927,1,'min','Min','other','2021-03-13 19:21:49','2021-03-13 19:21:49'),
      (928,1,'min','Min','other','2021-03-13 19:21:49','2021-03-13 19:21:49'),
      (929,1,'min','Min','other','2021-03-13 19:21:49','2021-03-13 19:21:49'),
      (930,1,'min','Min','other','2021-03-13 19:21:49','2021-03-13 19:21:49'),
      (931,1,'vimeo_embed_code','Vimeo embed code','other','2021-03-13 19:28:23','2021-03-13 19:28:23'),
      (932,1,'you_have_complete','You have complete','other','2021-03-13 19:39:13','2021-03-13 19:39:13'),
      (933,1,'out_of','Out of','other','2021-03-13 19:39:13','2021-03-13 19:39:13'),
      (934,1,'Invalid Try','Invalid Try','other','2021-03-15 15:34:55','2021-03-15 15:34:55'),
      (935,1,'Order Change Successfully !','Order Change Successfully !','other','2021-03-15 15:34:55','2021-03-15 15:34:55'),
      (936,1,'record_deleted_successfully','Record deleted successfully','other','2021-03-15 16:56:03','2021-03-15 16:56:03'),
      (937,1,'complete_section_contant','Complete section contant','other','2021-03-16 20:52:18','2021-03-16 20:52:18'),
      (938,1,'uncomplete_section_contant','Uncomplete section contant','other','2021-03-20 00:04:41','2021-03-20 00:04:41'),
      (939,1,'search_items','Search items','other','2021-03-20 20:25:49','2021-03-20 20:25:49'),
      (940,1,'search_items','Search items','other','2021-03-20 20:25:49','2021-03-20 20:25:49'),
      (941,1,'Search','Search','other','2021-03-20 20:25:49','2021-03-20 20:25:49'),
      (942,1,'search','Search','other','2021-03-20 20:26:10','2021-03-20 20:26:10'),
      (943,1,'search','Search','other','2021-03-20 20:26:10','2021-03-20 20:26:10'),
      (944,1,'no_result_found_for','No result found for','other','2021-03-20 20:26:10','2021-03-20 20:26:10'),
      (945,1,'filter_by_category','Filter by category','other','2021-03-20 20:26:10','2021-03-20 20:26:10'),
      (946,1,'filter_by_price','Filter by price','other','2021-03-20 20:26:10','2021-03-20 20:26:10'),
      (947,1,'filter_by_rating','Filter by rating','other','2021-03-20 20:26:10','2021-03-20 20:26:10'),
      (948,1,'five_star','Five star','other','2021-03-20 20:26:10','2021-03-20 20:26:10'),
      (949,1,'four_star','Four star','other','2021-03-20 20:26:10','2021-03-20 20:26:10'),
      (950,1,'three_star','Three star','other','2021-03-20 20:26:10','2021-03-20 20:26:10'),
      (951,1,'two_star','Two star','other','2021-03-20 20:26:10','2021-03-20 20:26:10'),
      (952,1,'one_star','One star','other','2021-03-20 20:26:10','2021-03-20 20:26:10'),
      (953,1,'filter_by_duration','Filter by duration','other','2021-03-20 20:26:10','2021-03-20 20:26:10'),
      (954,1,'we_are_showing_results_for','We are showing results for','other','2021-03-20 20:27:02','2021-03-20 20:27:02'),
      (955,1,'4 to 5 star','4 to 5 star','other','2021-03-21 08:09:31','2021-03-21 08:09:31'),
      (956,1,'3 to 4 star','3 to 4 star','other','2021-03-21 08:09:31','2021-03-21 08:09:31'),
      (957,1,'2 to 3 star','2 to 3 star','other','2021-03-21 08:09:31','2021-03-21 08:09:31'),
      (958,1,'1 to 2 star','1 to 2 star','other','2021-03-21 08:09:31','2021-03-21 08:09:31'),
      (959,1,'0 to 1 star','0 to 1 star','other','2021-03-21 08:09:31','2021-03-21 08:09:31'),
      (960,1,'categories','Categories','other','2021-03-22 12:09:18','2021-03-22 12:09:18'),
      (961,1,'quiz_grading','Quiz grading','other','2021-03-25 21:06:26','2021-03-25 21:06:26'),
      (962,1,'add_quiz_grading','Add quiz grading','other','2021-03-25 21:06:31','2021-03-25 21:06:31'),
      (963,1,'select_quiz_grading','Select quiz grading','other','2021-03-25 21:06:40','2021-03-25 21:06:40'),
      (964,1,'select_quiz_grading','Select quiz grading','other','2021-03-25 21:06:40','2021-03-25 21:06:40'),
      (965,1,'update_quiz_grading','Update quiz grading','other','2021-03-25 23:29:34','2021-03-25 23:29:34'),
      (966,1,'Quiz Level Name','Quiz Level Name','other','2021-03-25 23:29:34','2021-03-25 23:29:34'),
      (967,1,'Quiz Level Minimum Percentage','Quiz Level Minimum Percentage','other','2021-03-25 23:29:34','2021-03-25 23:29:34'),
      (968,1,'contaet_or_embed_code','Contaet or embed code','other','2021-03-27 13:53:26','2021-03-27 13:53:26'),
      (969,1,'Contact','Contact','other','2021-03-27 18:10:03','2021-03-27 18:10:03'),
      (970,1,'Profile','Profile','other','2021-03-27 18:10:03','2021-03-27 18:10:03'),
      (971,1,'Categories','Categories','other','2021-03-27 18:12:38','2021-03-27 18:12:38'),
      (972,1,'Quiz History','Quiz History','other','2021-03-27 18:12:38','2021-03-27 18:12:38'),
      (973,1,'Leader Board','Leader Board','other','2021-03-27 18:12:38','2021-03-27 18:12:38'),
      (974,1,'Contact Us','Contact Us','other','2021-03-27 18:12:38','2021-03-27 18:12:38'),
      (975,1,'Membership','Membership','other','2021-03-27 18:12:38','2021-03-27 18:12:38'),
      (976,1,'Blogs','Blogs','other','2021-03-27 18:12:38','2021-03-27 18:12:38'),
      (977,1,'Language','Language','other','2021-03-27 18:12:38','2021-03-27 18:12:38'),
      (978,1,'Home','Home','other','2021-03-27 18:12:38','2021-03-27 18:12:38'),
      (979,1,'Front Menu Items','Front Menu Items','other','2021-03-27 18:16:20','2021-03-27 18:16:20'),
      (980,1,'Menu List','Menu List','other','2021-03-27 18:16:20','2021-03-27 18:16:20'),
      (981,1,'Menu Order','Menu Order','other','2021-03-27 18:16:20','2021-03-27 18:16:20'),
      (982,1,'Menu Items','Menu Items','other','2021-03-27 18:16:27','2021-03-27 18:16:27'),
      (983,1,'admin table no','Admin table no','other','2021-03-27 18:16:27','2021-03-27 18:16:27'),
      (984,1,'Menu Title','Menu Title','other','2021-03-27 18:16:27','2021-03-27 18:16:27'),
      (985,1,'Slug','Slug','other','2021-03-27 18:16:27','2021-03-27 18:16:27'),
      (986,1,'Status','Status','other','2021-03-27 18:16:27','2021-03-27 18:16:27'),
      (987,1,'Arrange Menu Items','Arrange Menu Items','other','2021-03-27 18:16:30','2021-03-27 18:16:30'),
      (988,1,'admin_edit_blog_post','Admin edit blog post','other','2021-03-31 07:06:13','2021-03-31 07:06:13'),
      (989,1,'admin_Name','Admin Name','other','2021-04-01 08:02:36','2021-04-01 08:02:36'),
      (990,1,'admin_Profile','Admin Profile','other','2021-04-01 08:02:36','2021-04-01 08:02:36'),
      (991,1,'quiz','Quiz','other','2021-04-05 19:48:54','2021-04-05 19:48:54'),
      (992,1,'study_matrial','Study matrial','other','2021-04-05 19:48:54','2021-04-05 19:48:54'),
      (993,1,'Category','Category','other','2021-04-05 19:48:54','2021-04-05 19:48:54'),
      (994,1,'tutor','Tutor','other','2021-04-05 19:48:54','2021-04-05 19:48:54'),
      (995,1,'no_tutor_fond','No tutor fond','other','2021-04-05 19:49:20','2021-04-05 19:49:20'),
      (996,1,'quizes','Quizes','other','2021-04-05 19:49:29','2021-04-05 19:49:29'),
      (997,1,'study_data','Study data','other','2021-04-05 19:49:29','2021-04-05 19:49:29'),
      (998,1,'tutor_study_materials','Tutor study materials','other','2021-04-05 19:49:35','2021-04-05 19:49:35'),
      (999,1,'study_materials','Study materials','other','2021-04-05 19:49:35','2021-04-05 19:49:35'),
      (1000,1,'tutor_quiz','Tutor quiz','other','2021-04-05 19:49:39','2021-04-05 19:49:39'),
      (1001,1,'tutor_quiz','Tutor quiz','other','2021-04-05 19:49:39','2021-04-05 19:49:39'),
      (1002,1,'Login please','Login please','other','2021-04-05 19:55:15','2021-04-05 19:55:15'),
      (1003,1,'manage_study_contant','Manage study contant','other','2021-04-05 19:55:40','2021-04-05 19:55:40'),
      (1004,1,'preview','Preview','other','2021-04-05 19:55:45','2021-04-05 19:55:45'),
      (1005,1,'manage_quiz','Manage quiz','other','2021-04-05 20:11:52','2021-04-05 20:11:52'),
      (1006,1,'Preview','Preview','other','2021-04-05 20:11:58','2021-04-05 20:11:58'),
      (1007,1,'Preview','Preview','other','2021-04-05 20:11:58','2021-04-05 20:11:58'),
      (1008,1,'Preview','Preview','other','2021-04-05 20:11:58','2021-04-05 20:11:58'),
      (1009,1,'Preview','Preview','other','2021-04-05 20:11:58','2021-04-05 20:11:58'),
      (1010,1,'Preview','Preview','other','2021-04-05 20:11:58','2021-04-05 20:11:58'),
      (1011,1,'Preview','Preview','other','2021-04-05 20:11:58','2021-04-05 20:11:58'),
      (1012,1,'Preview','Preview','other','2021-04-05 20:11:58','2021-04-05 20:11:58'),
      (1013,1,'Preview','Preview','other','2021-04-05 20:11:58','2021-04-05 20:11:58'),
      (1014,1,'Preview','Preview','other','2021-04-05 20:11:58','2021-04-05 20:11:58'),
      (1015,1,'Preview','Preview','other','2021-04-05 20:11:58','2021-04-05 20:11:58'),
      (1016,1,'premium','Premium','other','2021-04-05 21:21:49','2021-04-05 21:21:49'),
      (1017,1,'Get membership','Get membership','other','2021-04-05 21:21:49','2021-04-05 21:21:49'),
      (1018,1,'Pay Now','Pay Now','other','2021-04-05 21:21:49','2021-04-05 21:21:49'),
      (1019,1,'study_material_detail','Study material detail','other','2021-04-09 18:49:33','2021-04-09 18:49:33'),
      (1020,1,'my_courses','My courses','other','2021-04-09 18:49:33','2021-04-09 18:49:33'),
      (1021,1,'enroll_now','Enroll now','other','2021-04-09 18:54:18','2021-04-09 18:54:18'),
      (1022,1,'Enroll now','Enroll now','other','2021-04-09 18:54:18','2021-04-09 18:54:18'),
      (1023,1,'enrolled_success','Enrolled success','other','2021-04-09 18:54:21','2021-04-09 18:54:21'),
      (1024,1,'please_enroll_first','Please enroll first','other','2021-04-09 19:05:46','2021-04-09 19:05:46'),
      (1025,1,'please_login_first!','Please login first!','other','2021-04-10 00:17:10','2021-04-10 00:17:10'),
      (1026,1,'my_study_materials','My study materials','other','2021-04-10 00:19:04','2021-04-10 00:19:04'),
      (1027,1,'my_study_materials','My study materials','other','2021-04-10 00:19:04','2021-04-10 00:19:04'),
      (1028,1,'my_study_data','My study data','other','2021-04-10 00:19:04','2021-04-10 00:19:04'),
      (1029,1,'go_to_next_chapter','Go to next chapter','other','2021-04-10 17:44:09','2021-04-10 17:44:09'),
      (1030,1,'complete_chapter','Complete chapter','other','2021-04-12 21:09:56','2021-04-12 21:09:56'),
      (1031,1,'order','Order','other','2021-04-13 20:11:17','2021-04-13 20:11:17'),
      (1032,1,'Paragraph','Paragraph','other','2021-04-19 20:12:00','2021-04-19 20:12:00'),
      (1033,1,'Paragraph List','Paragraph List','other','2021-04-19 20:12:00','2021-04-19 20:12:00'),
      (1034,1,'paragraph Add','Paragraph Add','other','2021-04-19 20:12:00','2021-04-19 20:12:00'),
      (1035,1,'Sections','Sections','other','2021-04-19 20:12:00','2021-04-19 20:12:00'),
      (1036,1,'Section List','Section List','other','2021-04-19 20:12:00','2021-04-19 20:12:00'),
      (1037,1,'Section Add','Section Add','other','2021-04-19 20:12:00','2021-04-19 20:12:00'),
      (1038,1,'Upload File Type','Upload File Type','other','2021-04-19 20:12:42','2021-04-19 20:12:42'),
      (1039,1,'Image','Image','other','2021-04-19 20:12:42','2021-04-19 20:12:42'),
      (1040,1,'Audio','Audio','other','2021-04-19 20:12:42','2021-04-19 20:12:42'),
      (1041,1,'external_video_link','External video link','other','2021-04-19 20:12:42','2021-04-19 20:12:42'),
      (1042,1,'external_audio_link','External audio link','other','2021-04-19 20:12:42','2021-04-19 20:12:42'),
      (1043,1,'Questions Help Paragraph','Questions Help Paragraph','other','2021-04-19 20:12:42','2021-04-19 20:12:42'),
      (1044,1,'Questions Section','Questions Section','other','2021-04-19 20:12:42','2021-04-19 20:12:42'),
      (1045,1,'Is Match Type','Is Match Type','other','2021-04-19 20:12:42','2021-04-19 20:12:42'),
      (1046,1,'Add Paragraph','Add Paragraph','other','2021-04-20 08:16:15','2021-04-20 08:16:15'),
      (1047,1,'No','No','other','2021-04-20 08:16:15','2021-04-20 08:16:15'),
      (1048,1,'Title','Title','other','2021-04-20 08:16:15','2021-04-20 08:16:15'),
      (1049,1,'Sort Order','Sort Order','other','2021-04-20 08:16:15','2021-04-20 08:16:15'),
      (1050,1,'Action','Action','other','2021-04-20 08:16:15','2021-04-20 08:16:15'),
      (1051,1,'Add Section','Add Section','other','2021-04-20 08:16:39','2021-04-20 08:16:39'),
      (1052,1,'ParagraphTitle','ParagraphTitle','other','2021-04-20 08:16:48','2021-04-20 08:16:48'),
      (1053,1,'Paragraph Content','Paragraph Content','other','2021-04-20 08:16:48','2021-04-20 08:16:48'),
      (1054,1,'Paragraph Order','Paragraph Order','other','2021-04-20 08:16:48','2021-04-20 08:16:48'),
      (1055,1,'paragraph order','Paragraph order','other','2021-04-20 08:16:48','2021-04-20 08:16:48'),
      (1056,1,'Section Title','Section Title','other','2021-04-20 08:16:49','2021-04-20 08:16:49'),
      (1057,1,'Update Paragraph','Update Paragraph','other','2021-04-20 08:17:03','2021-04-20 08:17:03'),
      (1058,1,'Update Secton','Update Secton','other','2021-04-20 08:17:13','2021-04-20 08:17:13'),
      (1059,1,'level_name','Level name','other','2021-04-20 09:07:34','2021-04-20 09:07:34'),
      (1060,1,'levael_marks','Levael marks','other','2021-04-20 09:07:34','2021-04-20 09:07:34'),
      (1061,1,'images','Images','other','2021-04-22 15:55:57','2021-04-22 15:55:57'),
      (1062,1,'images','Images','other','2021-04-22 15:55:57','2021-04-22 15:55:57'),
      (1063,1,'images','Images','other','2021-04-22 15:55:57','2021-04-22 15:55:57'),
      (1064,1,'images','Images','other','2021-04-22 15:55:57','2021-04-22 15:55:57'),
      (1065,1,'images','Images','other','2021-04-22 15:55:57','2021-04-22 15:55:57'),
      (1066,1,'images','Images','other','2021-04-22 15:55:57','2021-04-22 15:55:57'),
      (1067,1,'images','Images','other','2021-04-22 15:55:57','2021-04-22 15:55:57'),
      (1068,1,'images','Images','other','2021-04-22 15:55:57','2021-04-22 15:55:57'),
      (1069,1,'display setting','Display setting','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1070,1,'study_material_manage_button_on_box','Study material manage button on box','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1071,1,'study_material_manage_button_on_box','Study material manage button on box','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1072,1,'study_material_social_share_icons','Study material social share icons','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1073,1,'study_material_social_share_icons','Study material social share icons','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1074,1,'study_material_display_stats_icons','Study material display stats icons','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1075,1,'study_material_display_stats_icons','Study material display stats icons','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1076,1,'study_material_display_social_share_icons','Study material display social share icons','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1077,1,'study_material_display_social_share_icons','Study material display social share icons','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1078,1,'study_material_box_contant_first_icons','Study material box contant first icons','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1079,1,'study_material_box_contant_first_icons','Study material box contant first icons','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1080,1,'study_material_box_contant_second_icons','Study material box contant second icons','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1081,1,'study_material_box_contant_second_icons','Study material box contant second icons','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1082,1,'study_material_box_contant_thrird_icons','Study material box contant thrird icons','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1083,1,'study_material_box_contant_thrird_icons','Study material box contant thrird icons','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1084,1,'quiz_display_stats_icons','Quiz display stats icons','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1085,1,'quiz_display_stats_icons','Quiz display stats icons','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1086,1,'quiz_display_social_share_icons','Quiz display social share icons','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1087,1,'quiz_display_social_share_icons','Quiz display social share icons','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1088,1,'ads_display_as','Ads display as','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1089,1,'ads_display_as','Ads display as','other','2021-04-22 17:43:32','2021-04-22 17:43:32'),
      (1090,1,'is_goole_adsense','Is goole adsense','other','2021-04-22 17:43:50','2021-04-22 17:43:50'),
      (1091,1,'google_ad_code','Google ad code','other','2021-04-22 17:43:50','2021-04-22 17:43:50'),
      (1092,1,'other','Other','other','2021-04-22 18:14:06','2021-04-22 18:14:06'),
      (1093,1,'other','Other','other','2021-04-22 18:14:06','2021-04-22 18:14:06'),
      (1094,1,'other','Other','other','2021-04-22 18:14:06','2021-04-22 18:14:06'),
      (1095,1,'other','Other','other','2021-04-22 18:14:06','2021-04-22 18:14:06'),
      (1096,1,'study_material','Study material','other','2021-04-29 07:48:06','2021-04-29 07:48:06'),
      (1097,1,'user_role','User role','other','2021-04-29 07:48:18','2021-04-29 07:48:18'),
      (1098,1,'admin','Admin','other','2021-04-29 07:48:18','2021-04-29 07:48:18'),
      (1099,1,'user','User','other','2021-04-29 07:48:18','2021-04-29 07:48:18'),
      (1100,1,'user','User','other','2021-04-29 07:48:18','2021-04-29 07:48:18'),
      (1101,1,'you_dont_have_permission_to_access_this_page','You dont have permission to access this page','other','2021-04-29 08:13:10','2021-04-29 08:13:10'),
      (1102,1,'preview','Preview','other','2021-04-29 11:18:54','2021-04-29 11:18:54'),
      (1103,1,'admin input items_per_page','Admin input items per page','other','2021-04-29 12:26:18','2021-04-29 12:26:18'),
      (1104,1,'invalid_uri_arguments_or_study_material_has_no_contant','Invalid uri arguments or study material has no contant','other','2021-04-29 12:57:19','2021-04-29 12:57:19'),
      (1105,1,'Paragraph','Paragraph','other','2021-05-01 09:34:10','2021-05-01 09:34:10'),
      (1106,1,'Paragraph List','Paragraph List','other','2021-05-01 09:34:10','2021-05-01 09:34:10'),
      (1107,1,'paragraph Add','Paragraph Add','other','2021-05-01 09:34:10','2021-05-01 09:34:10'),
      (1108,1,'Sections','Sections','other','2021-05-01 09:34:10','2021-05-01 09:34:10'),
      (1109,1,'Section List','Section List','other','2021-05-01 09:34:10','2021-05-01 09:34:10'),
      (1110,1,'Section Add','Section Add','other','2021-05-01 09:34:10','2021-05-01 09:34:10'),
      (1111,1,'images','Images','other','2021-05-04 07:24:30','2021-05-04 07:24:30'),
      (1112,1,'images','Images','other','2021-05-04 07:24:30','2021-05-04 07:24:30'),
      (1113,1,'images','Images','other','2021-05-04 07:24:30','2021-05-04 07:24:30'),
      (1114,1,'images','Images','other','2021-05-04 07:24:30','2021-05-04 07:24:30'),
      (1115,1,'images','Images','other','2021-05-04 07:24:30','2021-05-04 07:24:30'),
      (1116,1,'images','Images','other','2021-05-04 07:24:30','2021-05-04 07:24:30'),
      (1117,1,'images','Images','other','2021-05-04 07:24:30','2021-05-04 07:24:30'),
      (1118,1,'images','Images','other','2021-05-04 07:24:30','2021-05-04 07:24:30'),
      (1119,1,'display setting','Display setting','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1120,1,'study_material_manage_button_on_box','Study material manage button on box','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1121,1,'study_material_manage_button_on_box','Study material manage button on box','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1122,1,'study_material_social_share_icons','Study material social share icons','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1123,1,'study_material_social_share_icons','Study material social share icons','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1124,1,'study_material_display_stats_icons','Study material display stats icons','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1125,1,'study_material_display_stats_icons','Study material display stats icons','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1126,1,'study_material_display_social_share_icons','Study material display social share icons','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1127,1,'study_material_display_social_share_icons','Study material display social share icons','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1128,1,'study_material_box_contant_first_icons','Study material box contant first icons','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1129,1,'study_material_box_contant_first_icons','Study material box contant first icons','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1130,1,'study_material_box_contant_second_icons','Study material box contant second icons','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1131,1,'study_material_box_contant_second_icons','Study material box contant second icons','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1132,1,'study_material_box_contant_thrird_icons','Study material box contant thrird icons','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1133,1,'study_material_box_contant_thrird_icons','Study material box contant thrird icons','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1134,1,'quiz_display_stats_icons','Quiz display stats icons','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1135,1,'quiz_display_stats_icons','Quiz display stats icons','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1136,1,'quiz_display_social_share_icons','Quiz display social share icons','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1137,1,'quiz_display_social_share_icons','Quiz display social share icons','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1138,1,'ads_display_as','Ads display as','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1139,1,'ads_display_as','Ads display as','other','2021-05-04 07:31:50','2021-05-04 07:31:50'),
      (1140,1,'user_role','User role','other','2021-05-04 07:31:54','2021-05-04 07:31:54'),
      (1141,1,'admin','Admin','other','2021-05-04 07:31:54','2021-05-04 07:31:54'),
      (1142,1,'user','User','other','2021-05-04 07:31:54','2021-05-04 07:31:54'),
      (1143,1,'user','User','other','2021-05-04 07:31:54','2021-05-04 07:31:54'),
      (1144,1,'admin','Admin','other','2021-05-04 07:31:54','2021-05-04 07:31:54'),
      (1145,1,'admin','Admin','other','2021-05-04 07:31:54','2021-05-04 07:31:54'),
      (1146,1,'view_index','View index','other','2021-05-04 16:32:11','2021-05-04 16:32:11'),
      (1147,1,'view_index','View index','other','2021-05-04 16:32:11','2021-05-04 16:32:11'),
      (1148,1,'view_index','View index','other','2021-05-04 16:32:11','2021-05-04 16:32:11'),
      (1149,1,'view_index','View index','other','2021-05-04 16:32:11','2021-05-04 16:32:11'),
      (1150,1,'view_index','View index','other','2021-05-04 16:32:11','2021-05-04 16:32:11'),
      (1151,1,'view_index','View index','other','2021-05-04 16:32:11','2021-05-04 16:32:11'),
      (1152,1,'view_index','View index','other','2021-05-04 16:32:11','2021-05-04 16:32:11'),
      (1153,1,'view_index','View index','other','2021-05-04 16:32:11','2021-05-04 16:32:11'),
      (1154,1,'import','Import','other','2021-05-04 16:33:27','2021-05-04 16:33:27'),
      (1155,1,'select_study_material_contant_type','Select study material contant type','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1156,1,'Select Study Material Section','Select Study Material Section','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1157,1,'Select Study Material','Select Study Material','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1158,1,'Select Study Material','Select Study Material','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1159,1,'Select Study Material','Select Study Material','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1160,1,'Select Study Material','Select Study Material','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1161,1,'Select Study Material','Select Study Material','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1162,1,'Select Study Material','Select Study Material','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1163,1,'Select Study Material','Select Study Material','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1164,1,'Select Study Material','Select Study Material','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1165,1,'Select Study Material','Select Study Material','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1166,1,'Select Study Material','Select Study Material','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1167,1,'Select Study Material','Select Study Material','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1168,1,'Select Study Material','Select Study Material','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1169,1,'Select Study Material','Select Study Material','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1170,1,'Select Study Material','Select Study Material','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1171,1,'Select Study Material','Select Study Material','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1172,1,'Select Study Material','Select Study Material','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1173,1,'Select Study Material','Select Study Material','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1174,1,'import_study_data','Import study data','other','2021-05-04 16:33:30','2021-05-04 16:33:30'),
      (1175,1,'set_default_theme_in_dark_mode','Set default theme in dark mode','other','2021-05-04 16:40:30','2021-05-04 16:40:30'),
      (1176,1,'set_default_theme_in_dark_mode','Set default theme in dark mode','other','2021-05-04 16:40:30','2021-05-04 16:40:30'),
      (1177,1,'study_material_view_index_button_on_box','Study material view index button on box','other','2021-05-04 16:40:30','2021-05-04 16:40:30'),
      (1178,1,'study_material_view_index_button_on_box','Study material view index button on box','other','2021-05-04 16:40:30','2021-05-04 16:40:30'),
      (1179,1,'tutor_can_see_user_list','Tutor can see user list','other','2021-05-04 16:40:30','2021-05-04 16:40:30'),
      (1180,1,'tutor_can_see_user_list','Tutor can see user list','other','2021-05-04 16:40:30','2021-05-04 16:40:30'),
      (1181,1,'subadmin','Subadmin','other','2021-05-04 16:41:00','2021-05-04 16:41:00'),
      (1182,1,'study_material','Study material','other','2021-05-12 23:17:16','2021-05-12 23:17:16'),
      (1183,1,'Invalid Uri Arguments...!','Invalid Uri Arguments...!','other','2021-05-15 11:40:16','2021-05-15 11:40:16'),
      (1184,1,'course','Course','other','2021-05-17 19:56:47','2021-05-17 19:56:47'),
      (1185,1,'course','Course','other','2021-05-17 19:56:47','2021-05-17 19:56:47'),
      (1186,1,'db_backup','Db backup','other','2021-05-17 19:57:00','2021-05-17 19:57:00'),
      (1187,1,'institution','Institution','other','2021-05-17 19:57:00','2021-05-17 19:57:00'),
      (1188,1,'institution_list','Institution list','other','2021-05-17 19:57:00','2021-05-17 19:57:00'),
      (1189,1,'add_institution','Add institution','other','2021-05-17 19:57:00','2021-05-17 19:57:00'),
      (1190,1,'Course','Course','other','2021-05-17 19:57:00','2021-05-17 19:57:00'),
      (1191,1,'course_list','Course list','other','2021-05-17 19:57:00','2021-05-17 19:57:00'),
      (1192,1,'add_course','Add course','other','2021-05-17 19:57:00','2021-05-17 19:57:00'),
      (1193,1,'backup_now','Backup now','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1194,1,'File Name','File Name','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1195,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1196,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1197,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1198,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1199,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1200,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1201,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1202,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1203,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1204,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1205,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1206,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1207,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1208,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1209,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1210,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1211,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1212,1,'download','Download','other','2021-05-17 19:57:06','2021-05-17 19:57:06'),
      (1213,1,'db_backup_successfuly...!','Db backup successfuly...!','other','2021-05-17 19:57:11','2021-05-17 19:57:11'),
      (1214,1,'select_user_user_to_assign_contant','Select user user to assign contant','other','2021-05-17 19:57:27','2021-05-17 19:57:27'),
      (1215,1,'select_user_to_assign_existing_contant','Select user to assign existing contant','other','2021-05-17 19:57:27','2021-05-17 19:57:27'),
      (1216,1,'select_user_user_to_assign_contant','Select user user to assign contant','other','2021-05-17 19:57:27','2021-05-17 19:57:27'),
      (1217,1,'select_user_to_assign_existing_contant','Select user to assign existing contant','other','2021-05-17 19:57:27','2021-05-17 19:57:27'),
      (1218,1,'select_user_user_to_assign_contant','Select user user to assign contant','other','2021-05-17 19:57:27','2021-05-17 19:57:27'),
      (1219,1,'select_user_to_assign_existing_contant','Select user to assign existing contant','other','2021-05-17 19:57:27','2021-05-17 19:57:27'),
      (1220,1,'select_user_user_to_assign_contant','Select user user to assign contant','other','2021-05-17 19:57:27','2021-05-17 19:57:27'),
      (1221,1,'select_user_to_assign_existing_contant','Select user to assign existing contant','other','2021-05-17 19:57:27','2021-05-17 19:57:27'),
      (1222,1,'select_user_user_to_assign_contant','Select user user to assign contant','other','2021-05-17 19:57:27','2021-05-17 19:57:27'),
      (1223,1,'select_user_to_assign_existing_contant','Select user to assign existing contant','other','2021-05-17 19:57:27','2021-05-17 19:57:27'),
      (1224,1,'select_user_user_to_assign_contant','Select user user to assign contant','other','2021-05-17 19:57:27','2021-05-17 19:57:27'),
      (1225,1,'select_user_to_assign_existing_contant','Select user to assign existing contant','other','2021-05-17 19:57:27','2021-05-17 19:57:27'),
      (1226,1,'select_user_user_to_assign_contant','Select user user to assign contant','other','2021-05-17 19:57:27','2021-05-17 19:57:27'),
      (1227,1,'select_user_to_assign_existing_contant','Select user to assign existing contant','other','2021-05-17 19:57:27','2021-05-17 19:57:27'),
      (1228,1,'select_user_user_to_assign_contant','Select user user to assign contant','other','2021-05-17 19:57:27','2021-05-17 19:57:27'),
      (1229,1,'select_user_to_assign_existing_contant','Select user to assign existing contant','other','2021-05-17 19:57:27','2021-05-17 19:57:27'),
      (1230,1,'you_dont_have_permission_to_access_this_url','You dont have permission to access this url','other','2021-05-17 23:13:06','2021-05-17 23:13:06'),
      (1231,1,'add institution','Add institution','other','2021-05-17 23:14:34','2021-05-17 23:14:34'),
      (1232,1,'address','Address','other','2021-05-17 23:16:57','2021-05-17 23:16:57'),
      (1233,1,'instute_courses','Instute courses','other','2021-05-17 23:16:57','2021-05-17 23:16:57'),
      (1234,1,'instute_courses','Instute courses','other','2021-05-17 23:16:57','2021-05-17 23:16:57'),
      (1235,1,'address','Address','other','2021-05-17 23:16:57','2021-05-17 23:16:57'),
      (1236,1,'update_institution','Update institution','other','2021-05-17 23:17:13','2021-05-17 23:17:13'),
      (1237,1,'course_update','Course update','other','2021-05-17 23:17:25','2021-05-17 23:17:25'),
      (1238,1,'select_course','Select course','other','2021-05-19 09:13:51','2021-05-19 09:13:51'),
      (1239,1,'select_institution','Select institution','other','2021-05-19 09:13:51','2021-05-19 09:13:51'),
      (1240,1,'institutions','Institutions','other','2021-05-19 09:13:51','2021-05-19 09:13:51'),
      (1241,1,'backups','Backups','other','2021-05-19 09:14:09','2021-05-19 09:14:09'),
      (1242,1,'negative_marking_percantage','Negative marking percantage','other','2021-05-19 09:14:30','2021-05-19 09:14:30'),
      (1243,1,'percentage_lost_by_negative_marking','Percentage lost by negative marking','other','2021-05-19 09:14:30','2021-05-19 09:14:30'),
      (1244,1,'quizzy_reward_animation_image','Quizzy reward animation image','other','2021-05-19 09:14:41','2021-05-19 09:14:41'),
      (1245,1,'quizzy_reward_animation_image','Quizzy reward animation image','other','2021-05-19 09:14:41','2021-05-19 09:14:41'),
      (1246,1,'course_title','Course title','other','2021-05-19 09:15:32','2021-05-19 09:15:32'),
      (1247,1,'delete_all_related_users','Delete all related users','other','2021-05-19 11:54:06','2021-05-19 11:54:06'),
      (1248,1,'course_backups','Course backups','other','2021-05-19 11:54:19','2021-05-19 11:54:19'),
      (1249,1,'quiz_paragraphs','Quiz paragraphs','other','2021-05-19 15:13:58','2021-05-19 15:13:58'),
      (1250,1,'sections','Sections','other','2021-05-19 15:13:58','2021-05-19 15:13:58'),
      (1251,1,'users','Users','other','2021-05-19 15:13:58','2021-05-19 15:13:58'),
      (1252,1,'CMS','CMS','other','2021-05-19 15:13:58','2021-05-19 15:13:58'),
      (1253,1,'pages','Pages','other','2021-05-19 15:13:58','2021-05-19 15:13:58'),
      (1254,1,'blog_category','Blog category','other','2021-05-19 15:13:58','2021-05-19 15:13:58'),
      (1255,1,'blog_post','Blog post','other','2021-05-19 15:13:58','2021-05-19 15:13:58'),
      (1256,1,'front_menu_items','Front menu items','other','2021-05-19 15:13:58','2021-05-19 15:13:58'),
      (1257,1,'testimonials','Testimonials','other','2021-05-19 15:13:58','2021-05-19 15:13:58'),
      (1258,1,'SEO/Marketing','SEO/Marketing','other','2021-05-19 15:13:58','2021-05-19 15:13:58'),
      (1259,1,'sponsors','Sponsors','other','2021-05-19 15:13:58','2021-05-19 15:13:58'),
      (1260,1,'courses','Courses','other','2021-05-19 15:13:58','2021-05-19 15:13:58'),
      (1261,1,'reward_percentage','Reward percentage','other','2021-05-19 15:15:23','2021-05-19 15:15:23'),
      (1262,1,'negative_marking_percentage','Negative marking percentage','other','2021-05-19 15:15:23','2021-05-19 15:15:23'),
      (1263,1,'allowed_attempt_limit','Allowed attempt limit','other','2021-05-19 15:15:23','2021-05-19 15:15:23'),
      (1264,1,'db_backup_file_unlink_successfuly','Db backup file unlink successfuly','other','2021-05-19 18:29:47','2021-05-19 18:29:47'),
      (1265,1,'users_or_courses_backups','Users or courses backups','other','2021-05-19 19:01:44','2021-05-19 19:01:44'),
      (1266,1,'Please Choose One Correct Choices..! ','Please Choose One Correct Choices..! ','other','2021-05-20 15:22:12','2021-05-20 15:22:12'),
      (1267,1,'Cross Choice All Fields Are Required','Cross Choice All Fields Are Required','other','2021-05-20 15:22:12','2021-05-20 15:22:12'),
      (1268,1,'got','Got','other','2021-05-20 17:50:18','2021-05-20 17:50:18'),
      (1269,1,'marks_for_correct_answer','Marks for correct answer','other','2021-05-21 11:29:11','2021-05-21 11:29:11'),
      (1270,1,'select_negative_marking_percentage','Select negative marking percentage','other','2021-05-21 16:32:42','2021-05-21 16:32:42'),
      (1271,1,'invalid_reward_percentage','Invalid reward percentage','other','2021-05-21 16:52:50','2021-05-21 16:52:50'),
      (1272,1,'require_quiz_passing','Require quiz passing','other','2021-05-21 18:44:30','2021-05-21 18:44:30'),
      (1273,1,'select_passing_mark','Select passing mark','other','2021-05-21 18:44:30','2021-05-21 18:44:30'),
      (1274,1,'reward','Reward','other','2021-05-21 18:44:30','2021-05-21 18:44:30'),
      (1275,1,'select_reward','Select reward','other','2021-05-21 18:44:30','2021-05-21 18:44:30'),
      (1276,1,'negative_marking','Negative marking','other','2021-05-21 18:44:30','2021-05-21 18:44:30'),
      (1277,1,'select_negative_marking','Select negative marking','other','2021-05-21 18:44:30','2021-05-21 18:44:30'),
      (1278,1,'already_complete_section_contant','Already complete section contant','other','2021-05-22 12:09:59','2021-05-22 12:09:59'),
      (1279,1,'study_material','Study material','other','2021-05-25 16:51:59','2021-05-25 16:51:59'),
      (1280,1,'quiz_paragraphs','Quiz paragraphs','other','2021-05-25 16:51:59','2021-05-25 16:51:59'),
      (1281,1,'sections','Sections','other','2021-05-25 16:51:59','2021-05-25 16:51:59'),
      (1282,1,'images','Images','other','2021-05-25 16:52:04','2021-05-25 16:52:04'),
      (1283,1,'manage_study_contant','Manage study contant','other','2021-05-25 16:52:04','2021-05-25 16:52:04'),
      (1284,1,'images','Images','other','2021-05-25 16:52:04','2021-05-25 16:52:04'),
      (1285,1,'manage_study_contant','Manage study contant','other','2021-05-25 16:52:04','2021-05-25 16:52:04'),
      (1286,1,'images','Images','other','2021-05-25 16:52:04','2021-05-25 16:52:04'),
      (1287,1,'manage_study_contant','Manage study contant','other','2021-05-25 16:52:04','2021-05-25 16:52:04'),
      (1288,1,'images','Images','other','2021-05-25 16:52:04','2021-05-25 16:52:04'),
      (1289,1,'manage_study_contant','Manage study contant','other','2021-05-25 16:52:04','2021-05-25 16:52:04'),
      (1290,1,'images','Images','other','2021-05-25 16:52:04','2021-05-25 16:52:04'),
      (1291,1,'manage_study_contant','Manage study contant','other','2021-05-25 16:52:04','2021-05-25 16:52:04'),
      (1292,1,'images','Images','other','2021-05-25 16:52:04','2021-05-25 16:52:04'),
      (1293,1,'manage_study_contant','Manage study contant','other','2021-05-25 16:52:04','2021-05-25 16:52:04'),
      (1294,1,'images','Images','other','2021-05-25 16:52:04','2021-05-25 16:52:04'),
      (1295,1,'manage_study_contant','Manage study contant','other','2021-05-25 16:52:04','2021-05-25 16:52:04'),
      (1296,1,'images','Images','other','2021-05-25 16:52:04','2021-05-25 16:52:04'),
      (1297,1,'manage_study_contant','Manage study contant','other','2021-05-25 16:52:04','2021-05-25 16:52:04'),
      (1298,1,'select_course','Select course','other','2021-05-25 16:53:41','2021-05-25 16:53:41'),
      (1299,1,'select_institution','Select institution','other','2021-05-25 16:53:41','2021-05-25 16:53:41'),
      (1300,1,'other','Other','other','2021-05-25 16:53:41','2021-05-25 16:53:41'),
      (1301,1,'other','Other','other','2021-05-25 16:53:41','2021-05-25 16:53:41'),
      (1302,1,'institutions','Institutions','other','2021-05-25 16:53:41','2021-05-25 16:53:41'),
      (1303,1,'course','Course','other','2021-05-25 16:53:41','2021-05-25 16:53:41'),
      (1304,1,'User_registration_per_month','User registration per month','other','2021-06-11 18:15:56','2021-06-11 18:15:56'),
      (1305,1,'payment_per_month','Payment per month','other','2021-06-11 18:15:56','2021-06-11 18:15:56'),
      (1306,1,'New contents: study materials/quizzes','New contents: study materials/quizzes','other','2021-06-11 18:15:56','2021-06-11 18:15:56'),
      (1307,1,'media','Media','other','2021-06-11 18:15:56','2021-06-11 18:15:56'),
      (1308,1,'upload_media','Upload media','other','2021-06-11 18:16:06','2021-06-11 18:16:06'),
      (1309,1,'user_qualification_and_experience','User qualification and experience','other','2021-06-13 11:03:30','2021-06-13 11:03:30'),
      (1310,1,'passing_mark','Passing mark','other','2021-06-13 11:06:15','2021-06-13 11:06:15'),
      (1311,1,'hide_answesr_on_result','Hide answesr on result','other','2021-06-13 11:06:15','2021-06-13 11:06:15'),
      (1312,1,'select_category_for_membership','Select category for membership','other','2021-06-14 12:25:03','2021-06-14 12:25:03'),
      (1313,1,'media_file','Media file','other','2021-06-14 19:45:14','2021-06-14 19:45:14'),
      (1314,1,'upload_image','Upload image','other','2021-06-14 19:45:14','2021-06-14 19:45:14'),
      (1315,1,'cookies content','Cookies content','other','2021-06-16 14:42:24','2021-06-16 14:42:24'),
      (1316,1,'cookies_content_display','Cookies content display','other','2021-06-16 14:42:24','2021-06-16 14:42:24'),
      (1317,1,'cookies_content_display','Cookies content display','other','2021-06-16 14:42:24','2021-06-16 14:42:24'),
      (1318,1,'cookies_content','Cookies content','other','2021-06-16 14:42:24','2021-06-16 14:42:24'),
      (1319,1,'cookies_content','Cookies content','other','2021-06-16 14:42:24','2021-06-16 14:42:24'),
      (1320,1,'cookies_content_btn_text','Cookies content btn text','other','2021-06-16 14:42:24','2021-06-16 14:42:24'),
      (1321,1,'cookies_content_btn_text','Cookies content btn text','other','2021-06-16 14:42:24','2021-06-16 14:42:24'),
      (1322,1,'upload_file','Upload file','other','2021-06-29 01:29:15','2021-06-29 01:29:15'),
      (1323,1,'upload_file','Upload file','other','2021-06-29 01:29:15','2021-06-29 01:29:15'),
      (1324,1,'allowed_file_type','Allowed file type','other','2021-06-29 01:29:15','2021-06-29 01:29:15'),
      (1325,1,'Check for update','Check for update','other','2021-07-01 18:28:20','2021-07-01 18:28:20'),
      (1326,1,'Token is not match','Token is not match','other','2021-07-01 18:29:31','2021-07-01 18:29:31'),
      (1327,1,'Next version is available','Next version is available','other','2021-07-01 18:45:37','2021-07-01 18:45:37'),
      (1328,1,'url dose not match','Url dose not match','other','2021-07-02 14:12:06','2021-07-02 14:12:06'),
      (1329,1,NULL,'','other','2021-07-02 15:04:31','2021-07-02 15:04:31'),
      (1330,1,'4.0.0 Data Placed Successfully','4.0.0 Data Placed Successfully','other','2021-07-02 15:13:42','2021-07-02 15:13:42'),
      (1331,1,'update already send to this domain','Update already send to this domain','other','2021-07-05 19:24:34','2021-07-05 19:24:34'),
      (1332,1,'coupons','Coupons','other','2021-07-07 17:06:55','2021-07-07 17:06:55'),
      (1333,1,'coupon_list','Coupon list','other','2021-07-07 17:06:55','2021-07-07 17:06:55'),
      (1334,1,'add_coupon','Add coupon','other','2021-07-07 17:07:05','2021-07-07 17:07:05'),
      (1335,1,'coupon_code','Coupon code','other','2021-07-07 17:07:05','2021-07-07 17:07:05'),
      (1336,1,'coupon_for','Coupon for','other','2021-07-07 17:07:05','2021-07-07 17:07:05'),
      (1337,1,'discount_type','Discount type','other','2021-07-07 17:07:05','2021-07-07 17:07:05'),
      (1338,1,'expiry_date','Expiry date','other','2021-07-07 17:07:05','2021-07-07 17:07:05'),
      (1339,1,'category_id','Category id','other','2021-07-07 17:07:13','2021-07-07 17:07:13'),
      (1340,1,'no_time_used','No time used','other','2021-07-07 17:07:13','2021-07-07 17:07:13'),
      (1341,1,'discount_value','Discount value','other','2021-07-07 17:07:13','2021-07-07 17:07:13'),
      (1342,1,'category','Category','other','2021-07-07 17:07:13','2021-07-07 17:07:13'),
      (1343,1,'No of times can be used','No of times can be used','other','2021-07-07 17:07:13','2021-07-07 17:07:13'),
      (1344,1,'Discount value','Discount value','other','2021-07-07 17:07:13','2021-07-07 17:07:13'),
      (1345,1,'Expiry Date','Expiry Date','other','2021-07-07 17:07:13','2021-07-07 17:07:13'),
      (1346,1,'edit_coupon','Edit coupon','other','2021-07-07 17:08:07','2021-07-07 17:08:07'),
      (1347,1,'apply_coupon','Apply coupon','other','2021-07-07 17:08:34','2021-07-07 17:08:34'),
      (1348,1,'city','City','other','2021-07-07 17:08:34','2021-07-07 17:08:34'),
      (1349,1,'state','State','other','2021-07-07 17:08:34','2021-07-07 17:08:34'),
      (1350,1,'country','Country','other','2021-07-07 17:08:34','2021-07-07 17:08:34'),
      (1351,1,'postal_code','Postal code','other','2021-07-07 17:08:34','2021-07-07 17:08:34'),
      (1352,1,'save','Save','other','2021-07-07 17:08:34','2021-07-07 17:08:34'),
      (1353,1,'The Coupon code field is required.','The Coupon code field is required.','other','2021-07-07 17:25:47','2021-07-07 17:25:47'),
      (1354,1,'coupon_code_not_exist','Coupon code not exist','other','2021-07-07 17:25:54','2021-07-07 17:25:54'),
      (1355,1,'category_not_exist_for_this_coupon','Category not exist for this coupon','other','2021-07-07 17:26:16','2021-07-07 17:26:16'),
      (1356,1,'associate_quiz','Associate quiz','other','2021-07-08 19:48:01','2021-07-08 19:48:01'),
      (1357,1,'Select Quiz','Select Quiz','other','2021-07-08 19:48:01','2021-07-08 19:48:01'),
      (1358,1,'section_or_content','Section or content','other','2021-07-08 19:48:12','2021-07-08 19:48:12'),
      (1359,1,'associate quiz','Associate quiz','other','2021-07-08 19:48:56','2021-07-08 19:48:56'),
      (1360,1,'only_images_allowed','Only images allowed','other','2021-07-08 22:59:40','2021-07-08 22:59:40'),
      (1361,1,'is_compatible','Is compatible','other','2021-07-09 19:57:35','2021-07-09 19:57:35'),
      (1362,1,'is_compatible_help','Is compatible help','other','2021-07-09 19:57:35','2021-07-09 19:57:35'),
      (1363,1,'render_content','Render content','other','2021-07-09 19:57:52','2021-07-09 19:57:52'),
      (1364,1,'Yes','Yes','other','2021-07-09 19:57:52','2021-07-09 19:57:52'),
      (1365,1,'users','Users','other','2021-07-12 19:25:07','2021-07-12 19:25:07'),
      (1366,1,'backups','Backups','other','2021-07-12 19:25:07','2021-07-12 19:25:07'),
      (1367,1,'CMS','CMS','other','2021-07-12 19:25:07','2021-07-12 19:25:07'),
      (1368,1,'pages','Pages','other','2021-07-12 19:25:07','2021-07-12 19:25:07'),
      (1369,1,'blog_category','Blog category','other','2021-07-12 19:25:07','2021-07-12 19:25:07'),
      (1370,1,'blog_post','Blog post','other','2021-07-12 19:25:07','2021-07-12 19:25:07'),
      (1371,1,'front_menu_items','Front menu items','other','2021-07-12 19:25:07','2021-07-12 19:25:07'),
      (1372,1,'testimonials','Testimonials','other','2021-07-12 19:25:07','2021-07-12 19:25:07'),
      (1373,1,'SEO/Marketing','SEO/Marketing','other','2021-07-12 19:25:07','2021-07-12 19:25:07'),
      (1374,1,'sponsors','Sponsors','other','2021-07-12 19:25:07','2021-07-12 19:25:07'),
      (1375,1,'courses','Courses','other','2021-07-12 19:25:07','2021-07-12 19:25:07'),
      (1376,1,'db_backup','Db backup','other','2021-07-12 19:25:07','2021-07-12 19:25:07'),
      (1377,1,'select_courses','Select courses','other','2021-07-13 13:47:36','2021-07-13 13:47:36'),
      (1378,1,'display setting','Display setting','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1379,1,'quizzy_reward_animation_image','Quizzy reward animation image','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1380,1,'quizzy_reward_animation_image','Quizzy reward animation image','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1381,1,'set_default_theme_in_dark_mode','Set default theme in dark mode','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1382,1,'set_default_theme_in_dark_mode','Set default theme in dark mode','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1383,1,'study_material_view_index_button_on_box','Study material view index button on box','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1384,1,'study_material_view_index_button_on_box','Study material view index button on box','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1385,1,'study_material_manage_button_on_box','Study material manage button on box','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1386,1,'study_material_manage_button_on_box','Study material manage button on box','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1387,1,'tutor_can_see_user_list','Tutor can see user list','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1388,1,'tutor_can_see_user_list','Tutor can see user list','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1389,1,'study_material_social_share_icons','Study material social share icons','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1390,1,'study_material_social_share_icons','Study material social share icons','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1391,1,'study_material_display_stats_icons','Study material display stats icons','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1392,1,'study_material_display_stats_icons','Study material display stats icons','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1393,1,'study_material_display_social_share_icons','Study material display social share icons','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1394,1,'study_material_display_social_share_icons','Study material display social share icons','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1395,1,'study_material_box_contant_first_icons','Study material box contant first icons','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1396,1,'study_material_box_contant_first_icons','Study material box contant first icons','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1397,1,'study_material_box_contant_second_icons','Study material box contant second icons','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1398,1,'study_material_box_contant_second_icons','Study material box contant second icons','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1399,1,'study_material_box_contant_thrird_icons','Study material box contant thrird icons','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1400,1,'study_material_box_contant_thrird_icons','Study material box contant thrird icons','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1401,1,'quiz_display_stats_icons','Quiz display stats icons','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1402,1,'quiz_display_stats_icons','Quiz display stats icons','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1403,1,'quiz_display_social_share_icons','Quiz display social share icons','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1404,1,'quiz_display_social_share_icons','Quiz display social share icons','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1405,1,'ads_display_as','Ads display as','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1406,1,'ads_display_as','Ads display as','other','2021-07-17 00:49:44','2021-07-17 00:49:44'),
      (1407,1,'user_role','User role','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1408,1,'admin','Admin','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1409,1,'user','User','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1410,1,'admin','Admin','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1411,1,'subadmin','Subadmin','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1412,1,'user','User','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1413,1,'user','User','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1414,1,'subadmin','Subadmin','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1415,1,'user','User','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1416,1,'admin input items_per_page','Admin input items per page','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1417,1,'select_user_user_to_assign_contant','Select user user to assign contant','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1418,1,'select_user_to_assign_existing_contant','Select user to assign existing contant','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1419,1,'select_user_user_to_assign_contant','Select user user to assign contant','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1420,1,'select_user_to_assign_existing_contant','Select user to assign existing contant','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1421,1,'select_user_user_to_assign_contant','Select user user to assign contant','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1422,1,'select_user_to_assign_existing_contant','Select user to assign existing contant','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1423,1,'select_user_user_to_assign_contant','Select user user to assign contant','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1424,1,'select_user_to_assign_existing_contant','Select user to assign existing contant','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1425,1,'select_user_user_to_assign_contant','Select user user to assign contant','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1426,1,'select_user_to_assign_existing_contant','Select user to assign existing contant','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1427,1,'select_user_user_to_assign_contant','Select user user to assign contant','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1428,1,'select_user_to_assign_existing_contant','Select user to assign existing contant','other','2021-07-17 00:49:48','2021-07-17 00:49:48'),
      (1429,1,'Preview','Preview','other','2021-07-17 02:14:37','2021-07-17 02:14:37'),
      (1430,1,'Preview','Preview','other','2021-07-17 02:14:37','2021-07-17 02:14:37'),
      (1431,1,'Preview','Preview','other','2021-07-17 02:14:37','2021-07-17 02:14:37'),
      (1432,1,'Preview','Preview','other','2021-07-17 02:14:37','2021-07-17 02:14:37'),
      (1433,1,'Preview','Preview','other','2021-07-17 02:14:37','2021-07-17 02:14:37'),
      (1434,1,'Preview','Preview','other','2021-07-17 02:14:37','2021-07-17 02:14:37'),
      (1435,1,'Preview','Preview','other','2021-07-17 02:14:37','2021-07-17 02:14:37'),
      (1436,1,'Preview','Preview','other','2021-07-17 02:14:37','2021-07-17 02:14:37'),
      (1437,1,'Preview','Preview','other','2021-07-17 02:14:37','2021-07-17 02:14:37'),
      (1438,1,'Preview','Preview','other','2021-07-17 02:14:37','2021-07-17 02:14:37'),
      (1439,1,'import','Import','other','2021-07-17 02:23:36','2021-07-17 02:23:36'),
      (1440,1,'preview','Preview','other','2021-07-17 02:23:36','2021-07-17 02:23:36'),
      (1441,1,'select_study_material_contant_type','Select study material contant type','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1442,1,'Select Study Material Section','Select Study Material Section','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1443,1,'Select Study Material','Select Study Material','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1444,1,'Select Study Material','Select Study Material','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1445,1,'Select Study Material','Select Study Material','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1446,1,'Select Study Material','Select Study Material','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1447,1,'Select Study Material','Select Study Material','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1448,1,'Select Study Material','Select Study Material','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1449,1,'Select Study Material','Select Study Material','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1450,1,'Select Study Material','Select Study Material','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1451,1,'Select Study Material','Select Study Material','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1452,1,'Select Study Material','Select Study Material','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1453,1,'Select Study Material','Select Study Material','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1454,1,'Select Study Material','Select Study Material','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1455,1,'Select Study Material','Select Study Material','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1456,1,'Select Study Material','Select Study Material','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1457,1,'Select Study Material','Select Study Material','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1458,1,'Select Study Material','Select Study Material','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1459,1,'import_study_data','Import study data','other','2021-07-17 02:23:39','2021-07-17 02:23:39'),
      (1460,1,'No updates available','No updates available','other','2021-08-04 18:36:04','2021-08-04 18:36:04'),
      (1461,1,NULL,'','other','2021-08-04 20:23:17','2021-08-04 20:23:17'),
      (1462,1,NULL,'','other','2021-08-04 20:23:27','2021-08-04 20:23:27'),
      (1463,1,NULL,'','other','2021-08-04 20:25:41','2021-08-04 20:25:41'),
      (1464,1,NULL,'','other','2021-08-04 20:34:12','2021-08-04 20:34:12'),
      (1465,1,NULL,'','other','2021-08-04 20:34:17','2021-08-04 20:34:17'),
      (1466,1,NULL,'','other','2021-08-04 20:35:45','2021-08-04 20:35:45'),
      (1467,1,NULL,'','other','2021-08-04 20:36:18','2021-08-04 20:36:18'),
      (1468,1,NULL,'','other','2021-08-04 20:56:00','2021-08-04 20:56:00'),
      (1469,1,NULL,'','other','2021-08-04 20:56:59','2021-08-04 20:56:59'),
      (1470,1,NULL,'','other','2021-08-05 11:28:23','2021-08-05 11:28:23'),
      (1471,1,NULL,'','other','2021-08-05 11:29:04','2021-08-05 11:29:04'),
      (1472,1,NULL,'','other','2021-08-05 11:40:40','2021-08-05 11:40:40'),
      (1473,1,NULL,'','other','2021-08-05 11:41:46','2021-08-05 11:41:46'),
      (1474,1,NULL,'','other','2021-08-05 11:41:51','2021-08-05 11:41:51'),
      (1475,1,NULL,'','other','2021-08-05 11:48:16','2021-08-05 11:48:16'),
      (1476,1,NULL,'','other','2021-08-05 11:48:54','2021-08-05 11:48:54'),
      (1477,1,NULL,'','other','2021-08-05 11:55:24','2021-08-05 11:55:24'),
      (1478,1,'Error During Copy Zip Files! (single)','Error During Copy Zip Files! (single)','other','2021-08-05 13:47:53','2021-08-05 13:47:53'),
      (1479,1,'upcoming_quizes','Upcoming quizes','other','2021-08-05 14:57:30','2021-08-05 14:57:30'),
      (1480,1,'4.1.6 Data Placed Successfully','4.1.6 Data Placed Successfully','other','2021-08-05 15:09:31','2021-08-05 15:09:31'),
      (1481,1,'is_sticky_header','Is sticky header','other','2021-08-05 15:11:10','2021-08-05 15:11:10'),
      (1482,1,'is_sticky_header_help','Is sticky header help','other','2021-08-05 15:11:10','2021-08-05 15:11:10'),
      (1483,1,'display_countdown_before_starting_quiz','Display countdown before starting quiz','other','2021-08-05 15:11:10','2021-08-05 15:11:10'),
      (1484,1,'enter_only_number','Enter only number','other','2021-08-05 15:11:10','2021-08-05 15:11:10'),
      (1485,1,'file_uploader','File uploader','other','2021-08-05 15:11:10','2021-08-05 15:11:10'),
      (1486,1,'file_uploader_help','File uploader help','other','2021-08-05 15:11:10','2021-08-05 15:11:10'),
      (1487,1,'upcoming_quiz_show_on_home','Upcoming quiz show on home','other','2021-08-05 15:11:10','2021-08-05 15:11:10'),
      (1488,1,'upcoming_quiz_show_on_home_help','Upcoming quiz show on home help','other','2021-08-05 15:11:10','2021-08-05 15:11:10'),
      (1489,1,'4.1.7 Data Placed Successfully','4.1.7 Data Placed Successfully','other','2021-08-05 20:31:10','2021-08-05 20:31:10'),
      (1490,1,'4.2.0 Data Placed Successfully','4.2.0 Data Placed Successfully','other','2021-08-05 20:31:34','2021-08-05 20:31:34'),
      (1491,1,'Next Version Not Available','Next Version Not Available','other','2021-08-05 20:31:58','2021-08-05 20:31:58'),
      (1492,1,'footer_text_help','Footer text help','other','2021-08-06 19:43:37','2021-08-06 19:43:37'),
      (1493,1,'its_may_be_permission_issue_or_something_went_wrong','Its may be permission issue or something went wrong','other','2021-08-07 06:52:50','2021-08-07 06:52:50'),
      (1494,1,'updates_details','Updates details','other','2021-08-11 03:15:08','2021-08-11 03:15:08'),
      (1495,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1496,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1497,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1498,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1499,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1500,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1501,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1502,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1503,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1504,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1505,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1506,1,'language','Language','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1507,1,'user_qualification','User qualification','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1508,1,'additional_data','Additional data','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1509,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1510,1,'language','Language','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1511,1,'user_qualification','User qualification','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1512,1,'additional_data','Additional data','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1513,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1514,1,'language','Language','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1515,1,'user_qualification','User qualification','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1516,1,'additional_data','Additional data','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1517,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1518,1,'language','Language','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1519,1,'user_qualification','User qualification','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1520,1,'additional_data','Additional data','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1521,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1522,1,'language','Language','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1523,1,'user_qualification','User qualification','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1524,1,'additional_data','Additional data','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1525,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1526,1,'language','Language','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1527,1,'user_qualification','User qualification','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1528,1,'additional_data','Additional data','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1529,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1530,1,'language','Language','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1531,1,'user_qualification','User qualification','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1532,1,'additional_data','Additional data','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1533,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1534,1,'language','Language','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1535,1,'user_qualification','User qualification','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1536,1,'additional_data','Additional data','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1537,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1538,1,'language','Language','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1539,1,'user_qualification','User qualification','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1540,1,'additional_data','Additional data','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1541,1,'user_detail','User detail','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1542,1,'language','Language','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1543,1,'user_qualification','User qualification','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1544,1,'additional_data','Additional data','other','2021-08-16 07:43:01','2021-08-16 07:43:01'),
      (1545,1,'is_disable_result','Is disable result','other','2021-08-19 17:17:37','2021-08-19 17:17:37'),
      (1546,1,'report_question','Report question','other','2021-08-20 13:36:00','2021-08-20 13:36:00'),
      (1547,1,'pages_list','Pages list','other','2021-08-20 13:36:10','2021-08-20 13:36:10'),
      (1548,1,'pages_list_help','Pages list help','other','2021-08-20 13:36:10','2021-08-20 13:36:10'),
      (1549,1,'helpsheet','Helpsheet','other','2021-08-20 13:36:50','2021-08-20 13:36:50'),
      (1550,1,'helpsheet','Helpsheet','other','2021-08-20 13:36:50','2021-08-20 13:36:50'),
      (1551,1,'report_question_problem','Report question problem','other','2021-08-20 13:36:50','2021-08-20 13:36:50'),
      (1552,1,'i_think_the_answer_is_wrong','I think the answer is wrong','other','2021-08-20 13:36:50','2021-08-20 13:36:50'),
      (1553,1,'i_think_the_answer_is_wrong','I think the answer is wrong','other','2021-08-20 13:36:50','2021-08-20 13:36:50'),
      (1554,1,'there_are_multiple_correct_answers','There are multiple correct answers','other','2021-08-20 13:36:50','2021-08-20 13:36:50'),
      (1555,1,'there_are_multiple_correct_answers','There are multiple correct answers','other','2021-08-20 13:36:50','2021-08-20 13:36:50'),
      (1556,1,'there_is_a_typing_error','There is a typing error','other','2021-08-20 13:36:50','2021-08-20 13:36:50'),
      (1557,1,'there_is_a_typing_error','There is a typing error','other','2021-08-20 13:36:50','2021-08-20 13:36:50'),
      (1558,1,'image_or_comprehension_text_is_not_display_properly','Image or comprehension text is not display properly','other','2021-08-20 13:36:50','2021-08-20 13:36:50'),
      (1559,1,'image_or_comprehension_text_is_not_display_properly','Image or comprehension text is not display properly','other','2021-08-20 13:36:50','2021-08-20 13:36:50'),
      (1560,1,'something_else','Something else','other','2021-08-20 13:36:50','2021-08-20 13:36:50'),
      (1561,1,'something_else','Something else','other','2021-08-20 13:36:50','2021-08-20 13:36:50'),
      (1562,1,'any_more_information','Any more information','other','2021-08-20 13:36:50','2021-08-20 13:36:50'),
      (1563,1,'send_report','Send report','other','2021-08-20 13:36:50','2021-08-20 13:36:50'),
      (1564,1,'your_reort_problem_send_to_administration','Your reort problem send to administration','other','2021-08-20 13:37:16','2021-08-20 13:37:16'),
      (1565,1,'admin_report_question','Admin report question','other','2021-08-20 13:37:25','2021-08-20 13:37:25'),
      (1566,1,'user_name','User name','other','2021-08-20 13:37:25','2021-08-20 13:37:25'),
      (1567,1,'selected_option','Selected option','other','2021-08-20 13:37:25','2021-08-20 13:37:25'),
      (1568,1,'remark','Remark','other','2021-08-20 13:37:25','2021-08-20 13:37:25'),
      (1569,1,'datetime','Datetime','other','2021-08-20 13:37:25','2021-08-20 13:37:25'),
      (1570,1,'goto_question','Goto question','other','2021-08-20 13:37:25','2021-08-20 13:37:25'),
      (1571,1,'4.2.1 Data Placed Successfully','4.2.1 Data Placed Successfully','other','2021-08-20 13:48:39','2021-08-20 13:48:39'),
      (1572,1,'next_updates_details','Next updates details','other','2021-08-20 13:48:39','2021-08-20 13:48:39'),
      (1573,1,'choose_landing_page','Choose landing page','other','2021-08-20 19:06:02','2021-08-20 19:06:02'),
      (1574,1,'is_compatible_used_for_mobile_view_on_test_page_if_choose_yes','Is compatible used for mobile view on test page if choose yes','other','2021-08-20 19:28:42','2021-08-20 19:28:42'),
      (1575,1,'sticky_header_used_for_apply_sticky_menubar_which_is_fixed_for_webpage','Sticky header used for apply sticky menubar which is fixed for webpage','other','2021-08-20 19:28:42','2021-08-20 19:28:42'),
      (1576,1,'allow_number_of_attempt','Allow number of attempt','other','2021-08-23 12:52:09','2021-08-23 12:52:09'),
      (1577,1,'quiz_helpsheet','Quiz helpsheet','other','2021-08-23 12:52:09','2021-08-23 12:52:09'),
      (1578,1,'4.2.2 Data Placed Successfully','4.2.2 Data Placed Successfully','other','2021-08-25 05:36:31','2021-08-25 05:36:31'),
      (1579,1,'If You Cant View File Then Click On This Link','If You Cant View File Then Click On This Link','other','2021-08-31 19:08:57','2021-08-31 19:08:57'),
      (1580,1,'If You Cant View File Then Refresh Page Again Or Click On This Link','If You Cant View File Then Refresh Page Again Or Click On This Link','other','2021-08-31 19:31:02','2021-08-31 19:31:02'),
      (1581,1,'If You Cant View File Then Refresh Page Again Or','If You Cant View File Then Refresh Page Again Or','other','2021-08-31 19:32:24','2021-08-31 19:32:24'),
      (1582,1,'Invalid Uri Arguments','Invalid Uri Arguments','other','2021-09-16 13:49:08','2021-09-16 13:49:08'),
      (1583,1,'quiz setting','Quiz setting','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1584,1,'answered_question_on_test','Answered question on test','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1585,1,'answered_question_show_or_not_on_test_page','Answered question show or not on test page','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1586,1,'color_code_for_answered_question','Color code for answered question','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1587,1,'set_answered_question_palette_color_code_like_#000','Set answered question palette color code like #000','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1588,1,'not_answered_question_on_question_palette','Not answered question on question palette','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1589,1,'not_answered_question_show_or_not_on_test_page','Not answered question show or not on test page','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1590,1,'color_code_for_not_answered_question','Color code for not answered question','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1591,1,'set_not_answered_question_palette_color_code_like_#FF0000_this_color_code_of_red','Set not answered question palette color code like #FF0000 this color code of red','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1592,1,'total_attempt_hide_or_show','Total attempt hide or show','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1593,1,'total_attempt_hide_or_show_on_question_palette_test_page','Total attempt hide or show on question palette test page','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1594,1,'color_code_for_total_attempt_on_test_page','Color code for total attempt on test page','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1595,1,'set_color_code_for_total_attempt_on_question_palette_color_code_like_#000','Set color code for total attempt on question palette color code like #000','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1596,1,'not_visit_button_hide_or_show_on_testpage','Not visit button hide or show on testpage','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1597,1,'not_visit_hide_or_show_on_test_page','Not visit hide or show on test page','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1598,1,'color_code_of_not_visit','Color code of not visit','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1599,1,'set_color_code_for_not_visit_button_on_testpage_color_code_like_#000','Set color code for not visit button on testpage color code like #000','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1600,1,'mark_for_review_hide_or_show','Mark for review hide or show','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1601,1,'mark_for_review_hide_or_show_on_testpage','Mark for review hide or show on testpage','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1602,1,'color_code_for_mark_for_review','Color code for mark for review','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1603,1,'set_color_code_for_mark_for_review_on_testpage_color_code_like_#000','Set color code for mark for review on testpage color code like #000','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1604,1,'mark_for_answer_hide_or_show','Mark for answer hide or show','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1605,1,'mark_for_answer_show_or_hide_on_testpage','Mark for answer show or hide on testpage','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1606,1,'color_code_for_mark_for_answer','Color code for mark for answer','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1607,1,'set_color_code_for_mark_for_answer_on_testpage_color_code_like_#000','Set color code for mark for answer on testpage color code like #000','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1608,1,'note_for_previous_question_not_appear_when_you_goto_next_question','Note for previous question not appear when you goto next question','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1609,1,'previous_button_disable_on_test_page','Previous button disable on test page','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1610,1,'show_and_hide_mark_for_review_and_next_on_test_page','Show and hide mark for review and next on test page','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1611,1,'mark_for_review_and_next_show_or_hide','Mark for review and next show or hide','other','2021-09-24 11:23:36','2021-09-24 11:23:36'),
      (1612,1,'disable_previous_button_on_test_page','Disable previous button on test page','other','2021-09-24 11:23:43','2021-09-24 11:23:43'),
      (1613,1,'you_can_disable_previous_button_on_test_page','You can disable previous button on test page','other','2021-09-24 11:23:43','2021-09-24 11:23:43'),
      (1614,1,'marks_obtain','Marks obtain','other','2021-09-24 11:23:44','2021-09-24 11:23:44'),
      (1615,1,'send_email','Send email','other','2021-09-24 11:23:49','2021-09-24 11:23:49'),
      (1616,1,'download_view','Download view','other','2021-09-24 11:23:49','2021-09-24 11:23:49'),
      (1617,1,'subject','Subject','other','2021-09-24 11:23:49','2021-09-24 11:23:49'),
      (1618,1,'send_type','Send type','other','2021-09-24 11:23:50','2021-09-24 11:23:50'),
      (1619,1,'reference_data','Reference data','other','2021-09-27 17:08:41','2021-09-27 17:08:41'),
      (1620,1,'reference_data','Reference data','other','2021-09-27 17:08:41','2021-09-27 17:08:41'),
      (1621,1,'download_pdf','Download pdf','other','2021-09-27 17:12:59','2021-09-27 17:12:59'),
      (1622,1,'already_attemped_quiz','Already attemped quiz','other','2021-10-02 11:17:25','2021-10-02 11:17:25'),
      (1623,1,'you_cant_resubmit_this_quiz_answer','You cant resubmit this quiz answer','other','2021-10-02 11:17:25','2021-10-02 11:17:25'),
      (1624,1,'question_already_attempted','Question already attempted','other','2021-10-02 14:03:18','2021-10-02 14:03:18'),
      (1625,1,'you_cant_resubmit_attempted_question','You cant resubmit attempted question','other','2021-10-02 14:03:18','2021-10-02 14:03:18'),
      (1626,1,'are_you_a_tutor','Are you a tutor','other','2021-10-06 17:57:54','2021-10-06 17:57:54'),
      (1627,1,'sub_category','Sub category','other','2021-10-30 18:48:21','2021-10-30 18:48:21'),
      (1628,1,'rating','Rating','other','2021-10-30 18:48:21','2021-10-30 18:48:21'),
      (1629,1,'paid','Paid','other','2021-10-30 18:48:21','2021-10-30 18:48:21'),
      (1630,1,'sub_category_display_hide','Sub category display hide','other','2021-10-30 18:49:59','2021-10-30 18:49:59'),
      (1631,1,'sub_category_show_or_hide','Sub category show or hide','other','2021-10-30 18:49:59','2021-10-30 18:49:59'),
      (1632,1,'verified','Verified','other','2021-10-30 18:59:11','2021-10-30 18:59:11'),
      (1633,1,'mail_header_image','Mail header image','other','2021-11-01 16:49:23','2021-11-01 16:49:23'),
      (1634,1,'upload_mail_template_header_image','Upload mail template header image','other','2021-11-01 16:49:23','2021-11-01 16:49:23'),
      (1635,1,'mail_footer_image','Mail footer image','other','2021-11-01 16:49:23','2021-11-01 16:49:23'),
      (1636,1,'upload_mail_template_footer_image','Upload mail template footer image','other','2021-11-01 16:49:23','2021-11-01 16:49:23'),
      (1637,1,'4.2.4 Data Placed Successfully','4.2.4 Data Placed Successfully','other','2021-11-12 17:58:29','2021-11-12 17:58:29');
    
    /*!40000 ALTER TABLE `lang_token` ENABLE KEYS */;
    UNLOCK TABLES;
    
    
    # Dump of table language
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `language`;
    
    CREATE TABLE `language` (
      `id` int NOT NULL AUTO_INCREMENT,
      `lang` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `is_rtl` tinyint DEFAULT '0',
      `added` timestamp NULL DEFAULT NULL,
      `updated` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    LOCK TABLES `language` WRITE;
    /*!40000 ALTER TABLE `language` DISABLE KEYS */;
    
    INSERT INTO `language` (`id`, `lang`, `is_rtl`, `added`, `updated`)
    VALUES
      (1,'English',0,'2020-04-11 06:32:03','2020-04-12 06:05:02');
    
    /*!40000 ALTER TABLE `language` ENABLE KEYS */;
    UNLOCK TABLES;
    
    
    # Dump of table levels
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `levels`;
    
    CREATE TABLE `levels` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `min_points` int unsigned DEFAULT NULL,
      `level_order` int unsigned DEFAULT NULL,
      `badge` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    LOCK TABLES `levels` WRITE;
    /*!40000 ALTER TABLE `levels` DISABLE KEYS */;
    
    INSERT INTO `levels` (`id`, `title`, `description`, `min_points`, `level_order`, `badge`)
    VALUES
      (1,'FIRST','FIRST',72,1,'1602074946mechanical.jpg'),
      (2,'SECOND','SECOND',121,2,'1602074997cs.jpg'),
      (4,'THIRD','THIRD',222,3,'1602074997cs.jpg'),
      (5,'NO-LEVEL','NO-LEVEL',0,0,'1602074997cs.jpg');
    
    /*!40000 ALTER TABLE `levels` ENABLE KEYS */;
    UNLOCK TABLES;
    
    
    # Dump of table login_attempts
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `login_attempts`;
    
    CREATE TABLE `login_attempts` (
      `ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `attempt` datetime DEFAULT NULL,
      KEY `ip` (`ip`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table membership
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `membership`;
    
    CREATE TABLE `membership` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `title` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `amount` float DEFAULT NULL,
      `duration` int unsigned DEFAULT NULL,
      `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `category_id` int DEFAULT '0',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table package_quizes
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `package_quizes`;
    
    CREATE TABLE `package_quizes` (
      `package_id` int unsigned DEFAULT NULL,
      `quiz_id` int unsigned DEFAULT NULL,
      `quiz_order` int unsigned DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table package_study_material
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `package_study_material`;
    
    CREATE TABLE `package_study_material` (
      `package_id` int unsigned DEFAULT NULL,
      `study_material_id` int unsigned DEFAULT NULL,
      `study_material_order` int unsigned DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table packages
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `packages`;
    
    CREATE TABLE `packages` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `price` int unsigned DEFAULT NULL,
      `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `leader_board` tinyint DEFAULT '0' COMMENT '0=not display on front,1=display on front',
      `meta_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `meta_keywords` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `meta_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table pages
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `pages`;
    
    CREATE TABLE `pages` (
      `id` int NOT NULL AUTO_INCREMENT,
      `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `on_menu` tinyint DEFAULT NULL,
      `featured_image` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `added` timestamp NULL DEFAULT NULL,
      `updated` timestamp NULL DEFAULT NULL,
      `meta_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `meta_keywords` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `meta_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table paragraph
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `paragraph`;
    
    CREATE TABLE `paragraph` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `title` varchar(255) DEFAULT NULL,
      `order` int DEFAULT '0',
      `content` longtext,
      `added` datetime DEFAULT NULL,
      `updated` datetime DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
    
    
    
    # Dump of table participants
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `participants`;
    
    CREATE TABLE `participants` (
      `id` int NOT NULL AUTO_INCREMENT,
      `user_id` int DEFAULT NULL,
      `guest_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `quiz_id` int DEFAULT NULL,
      `questions` int DEFAULT NULL,
      `correct` int DEFAULT NULL,
      `total_attemp` int DEFAULT NULL,
      `started` timestamp NULL DEFAULT NULL,
      `completed` timestamp NULL DEFAULT NULL,
      `test_language` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `quiz_passing_marks` int DEFAULT NULL,
      `earned_points` int DEFAULT NULL,
      `quiz_levels_data` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `quiz_grading_id` int DEFAULT '0',
      `reward_percentage` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0',
      `negative_marking_percentage` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0',
      `marks_for_correct_answer` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '1',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table payments
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `payments`;
    
    CREATE TABLE `payments` (
      `id` int NOT NULL AUTO_INCREMENT,
      `user_id` int DEFAULT NULL,
      `quiz_id` int DEFAULT NULL,
      `payer_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `customer_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `item_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `item_price` float(10,2) unsigned DEFAULT NULL,
      `item_price_currency` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `txn_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `payment_status` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `created` datetime DEFAULT NULL,
      `modified` datetime DEFAULT NULL,
      `payment_gateway` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `token_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `invoice_no` int DEFAULT NULL,
      `purchases_type` enum('quiz','material','package','membership') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'quiz',
      `coupon_id` int NOT NULL DEFAULT '0',
      `coupon_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `discount_value` bigint NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table post_count
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `post_count`;
    
    CREATE TABLE `post_count` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `post_id` int unsigned DEFAULT NULL,
      `ip_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `user_agent` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table post_like
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `post_like`;
    
    CREATE TABLE `post_like` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `post_id` int DEFAULT NULL,
      `user_id` int DEFAULT NULL,
      `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table question_report_problem
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `question_report_problem`;
    
    CREATE TABLE `question_report_problem` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `user_id` int unsigned DEFAULT NULL,
      `question_id` int unsigned DEFAULT NULL,
      `report_option` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `other_info` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table questions
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `questions`;
    
    CREATE TABLE `questions` (
      `id` int NOT NULL AUTO_INCREMENT,
      `quiz_id` int DEFAULT NULL,
      `title` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `solution` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `solution_image` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `is_multiple` tinyint DEFAULT NULL,
      `choices` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `correct_choice` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `updated` timestamp NULL DEFAULT NULL,
      `deleted` tinyint DEFAULT '0',
      `addon_type` tinyint DEFAULT '0',
      `addon_value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `queston_choies_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'choices',
      `question_type_is_match` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'NO',
      `question_paragraph_id` int DEFAULT '0',
      `question_section_id` int DEFAULT '0',
      `upload_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'image',
      `render_content` int NOT NULL DEFAULT '0',
      `helpsheet` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table quiz_count
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `quiz_count`;
    
    CREATE TABLE `quiz_count` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `quiz_id` int unsigned DEFAULT NULL,
      `ip_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `user_agent` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table quiz_grading
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `quiz_grading`;
    
    CREATE TABLE `quiz_grading` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `title` varchar(255) DEFAULT NULL,
      `data` longtext,
      `user_id` int DEFAULT '0',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
    
    
    
    # Dump of table quiz_like
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `quiz_like`;
    
    CREATE TABLE `quiz_like` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `quiz_id` int unsigned DEFAULT NULL,
      `user_id` int unsigned DEFAULT NULL,
      `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table quiz_reviews
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `quiz_reviews`;
    
    CREATE TABLE `quiz_reviews` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `rel_id` int unsigned DEFAULT NULL,
      `user_id` int unsigned DEFAULT NULL,
      `review_content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `rating` tinyint DEFAULT NULL,
      `status` tinyint(1) DEFAULT '0' COMMENT '0=not display on front,1=display on front',
      `rel_type` enum('quiz','material','package','membership') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'quiz',
      `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table quizes
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `quizes`;
    
    CREATE TABLE `quizes` (
      `id` int NOT NULL AUTO_INCREMENT,
      `category_id` int DEFAULT NULL,
      `user_id` int DEFAULT NULL,
      `number_questions` int DEFAULT NULL,
      `price` int DEFAULT NULL,
      `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `featured_image` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `duration_min` int DEFAULT NULL,
      `leader_board` tinyint DEFAULT NULL,
      `quiz_instruction` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `is_random` tinyint DEFAULT '0',
      `is_random_option` tinyint DEFAULT NULL,
      `is_registered` tinyint DEFAULT '0' COMMENT '0=not registered,1=registered',
      `attempt` int DEFAULT NULL,
      `is_paid` tinyint DEFAULT NULL,
      `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `updated` timestamp NULL DEFAULT NULL,
      `deleted` tinyint DEFAULT NULL,
      `meta_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `meta_keywords` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `meta_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `passing` tinyint DEFAULT NULL,
      `difficulty_level` tinyint DEFAULT '0' COMMENT '0=none,1=easy,2=medium,3=hard',
      `points_on_correct` int unsigned DEFAULT NULL,
      `is_premium` tinyint DEFAULT NULL COMMENT '0=not premium,1=premium',
      `bonus_points` int unsigned DEFAULT NULL,
      `is_unlimited_time` tinyint DEFAULT NULL COMMENT '0=limited,1=unlimited',
      `multiple_attemp` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `evaluation_test` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'NO',
      `is_sheduled_test` tinyint DEFAULT '0',
      `start_date_time` bigint NOT NULL DEFAULT '0',
      `end_date_time` bigint NOT NULL DEFAULT '33168837600',
      `is_quiz_active` tinyint NOT NULL DEFAULT '1',
      `quiz_grading_id` int DEFAULT '0',
      `negative_marking_percentage` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0',
      `reward_percentage` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0',
      `marks_for_correct_answer` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0',
      `hide_correct_answer` tinyint DEFAULT '0',
      `is_disable_result` tinyint NOT NULL DEFAULT '0',
      `quiz_helpsheet` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `is_previous_disable` tinyint DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table review_likes
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `review_likes`;
    
    CREATE TABLE `review_likes` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `review_id` int DEFAULT NULL,
      `user_id` int DEFAULT NULL,
      `rel_type` enum('quiz','material','package','membership') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'quiz',
      `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table section
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `section`;
    
    CREATE TABLE `section` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `title` varchar(255) DEFAULT NULL,
      `added` datetime DEFAULT NULL,
      `updated` datetime DEFAULT NULL,
      `order` int DEFAULT '0',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
    
    
    
    # Dump of table settings
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `settings`;
    
    CREATE TABLE `settings` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `input_type` enum('input','password','textarea','radio','dropdown','timezones','file','hidden') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'input',
      `options` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'Use for radio and dropdown: key|value on each line',
      `is_numeric` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0' COMMENT 'forces numeric keypad on mobile devices',
      `show_editor` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0',
      `input_size` enum('large','medium','small') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `translate` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0',
      `help_text` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `validation` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `sort_order` tinyint unsigned DEFAULT NULL,
      `label` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'If translate is 1, just start with your default language',
      `setting_group` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `last_update` datetime DEFAULT NULL,
      `updated_by` int unsigned DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `name` (`name`),
      KEY `updated_by` (`updated_by`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    LOCK TABLES `settings` WRITE;
    /*!40000 ALTER TABLE `settings` DISABLE KEYS */;
    
    INSERT INTO `settings` (`id`, `name`, `input_type`, `options`, `is_numeric`, `show_editor`, `input_size`, `translate`, `help_text`, `validation`, `sort_order`, `label`, `value`, `setting_group`, `last_update`, `updated_by`)
    VALUES
      (1,'site_name','input',NULL,'0','0','large','0','admin_setting_site_name_help','required|trim|min_length[3]|max_length[128]',10,'admin_setting_site_name','Quizzy','general setting','2021-11-11 17:49:38',1),
      (2,'meta_keywords','input',NULL,'0','0','large','0','setting_meta_key_help','trim',20,'setting_meta_key','quiz, test, online, exams','general setting','2021-11-11 17:49:38',1),
      (3,'meta_description','textarea',NULL,'0','0','large','0','setting_meta_desc_help','trim',30,'setting_meta_desc','A highly scalable and powerful Online Exam System to manage categories, quizes and multiple choice questions. Prepare candidates to perform extraordinarily with an easy to use highly interactive platform and simplify the assessment cycle.','general setting','2021-11-11 17:49:38',1),
      (4,'site_email','input',NULL,'0','0','medium','0','setting_site_email_help','required|trim|valid_email',40,'setting_site_email','info@quizzy.com','general setting','2021-11-11 17:49:38',1),
      (5,'site_logo','file',NULL,'0','0','large','0','setting_upload_site_logo_help','',80,'setting_upload_site_logo','logo.png','general setting','2020-07-13 17:09:05',1),
      (6,'site_favicon','file',NULL,'0','0','large','0','setting_upload_site_favicon_help','',90,'setting_upload_site_favicon','favicon.png','general setting','2020-07-26 00:40:33',1),
      (13,'site_phone_number','input',NULL,'0','0','large','0','site_ph_nbr_help','required',41,'site_ph_nbr','+1 987654321','general setting','2021-11-11 17:49:38',1),
      (14,'smtp_protocol','input',NULL,'0','0','large','0','smto_protocol_help','required',182,'smto_protocol','smtp','mail setting','2021-11-11 17:49:38',1),
      (15,'smtp_host','input',NULL,'0','0','large','0','smtp_host_help','required',183,'smtp_host','smtp.gmail.com','mail setting','2021-11-11 17:49:38',1),
      (16,'smtp_user','input',NULL,'0','0','large','0','smtp_user_help','required',184,'smtp_user','developer.atn@gmail.com','mail setting','2021-11-11 17:49:38',1),
      (17,'smtp_pass','password',NULL,'0','0','large','0','smtp_pass_help','required',185,'smtp_pass','pyfqndoflkdbeyhw','mail setting','2021-11-11 17:49:38',1),
      (18,'smtp_port','input',NULL,'0','0','large','0','smtp_port_help','required',186,'smtp_port','465','mail setting','2021-11-11 17:49:38',1),
      (19,'smtp_crypto','input',NULL,'0','0','large','0','smtp_crypt_help','required',187,'smtp_crypt','SSL','mail setting','2021-11-11 17:49:38',1),
      (20,'default_site_language','dropdown','{\"English\":\"English\",\"3.3\":\"3.3\",\"Hindi\":\"Hindi\",\"Testlanguage\":\"Testlanguage\",\"3333\":\"3333\"}','0','0','large','0','select_default_lang_help','required',187,'select_default_lang','English','general setting','2021-11-11 17:49:38',1),
      (21,'general_instructions','textarea',NULL,'0','1','large','0','quz_instructions_help','required',200,'quz_instructions','<h2 class=\"mt-0 mb-4\">General Instructions</h2>\r\n            <p class=\"normaltext\" xss=\"removed\">The quizzes consists of questions carefully designed to help you self-assess your comprehension of the information presented on the topics covered in the module. No data will be collected on the website regarding your responses or how many times you take the quiz.</p><p class=\"normaltext\" xss=\"removed\">Each question in the quiz is of multiple-choice or \"true or false\" format. Read each question carefully, and click on the button next to your response that is based on the information covered on the topic in the module. Each correct or incorrect response will result in appropriate feedback immediately at the bottom of the screen.</p><p class=\"normaltext\" xss=\"removed\">After responding to a question, click on the \"Next Question\" button at the bottom to go to the next questino. After responding to the 8th question, click on \"Close\" on the top of the window to exit the quiz.</p><p class=\"normaltext\" xss=\"removed\">If you select an incorrect response for a question, you can try again until you get the correct response. If you retake the quiz, the questions and their respective responses will be randomized.</p><p class=\"normaltext\" xss=\"removed\">The total score for the quiz is based on your responses to all questions. If you respond incorrectly to a question or retake a question again and get the correct response, your quiz score will reflect it appropriately. However, your quiz will not be graded, if you skip a question or exit before responding to all the questions.</p>','general setting','2021-11-11 17:49:38',1),
      (22,'email_user_activation','dropdown','{\"YES\":\"YES\",\"NO\":\"NO\"}','0','0','large','0','email_active_status_help','required',189,'email_active_status','NO','mail setting','2021-11-11 17:49:38',1),
      (23,'disable_right_click','dropdown','{\"YES\":\"YES\",\"NO\":\"NO\"}','0','0','large','0','disable_right_click_help','required',189,'disable_right_click','NO','general setting','2021-11-11 17:49:38',1),
      (24,'disable_print_screen','dropdown','{\"YES\":\"YES\",\"NO\":\"NO\"}','0','0','large','0','disable_print_screen_help','required',189,'disable_print_screen','NO','general setting','2021-11-11 17:49:38',1),
      (25,'facebook_app_id','input',NULL,'0','0','large','0','facebook_app_id_help','',184,'facebook_app_id','your_app_key','social login setting','2021-11-11 17:49:38',1),
      (26,'facebook_app_secret','input',NULL,'0','0','large','0','facebook_app_secret_help','',184,'facebook_app_secret','','social login setting','2021-11-11 17:49:38',1),
      (27,'google_key','input',NULL,'0','0','large','0','google_key_help','',184,'google_key','your_google_key','social login setting','2021-11-11 17:49:38',1),
      (28,'google_secret','input',NULL,'0','0','large','0','google_secret_help','',184,'google_secret','','social login setting','2021-11-11 17:49:38',1),
      (29,'paypal_mode','dropdown','{\"SANDBOX\":\"SANDBOX\",\"LIVE\":\"LIVE\"}','0','0','large','0','paypal_mode_help','required',189,'paypal_mode','SANDBOX','payment setting','2021-11-11 17:49:38',1),
      (30,'paypal_key','input','{\"English\":\"English\",\"3.3\":\"3.3\",\"Hindi\":\"Hindi\",\"Testlanguage\":\"Testlanguage\",\"3333\":\"3333\"}','0','0','large','0','paypal_key_help','',184,'paypal_key','AXt5Q5O_xouOI-hqazGQg1df-XkM_hDfOdiitN5MkmRfK4Q_7FBP6JHYo3BuQbCzNxoXScYU9833P0uw','payment setting','2021-11-11 17:49:38',1),
      (31,'paypal_secret_key','input',NULL,'0','0','large','0','paypal_secret_key_help','',184,'paypal_secret_key','EMdSqevavkJ96OpgnG7rYNU9nFrvXJLipDpoyO65CaK0ojrEnwbX4c3-fo3o7KlSetRxlDjGf4ykRo3j','payment setting','2021-11-11 17:49:38',1),
      (32,'stripe_key','input',NULL,'0','0','large','0','stripe_key_help','',184,'stripe_key','your_api_key','payment setting','2021-11-11 17:49:38',1),
      (33,'stripe_secret_key','input',NULL,'0','0','large','0','stripe_secret_key_help','',184,'stripe_secret_key','','payment setting','2021-11-11 17:49:38',1),
      (34,'paid_currency','dropdown','{\"USD\":\"USD\",\"AED\":\"AED\",\"ALL\":\"ALL\",\"AMD\":\"AMD\",\"AOA\":\"AOA\",\"ARS\":\"ARS\",\"BAM\":\"BAM\",\"BGN\":\"BGN\",\"BOB\":\"BOB\",\"BSD\":\"BSD\",\"CDF\":\"CDF\",\"CNY\":\"CNY\",\"DKK\":\"DKK\",\"EGP\":\"EGP\",\"GBP\":\"GBP\",\"HRK\":\"HRK\",\"ILS\":\"ILS\",\"INR\":\"INR\",\"KYD\":\"KYD\",\"LBP\":\"LBP\",\"MMK\":\"MMK\",\"MUR\":\"MUR\",\"NIO\":\"NIO\",\"PLN\":\"PLN\",\"RSD\":\"RSD\",\"SAR\":\"SAR\",\"LKR\":\"LKR\",\"LRD\":\"LRD\",\"LSL\":\"LSL\",\"MAD\":\"MAD\",\"MDL\":\"MDL\",\"MGA\":\"MGA\",\"MKD\":\"MKD\",\"MMK\":\"MMK\",\"MNT\":\"MNT\",\"MOP\":\"MOP\",\"MRO\":\"MRO\",\"MUR\":\"MUR\",\"MVR\":\"MVR\",\"MWK\":\"MWK\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"MZN\":\"NAD\",\"NGN\":\"NGN\",\"NIO\":\"NIO\",\"NOK\":\"NOK\",\"NPR\":\"NPR\",\"NZD\":\"NZD\",\"PAB\":\"PAB\",\"PEN\":\"PEN\",\"PGK\":\"PGK\",\"PHP\":\"PHP\",\"PKR\":\"PKR\",\"PLN\":\"PLN\",\"PYG\":\"PYG\",\"QAR\":\"QAR\",\"RON\":\"RON\",\"RSD\":\"RSD\",\"RUB\":\"RUB\",\"RWF\":\"RWF\",\"SAR\":\"SAR\",\"SBD\":\"SBD\",\"SCR\":\"SCR\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"SHP\":\"SHP\",\"SLL\":\"SLL\",\"SOS\":\"SOS\",\"SRD\":\"SRD\",\"STD\":\"STD\",\"SZL\":\"SZL\",\"EUR\":\"EUR\",\"BRL\":\"BRL\",\"TRY\":\"TRY\"}','0','0','large','0','paid_currency_help','required',189,'paid_currency','USD','payment setting','2021-11-11 17:49:38',1),
      (35,'bank_transfer','textarea',NULL,'0','1','large','0','bank_transfer_help','',200,'bank_transfer','<p></p><div><div class=\"row\"></div><div><div>   <div class=\"col-md-6\" xss=removed>Bank Name: Hdfc ifsc Code: hdajsj14562aeee</div></div><div>   <div class=\"col-md-6\" xss=removed>Branch: Jodhpur Account No.:hdd456284541ui</div></div></div><div></div></div><p></p>','payment setting','2021-11-11 17:49:38',1),
      (36,'tax_name','input',NULL,'0','0','large','0','tax_name_help','',184,'tax_name','GST','general setting','2021-11-11 17:49:38',1),
      (37,'tax_percentage','input',NULL,'0','0','large','0','tax_percentage_help','',184,'tax_percentage','18','general setting','2021-11-11 17:49:38',1),
      (38,'invoice_header_text','textarea',NULL,'0','1','large','0','invoice_header_text_help','',200,'invoice_header_text','<h2 class=\"name\" xss=removed>Quizzy</h2><div xss=removed>455 Foggy Heights, AZ 85004, US</div><div xss=removed>(602) 519-0450</div><div xss=removed><a href=\"mailto:info@quizzy.com\">info@quizzy.com</a></div>','invoice setting','2021-11-11 17:49:38',1),
      (39,'invoice_footer_text','textarea',NULL,'0','1','large','0','invoice_footer_text_help','',200,'invoice_footer_text','<p><span xss=removed>Invoice was created on a computer and is valid without the signature and seal.</span><br></p>','invoice setting','2021-11-11 17:49:38',1),
      (40,'invoice_start_number','input',NULL,'0','0','large','0','invoice_start_number_help','',184,'invoice_start_number','1000','invoice setting','2021-11-11 17:49:38',1),
      (41,'disable_copy_paste_click','dropdown','{\"YES\":\"YES\",\"NO\":\"NO\"}','0','0','large','0','disable_copy_paste_click_help','required',189,'disable_copy_paste_click','NO','general setting','2021-11-11 17:49:38',1),
      (42,'top_text_left','textarea',NULL,'0','1','large','0','top_text_left_help','',200,'top_text_left','Best online quiz platform','header setting','2021-11-11 17:49:38',1),
      (43,'top_text_right','textarea',NULL,'0','1','large','0','top_text_right_help','',200,'top_text_right','Contact +91-987654321','header setting','2021-11-11 17:49:38',1),
      (46,'testimonial_background','input',NULL,'0','0','large','0','testimonial_background_help','',184,'testimonial_background','background-image: linear-gradient( to right top, #051937, #004d7a, #008793, #00bf72, #ffffff);','testimonial setting','2021-11-11 17:49:38',1),
      (47,'site_update_token','input',NULL,'0','0','large','0','site_update_token','',183,'site_update_token','test-token-for-ishalabs-com','general setting','2021-11-11 17:49:38',1),
      (48,'date_formate','dropdown','{\"d-m-Y\":\"d-m-Y\",\"m-d-Y\":\"m-d-Y\",\"Y-m-d\":\"Y-m-d\",\"d/m/Y\":\"d/m/Y\",\"m/d/Y\":\"m/d/Y\",\"Y/m/d\":\"Y/m/d\",\"d.m.Y\":\"d.m.Y\",\"m.d.Y\":\"m.d.Y\",\"Y.m.d\":\"Y.m.d\"}','0','0','large','0','admin_date_formate','',189,'admin_date_formate','d-m-Y','general setting','2021-11-11 17:49:38',1),
      (49,'date_or_time_formate','dropdown','{\"h:i:s A\":\"h : i : s  A\",\"h:i:s a\":\"h : i : s  a\",\"H:i:s\":\"H : i : s\"}','0','0','large','0','date_or_time_formate','',189,'date_or_time_formate','h:i:s A','general setting','2021-11-11 17:49:38',1),
      (50,'enable_captch_code_login','dropdown','{\"YES\":\"YES\",\"NO\":\"NO\"}','0','0','large','0','enable_captch_code_login','',189,'enable_captch_code_login','NO','api keys','2021-11-11 17:49:38',1),
      (51,'header_javascript','textarea',NULL,'0','0','large','0','header_javascript_help','',200,'header_javascript','','header setting','2021-11-11 17:49:38',1),
      (52,'footer_javascript','textarea',NULL,'0','0','large','0','footer_javascript_help','',200,'footer_javascript','','header setting','2021-11-11 17:49:38',1),
      (53,'custom_css','textarea',NULL,'0','0','large','0','custom_css_help','',200,'custom_css','','header setting','2021-11-11 17:49:38',1),
      (54,'recaptcha_secret_key','input',NULL,'0','0','large','0','recaptcha_secret_key','',183,'recaptcha_secret_key','your_captcha_key','api keys','2021-11-11 17:49:38',1),
      (55,'recaptcha_site_key','input',NULL,'0','0','large','0','recaptcha_site_key','',183,'recaptcha_site_key','','api keys','2021-11-11 17:49:38',1),
      (56,'header_logo_height','input',NULL,'0','0','large','0','header_logo_height','required|trim',10,'header_logo_height','65','header setting','2021-11-11 17:49:38',1),
      (57,'razorpay_key','input',NULL,'0','0','large','0','razorpay_key_help','',184,'razorpay_key','your_key','payment setting','2021-11-11 17:49:38',1),
      (58,'razorpay_secret_key','input',NULL,'0','0','large','0','razorpay_secret_key_help','',184,'razorpay_secret_key','','payment setting','2021-11-11 17:49:38',1),
      (59,'instamojo_apikey','input',NULL,'0','0',NULL,'0','instamojo_apikey_help','',11,'instamojo_apikey','your_api_key','payment setting','2021-11-11 17:49:38',1),
      (60,'instamojo_token','input',NULL,'0','0',NULL,'0','instamojo_token_help','',10,'instamojo_token','','payment setting','2021-11-11 17:49:38',1),
      (61,'instamojo_mode','dropdown','{\"SANDBOX\":\"SANDBOX\",\"LIVE\":\"LIVE\"}','0','0',NULL,'0','instamojo_mode_help','',12,'instamojo_mode','SANDBOX','payment setting','2021-11-11 17:49:38',1),
      (62,'default_time_zone','dropdown','{\"Africa/Abidjan\":\"Africa/Abidjan\",\"Africa/Accra\":\"Africa/Accra\",\"Africa/Addis_Ababa\":\"Africa/Addis_Ababa\",\"Africa/Algiers\":\"Africa/Algiers\",\"Africa/Asmara\":\"Africa/Asmara\",\"Africa/Bamako\":\"Africa/Bamako\",\"Africa/Bangui\":\"Africa/Bangui\",\"Africa/Banjul\":\"Africa/Banjul\",\"Africa/Bissau\":\"Africa/Bissau\",\"Africa/Blantyre\":\"Africa/Blantyre\",\"Africa/Brazzaville\":\"Africa/Brazzaville\",\"Africa/Bujumbura\":\"Africa/Bujumbura\",\"Africa/Cairo\":\"Africa/Cairo\",\"Africa/Casablanca\":\"Africa/Casablanca\",\"Africa/Ceuta\":\"Africa/Ceuta\",\"Africa/Conakry\":\"Africa/Conakry\",\"Africa/Dakar\":\"Africa/Dakar\",\"Africa/Dar_es_Salaam\":\"Africa/Dar_es_Salaam\",\"Africa/Djibouti\":\"Africa/Djibouti\",\"Africa/Douala\":\"Africa/Douala\",\"Africa/El_Aaiun\":\"Africa/El_Aaiun\",\"Africa/Freetown\":\"Africa/Freetown\",\"Africa/Gaborone\":\"Africa/Gaborone\",\"Africa/Harare\":\"Africa/Harare\",\"Africa/Johannesburg\":\"Africa/Johannesburg\",\"Africa/Juba\":\"Africa/Juba\",\"Africa/Kampala\":\"Africa/Kampala\",\"Africa/Khartoum\":\"Africa/Khartoum\",\"Africa/Kigali\":\"Africa/Kigali\",\"Africa/Kinshasa\":\"Africa/Kinshasa\",\"Africa/Lagos\":\"Africa/Lagos\",\"Africa/Libreville\":\"Africa/Libreville\",\"Africa/Lome\":\"Africa/Lome\",\"Africa/Luanda\":\"Africa/Luanda\",\"Africa/Lubumbashi\":\"Africa/Lubumbashi\",\"Africa/Lusaka\":\"Africa/Lusaka\",\"Africa/Malabo\":\"Africa/Malabo\",\"Africa/Maputo\":\"Africa/Maputo\",\"Africa/Maseru\":\"Africa/Maseru\",\"Africa/Mbabane\":\"Africa/Mbabane\",\"Africa/Mogadishu\":\"Africa/Mogadishu\",\"Africa/Monrovia\":\"Africa/Monrovia\",\"Africa/Nairobi\":\"Africa/Nairobi\",\"Africa/Ndjamena\":\"Africa/Ndjamena\",\"Africa/Niamey\":\"Africa/Niamey\",\"Africa/Nouakchott\":\"Africa/Nouakchott\",\"Africa/Ouagadougou\":\"Africa/Ouagadougou\",\"Africa/Porto-Novo\":\"Africa/Porto-Novo\",\"Africa/Sao_Tome\":\"Africa/Sao_Tome\",\"Africa/Tripoli\":\"Africa/Tripoli\",\"Africa/Tunis\":\"Africa/Tunis\",\"Africa/Windhoek\":\"Africa/Windhoek\",\"America/Adak\":\"America/Adak\",\"America/Anchorage\":\"America/Anchorage\",\"America/Anguilla\":\"America/Anguilla\",\"America/Antigua\":\"America/Antigua\",\"America/Araguaina\":\"America/Araguaina\",\"America/Argentina/Buenos_Aires\":\"America/Argentina/Buenos_Aires\",\"America/Argentina/Catamarca\":\"America/Argentina/Catamarca\",\"America/Argentina/Cordoba\":\"America/Argentina/Cordoba\",\"America/Argentina/Jujuy\":\"America/Argentina/Jujuy\",\"America/Argentina/La_Rioja\":\"America/Argentina/La_Rioja\",\"America/Argentina/Mendoza\":\"America/Argentina/Mendoza\",\"America/Argentina/Rio_Gallegos\":\"America/Argentina/Rio_Gallegos\",\"America/Argentina/Salta\":\"America/Argentina/Salta\",\"America/Argentina/San_Juan\":\"America/Argentina/San_Juan\",\"America/Argentina/San_Luis\":\"America/Argentina/San_Luis\",\"America/Argentina/Tucuman\":\"America/Argentina/Tucuman\",\"America/Argentina/Ushuaia\":\"America/Argentina/Ushuaia\",\"America/Aruba\":\"America/Aruba\",\"America/Asuncion\":\"America/Asuncion\",\"America/Atikokan\":\"America/Atikokan\",\"America/Bahia\":\"America/Bahia\",\"America/Bahia_Banderas\":\"America/Bahia_Banderas\",\"America/Barbados\":\"America/Barbados\",\"America/Belem\":\"America/Belem\",\"America/Belize\":\"America/Belize\",\"America/Blanc-Sablon\":\"America/Blanc-Sablon\",\"America/Boa_Vista\":\"America/Boa_Vista\",\"America/Bogota\":\"America/Bogota\",\"America/Boise\":\"America/Boise\",\"America/Cambridge_Bay\":\"America/Cambridge_Bay\",\"America/Campo_Grande\":\"America/Campo_Grande\",\"America/Cancun\":\"America/Cancun\",\"America/Caracas\":\"America/Caracas\",\"America/Cayenne\":\"America/Cayenne\",\"America/Cayman\":\"America/Cayman\",\"America/Chicago\":\"America/Chicago\",\"America/Chihuahua\":\"America/Chihuahua\",\"America/Costa_Rica\":\"America/Costa_Rica\",\"America/Creston\":\"America/Creston\",\"America/Cuiaba\":\"America/Cuiaba\",\"America/Curacao\":\"America/Curacao\",\"America/Danmarkshavn\":\"America/Danmarkshavn\",\"America/Dawson\":\"America/Dawson\",\"America/Dawson_Creek\":\"America/Dawson_Creek\",\"America/Denver\":\"America/Denver\",\"America/Detroit\":\"America/Detroit\",\"America/Dominica\":\"America/Dominica\",\"America/Edmonton\":\"America/Edmonton\",\"America/Eirunepe\":\"America/Eirunepe\",\"America/El_Salvador\":\"America/El_Salvador\",\"America/Fort_Nelson\":\"America/Fort_Nelson\",\"America/Fortaleza\":\"America/Fortaleza\",\"America/Glace_Bay\":\"America/Glace_Bay\",\"America/Godthab\":\"America/Godthab\",\"America/Goose_Bay\":\"America/Goose_Bay\",\"America/Grand_Turk\":\"America/Grand_Turk\",\"America/Grenada\":\"America/Grenada\",\"America/Guadeloupe\":\"America/Guadeloupe\",\"America/Guatemala\":\"America/Guatemala\",\"America/Guayaquil\":\"America/Guayaquil\",\"America/Guyana\":\"America/Guyana\",\"America/Halifax\":\"America/Halifax\",\"America/Havana\":\"America/Havana\",\"America/Hermosillo\":\"America/Hermosillo\",\"America/Indiana/Indianapolis\":\"America/Indiana/Indianapolis\",\"America/Indiana/Knox\":\"America/Indiana/Knox\",\"America/Indiana/Marengo\":\"America/Indiana/Marengo\",\"America/Indiana/Petersburg\":\"America/Indiana/Petersburg\",\"America/Indiana/Tell_City\":\"America/Indiana/Tell_City\",\"America/Indiana/Vevay\":\"America/Indiana/Vevay\",\"America/Indiana/Vincennes\":\"America/Indiana/Vincennes\",\"America/Indiana/Winamac\":\"America/Indiana/Winamac\",\"America/Inuvik\":\"America/Inuvik\",\"America/Iqaluit\":\"America/Iqaluit\",\"America/Jamaica\":\"America/Jamaica\",\"America/Juneau\":\"America/Juneau\",\"America/Kentucky/Louisville\":\"America/Kentucky/Louisville\",\"America/Kentucky/Monticello\":\"America/Kentucky/Monticello\",\"America/Kralendijk\":\"America/Kralendijk\",\"America/La_Paz\":\"America/La_Paz\",\"America/Lima\":\"America/Lima\",\"America/Los_Angeles\":\"America/Los_Angeles\",\"America/Lower_Princes\":\"America/Lower_Princes\",\"America/Maceio\":\"America/Maceio\",\"America/Managua\":\"America/Managua\",\"America/Manaus\":\"America/Manaus\",\"America/Marigot\":\"America/Marigot\",\"America/Martinique\":\"America/Martinique\",\"America/Matamoros\":\"America/Matamoros\",\"America/Mazatlan\":\"America/Mazatlan\",\"America/Menominee\":\"America/Menominee\",\"America/Merida\":\"America/Merida\",\"America/Metlakatla\":\"America/Metlakatla\",\"America/Mexico_City\":\"America/Mexico_City\",\"America/Miquelon\":\"America/Miquelon\",\"America/Moncton\":\"America/Moncton\",\"America/Monterrey\":\"America/Monterrey\",\"America/Montevideo\":\"America/Montevideo\",\"America/Montserrat\":\"America/Montserrat\",\"America/Nassau\":\"America/Nassau\",\"America/New_York\":\"America/New_York\",\"America/Nipigon\":\"America/Nipigon\",\"America/Nome\":\"America/Nome\",\"America/Noronha\":\"America/Noronha\",\"America/North_Dakota/Beulah\":\"America/North_Dakota/Beulah\",\"America/North_Dakota/Center\":\"America/North_Dakota/Center\",\"America/North_Dakota/New_Salem\":\"America/North_Dakota/New_Salem\",\"America/Ojinaga\":\"America/Ojinaga\",\"America/Panama\":\"America/Panama\",\"America/Pangnirtung\":\"America/Pangnirtung\",\"America/Paramaribo\":\"America/Paramaribo\",\"America/Phoenix\":\"America/Phoenix\",\"America/Port-au-Prince\":\"America/Port-au-Prince\",\"America/Port_of_Spain\":\"America/Port_of_Spain\",\"America/Porto_Velho\":\"America/Porto_Velho\",\"America/Puerto_Rico\":\"America/Puerto_Rico\",\"America/Punta_Arenas\":\"America/Punta_Arenas\",\"America/Rainy_River\":\"America/Rainy_River\",\"America/Rankin_Inlet\":\"America/Rankin_Inlet\",\"America/Recife\":\"America/Recife\",\"America/Regina\":\"America/Regina\",\"America/Resolute\":\"America/Resolute\",\"America/Rio_Branco\":\"America/Rio_Branco\",\"America/Santarem\":\"America/Santarem\",\"America/Santiago\":\"America/Santiago\",\"America/Santo_Domingo\":\"America/Santo_Domingo\",\"America/Sao_Paulo\":\"America/Sao_Paulo\",\"America/Scoresbysund\":\"America/Scoresbysund\",\"America/Sitka\":\"America/Sitka\",\"America/St_Barthelemy\":\"America/St_Barthelemy\",\"America/St_Johns\":\"America/St_Johns\",\"America/St_Kitts\":\"America/St_Kitts\",\"America/St_Lucia\":\"America/St_Lucia\",\"America/St_Thomas\":\"America/St_Thomas\",\"America/St_Vincent\":\"America/St_Vincent\",\"America/Swift_Current\":\"America/Swift_Current\",\"America/Tegucigalpa\":\"America/Tegucigalpa\",\"America/Thule\":\"America/Thule\",\"America/Thunder_Bay\":\"America/Thunder_Bay\",\"America/Tijuana\":\"America/Tijuana\",\"America/Toronto\":\"America/Toronto\",\"America/Tortola\":\"America/Tortola\",\"America/Vancouver\":\"America/Vancouver\",\"America/Whitehorse\":\"America/Whitehorse\",\"America/Winnipeg\":\"America/Winnipeg\",\"America/Yakutat\":\"America/Yakutat\",\"America/Yellowknife\":\"America/Yellowknife\",\"Antarctica/Casey\":\"Antarctica/Casey\",\"Antarctica/Davis\":\"Antarctica/Davis\",\"Antarctica/DumontDUrville\":\"Antarctica/DumontDUrville\",\"Antarctica/Macquarie\":\"Antarctica/Macquarie\",\"Antarctica/Mawson\":\"Antarctica/Mawson\",\"Antarctica/McMurdo\":\"Antarctica/McMurdo\",\"Antarctica/Palmer\":\"Antarctica/Palmer\",\"Antarctica/Rothera\":\"Antarctica/Rothera\",\"Antarctica/Syowa\":\"Antarctica/Syowa\",\"Antarctica/Troll\":\"Antarctica/Troll\",\"Antarctica/Vostok\":\"Antarctica/Vostok\",\"Arctic/Longyearbyen\":\"Arctic/Longyearbyen\",\"Asia/Aden\":\"Asia/Aden\",\"Asia/Almaty\":\"Asia/Almaty\",\"Asia/Amman\":\"Asia/Amman\",\"Asia/Anadyr\":\"Asia/Anadyr\",\"Asia/Aqtau\":\"Asia/Aqtau\",\"Asia/Aqtobe\":\"Asia/Aqtobe\",\"Asia/Ashgabat\":\"Asia/Ashgabat\",\"Asia/Atyrau\":\"Asia/Atyrau\",\"Asia/Baghdad\":\"Asia/Baghdad\",\"Asia/Bahrain\":\"Asia/Bahrain\",\"Asia/Baku\":\"Asia/Baku\",\"Asia/Bangkok\":\"Asia/Bangkok\",\"Asia/Barnaul\":\"Asia/Barnaul\",\"Asia/Beirut\":\"Asia/Beirut\",\"Asia/Bishkek\":\"Asia/Bishkek\",\"Asia/Brunei\":\"Asia/Brunei\",\"Asia/Chita\":\"Asia/Chita\",\"Asia/Choibalsan\":\"Asia/Choibalsan\",\"Asia/Colombo\":\"Asia/Colombo\",\"Asia/Damascus\":\"Asia/Damascus\",\"Asia/Dhaka\":\"Asia/Dhaka\",\"Asia/Dili\":\"Asia/Dili\",\"Asia/Dubai\":\"Asia/Dubai\",\"Asia/Dushanbe\":\"Asia/Dushanbe\",\"Asia/Famagusta\":\"Asia/Famagusta\",\"Asia/Gaza\":\"Asia/Gaza\",\"Asia/Hebron\":\"Asia/Hebron\",\"Asia/Ho_Chi_Minh\":\"Asia/Ho_Chi_Minh\",\"Asia/Hong_Kong\":\"Asia/Hong_Kong\",\"Asia/Hovd\":\"Asia/Hovd\",\"Asia/Irkutsk\":\"Asia/Irkutsk\",\"Asia/Jakarta\":\"Asia/Jakarta\",\"Asia/Jayapura\":\"Asia/Jayapura\",\"Asia/Jerusalem\":\"Asia/Jerusalem\",\"Asia/Kabul\":\"Asia/Kabul\",\"Asia/Kamchatka\":\"Asia/Kamchatka\",\"Asia/Karachi\":\"Asia/Karachi\",\"Asia/Kathmandu\":\"Asia/Kathmandu\",\"Asia/Khandyga\":\"Asia/Khandyga\",\"Asia/Kolkata\":\"Asia/Kolkata\",\"Asia/Krasnoyarsk\":\"Asia/Krasnoyarsk\",\"Asia/Kuala_Lumpur\":\"Asia/Kuala_Lumpur\",\"Asia/Kuching\":\"Asia/Kuching\",\"Asia/Kuwait\":\"Asia/Kuwait\",\"Asia/Macau\":\"Asia/Macau\",\"Asia/Magadan\":\"Asia/Magadan\",\"Asia/Makassar\":\"Asia/Makassar\",\"Asia/Manila\":\"Asia/Manila\",\"Asia/Muscat\":\"Asia/Muscat\",\"Asia/Nicosia\":\"Asia/Nicosia\",\"Asia/Novokuznetsk\":\"Asia/Novokuznetsk\",\"Asia/Novosibirsk\":\"Asia/Novosibirsk\",\"Asia/Omsk\":\"Asia/Omsk\",\"Asia/Oral\":\"Asia/Oral\",\"Asia/Phnom_Penh\":\"Asia/Phnom_Penh\",\"Asia/Pontianak\":\"Asia/Pontianak\",\"Asia/Pyongyang\":\"Asia/Pyongyang\",\"Asia/Qatar\":\"Asia/Qatar\",\"Asia/Qostanay\":\"Asia/Qostanay\",\"Asia/Qyzylorda\":\"Asia/Qyzylorda\",\"Asia/Riyadh\":\"Asia/Riyadh\",\"Asia/Sakhalin\":\"Asia/Sakhalin\",\"Asia/Samarkand\":\"Asia/Samarkand\",\"Asia/Seoul\":\"Asia/Seoul\",\"Asia/Shanghai\":\"Asia/Shanghai\",\"Asia/Singapore\":\"Asia/Singapore\",\"Asia/Srednekolymsk\":\"Asia/Srednekolymsk\",\"Asia/Taipei\":\"Asia/Taipei\",\"Asia/Tashkent\":\"Asia/Tashkent\",\"Asia/Tbilisi\":\"Asia/Tbilisi\",\"Asia/Tehran\":\"Asia/Tehran\",\"Asia/Thimphu\":\"Asia/Thimphu\",\"Asia/Tokyo\":\"Asia/Tokyo\",\"Asia/Tomsk\":\"Asia/Tomsk\",\"Asia/Ulaanbaatar\":\"Asia/Ulaanbaatar\",\"Asia/Urumqi\":\"Asia/Urumqi\",\"Asia/Ust-Nera\":\"Asia/Ust-Nera\",\"Asia/Vientiane\":\"Asia/Vientiane\",\"Asia/Vladivostok\":\"Asia/Vladivostok\",\"Asia/Yakutsk\":\"Asia/Yakutsk\",\"Asia/Yangon\":\"Asia/Yangon\",\"Asia/Yekaterinburg\":\"Asia/Yekaterinburg\",\"Asia/Yerevan\":\"Asia/Yerevan\",\"Atlantic/Azores\":\"Atlantic/Azores\",\"Atlantic/Bermuda\":\"Atlantic/Bermuda\",\"Atlantic/Canary\":\"Atlantic/Canary\",\"Atlantic/Cape_Verde\":\"Atlantic/Cape_Verde\",\"Atlantic/Faroe\":\"Atlantic/Faroe\",\"Atlantic/Madeira\":\"Atlantic/Madeira\",\"Atlantic/Reykjavik\":\"Atlantic/Reykjavik\",\"Atlantic/South_Georgia\":\"Atlantic/South_Georgia\",\"Atlantic/St_Helena\":\"Atlantic/St_Helena\",\"Atlantic/Stanley\":\"Atlantic/Stanley\",\"Australia/Adelaide\":\"Australia/Adelaide\",\"Australia/Brisbane\":\"Australia/Brisbane\",\"Australia/Broken_Hill\":\"Australia/Broken_Hill\",\"Australia/Currie\":\"Australia/Currie\",\"Australia/Darwin\":\"Australia/Darwin\",\"Australia/Eucla\":\"Australia/Eucla\",\"Australia/Hobart\":\"Australia/Hobart\",\"Australia/Lindeman\":\"Australia/Lindeman\",\"Australia/Lord_Howe\":\"Australia/Lord_Howe\",\"Australia/Melbourne\":\"Australia/Melbourne\",\"Australia/Perth\":\"Australia/Perth\",\"Australia/Sydney\":\"Australia/Sydney\",\"Europe/Amsterdam\":\"Europe/Amsterdam\",\"Europe/Andorra\":\"Europe/Andorra\",\"Europe/Astrakhan\":\"Europe/Astrakhan\",\"Europe/Athens\":\"Europe/Athens\",\"Europe/Belgrade\":\"Europe/Belgrade\",\"Europe/Berlin\":\"Europe/Berlin\",\"Europe/Bratislava\":\"Europe/Bratislava\",\"Europe/Brussels\":\"Europe/Brussels\",\"Europe/Bucharest\":\"Europe/Bucharest\",\"Europe/Budapest\":\"Europe/Budapest\",\"Europe/Busingen\":\"Europe/Busingen\",\"Europe/Chisinau\":\"Europe/Chisinau\",\"Europe/Copenhagen\":\"Europe/Copenhagen\",\"Europe/Dublin\":\"Europe/Dublin\",\"Europe/Gibraltar\":\"Europe/Gibraltar\",\"Europe/Guernsey\":\"Europe/Guernsey\",\"Europe/Helsinki\":\"Europe/Helsinki\",\"Europe/Isle_of_Man\":\"Europe/Isle_of_Man\",\"Europe/Istanbul\":\"Europe/Istanbul\",\"Europe/Jersey\":\"Europe/Jersey\",\"Europe/Kaliningrad\":\"Europe/Kaliningrad\",\"Europe/Kiev\":\"Europe/Kiev\",\"Europe/Kirov\":\"Europe/Kirov\",\"Europe/Lisbon\":\"Europe/Lisbon\",\"Europe/Ljubljana\":\"Europe/Ljubljana\",\"Europe/London\":\"Europe/London\",\"Europe/Luxembourg\":\"Europe/Luxembourg\",\"Europe/Madrid\":\"Europe/Madrid\",\"Europe/Malta\":\"Europe/Malta\",\"Europe/Mariehamn\":\"Europe/Mariehamn\",\"Europe/Minsk\":\"Europe/Minsk\",\"Europe/Monaco\":\"Europe/Monaco\",\"Europe/Moscow\":\"Europe/Moscow\",\"Europe/Oslo\":\"Europe/Oslo\",\"Europe/Paris\":\"Europe/Paris\",\"Europe/Podgorica\":\"Europe/Podgorica\",\"Europe/Prague\":\"Europe/Prague\",\"Europe/Riga\":\"Europe/Riga\",\"Europe/Rome\":\"Europe/Rome\",\"Europe/Samara\":\"Europe/Samara\",\"Europe/San_Marino\":\"Europe/San_Marino\",\"Europe/Sarajevo\":\"Europe/Sarajevo\",\"Europe/Saratov\":\"Europe/Saratov\",\"Europe/Simferopol\":\"Europe/Simferopol\",\"Europe/Skopje\":\"Europe/Skopje\",\"Europe/Sofia\":\"Europe/Sofia\",\"Europe/Stockholm\":\"Europe/Stockholm\",\"Europe/Tallinn\":\"Europe/Tallinn\",\"Europe/Tirane\":\"Europe/Tirane\",\"Europe/Ulyanovsk\":\"Europe/Ulyanovsk\",\"Europe/Uzhgorod\":\"Europe/Uzhgorod\",\"Europe/Vaduz\":\"Europe/Vaduz\",\"Europe/Vatican\":\"Europe/Vatican\",\"Europe/Vienna\":\"Europe/Vienna\",\"Europe/Vilnius\":\"Europe/Vilnius\",\"Europe/Volgograd\":\"Europe/Volgograd\",\"Europe/Warsaw\":\"Europe/Warsaw\",\"Europe/Zagreb\":\"Europe/Zagreb\",\"Europe/Zaporozhye\":\"Europe/Zaporozhye\",\"Europe/Zurich\":\"Europe/Zurich\",\"Indian/Antananarivo\":\"Indian/Antananarivo\",\"Indian/Chagos\":\"Indian/Chagos\",\"Indian/Christmas\":\"Indian/Christmas\",\"Indian/Cocos\":\"Indian/Cocos\",\"Indian/Comoro\":\"Indian/Comoro\",\"Indian/Kerguelen\":\"Indian/Kerguelen\",\"Indian/Mahe\":\"Indian/Mahe\",\"Indian/Maldives\":\"Indian/Maldives\",\"Indian/Mauritius\":\"Indian/Mauritius\",\"Indian/Mayotte\":\"Indian/Mayotte\",\"Indian/Reunion\":\"Indian/Reunion\",\"Pacific/Apia\":\"Pacific/Apia\",\"Pacific/Auckland\":\"Pacific/Auckland\",\"Pacific/Bougainville\":\"Pacific/Bougainville\",\"Pacific/Chatham\":\"Pacific/Chatham\",\"Pacific/Chuuk\":\"Pacific/Chuuk\",\"Pacific/Easter\":\"Pacific/Easter\",\"Pacific/Efate\":\"Pacific/Efate\",\"Pacific/Enderbury\":\"Pacific/Enderbury\",\"Pacific/Fakaofo\":\"Pacific/Fakaofo\",\"Pacific/Fiji\":\"Pacific/Fiji\",\"Pacific/Funafuti\":\"Pacific/Funafuti\",\"Pacific/Galapagos\":\"Pacific/Galapagos\",\"Pacific/Gambier\":\"Pacific/Gambier\",\"Pacific/Guadalcanal\":\"Pacific/Guadalcanal\",\"Pacific/Guam\":\"Pacific/Guam\",\"Pacific/Honolulu\":\"Pacific/Honolulu\",\"Pacific/Kiritimati\":\"Pacific/Kiritimati\",\"Pacific/Kosrae\":\"Pacific/Kosrae\",\"Pacific/Kwajalein\":\"Pacific/Kwajalein\",\"Pacific/Majuro\":\"Pacific/Majuro\",\"Pacific/Marquesas\":\"Pacific/Marquesas\",\"Pacific/Midway\":\"Pacific/Midway\",\"Pacific/Nauru\":\"Pacific/Nauru\",\"Pacific/Niue\":\"Pacific/Niue\",\"Pacific/Norfolk\":\"Pacific/Norfolk\",\"Pacific/Noumea\":\"Pacific/Noumea\",\"Pacific/Pago_Pago\":\"Pacific/Pago_Pago\",\"Pacific/Palau\":\"Pacific/Palau\",\"Pacific/Pitcairn\":\"Pacific/Pitcairn\",\"Pacific/Pohnpei\":\"Pacific/Pohnpei\",\"Pacific/Port_Moresby\":\"Pacific/Port_Moresby\",\"Pacific/Rarotonga\":\"Pacific/Rarotonga\",\"Pacific/Saipan\":\"Pacific/Saipan\",\"Pacific/Tahiti\":\"Pacific/Tahiti\",\"Pacific/Tarawa\":\"Pacific/Tarawa\",\"Pacific/Tongatapu\":\"Pacific/Tongatapu\",\"Pacific/Wake\":\"Pacific/Wake\",\"Pacific/Wallis\":\"Pacific/Wallis\",\"UTC\":\"UTC\"}','0','0','large','0','select_default_time_zone','required',187,'select_default_time_zone','Asia/Kolkata','general setting','2021-11-11 17:49:38',1),
      (63,'is_mainteness_mode_on','dropdown','{\"YES\":\"YES\",\"NO\":\"NO\"}','0','0','large','0','mainteness_mode_on','required',200,'mainteness_mode_on','NO','general setting','2021-11-11 17:49:38',1),
      (64,'mainteness_mode_contant','textarea',NULL,'0','1','large','0','mainteness_mode_contant','required',200,'mainteness_mode_contant','<article><div><h1>We’ll be back soon!</h1><p>Sorry for the inconvenience but we’re performing some maintenance at the moment. If you need to you can always <a>contact us</a>, otherwise we’ll be back online shortly!</p>— The Team</p></p></div></article>','general setting','2021-11-11 17:49:38',1),
      (65,'popular_quiz_show_on_home','dropdown','{\"YES\":\"YES\",\"NO\":\"NO\"}','0','0','large','0','popular_quiz_show_on_home','required',189,'popular_quiz_show_on_home','YES','home page contant','2021-11-11 17:49:38',1),
      (66,'latest_quiz_show_on_home','dropdown','{\"YES\":\"YES\",\"NO\":\"NO\"}','0','0','large','0','latest_quiz_show_on_home','required',189,'latest_quiz_show_on_home','YES','home page contant','2021-11-11 17:49:38',1),
      (68,'show_home_page_latest_study_material','dropdown','{\"YES\":\"YES\",\"NO\":\"NO\"}','0','0','large','0','show_home_page_latest_study_material','required',189,'show_home_page_latest_study_material','YES','home page contant','2021-11-11 17:49:38',1),
      (69,'show_home_page_popular_study_material','dropdown','{\"YES\":\"YES\",\"NO\":\"NO\"}','0','0','large','0','show_home_page_popular_study_material','required',189,'show_home_page_popular_study_material','YES','home page contant','2021-11-11 17:49:38',1),
      (70,'show_home_page_testimonial','dropdown','{\"YES\":\"YES\",\"NO\":\"NO\"}','0','0','large','0','show_home_page_testimonial','required',189,'show_home_page_testimonial','NO','home page contant','2021-11-11 17:49:38',1),
      (71,'show_home_page_sponser','dropdown','{\"YES\":\"YES\",\"NO\":\"NO\"}','0','0','large','0','show_home_page_sponser','required',189,'show_home_page_sponser','YES','home page contant','2021-11-11 17:49:38',1),
      (72,'update_info','hidden','{\"purchase_code\":\"\",\"current_version_code\":\"2\",\"current_version_name\":\"4.2.6\",\"update_description\":\"4.2.6 version inital\",\"last_updated\":\"2021-11-15 18:31:47\",\"purchase_code_updated\":false,\"is_verified\":false,\"message\":\"\",\"next_version\":\"[]\",\"next_version_data\":\"[\\\"next_version_url_1\\\",\\\"next_version_url_2\\\"]\",\"added\":\"2021-11-15 18:31:47\",\"updated\":\"2021-11-15 18:31:47\"}','0','0',NULL,'0','update_info','',10,'update_info','{\"current_version_code\":15,\"current_version_name\":\"4.2.6\",\"purchase_code\":\"\",\"purchase_code_updated\":false,\"is_verified\":false,\"next_version_name\":\"\",\"next_version_description\":\"\",\"next_version_all_data\":\"[]\",\"next_version_zip_urls\":\"[]\",\"next_version_all_in_one_zip\":\"\",\"last_updated\":\"2021-11-12 17:59:04\",\"message\":\"You are on Latest Version\",\"added\":\"2021-11-12 12:28:29\",\"updated\":\"2021-11-12 17:59:04\"}','hidden','2021-02-10 11:57:13',1),
      (73,'ads_display_as','dropdown','{\"ALL\":\"ALL\",\"RANDOM\":\"RANDOM\"}','0','0','large','0','ads_display_as','required',189,'ads_display_as','RANDOM','display setting','2021-11-11 17:49:38',1),
      (74,'study_material_manage_button_on_box','dropdown','{\"NO\":\"NO\",\"YES\":\"YES\"}','0','0','large','0','study_material_manage_button_on_box','required',1,'study_material_manage_button_on_box','YES','display setting','2021-11-11 17:49:38',1),
      (75,'study_material_display_stats_icons','dropdown','{\"NO\":\"NO\",\"YES\":\"YES\"}','0','0','large','0','study_material_display_stats_icons','required',2,'study_material_display_stats_icons','NO','display setting','2021-11-11 17:49:38',1),
      (76,'quiz_display_stats_icons','dropdown','{\"NO\":\"NO\",\"YES\":\"YES\"}','0','0','large','0','quiz_display_stats_icons','required',7,'quiz_display_stats_icons','NO','display setting','2021-11-11 17:49:38',1),
      (77,'study_material_display_social_share_icons','dropdown','{\"NO\":\"NO\",\"YES\":\"YES\"}','0','0','large','0','study_material_display_social_share_icons','required',3,'study_material_display_social_share_icons','NO','display setting','2021-11-11 17:49:38',1),
      (78,'quiz_display_social_share_icons','dropdown','{\"NO\":\"NO\",\"YES\":\"YES\"}','0','0','large','0','quiz_display_social_share_icons','required',8,'quiz_display_social_share_icons','YES','display setting','2021-11-11 17:49:38',1),
      (79,'study_material_box_contant_first_icons','dropdown','{\"docs\":\"docs\",\"video\":\"video\",\"audio\":\"audio\",\"images\":\"images\",\"pdf\":\"pdf\",\"other\":\"other\"}','0','0','large','0','study_material_box_contant_first_icons','required',4,'study_material_box_contant_first_icons','images','display setting','2021-11-11 17:49:38',1),
      (80,'study_material_box_contant_second_icons','dropdown','{\"docs\":\"docs\",\"video\":\"video\",\"audio\":\"audio\",\"images\":\"images\",\"pdf\":\"pdf\",\"other\":\"other\"}','0','0','large','0','study_material_box_contant_second_icons','required',5,'study_material_box_contant_second_icons','video','display setting','2021-11-11 17:49:38',1),
      (81,'study_material_box_contant_thrird_icons','dropdown','{\"docs\":\"docs\",\"video\":\"video\",\"audio\":\"audio\",\"images\":\"images\",\"pdf\":\"pdf\",\"other\":\"other\"}','0','0','large','0','study_material_box_contant_thrird_icons','required',6,'study_material_box_contant_thrird_icons','audio','display setting','2021-11-11 17:49:38',1),
      (82,'study_material_social_share_icons','dropdown','{\"NO\":\"NO\",\"YES\":\"YES\"}','0','0','large','0','study_material_social_share_icons','required',2,'study_material_social_share_icons','NO','display setting','2021-11-11 17:49:38',1),
      (83,'tutor_can_see_user_list','dropdown','{\"NO\":\"NO\",\"YES\":\"YES\"}','0','0','large','0','tutor_can_see_user_list','required',2,'tutor_can_see_user_list','NO','display setting','2021-11-11 17:49:38',1),
      (84,'study_material_view_index_button_on_box','dropdown','{\"NO\":\"NO\",\"YES\":\"YES\"}','0','0','large','0','study_material_view_index_button_on_box','required',1,'study_material_view_index_button_on_box','NO','display setting','2021-11-11 17:49:38',1),
      (85,'set_default_theme_in_dark_mode','dropdown','{\"NO\":\"NO\",\"YES\":\"YES\"}','0','0','large','0','set_default_theme_in_dark_mode','required',1,'set_default_theme_in_dark_mode','NO','display setting','2021-11-11 17:49:38',1),
      (140,'quizzy_reward_animation_image','file',NULL,'0','0','large','0','quizzy_reward_animation_image','',90,'quizzy_reward_animation_image','16213958991.gif','general setting','2021-05-19 09:15:00',1),
      (175,'cookies_content','textarea',NULL,'0','1','large','0','cookies_content','',200,'cookies_content','<span xss=removed>Do you like cookies</span><span xss=removed> . We use cookies </span><a href=\"https://cookiesandyou.com/\" target=\"_blank\">Learn more</a><br>','cookies content','2021-11-11 17:49:38',1),
      (176,'cookies_content_btn_text','input',NULL,'0','0','large','0','cookies_content_btn_text','',200,'cookies_content_btn_text','I Agree','cookies content','2021-11-11 17:49:38',1),
      (177,'cookies_content_display','dropdown','{\"NO\":\"NO\",\"YES\":\"YES\"}','0','0','large','0','cookies_content_display','required',2,'cookies_content_display','YES','cookies content','2021-11-11 17:49:38',1),
      (178,'is_compatible','dropdown','{\"NORMAL\":\"NORMAL\",\"MOBILE\":\"MOBILE\"}','0','0',NULL,'0','is_compatible_used_for_mobile_view_on_test_page_if_choose_yes','',NULL,'is_compatible','NORMAL','general setting','2021-11-11 17:49:38',1),
      (179,'file_uploader','dropdown','{\"default uploader\":\"default uploader\",\"media manager\":\"media manager\"}','0','0',NULL,'0','file_uploader_help','',NULL,'file_uploader','default uploader','general setting','2021-11-11 17:49:38',1),
      (180,'upcoming_quiz_show_on_home','dropdown','{\"YES\":\"YES\",\"NO\":\"NO\"}','0','0','large','0','upcoming_quiz_show_on_home_help','required',NULL,'upcoming_quiz_show_on_home','YES','home page contant','2021-11-11 17:49:38',1),
      (181,'display_countdown_before_starting_quiz','input',NULL,'0','0',NULL,'0','enter_only_number','required',NULL,'display_countdown_before_starting_quiz','2','general setting','2021-11-11 17:49:38',1),
      (182,'is_sticky_header','dropdown','{\"YES\":\"YES\",\"NO\":\"NO\"}','0','0','large','0','sticky_header_used_for_apply_sticky_menubar_which_is_fixed_for_webpage','',NULL,'is_sticky_header','YES','general setting','2021-11-11 17:49:38',1),
      (183,'footer_text','textarea',NULL,'0','1','large','0','footer_text_help','',NULL,'footer_text','© 2021 copyright all rights reserved&nbsp;','general setting','2021-11-11 17:49:38',1),
      (184,'pages_list','dropdown','{\"\":\"None\",\"about\":\"About\",\"terms-condition\":\"Terms & Condition\",\"privacy-policy\":\"Privacy Policy\"}','0','0','large','0','pages_list_help','',NULL,'choose_landing_page','about','home page contant','2021-11-11 17:49:38',1),
      (199,'sub_category_show_or_not','dropdown','{\"Enable\":\"Enable\",\"Disable\":\"Disable\"}','0','0','large','0','sub_category_show_or_hide','',26,'sub_category_display_hide','Enable','display setting','2021-11-11 17:49:38',1),
      (200,'mail_template_header_image','file',NULL,'0','0','large','0','upload_mail_template_header_image','',88,'mail_header_image','1636187487headertemplate.png','mail setting','2021-11-06 14:01:27',1),
      (201,'mail_template_footer_image','file',NULL,'0','0','large','0','upload_mail_template_footer_image','',89,'mail_footer_image','1636187487footertemplate.png','mail setting','2021-11-06 14:01:27',1),
      (202,'answered_question','dropdown','{\"Enable\":\"Enable\",\"Disable\":\"Disable\"}','0','0','medium','0','answered_question_show_or_not_on_test_page','',12,'answered_question_on_test','Enable','quiz setting','2021-09-22 17:28:46',1),
      (203,'answered_question_color_code','input',NULL,'0','0','medium','0','set_answered_question_palette_color_code_like_#000','',13,'color_code_for_answered_question','#FF0000','quiz setting','2021-09-22 17:28:46',1),
      (204,'not_answered_question','dropdown','{\"Enable\":\"Enable\",\"Disable\":\"Disable\"}','0','0','medium','0','not_answered_question_show_or_not_on_test_page','',14,'not_answered_question_on_question_palette','Enable','quiz setting','2021-09-22 17:28:46',1),
      (205,'not_answered_question_color_code','input',NULL,'0','0','medium','0','set_not_answered_question_palette_color_code_like_#FF0000_this_color_code_of_red','',15,'color_code_for_not_answered_question','','quiz setting','2021-09-22 17:28:46',1),
      (206,'total_attempt_show_or_not','dropdown','{\"Enable\":\"Enable\",\"Disable\":\"Disable\"}','0','0','medium','0','total_attempt_hide_or_show_on_question_palette_test_page','',16,'total_attempt_hide_or_show','Disable','quiz setting','2021-09-22 17:28:46',1),
      (207,'total_attempt_color_code','input',NULL,'0','0','medium','0','set_color_code_for_total_attempt_on_question_palette_color_code_like_#000','',17,'color_code_for_total_attempt_on_test_page','#888','quiz setting','2021-09-22 17:28:46',1),
      (208,'not_visit_show_or_not','dropdown','{\"Enable\":\"Enable\",\"Disable\":\"Disable\"}','0','0','medium','0','not_visit_hide_or_show_on_test_page','',18,'not_visit_button_hide_or_show_on_testpage','Enable','quiz setting','2021-09-22 17:28:46',1),
      (209,'not_visit_color_code','input',NULL,'0','0','medium','0','set_color_code_for_not_visit_button_on_testpage_color_code_like_#000','',19,'color_code_of_not_visit','','quiz setting','2021-09-22 17:28:46',1),
      (210,'mark_for_review_show_or_not','dropdown','{\"Enable\":\"Enable\",\"Disable\":\"Disable\"}','0','0','medium','0','mark_for_review_hide_or_show_on_testpage','',20,'mark_for_review_hide_or_show','Enable','quiz setting','2021-09-22 17:28:46',1),
      (211,'mark_for_review_color_code','input',NULL,'0','0','medium','0','set_color_code_for_mark_for_review_on_testpage_color_code_like_#000','',21,'color_code_for_mark_for_review','','quiz setting','2021-09-22 17:28:46',1),
      (212,'mark_for_answer_show_or_not','dropdown','{\"Enable\":\"Enable\",\"Disable\":\"Disable\"}','0','0','medium','0','mark_for_answer_show_or_hide_on_testpage','',22,'mark_for_answer_hide_or_show','Enable','quiz setting','2021-09-22 17:28:46',1),
      (213,'mark_for_answer_color_code','input',NULL,'0','0','medium','0','set_color_code_for_mark_for_answer_on_testpage_color_code_like_#000','',23,'color_code_for_mark_for_answer','#00008B','quiz setting','2021-09-22 17:28:47',1),
      (214,'previous_disable_note','input',NULL,'0','0','large','0','previous_button_disable_on_test_page','',24,'note_for_previous_question_not_appear_when_you_goto_next_question','If you click on next then you can not go back','quiz setting','2021-09-22 17:28:47',1),
      (215,'mark_for_review_and_next','dropdown','{\"Enable\":\"Enable\",\"Disable\":\"Disable\"}','0','0','large','0','mark_for_review_and_next_show_or_hide','',25,'show_and_hide_mark_for_review_and_next_on_test_page','Enable','quiz setting','2021-09-22 17:28:47',1);
    
    /*!40000 ALTER TABLE `settings` ENABLE KEYS */;
    UNLOCK TABLES;
    
    
    # Dump of table sponsors
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `sponsors`;
    
    CREATE TABLE `sponsors` (
      `id` int NOT NULL AUTO_INCREMENT,
      `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `logo` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `link` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `added` timestamp NULL DEFAULT NULL,
      `updated` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    LOCK TABLES `sponsors` WRITE;
    /*!40000 ALTER TABLE `sponsors` DISABLE KEYS */;
    
    INSERT INTO `sponsors` (`id`, `name`, `logo`, `link`, `added`, `updated`)
    VALUES
      (1,'Company 1','logo-08.png','#','2020-07-17 22:49:40','2020-07-22 03:28:23'),
      (2,'Company 2','logo-07.png','#','2020-07-18 23:34:57','2020-07-22 03:28:35'),
      (3,'Company 3','logo-06.png','#','2020-07-18 23:35:11','2020-07-22 03:28:45'),
      (4,'Company 4','logo-05.png','#','2020-07-18 23:35:39','2020-07-22 03:28:56'),
      (5,'Company 5','logo-04.png','#','2020-07-18 23:39:00','2020-07-22 03:29:12'),
      (6,'Company 6','logo-03.png','#','2020-07-22 03:29:33',NULL),
      (7,'Company 7','logo-02.png','#','2020-07-22 03:29:45',NULL),
      (8,'Company 8','logo-01.png','#','2020-07-22 03:29:56',NULL);
    
    /*!40000 ALTER TABLE `sponsors` ENABLE KEYS */;
    UNLOCK TABLES;
    
    
    # Dump of table study_material
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `study_material`;
    
    CREATE TABLE `study_material` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `category_id` int unsigned DEFAULT NULL,
      `user_id` int unsigned DEFAULT NULL,
      `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `status` tinyint DEFAULT '1',
      `price` int unsigned DEFAULT NULL,
      `is_registered` tinyint DEFAULT '0' COMMENT '0= not registered,1=registered',
      `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `is_premium` tinyint DEFAULT NULL COMMENT '0=not premium,1=premium',
      `meta_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `meta_keywords` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `meta_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `media_file` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table study_material_content
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `study_material_content`;
    
    CREATE TABLE `study_material_content` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `study_material_id` int unsigned DEFAULT NULL,
      `section_id` int DEFAULT '0',
      `material_order` int DEFAULT '0',
      `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `type` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `added` datetime DEFAULT NULL,
      `updated` datetime DEFAULT NULL,
      `size` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `duration` int DEFAULT '0',
      `is_media_file` tinyint DEFAULT '0',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table study_material_like
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `study_material_like`;
    
    CREATE TABLE `study_material_like` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `study_material_id` int unsigned DEFAULT NULL,
      `user_id` int unsigned DEFAULT NULL,
      `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table study_material_user_history
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `study_material_user_history`;
    
    CREATE TABLE `study_material_user_history` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `user_id` int DEFAULT '0',
      `s_m_id` int DEFAULT '0',
      `s_m_section_id` int DEFAULT '0',
      `s_m_contant_id` int DEFAULT '0',
      `comlete_on` datetime DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
    
    
    
    # Dump of table study_material_user_histry
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `study_material_user_histry`;
    
    CREATE TABLE `study_material_user_histry` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `study_material_id` int DEFAULT '0',
      `user_id` int DEFAULT '0',
      `enroll_date` datetime DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table study_material_view
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `study_material_view`;
    
    CREATE TABLE `study_material_view` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `study_material_id` int unsigned DEFAULT NULL,
      `ip_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `user_agent` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table study_quiz
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `study_quiz`;
    
    CREATE TABLE `study_quiz` (
      `study_material_id` int DEFAULT '0',
      `quiz_id` int unsigned DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table study_section
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `study_section`;
    
    CREATE TABLE `study_section` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `study_material_id` int DEFAULT '0',
      `title` varchar(255) DEFAULT NULL,
      `slug` varchar(255) DEFAULT NULL,
      `order` int DEFAULT '0',
      `added` datetime DEFAULT NULL,
      `updated` datetime DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
    
    
    
    # Dump of table testimonial
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `testimonial`;
    
    CREATE TABLE `testimonial` (
      `id` int NOT NULL AUTO_INCREMENT,
      `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `profile` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `added` timestamp NULL DEFAULT NULL,
      `updated` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    LOCK TABLES `testimonial` WRITE;
    /*!40000 ALTER TABLE `testimonial` DISABLE KEYS */;
    
    INSERT INTO `testimonial` (`id`, `name`, `profile`, `content`, `added`, `updated`)
    VALUES
      (1,'John Doe','1595348622User-2.jpg','<p><strong xss=\"removed\">Lorem Ipsum</strong><span xss=\"removed\"> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. </span><br></p>','2020-07-15 20:18:36','2020-07-22 03:27:14'),
      (2,'Peter Samson','1595348636User-1.jpg','<p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged<br></p>','2020-07-20 17:21:17','2020-07-22 03:27:19'),
      (3,'Alexandra Anderson','1595348801user-3.jpeg','<p><strong xss=\"removed\">Lorem Ipsum</strong><span xss=\"removed\"> is simply dummy text of the printing and typesetting industry.  It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged</span><br></p>','2020-07-20 17:21:49','2020-07-22 03:27:27'),
      (4,'Amanda Cameron','1595348821user-4.jpeg','<p><img src=\"http://localhost/quiz-gboss/assets/images/wysiwyg/1602507207_mechanical.jpg\">Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.<br></p>','2020-07-20 17:22:10','2020-10-12 23:53:30');
    
    /*!40000 ALTER TABLE `testimonial` ENABLE KEYS */;
    UNLOCK TABLES;
    
    
    # Dump of table translations
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `translations`;
    
    CREATE TABLE `translations` (
      `id` int NOT NULL AUTO_INCREMENT,
      `table` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `forigen_table_id` int DEFAULT NULL,
      `lang_id` int DEFAULT NULL,
      `column` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `value` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table user_membership_payment
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `user_membership_payment`;
    
    CREATE TABLE `user_membership_payment` (
      `user_id` int unsigned DEFAULT NULL,
      `membership_id` int unsigned DEFAULT NULL,
      `payment_id` int unsigned DEFAULT NULL,
      `purchased` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `validity` date DEFAULT NULL,
      `category_id` int DEFAULT '0'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table user_questions
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `user_questions`;
    
    CREATE TABLE `user_questions` (
      `id` int NOT NULL AUTO_INCREMENT,
      `user_id` int DEFAULT NULL,
      `participant_id` int DEFAULT NULL,
      `question_id` int DEFAULT NULL,
      `question` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `choices` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `correct_choice` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `given_answer` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `is_correct` tinyint DEFAULT NULL,
      `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `upload_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'image',
      `question_type_is_match` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'NO',
      `question_paragraph_text` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `queston_choies_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'choices',
      `question_section_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'GENRAL QUESTIONSGENRAL QUESTIONS',
      `addon_type` tinyint DEFAULT '0',
      `addon_value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    # Dump of table users
    # ------------------------------------------------------------
    
    DROP TABLE IF EXISTS `users`;
    
    CREATE TABLE `users` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `username` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `first_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `last_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `password` char(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `salt` char(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `email` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `image` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
      `language` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `is_admin` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0',
      `status` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '1',
      `deleted` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0',
      `validation_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Temporary code for opt-in registration',
      `created` datetime DEFAULT NULL,
      `updated` datetime DEFAULT NULL,
      `token` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `auth_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `login_from` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
      `role` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'user',
      `last_access` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `time_accommodation` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
      `course_id` int DEFAULT '0',
      `institution_id` int DEFAULT '0',
      `user_qualification_experience` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
      `user_request_for_tutor` tinyint DEFAULT '0',
      PRIMARY KEY (`id`),
      UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
    
    
    
    
    
    INSERT INTO `users` (`username`, `first_name`, `last_name`, `password`, `salt`, `email`, `image`, `language`, `is_admin`, `status`, `deleted`, `validation_code`, `created`, `updated`, `token`, `auth_id`, `login_from`, `role`, `last_access`)
    VALUES
        ('install_users_username','install_admin_first_name','install_admin_last_name','install_users_password','install_users_salt','install_admin_email_address','','English','1','1','0',NULL,'install_admin_created','0000-00-00 00:00:00',NULL,NULL,NULL,'admin','0000-00-00 00:00:00');
    
    /*!40000 ALTER TABLE `users` ENABLE KEYS */;
    UNLOCK TABLES;
    
    
    
    /*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
    /*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
    /*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
    /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
    /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
    /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";

    // $sql = file_get_contents('database.sql');
    //set admin information to database
    $now = date('Y-m-d H:i:s');
    $salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
    $password = hash('sha512', $login_password . $salt);

    $sql = str_replace('install_users_username', $username, $sql);
    $sql = str_replace('install_admin_first_name', $first_name, $sql);
    $sql = str_replace('install_admin_last_name', $last_name, $sql);
    $sql = str_replace('install_admin_email_address', $email, $sql);
    $sql = str_replace('install_users_password', $password, $sql);
    $sql = str_replace('install_users_salt', $salt, $sql);
    $sql = str_replace('install_admin_created', $now, $sql);
    //create tables in datbase

    $mysqli->multi_query($sql);

    do {
    } while (mysqli_more_results($mysqli) && mysqli_next_result($mysqli));

    if ($mysqli->error) {
        echo json_encode(['success' => false, 'message' => $mysqli->error]);
        exit();
    }

    $mysqli->close();
    // database created
    // set the database config file
    $db_file = str_replace('install_enter_hostname', $host, $db_file);
    $db_file = str_replace('install_enter_db_username', $dbuser, $db_file);
    $db_file = str_replace('install_enter_db_password', $dbpassword, $db_file);
    $db_file = str_replace('install_enter_database_name', $dbname, $db_file);
    file_put_contents($db_file_path, $db_file);

    // set random enter_encryption_key
    $config_file_path = '../application/config/config.php';
    $encryption_key = substr(md5(rand()), 0, 15);
    $config_file = file_get_contents($config_file_path);
    $config_file = str_replace('install_enter_encryption_key', $encryption_key, $config_file);

    $base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http');
    $base_url .= '://' . $_SERVER['HTTP_HOST'];
    $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    $base_url = str_replace('/install/', '/', $base_url);

    $config_file = str_replace('install_enter_base_url_here', $base_url, $config_file);
    file_put_contents($config_file_path, $config_file);
    // set the environment = production

    $index_file_path = '../index.php';

    $index_file = file_get_contents($index_file_path);
    $index_file = preg_replace('/pre_installation/', 'production', $index_file, 1); //replace the first occurence of 'pre_installation'
    file_put_contents($index_file_path, $index_file);

    echo json_encode(['success' => true, 'message' => 'Installation successfull.']);
    exit();
}
