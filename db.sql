CREATE TABLE `images` ( 
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gallery_id` int(11) NOT NULL,
  `name` varchar(255) character SET utf8 NOT NULL,
  `description` TEXT,
  `image_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `path` varchar(255) character SET utf8 NOT NULL,
  `order_id` int(11),
  PRIMARY KEY  (`id`) 
) ENGINE = myisam;

CREATE TABLE `galleries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) character SET utf8 NOT NULL,
  `description` TEXT,
  `thumb_path` varchar(255) character SET utf8,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `url` varchar(64) character SET utf8,
  `order_id` int(11),
  PRIMARY KEY  (`id`) 
) ENGINE = myisam;

CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,  
  `name` varchar(255) character SET utf8 NOT NULL,
  `order_id` int(11),
  `description` TEXT,  
  `attachment_path` varchar(255) character SET utf8,
  PRIMARY KEY  (`id`) 
) ENGINE = myisam;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) character SET utf8 NOT NULL,
  `password` varchar(64) character SET utf8 NOT NULL,
  PRIMARY KEY  (`id`) 
) ENGINE = myisam;

CREATE TABLE `tags` (
	`name` varchar(64) character SET utf8 NOT NULL,
	`id` int(11) NOT NULL AUTO_INCREMENT,
	PRIMARY KEY  (`id`) 
) ENGINE = myisam;

-- item types
-- 1 : gallery
-- 2 : news

CREATE TABLE `tags_items` (
	`tag_id` int(11) NOT NULL,
	`item_id` int(11) NOT NULL,
	`item_type` int(11) NOT NULL,	
	PRIMARY KEY  (`item_id`, `item_type`, `tag_id`) 
) ENGINE = myisam;

