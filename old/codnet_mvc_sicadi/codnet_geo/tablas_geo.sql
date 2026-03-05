-- 
-- Estructura de tabla para la tabla `localidad`
-- 

CREATE TABLE `localidad` (
  `cd_localidad` int(11) NOT NULL AUTO_INCREMENT,
  `ds_localidad` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  `cd_provincia` int(11) NOT NULL,
  PRIMARY KEY (`cd_localidad`),
  KEY `cd_provincia` (`cd_provincia`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `pais`
-- 

CREATE TABLE `pais` (
  `cd_pais` int(11) NOT NULL AUTO_INCREMENT,
  `ds_pais` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`cd_pais`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `provincia`
-- 

CREATE TABLE `provincia` (
  `cd_provincia` int(11) NOT NULL AUTO_INCREMENT,
  `ds_provincia` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  `cd_pais` int(11) NOT NULL,
  PRIMARY KEY (`cd_provincia`),
  KEY `cd_pais` (`cd_pais`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=1 ;
