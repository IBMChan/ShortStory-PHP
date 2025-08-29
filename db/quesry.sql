-- Database Schema//Table

-- user/
CREATE TABLE shortstory.user (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  u_name VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  contact VARCHAR(15),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--author/
CREATE TABLE shortstory.author (
  auth_id INT AUTO_INCREMENT PRIMARY KEY,
  auth_name VARCHAR(50) NOT NULL);

--book/
CREATE TABLE shortstory.book (
  book_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  author_id INT NOT NULL,
  pub_year DATE,
  price DECIMAL(10,2),
  abstract TEXT,
  FOREIGN KEY (author_id) REFERENCES shortstory.author(auth_id),
  FOREIGN KEY (user_id) REFERENCES shortstory.user(uesr_id)
);

--review/
CREATE TABLE shortstory.review (
  rev_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  book_id INT NOT NULL,
  r_title VARCHAR(200) NOT NULL,
  review TEXT NOT NULL,
  r_date DATE,
  FOREIGN KEY (user_id) REFERENCES shortstory.user(user_id),
  FOREIGN KEY (book_id) REFERENCES shortstory.book(book_id));