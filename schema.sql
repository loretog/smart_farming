-- Create the 'plantinfo' table
CREATE TABLE plantinfo (
    plantID INT AUTO_INCREMENT PRIMARY KEY,
    plantName VARCHAR(30),
    plantVariety VARCHAR(30)
);

-- Create the 'plantnutrionneed' table
CREATE TABLE plantnutrionneed (
    nutritionID INT AUTO_INCREMENT PRIMARY KEY,
    nutritionSetName VARCHAR(30),
    plantID INT, -- Foreign key to plantinfo table
    soilN INT(10),
    soilP INT(10),
    soilK INT(10),
    soilEC INT(10),
    soilPH FLOAT,
    soilT FLOAT,
    soilM FLOAT,
    flowRate FLOAT,
    FOREIGN KEY (plantID) REFERENCES plantinfo(plantID)
);

-- Create the 'users' table
CREATE TABLE users (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL, -- Store hashed passwords, never plain text
    email VARCHAR(100) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);
