-- phpMyAdmin SQL Dump
-- version 3.5.3
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 14-01-2013 a las 20:03:21
-- Versión del servidor: 5.1.66-cll
-- Versión de PHP: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `codemexi_biblioteca`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `addBorrow`(IN rmem_code VARCHAR(10),
IN rmem_name VARCHAR(45),
IN rfinal_date DATE, IN ruser_id INT, IN rcopy_id INT)
BEGIN
    INSERT INTO `borrow`
    VALUES(NULL, rmem_code, rmem_name, CURRENT_DATE, rfinal_date, ruser_id, rcopy_id, DEFAULT);
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `addCopy`(IN rid_book INTEGER, IN rnum_copy TINYINT)
BEGIN
    DECLARE lc, cp INTEGER;
    DECLARE EXIT HANDLER FOR SQLSTATE '23000'
        SELECT 'Libro desconocido' AS 'error';
    
    IF(SELECT COUNT(*) FROM `book` WHERE idbook = rid_book) = 0
    THEN
        INSERT INTO `error_msg` VALUES('Foreign Key Constraint Violated!');
    END IF;
    SET lc := (SELECT COUNT(*) FROM `copy` WHERE id_book = rid_book);
    SET cp = lc + 1;
    REPEAT
        INSERT INTO `copy`
        VALUES(NULL, cp, DEFAULT, rid_book);
        SET cp = cp + 1;
    UNTIL cp > (lc + rnum_copy)
    END REPEAT;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `addUser`(
IN rname VARCHAR(50),
IN rusername VARCHAR(10),
IN rpassword VARCHAR(10))
BEGIN
    INSERT INTO `user` VALUES(NULL, rname, rusername, MD5(rpassword), DEFAULT);
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `changeBookClass`(IN idb INT, IN idc INT)
BEGIN
UPDATE book SET id_class = idc WHERE idbook = idb;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `changePass`(IN id INT, IN npass VARCHAR(10))
BEGIN
UPDATE user SET uspswd = MD5(npass) WHERE iduser = id;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `check_borrow`()
BEGIN
    UPDATE borrow SET status = FALSE
    WHERE CURRENT_DATE > final_date AND status = TRUE;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `filterBooks`(IN `keys` TEXT, IN _page INT)
BEGIN
SET @keys = CONCAT('%', `keys` ,'%');
SET @strStmt = CONCAT('SELECT * FROM books WHERE
MATCH(title) AGAINST ("', @keys, '") OR MATCH(author) AGAINST ("',
@keys, '") LIMIT ', (_page -1) * 10, ', 10');
PREPARE stmt FROM @strStmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `filterBooksCount`(IN `keys` TEXT)
BEGIN
SET @keys = CONCAT('%', `keys` ,'%');
SET @strStmt = CONCAT('SELECT COUNT(*) AS count FROM books WHERE
MATCH(title) AGAINST ("', @keys, '") OR MATCH(author) AGAINST ("',
@keys, '")');
PREPARE stmt FROM @strStmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `getAllBooks`()
BEGIN
    SELECT * FROM book;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `getAllBorrows`()
BEGIN
    SELECT * FROM borrows ORDER BY status ASC;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `getAllClass`()
BEGIN
SELECT * FROM class;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `getAllUsers`()
BEGIN
    SELECT * FROM user;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `getBookByNum`(IN rnum INTEGER)
BEGIN
    SELECT * FROM book WHERE idbook = rnum;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `getBookCard`(IN `keys` TEXT)
BEGIN
SET @keys := CONCAT('%', `keys`, '%');
SELECT * FROM indexCard WHERE MATCH(title) AGAINST(@keys)
OR MATCH(author) AGAINST(@keys);
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `getClassById`(IN id INTEGER)
BEGIN
    SELECT * FROM class WHERE idclass = id;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `getCopyBook`(IN idb INT)
BEGIN
SELECT MIN(idcopy) AS cpid FROM copy WHERE id_book = idb AND availability = 1;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `getCopyByNum`(IN rnum INTEGER)
BEGIN
    SELECT * FROM copy WHERE id_book = rnum;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `getTableCount`(IN tblName VARCHAR(10))
BEGIN
SET @stat = CONCAT('SELECT COUNT(*) AS count FROM ', tblName);
PREPARE stmt FROM @stat;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `getTablePage`(IN _tblName VARCHAR(10), IN _page INT)
BEGIN
SET @strStmt = CONCAT('SELECT * FROM ', _tblName, ' LIMIT ', (_page - 1) * 10, ', 10');
PREPARE stmt FROM @strStmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `getUserByNum`(IN num INTEGER)
BEGIN
SELECT * FROM user WHERE iduser = num;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `getUserInfo`()
BEGIN
    SELECT * FROM userInfo;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `loginUser`(IN suname VARCHAR(10), IN spswd VARCHAR(10))
BEGIN
    SELECT iduser, usfname FROM user
	WHERE usuname = suname AND uspswd = MD5(spswd) AND usstat = TRUE;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `newBook`(IN rcode VARCHAR(5), IN risbn CHAR(13),
IN rtitle VARCHAR(45),
IN rauthor VARCHAR(45),
IN reditorial VARCHAR(45),
IN rpubl_place VARCHAR(20),
IN rpubl_date YEAR,
IN redition TINYINT,
IN rid_class INTEGER(11),
IN rcopy_num TINYINT)
BEGIN
    DECLARE cp, rid INTEGER;
    INSERT INTO `book`
    VALUES(NULL, rcode, risbn, rtitle, rauthor, reditorial, rpubl_place, rpubl_date, redition, rid_class);
    
    SET cp = 1;
    SET rid = LAST_INSERT_ID();
    REPEAT
        INSERT INTO `copy`
        VALUES(NULL, cp, DEFAULT, rid);
        SET cp = cp + 1;
    UNTIL cp > rcopy_num
    END REPEAT;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `newClass`(
IN cmain SMALLINT(3),
IN csub SMALLINT(5),
IN cname VARCHAR(30))
BEGIN
    INSERT INTO `class` VALUES(NULL, cmain, csub, cname);
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `terminateBorrow`(IN rnum INTEGER)
BEGIN
    UPDATE borrow SET status = 2 WHERE idborrow = rnum;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `updateBook`(IN rid INT, IN rcode VARCHAR(5), 
IN risbn CHAR(13), IN rtitle VARCHAR(45), IN rauthor VARCHAR(45),
IN reditorial VARCHAR(45), IN rpubl_place VARCHAR(20),
IN rpubl_year YEAR, IN redition TINYINT)
BEGIN
UPDATE book SET code = rcode, isbn = risbn, title = rtitle,
author = rauthor, editorial = reditorial, edition = redition
WHERE idbook = rid;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `updateClass`(IN cid INT, IN cnmmain SMALLINT,
IN cnmsub SMALLINT, IN cname VARCHAR(30))
BEGIN
    UPDATE `class` SET clmain = cnmmain, clsub = cnmsub,
name = cname WHERE idclass = cid;
END$$

CREATE DEFINER=`codemexi`@`localhost` PROCEDURE `updateUser`(
IN riduser INT,
IN rname VARCHAR(50),
IN rusername VARCHAR(10),
IN rpassword VARCHAR(10),
IN rstatus TINYINT(1))
BEGIN    
    IF rpassword = '' THEN
        UPDATE `user` SET usfname = rname, usuname = rusername, usstat = rstatus WHERE iduser = riduser;
    ELSE
        UPDATE `user` SET usfname = rname, usuname = rusername, uspswd = MD5(rpassword), usstat = rstatus
        WHERE iduser = riduser;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `book`
--

CREATE TABLE IF NOT EXISTS `book` (
  `idbook` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL,
  `isbn` char(13) DEFAULT NULL,
  `title` varchar(60) NOT NULL,
  `author` varchar(45) NOT NULL,
  `editorial` varchar(40) NOT NULL,
  `publ_place` varchar(20) DEFAULT NULL,
  `publ_year` year(4) DEFAULT NULL,
  `edition` tinyint(4) unsigned NOT NULL,
  `id_class` int(11) unsigned NOT NULL,
  PRIMARY KEY (`idbook`),
  KEY `idxclass` (`id_class`),
  FULLTEXT KEY `idxauthor` (`author`),
  FULLTEXT KEY `idxtitle` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=104 ;

--
-- Volcado de datos para la tabla `book`
--

INSERT INTO `book` (`idbook`, `code`, `isbn`, `title`, `author`, `editorial`, `publ_place`, `publ_year`, `edition`, `id_class`) VALUES
(1, 'C6', '0394338111', 'Compstat', 'Leonard Presby', 'Random House Bussiness Division', NULL, NULL, 1, 9),
(2, 'B3', '9688802239', 'Experimentación', 'D.C. Baird', 'Prentice Hall', NULL, NULL, 2, 8),
(3, 'V5', '9681819411', 'Teoría de la comunicación', 'Ismael Vidales Delgado', 'Limusa', NULL, NULL, 1, 3),
(4, 'S7', '9681315138', 'Comunicación humana', 'Thomas M. Steinfatt', 'Diana', NULL, NULL, 2, 3),
(5, 'W19C', '968823071', 'Cibernética y sociedad', 'Norbert Wiener', 'Sudamericana', NULL, NULL, 1, 4),
(6, 'O6', '8428322694', 'Optimización heurística y redes neuronales', 'Adenso Díaz y Fred Glover', 'Rarainfo', NULL, NULL, 1, 4),
(7, 'F5', '9706860622', 'Sistemas operativos', 'Flyn - Mchoes', 'Fiscales y legales', NULL, NULL, 3, 10),
(8, 'R64', '9586000477', 'Inteligencia artificial y sistemas expertos', 'David W. Rolston', 'McGraw Hill', NULL, NULL, 1, 11),
(9, 'A5', '8428314519', 'Inteligencia artificial', 'José María Angulo U.', 'Paraninfo', NULL, NULL, 2, 11),
(10, 'B8', '9681830121', 'Prolog, programación y aplicaciones', 'W. D. Burnham y A. R. Hall', 'Limusa', NULL, NULL, 1, 11),
(11, 'I5', '8426706398', 'Inteligencia artificial', 'José Mopin Poblet', 'Boixereu', NULL, NULL, 1, 11),
(12, 'B77', '0201416069', 'Prolog programming for artificial inteligence', 'Bratko Ivan', 'Addison Wisley', NULL, NULL, 2, 11),
(13, 'H6', '020102988X', 'Introduction to automata thoery languages and computation', 'John E. Hopcroft', 'Addison Wisley', NULL, NULL, 1, 11),
(14, 'S3', '8476152078', 'Turbo Prolog, programación avanzada', 'Herbert Schildt', 'McGraw Hill', NULL, NULL, 1, 11),
(15, 'P4', '9701510126', 'MySQL para Windows y Linux', 'César Pérez', 'Alfaomega', NULL, NULL, 1, 2),
(16, 'W5', '0201533774', 'Artificial intelligence', 'Patrick H. Winston', 'Addison Wisley', NULL, NULL, 3, 11),
(17, 'L5', '089435235', 'Practical applications for expert systems', 'Susan Lindsay', 'QED', NULL, NULL, 1, 11),
(18, 'A4', '020118043X', 'Logic programming and knowledge engineering', 'Amble Tore', 'Addison Wisley', NULL, NULL, 1, 11),
(19, 'I6', '0471503460', 'Intelligent databases', 'Kamran Parsaye', 'Wiley', NULL, NULL, 1, 15),
(20, 'C6', '0394338111', 'Compstat', 'Leonard Presby', 'Random House Bussiness Division', NULL, NULL, 2, 9),
(21, 'S73', '9681812026', 'Principios de procesamiento de datos', 'Robert A. Stern', 'Limusa', NULL, NULL, 1, 2),
(22, 'P2', '8476154933', 'Autómatas programables', 'Alejandro Porras Criado', 'McGraw Hill', NULL, NULL, 1, 11),
(23, 'K4', '0135187052', 'Teoría de autómatas y lenguajes formales', 'Dean Kelley', 'Prentice Hall', NULL, NULL, 1, 5),
(24, 'C3', '9701503287', 'Seguridad informática', 'Caballero Pino', 'Alfaomega', NULL, NULL, 1, 16),
(25, 'T4', '9701503511', 'Técnicas criptográficas de protección de datos', 'Amparo Fúster Sabater', 'Alfaomega', NULL, NULL, 1, 16),
(26, 'A42', '8476159021', 'Introducción a los PC', 'Bob Albercht', 'McGraw Hill', NULL, NULL, 1, 2),
(27, 'F6', '0471941522', 'Simply logical', 'Petr Flach', 'John Wiley and sons', NULL, NULL, 1, 12),
(28, 'J3', '9684031009', 'Computadoras electrónicas', 'Henry Jacobwitz', 'Minerva', NULL, NULL, 1, 7),
(29, 'A5', '0894351737', 'Microcomputer decision support systems', 'Stephen J. Andriole', 'QES information science', NULL, NULL, 1, 12),
(30, 'H8', '8428321655', 'Comunicaciones de voz y datos', 'Juan Manuel Huidobro', 'Paraninfo', NULL, NULL, 2, 13),
(31, 'V5', '9681819411', 'Teoría de la comunicación', 'Ismael Vidales Delgado', 'Limusa Noriega', NULL, NULL, 1, 3),
(32, 'F5', '0471578061', 'Object oriented requirements analysis and logical design', 'Donald G. Firesmith', 'John Wiley and sons', NULL, NULL, 1, 12),
(33, 'T4', '0202631121', 'MS-DOS 6.2', 'Telsys Ingenética', 'Addison Wisley', NULL, NULL, 1, 14),
(34, 'E5', '0023338202', 'Say it clearly', 'Susan Lewis English', 'Collier McMillan', NULL, NULL, 1, 5),
(35, 'L11I', '8471461749', 'Introducción al proceso de datos', 'Robert G. Langenbach', 'Técnicos y asociados', NULL, NULL, 1, 2),
(36, 'A6', '9681809610', 'Aplicaciones en tiempo real', 'Maurice Blackman', 'Limusa', NULL, NULL, 1, 2),
(37, 'P3', '0849371716', 'Algorithms and data structures in C++', 'Alan Parker', 'CRC Press', NULL, NULL, 1, 2),
(38, 'A4', '9681830466', 'Estructura de datos', 'Miren Begoña Albizuri Romero', 'Limusa Noriega', NULL, NULL, 1, 2),
(39, 'T3', '970100101X', 'Procesamiento de datos en Unix', 'R. S. Tare', 'McGraw Hill', NULL, NULL, 1, 2),
(40, 'T4', '0471543640', 'Object oriented information systems', 'David A. Taylor', 'Wiley', NULL, NULL, 1, 6),
(41, 'K46', '00783333024', 'Discrete simulation systems', 'B. Khoshnevis', 'McGraw Hill', NULL, NULL, 1, 6),
(42, 'C37', '9681814614', 'Sistemas de administración de bancos de datos', 'Alfonso F. Cárdenas', 'Limusa Noriega', NULL, NULL, 1, 6),
(43, 'V5', '9586005054', 'Diseño y manejo de estructuras de datos en C', 'Joge A. Villalobos S.', 'McGraw Hill', NULL, NULL, 1, 6),
(44, 'H5', '0471928674', 'Hihg performance computing research and practice in Japan', 'John Willie', 'Wiley', NULL, NULL, 1, 7),
(45, 'C4', '0070038996', 'Digital computer fundamentals', 'C. Bartee Thomas', 'McGraw Hill', NULL, NULL, 1, 7),
(46, 'E1', '9688804061', 'Toda la PC', 'Peter Norton', 'PHH', NULL, NULL, 1, 7),
(47, 'I5', '1555121039', 'Programmable logic', 'Intel', '', NULL, NULL, 1, 7),
(48, 'M3', '0314875603', 'Computers and data processing', 'Steven L. Mandell', 'West publishing company', NULL, NULL, 3, 7),
(49, 'H6', '0070289778', 'Microcontrollers', 'Hintz Tabak', 'McGraw Hill', NULL, NULL, 1, 17),
(50, 'D3', '0471896160', 'The design and description of computer architectures', 'Subrata Dasgupta', 'Wiley', NULL, NULL, 1, 17),
(51, 'Z3', '0895883775', 'From chips to systems', 'Rodnay Zaks', 'Sybex', NULL, NULL, 1, 17),
(52, 'O4', '9684272103', 'La informática en México', 'Enrique Olivares', 'Nuestro tiempo', NULL, NULL, 1, 17),
(53, 'D3', '0078818184', 'Microsoft Access', 'Mary Campbell', 'McGraw Hill', NULL, NULL, 1, 15),
(54, 'J6', '0070326274', 'Ada, applications and administration', 'Philip I. Johnson', 'McGraw Hill', NULL, NULL, 1, 15),
(55, 'S3', '0070568804', 'First look DOS 6.0', 'Ruth Schmitz', 'McGraw Hill', NULL, NULL, 1, 15),
(56, 'W53', '0070701334', 'File organization for database design', 'Gio Wiederhold', 'McGraw Hill', NULL, NULL, 1, 15),
(57, 'B7', '8448131843', 'Perl sin errores', 'Martin Brown', 'McGraw Hill', NULL, NULL, 1, 15),
(58, 'C4', '0070662150', 'Distributed databases, principles and systems', 'Stefano Ceri', 'McGraw Hill', NULL, NULL, 1, 15),
(59, 'D6', '8448100417', 'Introducción a dBase', 'Pedro Luis Moreno Martín', 'McGraw Hill', NULL, NULL, 1, 15),
(60, 'K4', '9688802050', 'El lenguaje de programación C', 'Brian W. Kernighan', 'Prentice Hall', NULL, NULL, 2, 8),
(61, 'L6', '968880083X', 'Programación en BASIC', 'Larry Long', 'Prentice Hall', NULL, NULL, 1, 8),
(62, 'D3', '0471561525', 'C pointers and dynamic memory management', 'Michael C. Daconta', 'Wiley', NULL, NULL, 1, 8),
(63, 'C1', '0071008497', 'Learning C++', 'Neill Graham', 'McGraw Hill', NULL, NULL, 1, 8),
(64, 'P7', '9701510151', 'Domine ASP.NET', 'Joan J. Pratdepadua', 'Alfaomega', NULL, NULL, 1, 8),
(65, 'C7', '0471571598', 'Advanced graphics programming using C/C++', 'Loren Heiny', 'Wiley', NULL, NULL, 1, 8),
(66, 'C6', '8448101316', 'Corel Draw 3', 'Emil Ihring', 'McGraw Hill', NULL, NULL, 1, 19),
(67, 'W3', '9681851374', 'Excel para inexpertos', 'Dummies', 'Megabyte', NULL, NULL, 1, 19),
(68, 'R7', '0849325161', 'The image processing handbook', 'John C. Russ', 'Wiley', NULL, NULL, 2, 19),
(69, 'Q9', '8448119975', 'Quattro Pro 5 a su alcance', 'Lisa Biow', 'McGraw Hill', NULL, NULL, 1, 19),
(70, 'L49', '970100096X', 'Virus informáticos', 'Richard B. Levin', 'McGraw Hill', NULL, NULL, 1, 19),
(71, 'W1', '844811165', 'Programación avanzada en Windows', 'Jeffrey Richter', 'McGraw Hill', NULL, NULL, 1, 19),
(72, 'N5', NULL, 'El porvenir de la filosofía', 'Eduardo Nicol', 'Fondo de Cultura Económica', NULL, NULL, 1, 20),
(73, 'M8', NULL, 'La psicología contemporánea', 'F. L. Mueller', 'Fonde de Cultura Económica', NULL, NULL, 1, 20),
(74, 'H4', NULL, 'Psicologías del siglo XX', 'Edna Heidbreder', 'Paidos', NULL, NULL, 1, 20),
(75, 'D8', '9682432723', 'Psicología industrial', 'Marvin Dunnette', 'Trillas', NULL, NULL, 1, 28),
(76, 'S5', NULL, 'Psicología en las organizaciones industriales', 'Laurence Siegel', 'CECSA', NULL, NULL, 1, 28),
(77, 'S3', '9701000358', 'Psicología industrial', 'D. P. Schultz', 'McGraw Hill', NULL, NULL, 3, 28),
(78, 'H4', NULL, 'Psicologías del siglo XX', 'Edna Heidbreder', 'Paidos', NULL, NULL, 1, 20),
(79, 'C64', '970665092X', 'Código de ética profesional', 'IMCP', 'IMCP', NULL, NULL, 5, 30),
(80, 'C6', '9687681012', 'Código de ética', 'ECAFSA', 'Thompson', NULL, NULL, 1, 30),
(81, 'B3', NULL, 'Estudios de sociología industrial', 'Baseiga Eduardo', 'Ciencias sociales', NULL, NULL, 1, 32),
(82, 'B6', NULL, 'Introducción a la sociología', 'T. B. Bottomore', 'Península', NULL, NULL, 1, 32),
(83, 'A4', NULL, 'Sociología general', 'Amaya Serrano', 'McGraw Hill', NULL, NULL, 1, 32),
(84, 'P4', NULL, 'Sociología en las organizaciones', 'Charles Perrow', 'McGraw Hill', NULL, NULL, 1, 32),
(85, 'C8', NULL, 'Población y desarrollo en América Latina', 'CEPAL', 'CFE', NULL, NULL, 1, 34),
(86, 'T8', '9682507197', 'Tratado de ecología', 'Turk y Wittes', 'Interamericana', NULL, NULL, 2, 35),
(87, 'C6', '8471146975', 'Auditorías medioambientales', 'Vicente Conesa Fernández', 'Mundi-prensa', NULL, NULL, 2, 35),
(88, 'T2', '9682304482', 'La reforma política y los partidos en México', 'Octavio Rodríguez Araujo', 'XXI', NULL, NULL, 6, 39),
(89, 'T3', NULL, 'La política de Estados Unidos en América Latina', 'Arthur S. Link', 'CFE', NULL, NULL, 1, 40),
(90, 'T21', NULL, 'La política exterior en México', 'Manuel Tello', 'CFE', NULL, NULL, 1, 40),
(91, 'D8', NULL, 'Los partidos políticos', 'Maurice Duverger', 'CFE', NULL, NULL, 1, 39),
(92, 'B15', NULL, 'Teoría económica', 'Gary S. Becker', 'CFE', NULL, NULL, 1, 41),
(93, 'C3', '9681607937', 'Excedente y reproducción', 'Jean Cartelier', 'CFE', NULL, NULL, 1, 41),
(94, 'L11', '9681605578', 'Economía política II', 'Oskar Lange', 'CFE', NULL, NULL, 1, 41),
(95, 'M4', '9701010205', 'Fundamentos de economía', 'J. Silvestre Méndez', 'McGraw Hill', NULL, NULL, 3, 41),
(96, 'G6', '9687270217', 'Tratado moderno de economía general', 'Antonio J. González', 'Western', NULL, NULL, 1, 41),
(97, 'S2', '8448106075', 'Economía', 'Paul A. Samuelson', 'McGraw Hill', NULL, NULL, 15, 41),
(98, 'S25', '8403182805', 'Curso de economía moderna', 'Paul A. Samuelson', 'Aguilar', NULL, NULL, 1, 41),
(99, 'B7', '9682501075', 'Economía y administración', 'Eugene F. Birgham', 'Interamericana', NULL, NULL, 2, 42),
(100, 'V2', NULL, 'Valor y capital', 'J. R. Hicks', 'CFE', NULL, NULL, 1, 42),
(101, 'S4', NULL, 'Orígenes del capitalismo moderno', 'Henri See', 'CFE', NULL, NULL, 1, 43),
(102, 'A5', '9701503988', 'Diseño digital', 'Ramón Alcubilla', 'Alfaomega', NULL, NULL, 3, 7),
(103, 'A8', '0201629003', 'Fisicoquímica', 'P. W. Atkins', 'Iberoamericana', NULL, NULL, 3, 80);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `books`
--
CREATE TABLE IF NOT EXISTS `books` (
`idbook` int(11) unsigned
,`code` varchar(5)
,`isbn` char(13)
,`title` varchar(60)
,`author` varchar(45)
,`editorial` varchar(40)
,`edition` tinyint(4) unsigned
,`idclass` int(11) unsigned
,`name` varchar(50)
);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `borrow`
--

CREATE TABLE IF NOT EXISTS `borrow` (
  `idborrow` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mem_code` varchar(10) NOT NULL,
  `mem_name` varchar(45) NOT NULL,
  `init_date` date NOT NULL,
  `final_date` date NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `copy_id` int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idborrow`),
  FULLTEXT KEY `idx_member` (`mem_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `borrow`
--

INSERT INTO `borrow` (`idborrow`, `mem_code`, `mem_name`, `init_date`, `final_date`, `user_id`, `copy_id`, `status`) VALUES
(1, '08450528', 'Francisco', '2012-06-15', '2012-06-16', 1, 3, 0);

--
-- Disparadores `borrow`
--
DROP TRIGGER IF EXISTS `available`;
DELIMITER //
CREATE TRIGGER `available` AFTER UPDATE ON `borrow`
 FOR EACH ROW BEGIN
IF NEW.status = 2
THEN UPDATE copy SET availability = TRUE WHERE NEW.copy_id = idcopy;
END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `unavailable`;
DELIMITER //
CREATE TRIGGER `unavailable` AFTER INSERT ON `borrow`
 FOR EACH ROW BEGIN
UPDATE copy SET availability = FALSE WHERE New.copy_id = idcopy;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `borrows`
--
CREATE TABLE IF NOT EXISTS `borrows` (
`idborrow` int(10) unsigned
,`mem_code` varchar(10)
,`mem_name` varchar(45)
,`init_date` date
,`final_date` date
,`title` varchar(60)
,`copy_num` smallint(5) unsigned
,`usfname` varchar(50)
,`status` tinyint(1)
);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `class`
--

CREATE TABLE IF NOT EXISTS `class` (
  `idclass` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `clmain` smallint(3) unsigned zerofill NOT NULL,
  `clsub` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`idclass`),
  UNIQUE KEY `uqnum` (`clmain`,`clsub`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=81 ;

--
-- Volcado de datos para la tabla `class`
--

INSERT INTO `class` (`idclass`, `clmain`, `clsub`, `name`) VALUES
(1, 001, 5, 'Teoría de bases de datos'),
(2, 001, 6, 'Sistemas gestores de bases de datos'),
(3, 001, 51, 'Ciencias de comunicación'),
(4, 001, 53, 'Inteligencia Artificial'),
(5, 001, 54, 'Autómatas y Lenguajes formales'),
(6, 001, 61, 'Estructura de datos'),
(7, 001, 64, 'Arquitectura de computadoras'),
(8, 001, 434, 'Experimentación'),
(9, 001, 515, 'Ciencias de la computación'),
(10, 001, 533, 'Sistemas Operativos'),
(11, 001, 535, 'IA y Sistemas Expertos'),
(12, 001, 538, 'Lógica digital'),
(13, 001, 539, 'Comunicación de datos'),
(14, 001, 542, 'Uso de sistemas operativos'),
(15, 001, 642, 'Optimización de bases de datos'),
(16, 001, 5436, 'Seguridad informática'),
(17, 001, 6404, 'Diseño de computadoras'),
(18, 001, 6424, 'Diseño de sistemas de información'),
(19, 001, 6425, 'Programas de computadoras'),
(20, 100, 0, 'Filosofía y psicología'),
(21, 108, 0, 'Filosofía cognitiva'),
(22, 109, 0, 'Historia de la psicología'),
(23, 155, 92, 'Psicología social'),
(24, 141, 0, 'Idealismo'),
(25, 146, 4, 'Positivismo lógico'),
(26, 150, 0, 'Psicología contemporánea'),
(27, 153, 9, 'Inteligencia humana'),
(28, 158, 7, 'Psicología industrial'),
(29, 160, 0, 'Lógica'),
(30, 174, 0, 'Ética profesional'),
(31, 300, 0, 'Ciencias sociales'),
(32, 301, 0, 'Sociología'),
(33, 302, 0, 'Dinámica y psicología social'),
(34, 304, 0, 'Población y desarrollo'),
(35, 304, 2, 'Ecología'),
(36, 306, 0, 'Cultura'),
(37, 312, 0, 'Democracia'),
(38, 320, 1, 'Socialismo'),
(39, 324, 2, 'Partidos políticos en México'),
(40, 327, 3, 'Política exterior'),
(41, 330, 0, 'Teoría económica'),
(42, 330, 1, 'Economía y administración'),
(43, 330, 122, 'Capitalismo en América Latina'),
(44, 330, 15, 'Historia de la teoría económica'),
(45, 330, 153, 'Historia de la economía política'),
(46, 330, 41, 'Inflación'),
(47, 333, 7, 'Economía y medio ambiente'),
(48, 335, 4, 'Teoría del socialismo'),
(49, 336, 0, 'Economía pública'),
(50, 338, 5, 'Microeconomía'),
(51, 338, 521, 'Política de precios'),
(52, 339, 0, 'Macroeconomía'),
(53, 340, 0, 'Derecho civil'),
(54, 350, 0, 'Administración pública'),
(55, 370, 712, 'Métodos de enseñanza'),
(56, 382, 0, 'Economía internacional'),
(57, 510, 0, 'Matemáticas'),
(58, 511, 3, 'Lógica y algoritmos'),
(59, 511, 8, 'Algoritmos computacionales'),
(60, 512, 0, 'Álgebra'),
(61, 512, 5, 'Álgebra lineal'),
(62, 515, 15, 'Cálculo'),
(63, 515, 35, 'Ecuaciones diferenciales'),
(64, 515, 63, 'Análisis vectorial'),
(65, 516, 3, 'Geometría analítica'),
(66, 519, 4, 'Matemáticas discretas'),
(67, 519, 5, 'Estadítica aplicada'),
(68, 530, 0, 'Física'),
(69, 532, 0, 'Mecánica de fluidos'),
(70, 536, 7, 'Termodinámica'),
(71, 537, 0, 'Electricidad y magnetismo'),
(72, 540, 0, 'Química'),
(73, 547, 0, 'Química orgánica'),
(74, 614, 852, 'Seguridad industrial'),
(75, 620, 112, 'Resistencia de materiales'),
(76, 658, 0, 'Administración'),
(77, 660, 2, 'Ingeniería química'),
(78, 526, 9, 'Topografía'),
(79, 624, 0, 'Ingeniería Civil'),
(80, 539, 0, 'Físicoquímica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `copy`
--

CREATE TABLE IF NOT EXISTS `copy` (
  `idcopy` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `copy_num` smallint(5) unsigned NOT NULL,
  `availability` tinyint(1) NOT NULL DEFAULT '1',
  `id_book` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idcopy`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=203 ;

--
-- Volcado de datos para la tabla `copy`
--

INSERT INTO `copy` (`idcopy`, `copy_num`, `availability`, `id_book`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 1),
(3, 1, 0, 2),
(4, 2, 1, 2),
(5, 1, 1, 3),
(6, 2, 1, 3),
(7, 1, 1, 4),
(8, 1, 0, 5),
(9, 2, 0, 5),
(10, 1, 1, 6),
(11, 2, 1, 6),
(12, 3, 1, 6),
(13, 1, 1, 7),
(14, 1, 1, 8),
(15, 2, 1, 8),
(16, 1, 1, 9),
(17, 2, 1, 9),
(18, 1, 0, 10),
(19, 1, 1, 11),
(20, 2, 1, 11),
(21, 1, 1, 12),
(22, 2, 1, 12),
(23, 3, 1, 12),
(24, 1, 1, 13),
(25, 2, 1, 13),
(26, 3, 1, 13),
(27, 4, 1, 13),
(28, 5, 1, 13),
(29, 6, 1, 13),
(30, 7, 1, 13),
(31, 8, 1, 13),
(32, 9, 1, 13),
(33, 10, 1, 13),
(34, 1, 1, 14),
(35, 2, 1, 14),
(36, 3, 1, 14),
(37, 4, 1, 14),
(38, 5, 1, 14),
(39, 6, 1, 14),
(40, 1, 1, 15),
(41, 1, 1, 16),
(42, 2, 1, 16),
(43, 3, 1, 16),
(44, 1, 1, 17),
(45, 2, 1, 17),
(46, 1, 1, 18),
(47, 2, 1, 18),
(48, 3, 1, 18),
(49, 1, 1, 19),
(50, 2, 1, 19),
(51, 3, 1, 19),
(52, 1, 1, 20),
(53, 1, 1, 21),
(54, 2, 1, 21),
(55, 3, 1, 21),
(56, 4, 1, 21),
(57, 5, 1, 21),
(58, 6, 1, 21),
(59, 7, 1, 21),
(60, 8, 1, 21),
(61, 9, 1, 21),
(62, 10, 1, 21),
(63, 1, 1, 22),
(64, 1, 1, 22),
(65, 2, 1, 22),
(66, 1, 1, 23),
(67, 1, 1, 24),
(68, 2, 1, 24),
(69, 1, 1, 25),
(70, 1, 1, 26),
(71, 1, 1, 27),
(72, 2, 1, 27),
(73, 1, 1, 28),
(74, 2, 1, 28),
(75, 1, 1, 29),
(76, 1, 1, 30),
(77, 1, 1, 31),
(78, 1, 1, 32),
(79, 2, 1, 32),
(80, 1, 1, 33),
(81, 1, 1, 34),
(82, 2, 1, 34),
(83, 1, 1, 35),
(84, 2, 1, 35),
(85, 1, 1, 36),
(86, 2, 1, 36),
(87, 1, 1, 37),
(88, 1, 1, 38),
(89, 1, 1, 39),
(90, 1, 1, 40),
(91, 2, 1, 40),
(92, 3, 1, 40),
(93, 1, 1, 41),
(94, 1, 1, 42),
(95, 2, 1, 42),
(96, 1, 1, 43),
(97, 2, 1, 43),
(98, 3, 1, 43),
(99, 1, 1, 44),
(100, 2, 1, 44),
(101, 3, 1, 44),
(102, 1, 1, 45),
(103, 1, 1, 46),
(104, 1, 1, 47),
(105, 1, 1, 48),
(106, 2, 1, 48),
(107, 1, 1, 49),
(108, 1, 1, 50),
(109, 2, 1, 50),
(110, 3, 1, 50),
(111, 4, 1, 50),
(112, 5, 1, 50),
(113, 1, 1, 51),
(114, 1, 1, 52),
(115, 2, 1, 52),
(116, 1, 1, 53),
(117, 2, 1, 53),
(118, 1, 1, 54),
(119, 2, 1, 54),
(120, 3, 1, 54),
(121, 1, 1, 55),
(122, 1, 1, 56),
(123, 2, 1, 56),
(124, 1, 1, 57),
(125, 2, 1, 57),
(126, 1, 1, 58),
(127, 2, 1, 58),
(128, 1, 1, 59),
(129, 2, 1, 59),
(130, 3, 1, 59),
(131, 4, 1, 59),
(132, 5, 1, 59),
(133, 6, 1, 59),
(134, 7, 1, 59),
(135, 8, 1, 59),
(136, 9, 1, 59),
(137, 1, 1, 60),
(138, 1, 1, 61),
(139, 1, 1, 62),
(140, 1, 1, 63),
(141, 2, 1, 63),
(142, 1, 1, 64),
(143, 1, 1, 65),
(144, 2, 1, 65),
(145, 1, 1, 66),
(146, 1, 1, 67),
(147, 1, 1, 68),
(148, 1, 1, 69),
(149, 1, 1, 70),
(150, 1, 1, 71),
(151, 1, 1, 72),
(152, 1, 1, 73),
(153, 1, 1, 74),
(154, 1, 1, 75),
(155, 1, 1, 76),
(156, 2, 1, 76),
(157, 3, 1, 76),
(158, 4, 1, 76),
(159, 2, 1, 77),
(160, 3, 1, 77),
(161, 1, 1, 78),
(162, 2, 1, 78),
(163, 3, 1, 78),
(164, 4, 1, 78),
(165, 1, 1, 79),
(166, 1, 1, 80),
(167, 2, 1, 80),
(168, 1, 1, 81),
(169, 2, 1, 81),
(170, 3, 1, 81),
(171, 4, 1, 81),
(172, 5, 1, 81),
(173, 6, 1, 81),
(174, 7, 1, 81),
(175, 8, 1, 81),
(176, 9, 1, 81),
(177, 1, 1, 82),
(178, 1, 1, 83),
(179, 2, 1, 83),
(180, 1, 1, 84),
(181, 1, 1, 85),
(182, 1, 1, 86),
(183, 1, 1, 87),
(184, 2, 1, 87),
(185, 1, 1, 88),
(186, 1, 1, 89),
(187, 1, 1, 90),
(188, 1, 1, 91),
(189, 1, 1, 92),
(190, 1, 1, 93),
(191, 1, 1, 94),
(192, 2, 1, 94),
(193, 3, 1, 94),
(194, 1, 1, 95),
(195, 1, 1, 96),
(196, 1, 0, 97),
(197, 1, 1, 98),
(198, 1, 1, 99),
(199, 1, 1, 100),
(200, 1, 1, 101),
(201, 1, 1, 102),
(202, 1, 1, 103);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `indexCard`
--
CREATE TABLE IF NOT EXISTS `indexCard` (
`idbook` int(11) unsigned
,`clmain` smallint(3) unsigned zerofill
,`clsub` smallint(5) unsigned
,`code` varchar(5)
,`isbn` char(13)
,`title` varchar(60)
,`author` varchar(45)
,`editorial` varchar(40)
,`publ_place` varchar(20)
,`publ_year` year(4)
,`edition` tinyint(4) unsigned
,`av` decimal(25,0)
,`tt` smallint(5) unsigned
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `numav`
--
CREATE TABLE IF NOT EXISTS `numav` (
`id_book` int(10) unsigned
,`av` decimal(25,0)
,`tt` smallint(5) unsigned
);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `iduser` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usfname` varchar(50) NOT NULL,
  `usuname` varchar(10) NOT NULL,
  `uspswd` char(32) NOT NULL,
  `usstat` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`iduser`),
  UNIQUE KEY `uq_fname` (`usfname`),
  UNIQUE KEY `uq_uname` (`usuname`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`iduser`, `usfname`, `usuname`, `uspswd`, `usstat`) VALUES
(1, 'Administrador', 'admin', '21232f297a57a5a743894a0e4a801fc3', 1),
(2, 'Santos de Lira', 'santi.fire', 'b176932d78814a85692ee3fc24707368', 0);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `users`
--
CREATE TABLE IF NOT EXISTS `users` (
`iduser` int(10) unsigned
,`usfname` varchar(50)
,`usuname` varchar(10)
,`usstat` tinyint(1)
);
-- --------------------------------------------------------

--
-- Estructura para la vista `books`
--
DROP TABLE IF EXISTS `books`;

CREATE ALGORITHM=UNDEFINED DEFINER=`codemexi`@`localhost` SQL SECURITY DEFINER VIEW `books` AS select `book`.`idbook` AS `idbook`,`book`.`code` AS `code`,`book`.`isbn` AS `isbn`,`book`.`title` AS `title`,`book`.`author` AS `author`,`book`.`editorial` AS `editorial`,`book`.`edition` AS `edition`,`class`.`idclass` AS `idclass`,`class`.`name` AS `name` from (`book` join `class` on((`book`.`id_class` = `class`.`idclass`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `borrows`
--
DROP TABLE IF EXISTS `borrows`;

CREATE ALGORITHM=UNDEFINED DEFINER=`codemexi`@`localhost` SQL SECURITY DEFINER VIEW `borrows` AS select `borrow`.`idborrow` AS `idborrow`,`borrow`.`mem_code` AS `mem_code`,`borrow`.`mem_name` AS `mem_name`,`borrow`.`init_date` AS `init_date`,`borrow`.`final_date` AS `final_date`,`book`.`title` AS `title`,`copy`.`copy_num` AS `copy_num`,`user`.`usfname` AS `usfname`,`borrow`.`status` AS `status` from (((`borrow` join `user` on((`user`.`iduser` = `borrow`.`user_id`))) join `copy` on((`borrow`.`copy_id` = `copy`.`idcopy`))) join `book` on((`copy`.`id_book` = `book`.`idbook`))) where (`borrow`.`status` < 2);

-- --------------------------------------------------------

--
-- Estructura para la vista `indexCard`
--
DROP TABLE IF EXISTS `indexCard`;

CREATE ALGORITHM=UNDEFINED DEFINER=`codemexi`@`localhost` SQL SECURITY DEFINER VIEW `indexCard` AS select `book`.`idbook` AS `idbook`,`class`.`clmain` AS `clmain`,`class`.`clsub` AS `clsub`,`book`.`code` AS `code`,`book`.`isbn` AS `isbn`,`book`.`title` AS `title`,`book`.`author` AS `author`,`book`.`editorial` AS `editorial`,`book`.`publ_place` AS `publ_place`,`book`.`publ_year` AS `publ_year`,`book`.`edition` AS `edition`,`numav`.`av` AS `av`,`numav`.`tt` AS `tt` from ((`book` join `class` on((`class`.`idclass` = `book`.`id_class`))) join `numav` on((`book`.`idbook` = `numav`.`id_book`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `numav`
--
DROP TABLE IF EXISTS `numav`;

CREATE ALGORITHM=UNDEFINED DEFINER=`codemexi`@`localhost` SQL SECURITY DEFINER VIEW `numav` AS select `copy`.`id_book` AS `id_book`,sum(`copy`.`availability`) AS `av`,max(`copy`.`copy_num`) AS `tt` from `copy` group by `copy`.`id_book`;

-- --------------------------------------------------------

--
-- Estructura para la vista `users`
--
DROP TABLE IF EXISTS `users`;

CREATE ALGORITHM=UNDEFINED DEFINER=`codemexi`@`localhost` SQL SECURITY DEFINER VIEW `users` AS select `user`.`iduser` AS `iduser`,`user`.`usfname` AS `usfname`,`user`.`usuname` AS `usuname`,`user`.`usstat` AS `usstat` from `user`;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
