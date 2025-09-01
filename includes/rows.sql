
-- use shortstory;

-- INSERT INTO user (u_name, password, email, contact) VALUES
-- ('john_doe', '$2y$10$c7Jz.p1.pE.Ajjc4wG3JGe9j8L0tP/C4.2uO/n5r/6uF/n3r/2oE.', 'john.doe@example.com', '111-222-3333'),
-- ('jane_smith', '$2y$10$c7Jz.p1.pE.Ajjc4wG3JGe9j8L0tP/C4.2uO/n5r/6uF/n3r/2oE.', 'jane.smith@example.com', '444-555-6666');

-- INSERT INTO author (auth_name) VALUES
-- ('George Orwell'),
-- ('J.K. Rowling'),
-- ('Agatha Christie'),
-- ('Frank Herbert');

-- INSERT INTO genre (genre_name) VALUES
-- ('Dystopian'),
-- ('Fantasy'),
-- ('Mystery'),
-- ('Science Fiction');


-- INSERT INTO book (user_id, title, author_id, genre_id, pub_year, price, abstract, cover_image) VALUES
-- (1, '1984', 1, 1, '1949-06-08', 1299.00, 'A dystopian novel set in Airstrip One, a province of the superstate Oceania, in a world of perpetual war, omnipresent government surveillance, and public manipulation.', 'https://demobucketxyzmnoabc.s3.ap-south-1.amazonaws.com/assets/images/1/cover.jpg'),
-- (2, 'Harry Potter and the Sorcerer\'s Stone', 2, 2, '1997-06-26', 2099.00, 'The first novel in the Harry Potter series, following a young wizard, Harry Potter, and his friends Hermione Granger and Ron Weasley, all of whom are students at Hogwarts School of Witchcraft and Wizardry.', 'https://demobucketxyzmnoabc.s3.ap-south-1.amazonaws.com/assets/images/2/cover.jpg'),
-- (1, 'And Then There Were None', 3, 3, '1939-11-06', 999.00, 'Ten strangers are lured to an isolated island, and one by one they are murdered. The mystery is who among them is the killer.', 'https://demobucketxyzmnoabc.s3.ap-south-1.amazonaws.com/assets/images/3/cover.jpg'),
-- (2, 'Dune', 4, 4, '1965-08-01', 1549.00, 'Set in the distant future amidst a feudal interstellar society, the story of young Paul Atides, whose family accepts the stewardship of the desert planet Arrakis, the only source of the valuable spice "melange".', 'https://demobucketxyzmnoabc.s3.ap-south-1.amazonaws.com/assets/images/4/cover.jpg');


-- INSERT INTO review (user_id, book_id, r_title, review, rating) VALUES
-- (2, 1, 'A Timeless Warning', 'George Orwell\'s 1984 is more relevant today than ever. A chilling and masterfully written cautionary tale about the dangers of totalitarianism. A must-read for everyone.', 5),
-- (1, 2, 'Pure Magic!', 'A wonderful start to a magical series. J.K. Rowling builds an enchanting world that is impossible not to get lost in. Perfect for all ages.', 5),
-- (2, 3, 'Masterpiece of Suspense', 'Agatha Christie is the queen of mystery for a reason. This book keeps you guessing until the very last page. The plot is ingenious.', 5),
-- (1, 4, 'An Epic Sci-Fi Masterpiece', 'Frank Herbert\'s Dune is a monumental achievement in science fiction. The world-building is second to none, creating a complex and believable universe with its own politics, ecology, and culture. The story of Paul Atides is a captivating journey of destiny and power. It\'s a dense read, but every page is rewarding. A must-read for any fan of the genre.', 5);
