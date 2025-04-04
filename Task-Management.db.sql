CREATE TABLE User (
    User_ID INTEGER PRIMARY KEY AUTOINCREMENT,
    User_Fname VARCHAR(100) NOT NULL,
    User_Lname VARCHAR(100) NOT NULL,
    User_Email VARCHAR(255) NOT NULL UNIQUE,
    User_TelNo VARCHAR(20) NOT NULL,
    User_Password VARCHAR(255) NOT NULL,
    User_Role VARCHAR(50) NOT NULL
);

CREATE TABLE GroupTable (
    Group_ID INTEGER PRIMARY KEY AUTOINCREMENT,
    Group_Name VARCHAR(255) NOT NULL,
    User_ID INTEGER NOT NULL,
    FOREIGN KEY (User_ID) REFERENCES User(User_ID) ON DELETE CASCADE
);

CREATE TABLE Task_List (
    List_ID INTEGER PRIMARY KEY AUTOINCREMENT,
    TaskList_Name VARCHAR(255) NOT NULL,
    TaskList_Description TEXT NOT NULL,
    User_ID INTEGER NOT NULL,
    FOREIGN KEY (User_ID) REFERENCES User(User_ID) ON DELETE CASCADE
);

CREATE TABLE Collaborator (
    Collaborator_ID INTEGER PRIMARY KEY AUTOINCREMENT,
    User_ID INTEGER NOT NULL,
    List_ID INTEGER NOT NULL,
    FOREIGN KEY (User_ID) REFERENCES User(User_ID) ON DELETE CASCADE,
    FOREIGN KEY (List_ID) REFERENCES Task_List(List_ID) ON DELETE CASCADE
);

CREATE TABLE Task (
    Task_ID INTEGER PRIMARY KEY AUTOINCREMENT,
    Task_Title VARCHAR(255) NOT NULL,
    Task_Description TEXT NOT NULL,
    Task_Deadline DATE NOT NULL,
    List_ID INTEGER NOT NULL,
    FOREIGN KEY (List_ID) REFERENCES Task_List(List_ID) ON DELETE CASCADE
);

CREATE TABLE Task_Progress (
    Progress_ID INTEGER PRIMARY KEY AUTOINCREMENT,
    Task_ID INTEGER NOT NULL,
    Status_ID INTEGER NOT NULL,
    FOREIGN KEY (Task_ID) REFERENCES Task(Task_ID) ON DELETE CASCADE
);

CREATE TABLE Group_Task (
    GroupTask_ID INTEGER PRIMARY KEY AUTOINCREMENT,
    GroupTask_Name VARCHAR(255) NOT NULL,
    GroupTask_Description TEXT NOT NULL,
    GroupTask_Status VARCHAR(50) NOT NULL,
    Group_ID INTEGER NOT NULL,
    FOREIGN KEY (Group_ID) REFERENCES GroupTable(Group_ID) ON DELETE CASCADE
);
