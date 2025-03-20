-- Create database
CREATE DATABASE IF NOT EXISTS sportsvani;
USE sportsvani;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    mobile VARCHAR(15) UNIQUE NOT NULL,
    password VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Roles table
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User roles table
CREATE TABLE IF NOT EXISTS user_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- Player profiles table
CREATE TABLE IF NOT EXISTS player_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    player_type ENUM('Batter', 'Bowler', 'Allrounder', 'WicketKeeper') NOT NULL,
    batting_style ENUM('Right', 'Left'),
    bowling_style ENUM('Right Arm Fast', 'Left Arm Fast', 'Right Arm Medium Pacer', 'Left Arm Medium Pacer', 'Right Arm Off Spin', 'Right Arm Leg Spin', 'Left Arm Orthodox Spin', 'Left Arm Unorthodox Spin'),
    highest_score INT DEFAULT 0,
    best_bowling VARCHAR(10) DEFAULT '0/0',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Player stats table
CREATE TABLE IF NOT EXISTS player_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT UNIQUE NOT NULL,
    matches INT DEFAULT 0,
    runs INT DEFAULT 0,
    wickets INT DEFAULT 0,
    overs INT DEFAULT 0,
    balls_faced INT DEFAULT 0,
    average DECIMAL(8,2) DEFAULT 0,
    economy DECIMAL(8,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Teams table
CREATE TABLE IF NOT EXISTS teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    logo VARCHAR(255),
    captain_id INT NOT NULL,
    captain_name VARCHAR(100) NOT NULL,
    captain_mobile VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (captain_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Team players table
CREATE TABLE IF NOT EXISTS team_players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    player_id INT NOT NULL,
    role VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (team_id, player_id),
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Team stats table
CREATE TABLE IF NOT EXISTS team_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT UNIQUE NOT NULL,
    matches INT DEFAULT 0,
    won INT DEFAULT 0,
    lost INT DEFAULT 0,
    tied INT DEFAULT 0,
    drawn INT DEFAULT 0,
    win_percentage DECIMAL(5,2) DEFAULT 0,
    toss_won INT DEFAULT 0,
    bat_first INT DEFAULT 0,
    no_result INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
);

-- Tournaments table
CREATE TABLE IF NOT EXISTS tournaments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    logo VARCHAR(255),
    banner VARCHAR(255),
    organizer_id INT NOT NULL,
    organizer_name VARCHAR(100) NOT NULL,
    organizer_mobile VARCHAR(15) NOT NULL,
    organizer_email VARCHAR(100),
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    category ENUM('Open', 'Corporate', 'Community', 'School', 'College', 'University Series', 'Other') NOT NULL,
    ball_type ENUM('Leather', 'Tennis', 'Other') NOT NULL,
    pitch_type ENUM('Rough', 'Cement', 'Turf', 'Matt', 'Other') NOT NULL,
    match_type ENUM('Limited Overs', 'Box/Turf', 'Test Match') NOT NULL,
    team_count INT NOT NULL,
    fees DECIMAL(10,2),
    winning_prize ENUM('Cash', 'Trophy', 'Both'),
    match_days JSON,
    match_timings ENUM('Day', 'Night', 'Day & Night'),
    format ENUM('League', 'Knockout') NOT NULL,
    status ENUM('Upcoming', 'Active', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tournament teams table
CREATE TABLE IF NOT EXISTS tournament_teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT NOT NULL,
    team_id INT NOT NULL,
    group_id INT,
    status ENUM('Registered', 'Approved', 'Rejected') NOT NULL DEFAULT 'Registered',
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (tournament_id, team_id),
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
);

-- Tournament groups table
CREATE TABLE IF NOT EXISTS tournament_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE
);

-- Tournament rounds table
CREATE TABLE IF NOT EXISTS tournament_rounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    type ENUM('League', 'Knockout') NOT NULL,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE
);

-- Matches table
CREATE TABLE IF NOT EXISTS matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    match_type ENUM('Limited Overs', 'Test') NOT NULL,
    ball_type ENUM('Leather', 'Tennis', 'Other') NOT NULL,
    pitch_type ENUM('Rough', 'Matt', 'Cement', 'Turf', 'Other') NOT NULL,
    overs INT NOT NULL,
    powerplay_overs INT NOT NULL,
    overs_per_bowler INT NOT NULL,
    city VARCHAR(100) NOT NULL,
    ground VARCHAR(100) NOT NULL,
    date DATETIME NOT NULL,
    team_a_id INT NOT NULL,
    team_b_id INT NOT NULL,
    toss_winner_id INT,
    toss_decision ENUM('Bat', 'Bowl'),
    tournament_id INT,
    round_id INT,
    status ENUM('Scheduled', 'Live', 'Completed', 'Abandoned', 'Cancelled') NOT NULL DEFAULT 'Scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (team_a_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (team_b_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (toss_winner_id) REFERENCES teams(id) ON DELETE SET NULL,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE SET NULL,
    FOREIGN KEY (round_id) REFERENCES tournament_rounds(id) ON DELETE SET NULL
);

-- Match officials table
CREATE TABLE IF NOT EXISTS match_officials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('Umpire', 'Scorer', 'Commentator', 'Streamer') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (match_id, user_id, role),
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Match players table
CREATE TABLE IF NOT EXISTS match_players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    player_id INT NOT NULL,
    team_id INT NOT NULL,
    role VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (match_id, player_id),
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
);

-- Match results table
CREATE TABLE IF NOT EXISTS match_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT UNIQUE NOT NULL,
    winner_id INT,
    win_margin INT,
    win_margin_type ENUM('Runs', 'Wickets'),
    man_of_the_match_id INT,
    summary TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (winner_id) REFERENCES teams(id) ON DELETE SET NULL,
    FOREIGN KEY (man_of_the_match_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Innings table
CREATE TABLE IF NOT EXISTS innings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    team_id INT NOT NULL,
    innings_number INT NOT NULL,
    runs INT DEFAULT 0,
    wickets INT DEFAULT 0,
    overs DECIMAL(5,1) DEFAULT 0,
    extras_wides INT DEFAULT 0,
    extras_no_balls INT DEFAULT 0,
    extras_byes INT DEFAULT 0,
    extras_leg_byes INT DEFAULT 0,
    extras_penalty INT DEFAULT 0,
    extras_total INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (match_id, team_id, innings_number),
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
);

-- Batting scorecard table
CREATE TABLE IF NOT EXISTS batting_scorecard (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    innings_id INT NOT NULL,
    player_id INT NOT NULL,
    runs INT DEFAULT 0,
    balls INT DEFAULT 0,
    fours INT DEFAULT 0,
    sixes INT DEFAULT 0,
    strike_rate DECIMAL(8,2) DEFAULT 0,
    dismissal_type VARCHAR(50),
    bowler_id INT,
    fielder_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (match_id, innings_id, player_id),
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (innings_id) REFERENCES innings(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (bowler_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (fielder_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Bowling scorecard table
CREATE TABLE IF NOT EXISTS bowling_scorecard (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    innings_id INT NOT NULL,
    player_id INT NOT NULL,
    overs DECIMAL(5,1) DEFAULT 0,
    maidens INT DEFAULT 0,
    runs INT DEFAULT 0,
    wickets INT DEFAULT 0,
    economy DECIMAL(8,2) DEFAULT 0,
    wides INT DEFAULT 0,
    no_balls INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (match_id, innings_id, player_id),
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (innings_id) REFERENCES innings(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Match events table
CREATE TABLE IF NOT EXISTS match_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    innings_id INT NOT NULL,
    over_number DECIMAL(5,1) NOT NULL,
    ball_number INT NOT NULL,
    event_type ENUM('Run', 'Boundary', 'Six', 'Wicket', 'Wide', 'NoBall', 'Bye', 'LegBye', 'Penalty') NOT NULL,
    event_value INT NOT NULL,
    batsman_id INT NOT NULL,
    bowler_id INT NOT NULL,
    description TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (innings_id) REFERENCES innings(id) ON DELETE CASCADE,
    FOREIGN KEY (batsman_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (bowler_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Wagon wheel table
CREATE TABLE IF NOT EXISTS wagon_wheel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    innings_id INT NOT NULL,
    batsman_id INT NOT NULL,
    over_number DECIMAL(5,1) NOT NULL,
    ball_number INT NOT NULL,
    runs INT NOT NULL,
    x_coordinate INT NOT NULL,
    y_coordinate INT NOT NULL,
    angle INT NOT NULL,
    distance INT NOT NULL,
    shot_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (innings_id) REFERENCES innings(id) ON DELETE CASCADE,
    FOREIGN KEY (batsman_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Live streams table
CREATE TABLE IF NOT EXISTS live_streams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT UNIQUE NOT NULL,
    stream_id VARCHAR(100) NOT NULL,
    stream_url VARCHAR(255) NOT NULL,
    stream_key VARCHAR(255) NOT NULL,
    watch_url VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    privacy ENUM('public', 'private') NOT NULL DEFAULT 'public',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE
);

-- Gallery table
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('player', 'team', 'tournament', 'match') NOT NULL,
    user_id INT,
    team_id INT,
    tournament_id INT,
    match_id INT,
    image_path VARCHAR(255) NOT NULL,
    title VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE
);

-- Awards table
CREATE TABLE IF NOT EXISTS awards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    player_id INT,
    team_id INT,
    match_id INT,
    tournament_id INT,
    date DATE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE
);

-- Insert default roles
INSERT INTO roles (name, description) VALUES
('superadmin', 'Super Administrator with full access'),
('admin', 'Administrator with limited access'),
('player', 'Cricket player'),
('umpire', 'Match umpire'),
('scorer', 'Match scorer'),
('commentator', 'Match commentator'),
('organiser', 'Tournament organiser'),
('manager', 'Team manager');

-- Insert super admin user
INSERT INTO users (user_id, name, email, mobile, password, city) VALUES
('USR001', 'Super Admin', 'sportavani@gmail.com', '9876543210', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mumbai');

-- Assign super admin role
INSERT INTO user_roles (user_id, role_id) VALUES
(1, 1);

