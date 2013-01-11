CREATE TABLE `transactions` (
  `ID_MVT` int(11) NOT NULL auto_increment,
  `TYPE_MVT` tinyint(4) default NULL,
  `DATA` text,
  PRIMARY KEY  (`ID_MVT`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
