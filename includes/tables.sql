-- CREATE TABLE shortstory.user (
--   user_id INT AUTO_INCREMENT PRIMARY KEY,
--   u_name VARCHAR(50) NOT NULL UNIQUE,
--   password VARCHAR(255) NOT NULL,
--   email VARCHAR(100) NOT NULL UNIQUE,
--   contact VARCHAR(15),
--   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );


-- CREATE TABLE shortstory.author (
--   auth_id INT AUTO_INCREMENT PRIMARY KEY,
--   auth_name VARCHAR(50) NOT NULL
-- );


-- CREATE TABLE shortstory.genre (
--   genre_id INT AUTO_INCREMENT PRIMARY KEY,
--   genre_name VARCHAR(100) NOT NULL
-- );


-- CREATE TABLE shortstory.book (
--   book_id INT AUTO_INCREMENT PRIMARY KEY,
--   user_id INT NOT NULL,
--   title VARCHAR(200) NOT NULL,
--   author_id INT NOT NULL,
--   genre_id INT,
--   pub_year DATE,
--   price DECIMAL(10,2),
--   abstract TEXT,
--   cover_image VARCHAR(255),
--   FOREIGN KEY (author_id) REFERENCES shortstory.author(auth_id),
--   FOREIGN KEY (user_id) REFERENCES shortstory.user(user_id),
--   FOREIGN KEY (genre_id) REFERENCES shortstory.genre(genre_id)
-- );


-- CREATE TABLE shortstory.review (
--   rev_id INT AUTO_INCREMENT PRIMARY KEY,
--   user_id INT NOT NULL,
--   book_id INT NOT NULL,
--   r_title VARCHAR(200) NOT NULL,
--   review TEXT NOT NULL,
--   rating INT CHECK (rating BETWEEN 1 AND 5),
--   r_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   FOREIGN KEY (user_id) REFERENCES shortstory.user(user_id),
--   FOREIGN KEY (book_id) REFERENCES shortstory.book(book_id)
-- );
