-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 30, 2015 at 12:16 AM
-- Server version: 5.6.26-log
-- PHP Version: 5.6.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ox`
--

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(80) COLLATE utf8_slovenian_ci NOT NULL,
  `image` varchar(80) COLLATE utf8_slovenian_ci NOT NULL,
  `code_a2` varchar(2) COLLATE utf8_slovenian_ci NOT NULL,
  `code_a3` varchar(3) COLLATE utf8_slovenian_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci AUTO_INCREMENT=224 ;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `country`, `image`, `code_a2`, `code_a3`) VALUES
(1, 'Afganistan', 'Afghanistan.png', 'AF', 'AFG'),
(2, 'Albanija', 'Albania.png', 'AL', 'ALB'),
(3, 'Alžirija', 'Algeria.png', 'DZ', 'DZA'),
(4, 'Ameriška Samoa', 'American_Samoa.png', 'AS', 'ASM'),
(5, 'Andorra', 'Andorra.png', 'AD', 'AND'),
(6, 'Angola', 'Angola.png', 'AO', 'AGO'),
(7, 'Anguilla', 'Anguilla.png', 'AI', 'AIA'),
(8, 'Antigua in Barbuda', 'Antigua_and_Barbuda.png', 'AG', 'ATG'),
(9, 'Argentina', 'Argentina.png', 'AR', 'ARG'),
(10, 'Armenija', 'Armenia.png', 'AM', 'ARM'),
(11, 'Aruba', 'Aruba.png', 'AW', 'ABW'),
(12, 'Australija', 'Australia.png', 'AU', 'AUS'),
(13, 'Avstrija', 'Austria.png', 'AT', 'AUT'),
(14, 'Azerbajdžan', 'Azerbaijan.png', 'AZ', 'AZE'),
(15, 'Bahami', 'Bahamas.png', 'BS', 'BHS'),
(16, 'Bahrain', 'Bahrain.png', 'BH', 'BHR'),
(17, 'Bangladeš', 'Bangladesh.png', 'BD', 'BGD'),
(18, 'Barbados', 'Barbados.png', 'BB', 'BRB'),
(19, 'Belgija', 'Belgium.png', 'BE', 'BEL'),
(20, 'Belize', 'Belize.png', 'BZ', 'BLZ'),
(21, 'Belorusija', 'Belarus.png', 'BY', 'BLR'),
(22, 'Benin', 'Benin.png', 'BJ', 'BEN'),
(23, 'Bermuda', 'Bermuda.png', 'BM', 'BMU'),
(24, 'Bolgarija', 'Bulgaria.png', 'BG', 'BGR'),
(25, 'Bolivija', 'Bolivia.png', 'BO', 'BOL'),
(26, 'Bosna in Hercegovina', 'Bosnia.png', 'BA', 'BIH'),
(27, 'Botsvana', 'Botswana.png', 'BW', 'BWA'),
(28, 'Božični otoki', 'Christmas_Island.png', 'CX', 'CXR'),
(29, 'Brazilija', 'Brazil.png', 'BR', 'BRA'),
(30, 'Britanski Deviški otoki', 'British_Virgin_Islands.png', 'VG', 'VGB'),
(31, 'Brunei', 'Brunei.png', 'BN', 'BRN'),
(32, 'Burkina Faso', 'Burkina_Faso.png', 'BF', 'BFA'),
(33, 'Burundi', 'Burundi.png', 'BI', 'BDI'),
(34, 'Butan', 'Bhutan.png', 'BT', 'BTN'),
(35, 'Centralnoafriška republika', 'Central_African_Republic.png', 'CF', 'CAF'),
(36, 'Ciper', 'Cyprus.png', 'CY', 'CYP'),
(37, 'Cookovi otoki', 'Cook_Islands.png', 'CK', 'COK'),
(38, 'Čad', 'Chad.png', 'TD', 'TCD'),
(39, 'Česka republika', 'Czech_Republic.png', 'CZ', 'CZE'),
(40, 'Čile', 'Chile.png', 'CL', 'CHL'),
(41, 'Danska', 'Denmark.png', 'DK', 'DNK'),
(42, 'Democratična republika Kongo', 'Democratic_Republic_of_the_Congo.png', 'CD', 'COD'),
(43, 'Deviški otoki(ZDA)', 'US_Virgin_Islands.png', 'VI', 'VIR'),
(44, 'Dominika', 'Dominica.png', 'DM', 'DMA'),
(45, 'Dominikanska republika', 'Dominican_Republic.png', 'DO', 'DOM'),
(46, 'Džibuti', 'Djibouti.png', 'DJ', 'DJI'),
(47, 'Egipt', 'Egypt.png', 'EG', 'EGY'),
(48, 'Ekvador', 'Ecuador.png', 'EC', 'ECU'),
(49, 'Ekvatorialna Gvineja', 'Equatorial_Guinea.png', 'GQ', 'GNQ'),
(50, 'Eritreja', 'Eritrea.png', 'ER', 'ERI'),
(51, 'Estonija', 'Estonia.png', 'EE', 'EST'),
(52, 'Etiopija', 'Ethiopia.png', 'ET', 'ETH'),
(53, 'Falklandski otoki', 'Falkland_Islands.png', 'FK', 'FLK'),
(54, 'Farski otoki', 'Faroe_Islands.png', 'FO', 'FRO'),
(55, 'Fiji', 'Fiji.png', 'FJ', 'FJI'),
(56, 'Filipini', 'Philippines.png', 'PH', 'PHL'),
(57, 'Finska', 'Finland.png', 'FI', 'FIN'),
(58, 'Francija', 'France.png', 'FR', 'FRA'),
(59, 'Francoska polinezija', 'French_Polynesia.png', 'PF', 'PYF'),
(60, 'Gabon', 'Gabon.png', 'GA', 'GAB'),
(61, 'Gambija', 'Gambia.png', 'GM', 'GMB'),
(62, 'Gana', 'Ghana.png', 'GH', 'GHA'),
(63, 'Gibraltar', 'Gibraltar.png', 'GI', 'GIB'),
(64, 'Grčija', 'Greece.png', 'GR', 'GRC'),
(65, 'Grenada', 'Grenada.png', 'GD', 'GRD'),
(66, 'Grenlandija', 'Greenland.png', 'GL', 'GRL'),
(67, 'Gruzija', 'Georgia.png', 'GE', 'GEO'),
(68, 'Guam', 'Guam.png', 'GU', 'GUM'),
(69, 'Gvajana', 'Guyana.png', 'GY', 'GUY'),
(70, 'Gvatemala', 'Guatemala.png', 'GT', 'GTM'),
(71, 'Gvinea', 'Guinea.png', 'GN', 'GIN'),
(72, 'Gvinea Bissau', 'Guinea_Bissau.png', 'GW', 'GNB'),
(73, 'Haiti', 'Haiti.png', 'HT', 'HTI'),
(74, 'Honduras', 'Honduras.png', 'HN', 'HND'),
(75, 'Hong Kong', 'Hong_Kong.png', 'HK', 'HKG'),
(76, 'Hrvaška', 'Croatia.png', 'HR', 'HRV'),
(77, 'Indija', 'India.png', 'IN', 'IND'),
(78, 'Indonezija', 'Indonesia.png', 'ID', 'IDN'),
(79, 'Irak', 'Iraq.png', 'IQ', 'IRQ'),
(80, 'Iran', 'Iran.png', 'IR', 'IRN'),
(81, 'Irska', 'Ireland.png', 'IE', 'IRL'),
(82, 'Islandija', 'Iceland.png', 'IS', 'ISL'),
(83, 'Italija', 'Italy.png', 'IT', 'ITA'),
(84, 'Izrael', 'Israel.png', 'IL', 'ISR'),
(85, 'Jamaika', 'Jamaica.png', 'JM', 'JAM'),
(86, 'Japonska', 'Japan.png', 'JP', 'JPN'),
(87, 'Jemen', 'Yemen.png', 'YE', 'YEM'),
(88, 'Jordanija', 'Jordan.png', 'JO', 'JOR'),
(89, 'Južna Africa', 'South_Africa.png', 'ZA', 'ZAF'),
(90, 'Južna Georgija', 'South_Georgia.png', 'GS', 'SGS'),
(91, 'Južna Korea', 'South_Korea.png', 'KR', 'KOR'),
(92, 'Kajmanski otoki', 'Cayman_Islands.png', 'KY', 'CYM'),
(93, 'Kambodža', 'Cambodia.png', 'KH', 'KHM'),
(94, 'Kamerun', 'Cameroon.png', 'CM', 'CMR'),
(95, 'Kanada', 'Canada.png', 'CA', 'CAN'),
(96, 'Katar', 'Qatar.png', 'QA', 'QAT'),
(97, 'Kazahstan', 'Kazakhstan.png', 'KZ', 'KAZ'),
(98, 'Kenija', 'Kenya.png', 'KE', 'KEN'),
(99, 'Kirgizija', 'Kyrgyzstan.png', 'KG', 'KGZ'),
(100, 'Kiribati', 'Kiribati.png', 'KI', 'KIR'),
(101, 'Kitajska', 'China.png', 'CN', 'CHN'),
(102, 'Kolumbija', 'Colombia.png', 'CO', 'COL'),
(103, 'Komori', 'Comoros.png', 'KM', 'COM'),
(104, 'Kostarika', 'Costa_Rica.png', 'CR', 'CRI'),
(105, 'Kuba', 'Cuba.png', 'CU', 'CUB'),
(106, 'Kuwait', 'Kuwait.png', 'KW', 'KWT'),
(107, 'Laos', 'Laos.png', 'LA', 'LAO'),
(108, 'Latvija', 'Latvia.png', 'LV', 'LVA'),
(109, 'Lesoto', 'Lesotho.png', 'LS', 'LSO'),
(110, 'Libanon', 'Lebanon.png', 'LB', 'LBN'),
(111, 'Liberija', 'Liberia.png', 'LR', 'LBR'),
(112, 'Libija', 'Libya.png', 'LY', 'LBY'),
(113, 'Liechtenstein', 'Liechtenstein.png', 'LI', 'LIE'),
(114, 'Litva', 'Lithuania.png', 'LT', 'LTU'),
(115, 'Luxembourg', 'Luxembourg.png', 'LU', 'LUX'),
(116, 'Macao', 'Macao.png', 'MO', 'MAC'),
(117, 'Madagaskar', 'Madagascar.png', 'MG', 'MDG'),
(118, 'Madžarska', 'Hungary.png', 'HU', 'HUN'),
(119, 'Makedonija', 'Macedonia.png', 'MK', 'MKD'),
(120, 'Malavi', 'Malawi.png', 'MW', 'MWI'),
(121, 'Maldivi', 'Maldives.png', 'MV', 'MDV'),
(122, 'Malezija', 'Malaysia.png', 'MY', 'MYS'),
(123, 'Mali', 'Mali.png', 'ML', 'MLI'),
(124, 'Malta', 'Malta.png', 'MT', 'MLT'),
(125, 'Maroko', 'Morocco.png', 'MA', 'MAR'),
(126, 'Marshallovi otoki', 'Marshall_Islands.png', 'MH', 'MHL'),
(127, 'Martinique', 'Martinique.png', 'MQ', 'MTQ'),
(128, 'Mauretanija', 'Mauritania.png', 'MR', 'MRT'),
(129, 'Mauricius', 'Mauritius.png', 'MU', 'MUS'),
(130, 'Mehiko', 'Mexico.png', 'MX', 'MEX'),
(131, 'Mikronezija', 'Micronesia.png', 'FM', 'FSM'),
(132, 'Moldovija', 'Moldova.png', 'MD', 'MDA'),
(133, 'Monako', 'Monaco.png', 'MC', 'MCO'),
(134, 'Mongolija', 'Mongolia.png', 'MN', 'MNG'),
(135, 'Montserrat', 'Montserrat.png', 'MS', 'MSR'),
(136, 'Mozambik', 'Mozambique.png', 'MZ', 'MOZ'),
(137, 'Myanmar', 'Myanmar.png', 'MM', 'MMR'),
(138, 'Namibija', 'Namibia.png', 'NA', 'NAM'),
(139, 'Nauru', 'Nauru.png', 'NR', 'NRU'),
(140, 'Nemčija', 'Germany.png', 'DE', 'DEU'),
(141, 'Nepal', 'Nepal.png', 'NP', 'NPL'),
(142, 'Niger', 'Niger.png', 'NE', 'NER'),
(143, 'Nigerija', 'Nigeria.png', 'NG', 'NGA'),
(144, 'Nikaragva', 'Nicaragua.png', 'NI', 'NIC'),
(145, 'Niue', 'Niue.png', 'NU', 'NIU'),
(146, 'Nizozemska', 'Netherlands.png', 'NL', 'NLD'),
(147, 'Nizozemski antili', 'Netherlands_Antilles.png', '', ''),
(148, 'Norfolk otok', 'Norfolk_Island.png', 'NF', 'NFK'),
(149, 'Norveška', 'Norway.png', 'NO', 'NOR'),
(150, 'Nova Zelandija', 'New_Zealand.png', 'NZ', 'NZL'),
(151, 'Oman', 'Oman.png', 'OM', 'OMN'),
(152, 'Pakistan', 'Pakistan.png', 'PK', 'PAK'),
(153, 'Palau', 'Palau.png', 'PW', 'PLW'),
(154, 'Panama', 'Panama.png', 'PA', 'PAN'),
(155, 'Papua - Nova Gvineja', 'Papua_New_Guinea.png', 'PG', 'PNG'),
(156, 'Paragvaj', 'Paraguay.png', 'PY', 'PRY'),
(157, 'Peru', 'Peru.png', 'PE', 'PER'),
(158, 'Pitcairn otoki', 'Pitcairn_Islands.png', 'PN', 'PCN'),
(159, 'Polska', 'Poland.png', 'PO', 'POL'),
(160, 'Portorico', 'Puerto_Rico.png', 'PR', 'PRI'),
(161, 'Portugalska', 'Portugal.png', 'PT', 'PRT'),
(162, 'Republika Kongo', 'Republic_of_the_Congo.png', 'CG', 'COG'),
(163, 'Romunija', 'Romania.png', 'RO', 'ROU'),
(164, 'Rusija - Ruska federacija', 'Russian_Federation.png', 'RU', 'RUS'),
(165, 'Rwanda', 'Rwanda.png', 'RW', 'RWA'),
(166, 'Saint Kitts in Nevis', 'Saint_Kitts_and_Nevis.png', 'KN', 'KNA'),
(167, 'Salvador', 'El_Salvador.png', 'SV', 'SLV'),
(168, 'Samoa', 'Samoa.png', 'WS', 'WSM'),
(169, 'San Marino', 'San_Marino.png', 'SM', 'SMR'),
(170, 'Sao Tome in Principe', 'Sao_Tome_and_Principe.png', 'ST', 'STP'),
(171, 'Saudska Arabija', 'Saudi_Arabia.png', 'SA', 'SAU'),
(172, 'Sejšeli', 'Seychelles.png', 'SC', 'SYC'),
(173, 'Senegal', 'Senegal.png', 'SN', 'SEN'),
(174, 'Severna Koreja', 'North_Korea.png', 'KP', 'PRK'),
(175, 'Sierra Leone', 'Sierra_Leone.png', 'SL', 'SLE'),
(176, 'Singapur', 'Singapore.png', 'SG', 'SGP'),
(177, 'Sirija', 'Syria.png', 'SY', 'SYR'),
(178, 'Slonokoščena obala', 'Cote_divoire.png', 'CI', 'CIV'),
(179, 'Slovaška', 'Slovakia.png', 'SK', 'SVK'),
(180, 'Slovenija', 'Slovenia.png', 'SI', 'SVN'),
(181, 'Solomonovi Otoki', 'Soloman_Islands.png', 'SB', 'SLB'),
(182, 'Somalija', 'Somalia.png', 'SO', 'SOM'),
(183, 'Sovjetska zveza', 'Soviet_Union.png', 'SU', 'CS'),
(184, 'Srbija in Črna gora', 'Serbia_and_Montenegro.png', 'CS', 'CS'),
(185, 'Sudan', 'Sudan.png', 'SD', 'SDN'),
(186, 'Surinam', 'Suriname.png', 'SR', 'SUR'),
(187, 'Sveta Lucija', 'Saint_Lucia.png', 'LC', 'LCA'),
(188, 'Sveta Vicent in Grenadini', 'Saint_Vicent_and_the_Grenadines.png', 'VC', 'VCT'),
(189, 'Sveti Pierre', 'Saint_Pierre.png', 'PM', 'SPM'),
(190, 'Swaziland', 'Swaziland.png', 'SZ', 'SWZ'),
(191, 'Španija', 'Spain.png', 'ES', 'ESP'),
(192, 'Šrilanka', 'Sri_Lanka.png', 'LK', 'LKA'),
(193, 'Švedska', 'Sweden.png', 'SE', 'SWE'),
(194, 'Švica', 'Switzerland.png', 'CH', 'CHE'),
(195, 'Tadžikistan', 'Tajikistan.png', 'TJ', 'TJK'),
(196, 'Taiwan', 'Taiwan.png', 'TW', 'TWN'),
(197, 'Tanzanija', 'Tanzania.png', 'TZ', 'TZA'),
(198, 'Thailand', 'Thailand.png', 'TH', 'THA'),
(199, 'Tibet', 'Tibet.png', '', ''),
(200, 'Togo', 'Togo.png', 'TG', 'TGO'),
(201, 'Tonga', 'Tonga.png', 'TO', 'TON'),
(202, 'Trinidad in Tobago', 'Trinidad_and_Tobago.png', 'TT', 'TTO'),
(203, 'Tunizija', 'Tunisia.png', 'TN', 'TUN'),
(204, 'Turčija', 'Turkey.png', 'TR', 'TUR'),
(205, 'Turkmenistan', 'Turkmenistan.png', 'TM', 'TKM'),
(206, 'Turks in Caicos otoki', 'Turks_and_Caicos_Islands.png', 'TC', 'TCA'),
(207, 'Tuvalu', 'Tuvalu.png', 'TV', 'TUV'),
(208, 'Udruženi Arabski emirati', 'UAE.png', 'AE', 'ARE'),
(209, 'Uganda', 'Uganda.png', 'UG', 'UGA'),
(210, 'Ukrajina', 'Ukraine.png', 'UA', 'UKR'),
(211, 'Urugvaj', 'Uruguay.png', 'UY', 'URY'),
(212, 'Uzbekistan', 'Uzbekistan.png', 'UZ', 'UZB'),
(213, 'Vanuatu', 'Vanuatu.png', 'VU', 'VUT'),
(214, 'Vatikan', 'Vatican_City.png', 'VA', 'VAT'),
(215, 'Venezuela', 'Venezuela.png', 'VE', 'VEN'),
(216, 'Vietnam', 'Vietnam.png', 'VN', 'VNM'),
(217, 'Vzhodni timor', 'Timor-Leste.png', 'TL', 'TLS'),
(218, 'Wallis in Futuna', 'Wallis_and_Futuna.png', 'WF', 'WLF'),
(219, 'Zambija', 'Zambia.png', 'ZM', 'ZMB'),
(220, 'Združene države Amerike', 'United_States_of_America.png', 'US', 'USA'),
(221, 'Združeno kraljestvo VB in SI', 'United_Kingdom.png', 'GB', 'GBR'),
(222, 'Zelenortški otoki', 'Cape_Verde.png', 'CV', 'CPV'),
(223, 'Zimbabve', 'Zimbabwe.png', 'ZW', 'ZWE');

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE IF NOT EXISTS `language` (
  `language_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`language_id`, `name`, `last_update`) VALUES
(1, 'English', '2006-02-15 04:02:19'),
(2, 'Italian', '2006-02-15 04:02:19'),
(3, 'Japanese', '2006-02-15 04:02:19'),
(4, 'Mandarin', '2006-02-15 04:02:19'),
(5, 'French', '2006-02-15 04:02:19'),
(6, 'German', '2006-02-15 04:02:19');

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE IF NOT EXISTS `test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `a` int(11) NOT NULL,
  `b` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci AUTO_INCREMENT=95 ;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`id`, `a`, `b`) VALUES
(1, 1, 55),
(2, 1, 55),
(3, 1, 55),
(4, 1, 55),
(5, 1, 55),
(6, 1, 55),
(7, 1, 55),
(8, 1, 55),
(9, 1, 55),
(10, 1, 55),
(11, 1, 55),
(12, 1, 55),
(13, 1, 55),
(14, 1, 55),
(15, 1, 55),
(16, 1, 55),
(17, 1, 55),
(18, 1, 55),
(19, 1, 55),
(20, 1, 55),
(21, 1, 55),
(22, 1, 55),
(23, 1, 55),
(24, 1, 55),
(25, 1, 55),
(26, 1, 55),
(27, 1, 55),
(28, 1, 55),
(29, 1, 55),
(30, 1, 55),
(31, 1, 55),
(32, 1, 55),
(33, 1, 55),
(34, 1, 55),
(35, 1, 55),
(36, 1, 55),
(37, 1, 55),
(38, 1, 55),
(39, 1, 55),
(40, 1, 55),
(41, 1, 55),
(42, 1, 55),
(43, 1, 55),
(44, 1, 55),
(45, 1, 55),
(46, 1, 55),
(47, 1, 55),
(48, 1, 55),
(49, 1, 55),
(50, 1, 55),
(51, 1, 55),
(52, 1, 55),
(53, 1, 55),
(54, 1, 55),
(55, 1, 55),
(56, 1, 55),
(57, 1, 55),
(58, 1, 55),
(59, 1, 55),
(60, 1, 55),
(61, 1, 55),
(62, 1, 55),
(63, 1, 55),
(64, 1, 55),
(65, 1, 55),
(66, 1, 55),
(67, 1, 55),
(68, 1, 55),
(69, 1, 55),
(70, 1, 55),
(71, 1, 55),
(72, 1, 55),
(73, 1, 55),
(74, 1, 55),
(75, 1, 55),
(76, 1, 55),
(77, 1, 55),
(78, 1, 55),
(79, 1, 55),
(80, 1, 55),
(81, 1, 55),
(82, 1, 55),
(83, 1, 55),
(84, 1, 55),
(85, 1, 55),
(86, 1, 55),
(87, 1, 55),
(88, 1, 55),
(89, 1, 55),
(90, 1, 55),
(91, 1, 55),
(92, 1, 55),
(93, 1, 55),
(94, 1, 55);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) COLLATE utf8_slovenian_ci NOT NULL,
  `password` varchar(80) COLLATE utf8_slovenian_ci NOT NULL,
  `first_name` varchar(80) COLLATE utf8_slovenian_ci NOT NULL,
  `last_name` varchar(80) COLLATE utf8_slovenian_ci NOT NULL,
  `record_status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `first_name`, `last_name`, `record_status`) VALUES
(1, 'tomaz', '$2y$10$QhETvOcNzScKzznrZ4aF0.nrb6GerxjuSgdxI3egs91x6FLzFH6PW', 'Tomaž', 'Kovačič', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
