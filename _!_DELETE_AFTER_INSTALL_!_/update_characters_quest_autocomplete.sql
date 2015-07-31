DROP TABLE IF EXISTS `character_autocompletequestlist`;
CREATE TABLE `character_autocompletequestlist` (
  `quest` INT(11) NOT NULL,
  PRIMARY KEY  (`quest`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

INSERT INTO `character_autocompletequestlist` VALUES (1000),(2000);
-- тут просто вставляешь IDы, таким образом: INSERT INTO `character_autocompletequestlist` VALUES (ID1), (ID2) ... ;

*/
