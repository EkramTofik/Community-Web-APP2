CREATE TABLE ads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(255),  -- URL to the ad's image
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- When the ad was created
    user_id INT, -- Foreign key linking to the user table 
    FOREIGN KEY (user_id) REFERENCES user(id) 
);