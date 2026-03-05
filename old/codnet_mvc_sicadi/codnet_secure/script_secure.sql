--
-- Estructura de tabla para la tabla `cdt_action_function`
--

CREATE TABLE IF NOT EXISTS `cdt_action_function` (
  `cd_actionfunction` int(11) NOT NULL AUTO_INCREMENT,
  `cd_function` int(11) NOT NULL,
  `ds_action` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`cd_actionfunction`),
  UNIQUE KEY `ds_action` (`ds_action`),
  KEY `fk_funtion` (`cd_function`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `cdt_action_function`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cdt_function`
--

CREATE TABLE IF NOT EXISTS `cdt_function` (
  `cd_function` int(11) NOT NULL AUTO_INCREMENT,
  `ds_function` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`cd_function`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=41 ;

--
-- Volcar la base de datos para la tabla `cdt_function`
--

INSERT INTO `cdt_function` (`cd_function`, `ds_function`) VALUES
(1, 'Add CdtActionFunction'),
(2, 'Delete CdtActionFunction'),
(3, 'View CdtActionFunction'),
(4, 'Update CdtActionFunction'),
(5, 'List CdtActionFunction'),
(6, 'Add CdtFunction'),
(7, 'Delete CdtFunction'),
(8, 'View CdtFunction'),
(9, 'Update CdtFunction'),
(10, 'List CdtFunction'),
(11, 'Add CdtMenuGroup'),
(12, 'Delete CdtMenuGroup'),
(13, 'View CdtMenuGroup'),
(14, 'Update CdtMenuGroup'),
(15, 'List CdtMenuGroup'),
(16, 'Add CdtMenuOption'),
(17, 'Delete CdtMenuOption'),
(18, 'View CdtMenuOption'),
(19, 'Update CdtMenuOption'),
(20, 'List CdtMenuOption'),
(21, 'Add CdtRegistration'),
(22, 'Delete CdtRegistration'),
(23, 'View CdtRegistration'),
(24, 'Update CdtRegistration'),
(25, 'List CdtRegistration'),
(26, 'Add CdtUser'),
(27, 'Delete CdtUser'),
(28, 'View CdtUser'),
(29, 'Update CdtUser'),
(30, 'List CdtUser'),
(31, 'Add CdtUserGroup'),
(32, 'Delete CdtUserGroup'),
(33, 'View CdtUserGroup'),
(34, 'Update CdtUserGroup'),
(35, 'List CdtUserGroup'),
(36, 'Add CdtUserGroupFunction'),
(37, 'Delete CdtUserGroupFunction'),
(38, 'View CdtUserGroupFunction'),
(39, 'Update CdtUserGroupFunction'),
(40, 'List CdtUserGroupFunction');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cdt_menugroup`
--

CREATE TABLE IF NOT EXISTS `cdt_menugroup` (
  `cd_menugroup` int(11) NOT NULL AUTO_INCREMENT,
  `nu_order` int(11) NOT NULL,
  `nu_width` int(11) NOT NULL,
  `ds_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `ds_action` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `ds_cssclass` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`cd_menugroup`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Volcar la base de datos para la tabla `cdt_menugroup`
--

INSERT INTO `cdt_menugroup` (`cd_menugroup`, `nu_order`, `nu_width`, `ds_name`, `ds_action`, `ds_cssclass`) VALUES
(1, 1, 65, 'Acceso', 'panel_control&currentMenuGroup=1', 'accesos'),
(2, 6, 60, 'Salir', 'salir', NULL),
(3, 5, 60, 'Administración', 'panel_control&currentMenuGroup=3', 'config');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cdt_menuoption`
--

CREATE TABLE IF NOT EXISTS `cdt_menuoption` (
  `cd_menuoption` int(11) NOT NULL AUTO_INCREMENT,
  `ds_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `ds_href` varchar(255) CHARACTER SET latin1 NOT NULL,
  `cd_function` int(11) DEFAULT NULL,
  `nu_order` int(11) DEFAULT NULL,
  `cd_menugroup` int(11) DEFAULT NULL,
  `ds_cssclass` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `ds_description` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`cd_menuoption`),
  KEY `cd_function` (`cd_function`),
  KEY `fk_menuoption_menugroup1` (`cd_menugroup`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Volcar la base de datos para la tabla `cdt_menuoption`
--

INSERT INTO `cdt_menuoption` (`cd_menuoption`, `ds_name`, `ds_href`, `cd_function`, `nu_order`, `cd_menugroup`, `ds_cssclass`, `ds_description`) VALUES
(1, 'CdtActionFunctions', 'doAction?action=list_cdtactionfunctions', NULL, 5, 1, 'cdtactionfunctions', 'Action Function'),
(2, 'CdtFunctions', 'doAction?action=list_cdtfunctions', NULL, 5, 1, 'cdtfunctions', 'Functions'),
(3, 'CdtMenuGroups', 'doAction?action=list_cdtmenugroups', NULL, 5, 1, 'cdtmenugroups', 'Menu groups'),
(4, 'CdtMenuOptions', 'doAction?action=list_cdtmenuoptions', NULL, 5, 1, 'cdtmenuoptions', 'Menu options'),
(5, 'Registrations', 'doAction?action=list_cdtregistrations', NULL, 5, 1, 'cdtregistrations', ''),
(6, 'Users', 'doAction?action=list_cdtusers', NULL, 5, 1, 'cdtusers', ''),
(7, 'Usergroups', 'doAction?action=list_cdtusergroups', NULL, 5, 1, 'cdtusergroups', ''),
(8, 'CdtUserGroupFunctions', 'doAction?action=list_cdtusergroupfunctions', NULL, 5, 1, 'cdtusergroupfunctions', 'Usergroup Functions'),
(9, 'Salir', 'doAction?action=exit', NULL, 1, 4, 'exit', 'Salir');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cdt_registration`
--

CREATE TABLE IF NOT EXISTS `cdt_registration` (
  `cd_registration` int(11) NOT NULL AUTO_INCREMENT,
  `ds_activationcode` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `dt_date` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `ds_username` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `ds_name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ds_email` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ds_password` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `ds_phone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ds_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cd_registration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `cdt_registration`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cdt_user`
--

CREATE TABLE IF NOT EXISTS `cdt_user` (
  `cd_user` int(11) NOT NULL AUTO_INCREMENT,
  `ds_username` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `ds_name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ds_email` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ds_password` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cd_usergroup` int(11) NOT NULL,
  `ds_phone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ds_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cd_user`),
  KEY `fk_usergroup` (`cd_usergroup`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Volcar la base de datos para la tabla `cdt_user`
--

INSERT INTO `cdt_user` (`cd_user`, `ds_username`, `ds_name`, `ds_email`, `ds_password`, `cd_usergroup`, `ds_phone`, `ds_address`) VALUES
(1, 'admin', 'CODNET', 'info@codnet.com.ar', '21232f297a57a5a743894a0e4a801fc3', 1, '', '');

ALTER TABLE `cdt_user` ADD `ds_ips` VARCHAR( 25 ) NOT NULL ;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cdt_usergroup`
--

CREATE TABLE IF NOT EXISTS `cdt_usergroup` (
  `cd_usergroup` int(11) NOT NULL AUTO_INCREMENT,
  `ds_usergroup` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cd_usergroup`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Volcar la base de datos para la tabla `cdt_usergroup`
--

INSERT INTO `cdt_usergroup` (`cd_usergroup`, `ds_usergroup`) VALUES
(1, 'Administrator'),
(2, 'Usuario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cdt_usergroup_function`
--

CREATE TABLE IF NOT EXISTS `cdt_usergroup_function` (
  `cd_usergroup_function` int(11) NOT NULL AUTO_INCREMENT,
  `cd_usergroup` int(11) NOT NULL,
  `cd_function` int(11) NOT NULL,
  PRIMARY KEY (`cd_usergroup_function`),
  KEY `fk_usergroup` (`cd_usergroup`),
  KEY `fk_function` (`cd_function`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=41 ;

--
-- Volcar la base de datos para la tabla `cdt_usergroup_function`
--

INSERT INTO `cdt_usergroup_function` (`cd_usergroup_function`, `cd_usergroup`, `cd_function`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 1, 8),
(9, 1, 9),
(10, 1, 10),
(11, 1, 11),
(12, 1, 12),
(13, 1, 13),
(14, 1, 14),
(15, 1, 15),
(16, 1, 16),
(17, 1, 17),
(18, 1, 18),
(19, 1, 19),
(20, 1, 20),
(21, 1, 21),
(22, 1, 22),
(23, 1, 23),
(24, 1, 24),
(25, 1, 25),
(26, 1, 26),
(27, 1, 27),
(28, 1, 28),
(29, 1, 29),
(30, 1, 30),
(31, 1, 31),
(32, 1, 32),
(33, 1, 33),
(34, 1, 34),
(35, 1, 35),
(36, 1, 36),
(37, 1, 37),
(38, 1, 38),
(39, 1, 39),
(40, 1, 40);


INSERT INTO `cdt_action_function` (`cd_function`, `ds_action`) VALUES
(1, 'add_cdtactionfunction_init'),
(1, 'add_cdtactionfunction'),
(2, 'delete_cdtactionfunction'),
(3, 'view_cdtactionfunction'),
(4, 'update_cdtactionfunction'),
(4, 'update_cdtactionfunction_init'),
(5, 'list_cdtactionfunctions'),
(6, 'add_cdtfunction'),
(6, 'add_cdtfunction_init'),
(7, 'delete_cdtfunction'),
(8, 'view_cdtfunction'),
(9, 'update_cdtfunction'),
(9, 'update_cdtfunction_init'),
(10, 'list_cdtfunctions'),
(11, 'add_cdtmenugroup'),
(11, 'add_cdtmenugroup_init'),
(12, 'delete_cdtmenugroup'),
(13, 'view_cdtmenugroup'),
(14, 'update_cdtmenugroup'),
(14, 'update_cdtmenugroup_init'),
(15, 'list_cdtmenugroup'),
(16, 'add_cdtmenuoption'),
(16, 'add_cdtmenuoption_init'),
(17, 'delete_cdtmenuoption'),
(18, 'view_cdtmenuoption'),
(19, 'update_cdtmenuoption'),
(19, 'update_cdtmenuoption_init'),
(20, 'list_cdtmenuoption'),
(21, 'add_cdtregistration'),
(21, 'add_cdtregistration_init'),
(22, 'delete _cdtregistration'),
(23, 'view_cdtregistration'),
(24, 'update_cdtregistration'),
(24, 'update_cdtregistration_init'),
(25, 'list_cdtregistration'),
(26, 'add_cdtuser'),
(26, 'add_cdtuser_init'),
(27, 'delete _cdtuser'),
(28, 'view_cdtuser'),
(29, 'update_cdtuser'),
(29, 'update_cdtuser_init'),
(30, 'list_cdtuser'),
(31, 'add_cdtusergroup'),
(31, 'add_cdtusergroup_init'),
(32, 'delete _cdtusergroup'),
(33, 'view_cdtusergroup'),
(34, 'update_cdtusergroup'),
(34, 'update_cdtusergroup_init'),
(35, 'list_cdtusergroup'),
(36, 'add_cdtusergroupfunction'),
(36, 'add_cdtusergroupfunction_init'),
(37, 'delete _cdtusergroupfunction'),
(38, 'view_cdtusergroupfunction'),
(39, 'update_cdtusergroupfunction'),
(39, 'update_cdtusergroupfunction_init'),
(40, 'list_cdtusergroupfunction');
--
-- Filtros para las tablas descargadas (dump)
--

--
-- Filtros para la tabla `cdt_action_function`
--
ALTER TABLE `cdt_action_function`
  ADD CONSTRAINT `cdt_action_function_ibfk_1` FOREIGN KEY (`cd_function`) REFERENCES `cdt_function` (`cd_function`);

--
-- Filtros para la tabla `cdt_menuoption`
--
ALTER TABLE `cdt_menuoption`
  ADD CONSTRAINT `cdt_menuoption_ibfk_1` FOREIGN KEY (`cd_function`) REFERENCES `cdt_function` (`cd_function`),
  ADD CONSTRAINT `cdt_menuoption_ibfk_2` FOREIGN KEY (`cd_menugroup`) REFERENCES `cdt_menugroup` (`cd_menugroup`);

--
-- Filtros para la tabla `cdt_user`
--
ALTER TABLE `cdt_user`
  ADD CONSTRAINT `cdt_user_ibfk_1` FOREIGN KEY (`cd_usergroup`) REFERENCES `cdt_usergroup` (`cd_usergroup`);

--
-- Filtros para la tabla `cdt_usergroup_function`
--
ALTER TABLE `cdt_usergroup_function`
  ADD CONSTRAINT `cdt_usergroup_function_ibfk_1` FOREIGN KEY (`cd_usergroup`) REFERENCES `cdt_usergroup` (`cd_usergroup`),
  ADD CONSTRAINT `cdt_usergroup_function_ibfk_2` FOREIGN KEY (`cd_function`) REFERENCES `cdt_function` (`cd_function`);

