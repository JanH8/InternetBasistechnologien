CREATE DATABASE IF NOT EXISTS studyboard_db;

DRop Table if exists post;
drop table if exists forum;
drop table if exists student;

CREATE TABLE student (
    studentId int unsigned auto_increment,
    studentName varchar(255),
    password varchar(255),
    email varchar(255),
    color varchar(7),
    status char(1),
    PRIMARY KEY (studentId)
);

CREATE TABLE forum (
    forumId int unsigned auto_increment,
    creator int unsigned,
    forumName varchar(255),
    Primary Key (forumId),
    Foreign Key (creator) References student(studentId) 
);

CREATE TABLE post (
    postId int unsigned auto_increment,
    tstmp int unsigned,
    author int unsigned,
    forumId int unsigned,
    contend text, 
    Primary Key (postId),
    Foreign Key (author) References student(studentId),
 Foreign KEY (forumId) References forum(forumId)
);