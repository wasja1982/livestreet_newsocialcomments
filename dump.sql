SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
INSERT INTO `prefix_user` (user_id,user_login) values (0,"guest");

ALTER TABLE `prefix_comment` 
        ADD  `guest_name` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
        ADD  `guest_email` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
        ADD  `guest_avatar` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL