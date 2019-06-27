CREATE TABLE abonnement
(
    student int(10) UNSIGNED NOT NULL,
    forum   int(10) UNSIGNED NOT NULL,
    PRIMARY KEY (student, forum),
    CONSTRAINT FK_abonementStudent FOREIGN KEY (student)
        REFERENCES student (studentId),
    CONSTRAINT FK_abonementForum FOREIGN KEY (forum)
        REFERENCES forum (forumId)
);

CREATE TABLE lastvisited
(
    id      int unsigned auto_increment,
    tstmp   int unsigned,
    forumId int unsigned,
    student int unsigned,
    PRIMARY KEY (id)
);