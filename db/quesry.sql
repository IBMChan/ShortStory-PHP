-- Drop existing tables (order matters because of foreign keys)
DROP TABLE IF EXISTS blogs;
DROP TABLE IF EXISTS book;
DROP TABLE IF EXISTS author;
DROP TABLE IF EXISTS user;

-- Recreate tables with VARCHAR PKs

CREATE TABLE user (
    user_id VARCHAR(50) PRIMARY KEY,
    u_name VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contact VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE author (
    auth_id VARCHAR(50) PRIMARY KEY,
    auth_name VARCHAR(50) NOT NULL
);

CREATE TABLE book (
    book_id VARCHAR(50) PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL,
    author_id VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    pub_year YEAR,
    price DECIMAL(10,2),
    abstract TEXT,
    genre VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (author_id) REFERENCES author(auth_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE blogs (
    blog_id VARCHAR(50) PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL,
    b_title VARCHAR(200) NOT NULL,
    b_intro VARCHAR(500),
    b_body VARCHAR(2000),
    b_con VARCHAR(500),
    b_comm VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);
