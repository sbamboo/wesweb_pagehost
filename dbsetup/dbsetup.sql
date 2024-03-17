CREATE DATABASE pagehost;

USE pagehost;

CREATE TABLE users (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(45),
    Password VARCHAR(80),
    DispName VARCHAR(45),
    ProfPic BLOB
);

CREATE TABLE posts (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Header VARCHAR(255),
    Content MEDIUMTEXT,
    AuthorID INT(11),
    ContentType INT(1)
);

CREATE TABLE accessees (
    PostID INT(11),
    UserID INT(11)
);

CREATE TABLE comments (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Content MEDIUMTEXT,
    AuthorID INT(11),
    ParentID INT(11),
    IsForPost TINYINT
);

INSERT INTO users (Username, Password, DispName) VALUES ("test","test","TestingAcc");