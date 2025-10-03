-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 29, 2025 at 07:26 PM
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
-- Database: `pet_grooming`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `username` varchar(500) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(100) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(500) NOT NULL,
  `gender` varchar(500) NOT NULL,
  `dob` text NOT NULL,
  `contact` text NOT NULL,
  `address` varchar(500) NOT NULL,
  `image` varchar(2000) NOT NULL,
  `created_on` date NOT NULL,
  `role` varchar(11) NOT NULL,
  `bank_name` text NOT NULL,
  `acc_name` text NOT NULL,
  `acc_no` text NOT NULL,
  `amount` float NOT NULL,
  `total_amount` varchar(150) NOT NULL,
  `app_code` int(11) NOT NULL,
  `created_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_status` int(11) NOT NULL,
  `admin_user` int(11) NOT NULL,
  `project` int(50) DEFAULT NULL,
  `gstin` varchar(600) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id`, `role_id`, `username`, `email`, `password`, `fname`, `lname`, `gender`, `dob`, `contact`, `address`, `image`, `created_on`, `role`, `bank_name`, `acc_name`, `acc_no`, `amount`, `total_amount`, `app_code`, `created_date_time`, `delete_status`, `admin_user`, `project`, `gstin`) VALUES
(1, 0, 'mayurik', 'mdkhairnar92@gmail.com', 'aa7f019c326413d5b8bcad4314228bcd33ef557f5d81c7cc977f7728156f4357', 'Mayuri', 'K', 'Female', '', '+919529230459', 'Pawfect Grooming Studio,Shop No. 9, Bella Vista Arcade, Banjara Hills,Hyderabad, Telangana – 500034                                                                                                            ', '2.jpg', '2018-04-30', 'admin', 'Bank', 'Admin', '1111', 168276, '', 27057, '2025-08-29 17:26:18', 0, 1, 0, '27BEAAAACCCCC84K1ZR'),
(9, 2, '', 'ravi.sharma@example.com', '68207a305fdb9fc2d93ef80182e8cf91ca36c71338890efde351066dd2d218a9', 'Ravi	', 'Sharma', '', '', '9876543210', '101, MG Road, Pune, Maharashtra', '', '0000-00-00', 'admin', '', '', '', 0, '', 0, '2025-05-01 11:58:11', 0, 0, NULL, ''),
(10, 2, '', 'priya.mehta@example.com', '68207a305fdb9fc2d93ef80182e8cf91ca36c71338890efde351066dd2d218a9', 'Priya', '	Mehta', '', '', '9123456780', '12, Lake View Apartments, Mumbai', '', '0000-00-00', 'admin', '', '', '', 0, '', 0, '2025-05-01 11:59:27', 0, 0, NULL, ''),
(11, 2, '', 'aarav.patil@example.com', '68207a305fdb9fc2d93ef80182e8cf91ca36c71338890efde351066dd2d218a9', 'Aarav		', 'Patil', '', '', '9988776655', '5, Shivaji Nagar, Nashik', '', '0000-00-00', 'admin', '', '', '', 0, '', 0, '2025-05-01 12:00:23', 0, 0, NULL, ''),
(12, 2, '', 'sneha.joshi@example.com', '68207a305fdb9fc2d93ef80182e8cf91ca36c71338890efde351066dd2d218a9', 'Sneha				', 'Joshi', '', '', '9345612789', '22, Raj Palace, Nagpur', '', '0000-00-00', 'admin', '', '', '', 0, '', 0, '2025-05-01 12:01:08', 0, 0, NULL, ''),
(13, 2, '', 'aman@gmail.com', '68207a305fdb9fc2d93ef80182e8cf91ca36c71338890efde351066dd2d218a9', 'Aman		', 'Khan', '', '', '9001234567', '77, Green Park, Aurangabad', '', '0000-00-00', 'admin', '', '', '', 0, '', 0, '2025-05-01 12:02:25', 0, 0, NULL, ''),
(14, 2, '', 'aman@gmail.com', 'ffc5e558b8c73e7dfa74208684fd8a5c368cf40f67931def94fe16bbe1780cee', 'Aman ', 'Mehta', '', '', '9899516188', 'Akshya Nagar 1st Block 1st Cross, Rammurthy nagar, Bangalore-560016', '', '0000-00-00', 'admin', '', '', '', 0, '', 0, '2025-05-02 07:50:16', 0, 0, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer`
--

CREATE TABLE `tbl_customer` (
  `cust_id` int(11) NOT NULL,
  `cust_name` varchar(50) NOT NULL,
  `cust_mob` bigint(10) NOT NULL,
  `cust_email` varchar(50) NOT NULL,
  `cust_address` varchar(50) NOT NULL,
  `state` varchar(250) NOT NULL,
  `gstin` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customer`
--

INSERT INTO `tbl_customer` (`cust_id`, `cust_name`, `cust_mob`, `cust_email`, `cust_address`, `state`, `gstin`) VALUES
(1, 'Rajesh Sharma', 7987654321, 'rajesh.sharma@email.com', '123 MG Road, Mumbai, MH', '22', '27AAECR1234F1Z2'),
(2, 'Priya Verma', 9823456789, 'priya.verma@email.com', '56 Park Street, Kolkata, WB', '22', '19AAECP5678K1Z3'),
(3, 'Arjun Patel', 9898123457, 'arjun.patel@email.com', '78 CG Road, Ahmedabad, GJ', '12', '24AADCP7890M1Z1	'),
(4, 'Neha Nair', 9845032107, 'neha.nair@email.com', '102 MG Road, Bangalore, KA', '17', '29AABCN3456P1Z4'),
(5, 'Rohan Gupta', 9812345670, 'rohan.gupta@email.com', '45 Connaught Place, Delhi', '10', '07AACCR2345B1Z5	'),
(6, 'Kavita Joshi', 7775467892, 'kavita.joshi@email.com', '89 Civil Lines, Jaipur, RJ', '33', ''),
(7, 'Ankit Singh', 7987675432, 'ankit.singh@email.com', '210 Gomti Nagar, Lucknow, UP', '38', '09AAACS5678J1Z7'),
(8, 'Sneha Rao', 7980123456, 'sneha.rao@email.com', '67 Begumpet, Hyderabad, TS', '36', '36AABCD6789T1Z8'),
(9, 'Manish Das', 7983234567, 'manish.das@email.com', '34 Ballygunge, Kolkata, WB', '41', '19AAECM1234L1Z9'),
(10, 'Simran Kaur', 7881045678, 'simran.kaur@email.com', '77 Sector 17, Chandigarh, CH', '6', '04AABCK7890Y1Z0'),
(11, 'Suresh Kumar', 1324322323, 'ss@gmail.com', 'Pune', '22', '2345');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_groups`
--

CREATE TABLE `tbl_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(300) NOT NULL,
  `delete_status` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_groups`
--

INSERT INTO `tbl_groups` (`id`, `name`, `description`, `delete_status`) VALUES
(1, 'admin', 'admin', 0),
(2, 'user', 'user', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_installement`
--

CREATE TABLE `tbl_installement` (
  `id` int(11) NOT NULL,
  `added_date` date NOT NULL,
  `inv_no` varchar(200) NOT NULL,
  `insta_amt` int(100) NOT NULL,
  `due_total` int(11) NOT NULL,
  `ptype` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_installement`
--

INSERT INTO `tbl_installement` (`id`, `added_date`, `inv_no`, `insta_amt`, `due_total`, `ptype`) VALUES
(1, '2025-05-14', '1', 1200, 212, 1),
(2, '2025-05-14', '2', 500, 904, 1),
(3, '2025-05-27', '3', 1000, 200, 1),
(4, '2025-08-24', '4', 2500, 93, 1),
(5, '2025-08-24', '4', 4, 89, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_invoice`
--

CREATE TABLE `tbl_invoice` (
  `id` int(100) NOT NULL,
  `build_date` date NOT NULL,
  `inv_no` varchar(50) NOT NULL,
  `user` varchar(100) NOT NULL,
  `customer_id` varchar(200) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '0:order,1:estimate',
  `subtotal` double(10,2) NOT NULL,
  `discount` double(10,2) NOT NULL,
  `final_total` double(10,2) NOT NULL,
  `advance_total` int(100) NOT NULL,
  `due_total` int(100) NOT NULL,
  `paid_amt` int(100) NOT NULL,
  `ptype` int(50) NOT NULL,
  `delete_status` tinyint(4) NOT NULL,
  `currentdatetime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_date` date DEFAULT NULL,
  `due_date` date NOT NULL DEFAULT current_timestamp(),
  `gst_rate` double(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_invoice`
--

INSERT INTO `tbl_invoice` (`id`, `build_date`, `inv_no`, `user`, `customer_id`, `status`, `subtotal`, `discount`, `final_total`, `advance_total`, `due_total`, `paid_amt`, `ptype`, `delete_status`, `currentdatetime`, `created_date`, `due_date`, `gst_rate`) VALUES
(1, '2025-05-14', '1', '10', '2', 0, 1412.40, 0.00, 1412.40, 1200, 212, 1200, 1, 0, '2025-05-14 11:29:24', '2025-05-14', '2025-05-26', 0.00),
(2, '2025-05-14', '2', '11', '3', 0, 1404.00, 0.00, 1404.00, 500, 904, 500, 1, 0, '2025-05-14 11:28:24', '2025-05-14', '2025-06-04', 0.00),
(3, '2025-05-27', '3', '9', '1', 0, 1200.00, 0.00, 1200.00, 1000, 200, 1000, 1, 0, '2025-05-27 07:41:42', '2025-05-27', '2025-05-31', 0.00),
(4, '2025-08-24', '4', '10', '10', 0, 2593.20, 0.00, 2593.20, 2500, 89, 2504, 1, 0, '2025-08-23 18:33:38', '2025-08-24', '2025-08-18', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_manage_website`
--

CREATE TABLE `tbl_manage_website` (
  `id` int(11) NOT NULL,
  `title` varchar(600) NOT NULL,
  `short_title` varchar(600) NOT NULL,
  `logo` text NOT NULL,
  `footer` text NOT NULL,
  `currency_code` varchar(600) NOT NULL,
  `currency_symbol` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `login_logo` text NOT NULL,
  `invoice_logo` text NOT NULL,
  `background_login_image` text NOT NULL,
  `status` int(11) NOT NULL,
  `deduct` tinyint(4) NOT NULL,
  `instance` varchar(400) NOT NULL,
  `access` varchar(400) NOT NULL,
  `term` longtext NOT NULL,
  `sign` varchar(200) NOT NULL,
  `bank` varchar(600) NOT NULL,
  `acc_no` varchar(600) NOT NULL,
  `ifsc` varchar(600) NOT NULL,
  `branch` varchar(600) NOT NULL,
  `badd` varchar(600) NOT NULL,
  `favicon` varchar(500) NOT NULL,
  `qr` varchar(600) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_manage_website`
--

INSERT INTO `tbl_manage_website` (`id`, `title`, `short_title`, `logo`, `footer`, `currency_code`, `currency_symbol`, `login_logo`, `invoice_logo`, `background_login_image`, `status`, `deduct`, `instance`, `access`, `term`, `sign`, `bank`, `acc_no`, `ifsc`, `branch`, `badd`, `favicon`, `qr`) VALUES
(1, 'Pet Grooming Billing Software', '', 'Pets_Logo.png', 'Pet Grooming Billing Software', '', '₹', 'Pets_Logo.png', '', '', 0, 1, '', '', '&lt;h4&gt;1. &lt;strong&gt;Appointments &amp;amp; Timings&lt;/strong&gt;&lt;/h4&gt;&lt;p&gt;Clients must arrive on time for scheduled appointments.&lt;/p&gt;&lt;p&gt;A delay of more than 15 minutes may result in rescheduling.&lt;/p&gt;&lt;p&gt;Walk-ins are accepted based on availability.&lt;/p&gt;&lt;h4&gt;2&lt;strong&gt;. Health &amp;amp; Vaccinations&lt;/strong&gt;&lt;/h4&gt;&lt;p&gt;Pets must be up to date with vaccinations (especially Rabies).&lt;/p&gt;&lt;p&gt;We reserve the right to refuse service if a pet shows signs of illness, infection, or aggressive behavior.&lt;/p&gt;&lt;p&gt;The client must inform us of any medical conditions, allergies, or behavioral issues.&lt;/p&gt;&lt;h4&gt;3. &lt;strong&gt;Grooming Safety&lt;/strong&gt;&lt;/h4&gt;&lt;p&gt;We use safe, pet-friendly grooming products and tools.&lt;/p&gt;&lt;p&gt;In case of extreme matting, hair may need to be shaved to avoid injury.&lt;/p&gt;&lt;p&gt;We are not liable for any pre-existing conditions or issues arising from matting, skin problems, or aggressive behavior.&lt;/p&gt;&lt;h4&gt;4. &lt;strong&gt;Emergency Care&lt;/strong&gt;&lt;/h4&gt;&lt;p&gt;In the event of an injury or health concern during grooming, we will attempt to contact the owner immediately.&lt;/p&gt;&lt;p&gt;If needed, the pet may be taken to the nearest veterinary clinic at the owner&#039;s expense.&lt;/p&gt;&lt;h4&gt;5. &lt;strong&gt;Cancellations &amp;amp; No-Shows&lt;/strong&gt;&lt;/h4&gt;&lt;p&gt;Please provide at least 24 hours’ notice for cancellations.&lt;/p&gt;&lt;p&gt;No-shows or last-minute cancellations may incur a fee.&lt;/p&gt;&lt;h4&gt;6. &lt;strong&gt;Photographs&lt;/strong&gt;&lt;/h4&gt;&lt;p&gt;We may take photos of pets after grooming for promotional use on our website or social media unless the client requests otherwise.&lt;/p&gt;&lt;h4&gt;7. &lt;strong&gt;Payment Terms&lt;/strong&gt;&lt;/h4&gt;&lt;p&gt;Full payment is required upon completion of grooming.&lt;/p&gt;&lt;p&gt;Prices may vary depending on breed, size, coat condition, and service duration.&lt;/p&gt;', 'images (1).png', 'State Bank of India 8198', '405xxxx8198', 'SBINxxxx07497', 'India', 'City name will be here', 'favicon_pet.png', 'dumm_qr(1).png');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_permissions`
--

CREATE TABLE `tbl_permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `display_name` varchar(50) NOT NULL,
  `operation` varchar(50) NOT NULL,
  `main` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_permissions`
--

INSERT INTO `tbl_permissions` (`id`, `name`, `display_name`, `operation`, `main`) VALUES
(1, 'Dashboard', 'Dashboard', 'Dashboard', 0),
(2, 'Total Category Count', 'Total Category Count', 'Total Category Count', 1),
(3, 'Total Product Count', 'Total Product Count', 'Total Product Count', 1),
(4, 'Total Customer Count', 'Total Customer Count', 'Total Customer Count', 1),
(5, 'Total Orders Count', 'Total Orders Count', 'Total Orders Count', 1),
(6, 'Recent Orders', 'Recent Orders', 'Recent Orders', 1),
(7, 'Monthly Income Graph', 'Monthly Income Graph', 'Monthly Income Graph', 1),
(8, 'User Management', 'User Management', 'User Management', 0),
(9, 'View Users', 'View Users', 'View Users', 8),
(10, 'Add Users', 'Add Users', 'Add Users', 8),
(11, 'Edit Users', 'Edit Users', 'Edit Users', 8),
(12, 'Delete Users', 'Delete Users', 'Delete Users', 8),
(13, 'View Roles', 'View Roles', 'View Roles', 8),
(14, 'Add Roles', 'Add Roles', 'Add Roles', 8),
(15, 'Edit Roles', 'Edit Roles', 'Edit Roles', 8),
(16, 'Delete Roles', 'Delete Roles', 'Delete Roles', 8),
(17, 'Customer Management', 'Customer Management', 'Customer Management', 0),
(18, 'Add Customer', 'Add Customer', 'Add Customer', 17),
(19, 'Edit Customer', 'Edit Customer', 'Edit Customer', 17),
(20, 'Delete Customer', 'Delete Customer', 'Delete Customer', 17),
(21, 'Product Category', 'Product Category', 'Product Category', 0),
(22, 'Add Category', 'Add Category', 'Add Category', 21),
(23, 'Edit Category', 'Edit Category', 'Edit Category', 21),
(24, 'Delete  Category', 'Delete Category', 'Delete Category', 21),
(25, 'Product/Service Management', 'Product/Service Management', 'Product/Service Management', 0),
(26, 'Add Product/Service', 'Add Product/Service', 'Add Product/Service', 25),
(27, 'Edit Product/Service', 'Edit Product/Service', 'Edit Product/Service', 25),
(28, 'Delete Product/Service', 'Delete Product/Service', 'Delete Product/Service', 25),
(29, 'Add Product Stock', 'Add Product Stock', 'Add Product Stock', 25),
(30, 'Invoice', 'Invoice', 'Invoice', 0),
(31, 'Add Invoice', 'Add Invoice', 'Add Invoice', 30),
(32, 'Add Installment Payment', 'Add Installment Payment', 'Add Installment Payment', 30),
(33, 'Check Installment Payments', 'Check Installment Payments', 'Check Installment Payments', 30),
(34, 'Check Invoice Receipt', 'Check Invoice Receipt', 'Check Invoice Receipt', 30),
(35, 'Reports', 'Reports', 'Reports', 0),
(36, 'Daily Report', 'Daily Report', 'Daily Report', 35),
(37, 'Profit Report', 'Profit Report', 'Profit Report', 35),
(38, 'Sale Report', 'Sale Report', 'Sale Report', 35),
(39, 'Stock Report', 'Stock Report', 'Stock Report', 35),
(40, 'Pending Report', 'Pending Report', 'Pending Report', 35),
(41, 'Total Expired Products Counts', 'Total Expired Products Counts', 'Total Expired Products Counts', 1),
(42, 'View Barcode', 'View Barcode', 'View Barcode', 25),
(43, 'Tax', 'Tax', 'Tax', 0),
(44, 'Add Tax', 'Add Tax', 'Add Tax', 43),
(45, 'Edit Tax', 'Edit Tax', 'Edit Tax', 43),
(46, 'Delete Tax', 'Delete Tax', 'Delete Tax', 43),
(47, 'Customer Report', 'Customer Report', 'Customer Report', 35),
(51, 'User Report', 'User Report', 'User Report', 35);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_permission_role`
--

CREATE TABLE `tbl_permission_role` (
  `id` int(50) NOT NULL,
  `permission_id` int(50) NOT NULL,
  `group_id` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_permission_role`
--

INSERT INTO `tbl_permission_role` (`id`, `permission_id`, `group_id`) VALUES
(46, 1, 2),
(47, 9, 2),
(48, 13, 2),
(49, 17, 2),
(50, 25, 2),
(97, 1, 1),
(98, 2, 1),
(99, 3, 1),
(100, 4, 1),
(101, 5, 1),
(102, 6, 1),
(103, 7, 1),
(104, 41, 1),
(105, 8, 1),
(106, 9, 1),
(107, 10, 1),
(108, 11, 1),
(109, 12, 1),
(110, 13, 1),
(111, 14, 1),
(112, 15, 1),
(113, 16, 1),
(114, 17, 1),
(115, 18, 1),
(116, 19, 1),
(117, 20, 1),
(118, 21, 1),
(119, 22, 1),
(120, 23, 1),
(121, 24, 1),
(122, 25, 1),
(123, 26, 1),
(124, 27, 1),
(125, 28, 1),
(126, 29, 1),
(127, 42, 1),
(128, 30, 1),
(129, 31, 1),
(130, 32, 1),
(131, 33, 1),
(132, 34, 1),
(133, 35, 1),
(134, 36, 1),
(135, 37, 1),
(136, 38, 1),
(137, 39, 1),
(138, 40, 1),
(139, 43, 1),
(140, 44, 1),
(141, 45, 1),
(142, 46, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product`
--

CREATE TABLE `tbl_product` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pid` varchar(100) NOT NULL,
  `name` varchar(1500) NOT NULL,
  `hsn` varchar(600) NOT NULL,
  `unit_price` varchar(1500) NOT NULL,
  `purchase_price` varchar(20) NOT NULL,
  `gst` varchar(250) NOT NULL,
  `purchase_gst` varchar(50) DEFAULT NULL,
  `selling_gst` varchar(50) NOT NULL,
  `details` varchar(2500) NOT NULL,
  `image` varchar(200) NOT NULL DEFAULT 'product-default.jpg',
  `openning_stock` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_status` tinyint(4) NOT NULL,
  `group_id` int(20) NOT NULL,
  `unit` int(50) NOT NULL,
  `currentdate` date DEFAULT NULL,
  `exp` int(11) NOT NULL,
  `exp_date` date DEFAULT NULL,
  `min_stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_product`
--

INSERT INTO `tbl_product` (`id`, `user_id`, `pid`, `name`, `hsn`, `unit_price`, `purchase_price`, `gst`, `purchase_gst`, `selling_gst`, `details`, `image`, `openning_stock`, `created_date`, `delete_status`, `group_id`, `unit`, `currentdate`, `exp`, `exp_date`, `min_stock`) VALUES
(1, 1, '000001', 'Dog Bath &amp; Grooming', '998717', '600', '500', '', '', '600', ' Includes basic dog wash, shampoo, nail trimming, and brushing. Ideal for small to medium breeds.\r\n\r\n', 'product-default.jpg', 0, '2025-05-05 06:06:20', 1, 1, 0, '2025-05-01', 1, '0000-00-00', 0),
(2, 1, '000002', 'Cat Claw Trimming ', '998717', '400', '300', '', '', '400', 'Gentle nail trimming and ear hygiene for cats using cat-safe tools and products.\r\n\r\n', 'product-default.jpg', 0, '2025-05-14 12:20:35', 1, 2, 0, '2025-05-01', 1, NULL, 0),
(3, 1, '000003', 'Rabbit Fur Brushing &amp; Hygiene Check', '998717', '600', '500', '', '', '600', ' Fur brushing to reduce shedding and a basic health check for small rabbits.', 'product-default.jpg', 0, '2025-05-05 07:50:22', 0, 3, 0, '2025-05-01', 1, NULL, 0),
(4, 1, '000004', 'Bird Nail &amp; Beak Trimming', '998717', '600', '500', '', '', '600', 'Specialized trimming service for birds&#039; nails and beaks using safe techniques.\r\n\r\n', 'product-default.jpg', 0, '2025-05-05 07:51:11', 0, 4, 0, '2025-05-01', 1, NULL, 0),
(5, 1, '000005', ' Aromatherapy Pet Spa Session', '998716', '1000', '800', '', '', '1000', ' A relaxing spa session with essential oils and massage therapy for pets to reduce stress.', 'product-default.jpg', 0, '2025-05-02 06:56:39', 0, 5, 0, '2025-05-01', 1, '0000-00-00', 0),
(6, 1, '000006', 'Full Pet Haircut (All Breeds)', '998717  ', '800', '600', '', '', '800', ' Complete haircut and styling for pets based on breed standards and customer preference.', 'product-default.jpg', 0, '2025-05-01 12:51:47', 0, 6, 0, '2025-05-01', 1, '0000-00-00', 0),
(7, 1, '000007', 'Dog Shampoo  Anti-Tick', '330730', '199', '120', '4', '141.60', '234.82', 'Specially formulated anti-tick dog shampoo with neem extract.', 'product-default.jpg', 9, '2025-05-14 11:21:27', 0, 1, 0, '2025-05-01', 0, '2026-04-30', 10),
(8, 1, '000008', 'Cat Litter  Scented', '250810', '280', '180', '5', '201.60', '313.60', 'Clumping, deodorized cat litter for indoor use.\r\n', 'product-default.jpg', 0, '2025-05-01 12:05:51', 0, 2, 0, '2025-05-01', 0, '2025-12-31', 15),
(9, 1, '000009', 'Rabbit Food Pellets  1kg', '230990', '150', '90', '6', '94.50', '157.50', 'High-fiber nutritional food for healthy digestion.\r\n\r\n', 'product-default.jpg', 0, '2025-08-23 18:33:20', 0, 3, 0, '2025-05-01', 0, '2025-10-15', 8),
(10, 1, '000010', 'Bird Cage Cleaner Spray  500ml', '340290', '175', '110', '4', '129.80', '206.50', 'Non-toxic cage cleaner spray safe for all pet birds.', 'product-default.jpg', 2, '2025-05-02 07:31:18', 0, 4, 0, '2025-05-01', 0, '2026-01-20', 12),
(11, 1, '000011', 'Pet Massage Oil  Herbal', '330730', '220', '140', '9', '141.40', '222.20', 'Herbal oil to calm pets during spa sessions.', 'product-default.jpg', 0, '2025-08-23 18:33:20', 0, 5, 0, '2025-05-01', 0, '2026-05-05', 6),
(12, 1, '000012', 'Grooming Scissors  Stainless Steel', '821300', '702.00', '600', '5,6', '702.00', '702.00', 'Professional grooming scissors for pet hair trimming.', 'product-default.jpg', 4, '2025-05-14 11:28:24', 0, 6, 0, '2025-05-01', 0, '2025-05-16', 5),
(19, 1, '000013', 'Liquid body wash', '998717', '600', '500', '', '', '600', 'for cleaningi', 'product-default.jpg', 0, '2025-05-14 09:16:28', 0, 3, 0, '2025-05-08', 1, '0000-00-00', 0),
(20, 1, '000014', 'sadv', '23432', '800', '700', '5', '784.00', '896.00', 'dsv', 'product-default.jpg', 8, '2025-05-14 07:52:40', 1, 2, 0, '2025-05-08', 0, '2025-05-24', 7),
(21, 1, '000015', 'PawCare Anti-Tick &amp; Flea Shampoo (200ml)', '34543', '180', '120', '4', '141.60', '212.40', 'Mild, vet-approved shampoo that helps eliminate ticks, fleas, and lice. Contains neem and eucalyptus extracts. Suitable for all breeds.', 'product-default.jpg', 17, '2025-05-14 09:14:07', 0, 1, 0, '2025-05-14', 0, '2025-06-04', 3);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product_grp`
--

CREATE TABLE `tbl_product_grp` (
  `id` int(11) NOT NULL,
  `user_id` varchar(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `for` tinyint(4) NOT NULL,
  `status` varchar(100) NOT NULL,
  `delete_status` int(11) NOT NULL,
  `created_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_product_grp`
--

INSERT INTO `tbl_product_grp` (`id`, `user_id`, `name`, `for`, `status`, `delete_status`, `created_date_time`) VALUES
(1, '1', 'Dog', 1, 'Active', 0, '2025-05-05 05:44:22'),
(2, '1', 'Cat', 0, 'Active', 0, '2025-05-01 11:36:18'),
(3, '1', 'Rabbit', 0, 'Active', 0, '2025-05-01 11:36:29'),
(4, '1', 'Bird', 0, 'Active', 0, '2025-05-01 11:36:38'),
(5, '1', 'Spa', 0, 'Active', 0, '2025-05-01 11:36:46'),
(6, '1', 'Haircut', 0, 'Active', 0, '2025-05-01 11:36:57'),
(7, '1', 'asd', 0, 'Active', 1, '2025-05-02 10:10:46'),
(8, '1', 'qaa', 0, 'Deactive', 1, '2025-05-02 10:10:41'),
(9, '1', 'qds asc', 0, 'Active', 1, '2025-05-05 08:00:19');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_quot_inv_items`
--

CREATE TABLE `tbl_quot_inv_items` (
  `id` int(100) NOT NULL,
  `inv_id` int(50) DEFAULT NULL,
  `product_id` int(50) DEFAULT NULL,
  `quantity` varchar(10) DEFAULT NULL,
  `rate` double(10,2) DEFAULT NULL,
  `total` double(10,2) DEFAULT NULL,
  `delete_status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_quot_inv_items`
--

INSERT INTO `tbl_quot_inv_items` (`id`, `inv_id`, `product_id`, `quantity`, `rate`, `total`, `delete_status`) VALUES
(1, 1, 21, '1', 212.40, 212.40, 0),
(2, 1, 19, '2', 600.00, 1200.00, 0),
(3, 2, 12, '2', 702.00, 1404.00, 0),
(4, 3, 19, '2', 600.00, 1200.00, 0),
(5, 4, 11, '6', 222.20, 1333.20, 0),
(6, 4, 9, '8', 157.50, 1260.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_states`
--

CREATE TABLE `tbl_states` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `country_id` int(11) NOT NULL DEFAULT 1,
  `gst_code` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_states`
--

INSERT INTO `tbl_states` (`id`, `name`, `country_id`, `gst_code`) VALUES
(1, 'Andaman and Nicobar Islands', 101, 35),
(2, 'Andhra Pradesh', 101, 28),
(3, 'Arunachal Pradesh', 101, 12),
(4, 'Assam', 101, 18),
(5, 'Bihar', 101, 10),
(6, 'Chandigarh', 101, 4),
(7, 'Chhattisgarh', 101, 22),
(8, 'Dadra and Nagar Haveli', 101, 26),
(9, 'Daman and Diu', 101, 25),
(10, 'Delhi', 101, 7),
(11, 'Goa', 101, 30),
(12, 'Gujarat', 101, 24),
(13, 'Haryana', 101, 6),
(14, 'Himachal Pradesh', 101, 2),
(15, 'Jammu and Kashmir', 101, 1),
(16, 'Jharkhand', 101, 20),
(17, 'Karnataka', 101, 29),
(18, 'Kenmore', 101, 0),
(19, 'Kerala', 101, 32),
(20, 'Lakshadweep', 101, 31),
(21, 'Madhya Pradesh', 101, 23),
(22, 'Maharashtra', 101, 27),
(23, 'Manipur', 101, 14),
(24, 'Meghalaya', 101, 17),
(25, 'Mizoram', 101, 15),
(26, 'Nagaland', 101, 13),
(27, 'Narora', 101, 0),
(28, 'Natwar', 101, 0),
(29, 'Odisha', 101, 21),
(30, 'Paschim Medinipur', 101, 0),
(31, 'Pondicherry', 101, 34),
(32, 'Punjab', 101, 3),
(33, 'Rajasthan', 101, 8),
(34, 'Sikkim', 101, 11),
(35, 'Tamil Nadu', 101, 33),
(36, 'Telangana', 101, 36),
(37, 'Tripura', 101, 16),
(38, 'Uttar Pradesh', 101, 9),
(39, 'Uttarakhand', 101, 5),
(40, 'Vaishali', 101, 0),
(41, 'West Bengal', 101, 19);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tax`
--

CREATE TABLE `tbl_tax` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `percentage` varchar(250) NOT NULL,
  `delete_status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_tax`
--

INSERT INTO `tbl_tax` (`id`, `name`, `percentage`, `delete_status`) VALUES
(2, 'erwe', '33 ', 1),
(3, 'eeeeeeeeeeeeeeee', '44.4 ', 1),
(4, 'GST ', '18', 0),
(5, 'VAT', '12', 0),
(6, 'Service Tax', '5', 0),
(7, 'Luxury Tax', '28', 1),
(8, 'Cess', '2', 1),
(9, 'Health Tax', '1', 1),
(10, 'Environmental Tax', '3', 1),
(11, 'Import Duty', '10', 1),
(12, 'Sales Tax', '8', 1),
(13, 'Entertainment Tax', '15', 1),
(14, 'sad q', '20 q', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_unit_grp`
--

CREATE TABLE `tbl_unit_grp` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `status` varchar(100) NOT NULL,
  `delete_status` int(11) NOT NULL,
  `created_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_unit_grp`
--

INSERT INTO `tbl_unit_grp` (`id`, `name`, `status`, `delete_status`, `created_date_time`) VALUES
(2, 'Sqft', 'Active', 0, '2024-07-05 11:08:22'),
(3, 'Nos', 'Active', 0, '2024-07-05 11:08:30'),
(4, 'Brass', 'Active', 0, '2024-07-05 11:08:43'),
(5, 'Trip', 'Active', 0, '2024-07-05 11:08:52'),
(6, 'Bag', 'Active', 0, '2024-07-05 11:08:59'),
(7, 'KILOGRAM (K G) ', 'Active', 0, '2025-02-06 11:36:42'),
(8, 'ytr', 'Active', 1, '2025-02-03 13:33:23'),
(9, 'gdgf ', 'Active', 1, '2025-02-06 11:25:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_customer`
--
ALTER TABLE `tbl_customer`
  ADD PRIMARY KEY (`cust_id`);

--
-- Indexes for table `tbl_groups`
--
ALTER TABLE `tbl_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_installement`
--
ALTER TABLE `tbl_installement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_invoice`
--
ALTER TABLE `tbl_invoice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_manage_website`
--
ALTER TABLE `tbl_manage_website`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_permissions`
--
ALTER TABLE `tbl_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_permission_role`
--
ALTER TABLE `tbl_permission_role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_product`
--
ALTER TABLE `tbl_product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_product_grp`
--
ALTER TABLE `tbl_product_grp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_quot_inv_items`
--
ALTER TABLE `tbl_quot_inv_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_states`
--
ALTER TABLE `tbl_states`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_tax`
--
ALTER TABLE `tbl_tax`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_unit_grp`
--
ALTER TABLE `tbl_unit_grp`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbl_customer`
--
ALTER TABLE `tbl_customer`
  MODIFY `cust_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_groups`
--
ALTER TABLE `tbl_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_installement`
--
ALTER TABLE `tbl_installement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_invoice`
--
ALTER TABLE `tbl_invoice`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_manage_website`
--
ALTER TABLE `tbl_manage_website`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_permissions`
--
ALTER TABLE `tbl_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `tbl_permission_role`
--
ALTER TABLE `tbl_permission_role`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `tbl_product`
--
ALTER TABLE `tbl_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tbl_product_grp`
--
ALTER TABLE `tbl_product_grp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_quot_inv_items`
--
ALTER TABLE `tbl_quot_inv_items`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_states`
--
ALTER TABLE `tbl_states`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4121;

--
-- AUTO_INCREMENT for table `tbl_tax`
--
ALTER TABLE `tbl_tax`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbl_unit_grp`
--
ALTER TABLE `tbl_unit_grp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
