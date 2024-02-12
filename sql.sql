create database if not exists user_ucs;
use user_ucs;
create table `user` (
 iduser bigint not null  ,
`name` varchar(255) null,
`email` varchar(200) not null, 
`status` tinyint DEFAULT 1,
primary key (iduser),
unique (email)
);
INSERT INTO `user` VALUES (1,'Admin','admin@admin.pt', 1);

create table `uc` (
iduc bigint not null ,
`code` varchar(20) not null,
`name` varchar(255) not null,
`status` tinyint DEFAULT 1,
 primary key (iduc),
 unique (code)
);

create table `user_uc` (
iduc bigint not null, 
`iduser` bigint not null, 
`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP not null ,
`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP  not null ON UPDATE CURRENT_TIMESTAMP,
`status` tinyint DEFAULT 2
, primary key (iduc,iduser),
FOREIGN KEY (iduser) REFERENCES user(iduser),
FOREIGN KEY (iduc) REFERENCES uc(iduc)
);