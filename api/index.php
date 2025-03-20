<?php
require_once '../config/config.php';

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get request path
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/api';
$path = str_replace($base_path, '', parse_url($request_uri, PHP_URL_PATH));
$path = trim($path, '/');
$segments = explode('/', $path);

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get database connection
$conn = getDbConnection();

// Handle API routes
switch ($segments[0]) {
    case 'auth':
        handleAuthRoutes($segments, $method, $conn);
        break;
    case 'users':
        handleUserRoutes($segments, $method, $conn);
        break;
    case 'teams':
        handleTeamRoutes($segments, $method, $conn);
        break;
    case 'tournaments':
        handleTournamentRoutes($segments, $method, $conn);
        break;
    case 'matches':
        handleMatchRoutes($segments, $method, $conn);
        break;
    case 'player-profiles':
        handlePlayerProfileRoutes($segments, $method, $conn);
        break;
    case 'match-scoring':
        handleMatchScoringRoutes($segments, $method, $conn);
        break;
    case 'youtube':
        handleYouTubeRoutes($segments, $method, $conn);
        break;
    default:
        // Route not found
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'API endpoint not found']);
        break;
}

$conn->close();

// Authentication routes
function handleAuthRoutes($segments, $method, $conn) {
    if (count($segments) < 2) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'API endpoint not found']);
        return;
    }

    switch ($segments[1]) {
        case 'superadmin':
            if ($method === 'POST' && isset($segments[2]) && $segments[2] === 'login') {
                // Super admin login
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($data['email']) || !isset($data['password'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
                    return;
                }
                
                $email = $data['email'];
                $password = $data['password'];
                
                // Check credentials
                if ($email === SUPER_ADMIN_EMAIL && $password === SUPER_ADMIN_PASSWORD) {
                    // Get super admin user
                    $stmt = $conn->prepare("SELECT u.id, u.user_id, u.name, u.email FROM users u 
                                           JOIN user_roles ur ON u.id = ur.user_id 
                                           JOIN roles r ON ur.role_id = r.id 
                                           WHERE u.email = ? AND r.name = 'superadmin'");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $user = $result->fetch_assoc();
                        
                        // Generate token
                        $token = bin2hex(random_bytes(32));
                        
                        echo json_encode([
                            'success' => true,
                            'token' => $token,
                            'user' => [
                                'id' => $user['id'],
                                'user_id' => $user['user_id'],
                                'name' => $user['name'],
                                'email' => $user['email'],
                                'role' => 'superadmin',
                            ],
                        ]);
                    } else {
                        http_response_code(401);
                        echo json_encode(['success' => false, 'message' => 'Super admin user not found in database']);
                    }
                    
                    $stmt->close();
                } else {
                    http_response_code(401);
                    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
                }
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'API endpoint not found']);
            }
            break;
        case 'firebase':
            if ($method === 'POST' && isset($segments[2])) {
                if ($segments[2] === 'send-otp') {
                    // Send OTP
                    $data = json_decode(file_get_contents('php://input'), true);
                    
                    if (!isset($data['phoneNumber'])) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Phone number is required']);
                        return;
                    }
                    
                    // In a real app, this would use Firebase to send an OTP
                    echo json_encode([
                        'success' => true,
                        'message' => 'OTP sent successfully',
                        'verificationId' => 'mock-verification-id',
                    ]);
                } elseif ($segments[2] === 'verify-otp') {
                    // Verify OTP
                    $data = json_decode(file_get_contents('php://input'), true);
                    
                    if (!isset($data['verificationId']) || !isset($data['otp'])) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Verification ID and OTP are required']);
                        return;
                    }
                    
                    // In a real app, this would verify the OTP with Firebase
                    echo json_encode([
                        'success' => true,
                        'message' => 'OTP verified successfully',
                        'token' => 'firebase_token_' . bin2hex(random_bytes(16)),
                        'user' => [
                            'uid' => 'mock-user-id',
                            'phoneNumber' => $data['phoneNumber'] ?? '+919876543210',
                        ],
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'API endpoint not found']);
                }
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'API endpoint not found']);
            }
            break;
        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'API endpoint not found']);
            break;
    }
}

// User routes
function handleUserRoutes($segments, $method, $conn) {
    switch ($method) {
        case 'GET':
            if (count($segments) > 1 && is_numeric($segments[1])) {
                // Get specific user
                $id = (int)$segments[1];
                
                $stmt = $conn->prepare("SELECT u.*, GROUP_CONCAT(r.name) as role_names 
                                       FROM users u 
                                       LEFT JOIN user_roles ur ON u.id = ur.user_id 
                                       LEFT JOIN roles r ON ur.role_id = r.id 
                                       WHERE u.id = ?
                                       GROUP BY u.id");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    $user['roles'] = explode(',', $user['role_names']);
                    unset($user['role_names']);
                    unset($user['password']);
                    
                    echo json_encode(['success' => true, 'user' => $user]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'User not found']);
                }
                
                $stmt->close();
            } else {
                // Get all users
                $result = $conn->query("SELECT u.*, GROUP_CONCAT(r.name) as role_names 
                                       FROM users u 
                                       LEFT JOIN user_roles ur ON u.id = ur.user_id 
                                       LEFT JOIN roles r ON ur.role_id = r.id 
                                       GROUP BY u.id 
                                       ORDER BY u.created_at DESC");
                
                $users = [];
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $row['roles'] = explode(',', $row['role_names']);
                        unset($row['role_names']);
                        unset($row['password']);
                        $users[] = $row;
                    }
                }
                
                echo json_encode(['success' => true, 'users' => $users]);
            }
            break;
        case 'POST':
            // Create user
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['name']) || !isset($data['mobile']) || !isset($data['city']) || !isset($data['roles'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                return;
            }
            
            // Generate unique user ID
            $user_id = 'USR' . sprintf('%04d', rand(1000, 9999));
            
            // Check if user ID already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE user_id = ?");
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($result->num_rows > 0) {
                $user_id = 'USR' . sprintf('%04d', rand(1000, 9999));
                $stmt->bind_param("s", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
            }
            
            $stmt->close();
            
            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (user_id, name, email, mobile, city, location) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $user_id, $data['name'], $data['email'], $data['mobile'], $data['city'], $data['location']);
            
            if ($stmt->execute()) {
                $user_id_db = $conn->insert_id;
                
                // Add roles
                if (!empty($data['roles'])) {
                    $role_stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
                    
                    foreach ($data['roles'] as $role_id) {
                        $role_stmt->bind_param("ii", $user_id_db, $role_id);
                        $role_stmt->execute();
                    }
                    
                    $role_stmt->close();
                }
                
                // Get the created user
                $stmt = $conn->prepare("SELECT u.*, GROUP_CONCAT(r.name) as role_names 
                                       FROM users u 
                                       LEFT JOIN user_roles ur ON u.id = ur.user_id 
                                       LEFT JOIN roles r ON ur.role_id = r.id 
                                       WHERE u.id = ?
                                       GROUP BY u.id");
                $stmt->bind_param("i", $user_id_db);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    $user['roles'] = explode(',', $user['role_names']);
                    unset($user['role_names']);
                    unset($user['password']);
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'User created successfully',
                        'user' => $user,
                    ]);
                } else {
                    echo json_encode([
                        'success' => true,
                        'message' => 'User created successfully',
                    ]);
                }
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error creating user: ' . $conn->error]);
            }
            
            $stmt->close();
            break;
        case 'PUT':
            if (count($segments) > 1 && is_numeric($segments[1])) {
                // Update user
                $id = (int)$segments[1];
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($data['name']) || !isset($data['city'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                    return;
                }
                
                // Update user
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, mobile = ?, city = ?, location = ? WHERE id = ?");
                $stmt->bind_param("sssssi", $data['name'], $data['email'], $data['mobile'], $data['city'], $data['location'], $id);
                
                if ($stmt->execute()) {
                    // Update roles
                    if (isset($data['roles'])) {
                        $conn->query("DELETE FROM user_roles WHERE user_id = $id");
                        
                        if (!empty($data['roles'])) {
                            $role_stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
                            
                            foreach ($data['roles'] as $role_id) {
                                $role_stmt->bind_param("ii", $id, $role_id);
                                $role_stmt->execute();
                            }
                            
                            $role_stmt->close();
                        }
                    }
                    
                    // Get the updated user
                    $stmt = $conn->prepare("SELECT u.*, GROUP_CONCAT(r.name) as role_names 
                                           FROM users u 
                                           LEFT JOIN user_roles ur ON u.id = ur.user_id 
                                           LEFT JOIN roles r ON ur.role_id = r.id 
                                           WHERE u.id = ?
                                           GROUP BY u.id");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $user = $result->fetch_assoc();
                        $user['roles'] = explode(',', $user['role_names']);
                        unset($user['role_names']);
                        unset($user['password']);
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'User updated successfully',
                            'user' => $user,
                        ]);
                    } else {
                        echo json_encode([
                            'success' => true,
                            'message' => 'User updated successfully',
                        ]);
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Error updating user: ' . $conn->error]);
                }
                
                $stmt->close();
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'User ID is required']);
            }
            break;
        case 'DELETE':
            if (count($segments) > 1 && is_numeric($segments[1])) {
                // Delete user
                $id = (int)$segments[1];
                
                $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'User deleted successfully',
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Error deleting user: ' . $conn->error]);
                }
                
                $stmt->close();
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'User ID is required']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
}

// Team routes
function handleTeamRoutes($segments, $method, $conn) {
    switch ($method) {
        case 'GET':
            if (count($segments) > 1 && is_numeric($segments[1])) {
                // Get specific team
                $id = (int)$segments[1];
                
                $stmt = $conn->prepare("SELECT t.*, u.name as captain_name, u.mobile as captain_mobile 
                                       FROM teams t 
                                       JOIN users u ON t.captain_id = u.id 
                                       WHERE t.id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $team = $result->fetch_assoc();
                    
                    // Get team players
                    $players = [];
                    $player_result = $conn->query("SELECT u.id, u.user_id, u.name, u.mobile, tp.role 
                                                 FROM team_players tp 
                                                 JOIN users u ON tp.player_id = u.id 
                                                 WHERE tp.team_id = $id");
                    if ($player_result) {
                        while ($player_row = $player_result->fetch_assoc()) {
                            $players[] = $player_row;
                        }
                    }
                    
                    $team['players'] = $players;
                    
                    echo json_encode(['success' => true, 'team' => $team]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Team not found']);
                }
                
                $stmt->close();
            } else {
                // Get all teams
                $result = $conn->query("SELECT t.*, u.name as captain_name, u.mobile as captain_mobile 
                                       FROM teams t 
                                       JOIN users u ON t.captain_id = u.id 
                                       ORDER BY t.created_at DESC");
                
                $teams = [];
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $teams[] = $row;
                    }
                }
                
                echo json_encode(['success' => true, 'teams' => $teams]);
            }
            break;
        case 'POST':
            // Create team
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['name']) || !isset($data['captain_id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                return;
            }
            
            // Get captain details
            $stmt = $conn->prepare("SELECT name, mobile FROM users WHERE id = ?");
            $stmt->bind_param("i", $data['captain_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Captain not found']);
                return;
            }
            
            $captain = $result->fetch_assoc();
            $stmt->close();
            
            // Generate unique team ID
            $team_id = 'TM' . sprintf('%04d', rand(1000, 9999));
            
            // Check if team ID already exists
            $stmt = $conn->prepare("SELECT id FROM teams WHERE team_id = ?");
            $stmt->bind_param("s", $team_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($result->num_rows > 0) {
                $team_id = 'TM' . sprintf('%04d', rand(1000, 9999));
                $stmt->bind_param("s", $team_id);
                $stmt->execute();
                $result = $stmt->get_result();
            }
            
            $stmt->close();
            
            // Insert team
            $stmt = $conn->prepare("INSERT INTO teams (team_id, name, logo, captain_id, captain_name, captain_mobile) VALUES (?, ?, ?, ?, ?, ?)");
            $logo = isset($data['logo']) ? $data['logo'] : null;
            $stmt->bind_param("sssiss", $team_id, $data['name'], $logo, $data['captain_id'], $captain['name'], $captain['mobile']);
            
            if ($stmt->execute()) {
                $team_id_db = $conn->insert_id;
                
                // Add captain as a player in the team
                $role_stmt = $conn->prepare("INSERT INTO team_players (team_id, player_id, role) VALUES (?, ?, ?)");
                $role = 'Captain';
                $role_stmt->bind_param("iis", $team_id_db, $data['captain_id'], $role);
                $role_stmt->execute();
                $role_stmt->close();
                
                // Get the created team
                $stmt = $conn->prepare("SELECT t.*, u.name as captain_name, u.mobile as captain_mobile 
                                       FROM teams t 
                                       JOIN users u ON t.captain_id = u.id 
                                       WHERE t.id = ?");
                $stmt->bind_param("i", $team_id_db);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $team = $result->fetch_assoc();
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Team created successfully',
                        'team' => $team,
                    ]);
                } else {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Team created successfully',
                    ]);
                }
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error creating team: ' . $conn->error]);
            }
            
            $stmt->close();
            break;
        case 'PUT':
            if (count($segments) > 1 && is_numeric($segments[1])) {
                // Update team
                $id = (int)$segments[1];
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($data['name'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                    return;
                }
                
                // Update team
                $stmt = $conn->prepare("UPDATE teams SET name = ?, logo = ? WHERE id = ?");
                $logo = isset($data['logo']) ? $data['logo'] : null;
                $stmt->bind_param("ssi", $data['name'], $logo, $id);
                
                if ($stmt->execute()) {
                    // Get the updated team
                    $stmt = $conn->prepare("SELECT t.*, u.name as captain_name, u.mobile as captain_mobile 
                                           FROM teams t 
                                           JOIN users u ON t.captain_id = u.id 
                                           WHERE t.id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $team = $result->fetch_assoc();
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Team updated successfully',
                            'team' => $team,
                        ]);
                    } else {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Team updated successfully',
                        ]);
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Error updating team: ' . $conn->error]);
                }
                
                $stmt->close();
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Team ID is required']);
            }
            break;
        case 'DELETE':
            if (count($segments) > 1 && is_numeric($segments[1])) {
                // Delete team
                $id = (int)$segments[1];
                
                $stmt = $conn->prepare("DELETE FROM teams WHERE id = ?");
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Team deleted successfully',
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Error deleting team: ' . $conn->error]);
                }
                
                $stmt->close();
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Team ID is required']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
}

// Tournament routes
function handleTournamentRoutes($segments, $method, $conn) {
    switch ($method) {
        case 'GET':
            if (count($segments) > 1 && is_numeric($segments[1])) {
                // Get specific tournament
                $id = (int)$segments[1];
                
                $stmt = $conn->prepare("SELECT t.*, u.name as organizer_name, u.mobile as organizer_mobile, u.email as organizer_email 
                                       FROM tournaments t 
                                       JOIN users u ON t.organizer_id = u.id 
                                       WHERE t.id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $tournament = $result->fetch_assoc();
                    
                    // Get tournament teams
                    $teams = [];
                    $team_result = $conn->query("SELECT t.id, t.team_id, t.name, t.logo, tt.group_id, tt.status, tt.registered_at 
                                               FROM tournament_teams tt 
                                               JOIN teams t ON tt.team_id = t.id 
                                               WHERE tt.tournament_id = $id");
                    if ($team_result) {
                        while ($team_row = $team_result->fetch_assoc()) {
                            $teams[] = $team_row;
                        }
                    }
                    
                    $tournament['teams'] = $teams;
                    
                    // Get tournament groups
                    $groups = [];
                    $group_result = $conn->query("SELECT * FROM tournament_groups WHERE tournament_id = $id");
                    if ($group_result) {
                        while ($group_row = $group_result->fetch_assoc()) {
                            $groups[] = $group_row;
                        }
                    }
                    
                    $tournament['groups'] = $groups;
                    
                    // Get tournament rounds
                    $rounds = [];
                    $round_result = $conn->query("SELECT * FROM tournament_rounds WHERE tournament_id = $id");
                    if ($round_result) {
                        while ($round_row = $round_result->fetch_assoc()) {
                            $rounds[] = $round_row;
                        }
                    }
                    
                    $tournament['rounds'] = $rounds;
                    
                    echo json_encode(['success' => true, 'tournament' => $tournament]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Tournament not found']);
                }
                
                $stmt->close();
            } else {
                // Get all tournaments
                $result = $conn->query("SELECT t.*, u.name as organizer_name, u.mobile as organizer_mobile, u.email as organizer_email 
                                       FROM tournaments t 
                                       JOIN users u ON t.organizer_id = u.id 
                                       ORDER BY t.created_at DESC");
                
                $tournaments = [];
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $tournaments[] = $row;
                    }
                }
                
                echo json_encode(['success' => true, 'tournaments' => $tournaments]);
            }
            break;
        case 'POST':
            // Create tournament
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['name']) || !isset($data['organizer_id']) || !isset($data['start_date']) || !isset($data['end_date']) || !isset($data['category']) || !isset($data['ball_type']) || !isset($data['pitch_type']) || !isset($data['match_type']) || !isset($data['team_count']) || !isset($data['format'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                return;
            }
            
            // Get organizer details
            $stmt = $conn->prepare("SELECT name, mobile, email FROM users WHERE id = ?");
            $stmt->bind_param("i", $data['organizer_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Organizer not found']);
                return;
            }
            
            $organizer = $result->fetch_assoc();
            $stmt->close();
            
            // Generate unique tournament ID
            $tournament_id = 'TRN' . sprintf('%04d', rand(1000, 9999));
            
            // Check if tournament ID already exists
            $stmt = $conn->prepare("SELECT id FROM tournaments WHERE tournament_id = ?");
            $stmt->bind_param("s", $tournament_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($result->num_rows > 0) {
                $tournament_id = 'TRN' . sprintf('%04d', rand(1000, 9999));
                $stmt->bind_param("s", $tournament_id);
                $stmt->execute();
                $result = $stmt->get_result();
            }
            
            $stmt->close();
            
            // Insert tournament
            $stmt = $conn->prepare("INSERT INTO tournaments (tournament_id, name, logo, banner, organizer_id, organizer_name, organizer_mobile, organizer_email, start_date, end_date, category, ball_type, pitch_type, match_type, team_count, fees, winning_prize, match_days, match_timings, format, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $logo = isset($data['logo']) ? $data['logo'] : null;
            $banner = isset($data['banner']) ? $data['banner'] : null;
            $fees = isset($data['fees']) ? $data['fees'] : null;
            $winning_prize = isset($data['winning_prize']) ? $data['winning_prize'] : null;
            $match_days = isset($data['match_days']) ? json_encode($data['match_days']) : null;
            $match_timings = isset($data['match_timings']) ? $data['match_timings'] : null;
            $status = 'Upcoming';
            
            $stmt->bind_param("ssssisssssssssidsssss", $tournament_id, $data['name'], $logo, $banner, $data['organizer_id'], $organizer['name'], $organizer['mobile'], $organizer['email'], $data['start_date'], $data['end_date'], $data['category'], $data['ball_type'], $data['pitch_type'], $data['match_type'], $data['team_count'], $fees, $winning_prize, $match_days, $match_timings, $data['format'], $status);
            
            if ($stmt->execute()) {
                $tournament_id_db = $conn->insert_id;
                
                // Get the created tournament
                $stmt = $conn->prepare("SELECT t.*, u.name as organizer_name, u.mobile as organizer_mobile, u.email as organizer_email 
                                       FROM tournaments t 
                                       JOIN users u ON t.organizer_id = u.id 
                                       WHERE t.id = ?");
                $stmt->bind_param("i", $tournament_id_db);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $tournament = $result->fetch_assoc();
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Tournament created successfully',
                        'tournament' => $tournament,
                    ]);
                } else {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Tournament created successfully',
                    ]);
                }
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error creating tournament: ' . $conn->error]);
            }
            
            $stmt->close();
            break;
        case 'PUT':
            if (count($segments) > 1 && is_numeric($segments[1])) {
                // Update tournament
                $id = (int)$segments[1];
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($data['name']) || !isset($data['start_date']) || !isset($data['end_date'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                    return;
                }
                
                // Update tournament
                $stmt = $conn->prepare("UPDATE tournaments SET name = ?, logo = ?, banner = ?, start_date = ?, end_date = ?, category = ?, ball_type = ?, pitch_type = ?, match_type = ?, team_count = ?, fees = ?, winning_prize = ?, match_days = ?, match_timings = ?, format = ?, status = ? WHERE id = ?");
                
                $logo = isset($data['logo']) ? $data['logo'] : null;
                $banner = isset($data['banner']) ? $data['banner'] : null;
                $category = isset($data['category']) ? $data['category'] : null;
                $ball_type = isset($data['ball_type']) ? $data['ball_type'] : null;
                $pitch_type = isset($data['pitch_type']) ? $data['pitch_type'] : null;
                $match_type = isset($data['match_type']) ? $data['match_type'] : null;
                $team_count = isset($data['team_count']) ? $data['team_count'] : null;
                $fees = isset($data['fees']) ? $data['fees'] : null;
                $winning_prize = isset($data['winning_prize']) ? $data['winning_prize'] : null;
                $match_days = isset($data['match_days']) ? json_encode($data['match_days']) : null;
                $match_timings = isset($data['match_timings']) ? $data['match_timings'] : null;
                $format = isset($data['format']) ? $data['format'] : null;
                $status = isset($data['status']) ? $data['status'] : 'Upcoming';
                
                $stmt->bind_param("sssssssssidssssi", $data['name'], $logo, $banner, $data['start_date'], $data['end_date'], $category, $ball_type, $pitch_type, $match_type, $team_count, $fees, $winning_prize, $match_days, $match_timings, $format, $status, $id);
                
                if ($stmt->execute()) {
                    // Get the updated tournament
                    $stmt = $conn->prepare("SELECT t.*, u.name as organizer_name, u.mobile as organizer_mobile, u.email as organizer_email 
                                           FROM tournaments t 
                                           JOIN users u ON t.organizer_id = u.id 
                                           WHERE t.id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $tournament = $result->fetch_assoc();
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Tournament updated successfully',
                            'tournament' => $tournament,
                        ]);
                    } else {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Tournament updated successfully',
                        ]);
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Error updating tournament: ' . $conn->error]);
                }
                
                $stmt->close();
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Tournament ID is required']);
            }
            break;
        case 'DELETE':
            if (count($segments) > 1 && is_numeric($segments[1])) {
                // Delete tournament
                $id = (int)$segments[1];
                
                $stmt = $conn->prepare("DELETE FROM tournaments WHERE id = ?");
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Tournament deleted successfully',
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Error deleting tournament: ' . $conn->error]);
                }
                
                $stmt->close();
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Tournament ID is required']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
}

// Match routes
function handleMatchRoutes($segments, $method, $conn) {
    switch ($method) {
        case 'GET':
            if (count($segments) > 1 && is_numeric($segments[1])) {
                // Get specific match
                $id = (int)$segments[1];
                
                $stmt = $conn->prepare("SELECT m.*, 
                                       t1.name as team_a_name, t1.logo as team_a_logo,
                                       t2.name as team_b_name, t2.logo as team_b_logo,
                                       t3.name as toss_winner_name,
                                       tr.name as tournament_name
                                       FROM matches m 
                                       JOIN teams t1 ON m.team_a_id = t1.id 
                                       JOIN teams t2 ON m.team_b_id = t2.id 
                                       LEFT JOIN teams t3 ON m.toss_winner_id = t3.id 
                                       LEFT JOIN tournaments tr ON m.tournament_id = tr.id 
                                       WHERE m.id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $match = $result->fetch_assoc();
                    
                    // Get match officials
                    $officials = [];
                    $official_result = $conn->query("SELECT mo.role, u.id, u.user_id, u.name, u.mobile 
                                                   FROM match_officials mo 
                                                   JOIN users u ON mo.user_id = u.id 
                                                   WHERE mo.match_id = $id");
                    if ($official_result) {
                        while ($official_row = $official_result->fetch_assoc()) {
                            $officials[] = $official_row;
                        }
                    }
                    
                    $match['officials'] = $officials;
                    
                    // Get match players
                    $players = [];
                    $player_result = $conn->query("SELECT mp.team_id, mp.role, u.id, u.user_id, u.name, t.name as team_name 
                                                 FROM match_players mp 
                                                 JOIN users u ON mp.player_id = u.id 
                                                 JOIN teams t ON mp.team_id = t.id 
                                                 WHERE mp.match_id = $id");
                    if ($player_result) {
                        while ($player_row = $player_result->fetch_assoc()) {
                            $players[] = $player_row;
                        }
                    }
                    
                    $match['players'] = $players;
                    
                    // Get match result
                    $result_query = $conn->query("SELECT mr.*, t.name as winner_name, u.name as man_of_the_match_name 
                                                FROM match_results mr 
                                                LEFT JOIN teams t ON mr.winner_id = t.id 
                                                LEFT JOIN users u ON mr.man_of_the_match_id = u.id 
                                                WHERE mr.match_id = $id");
                    if ($result_query && $result_query->num_rows > 0) {
                        $match['result'] = $result_query->fetch_assoc();
                    }
                    
                    echo json_encode(['success' => true, 'match' => $match]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Match not found']);
                }
                
                $stmt->close();
            } else {
                // Get all matches
                $result = $conn->query("SELECT m.*, 
                                       t1.name as team_a_name, t1.logo as team_a_logo,
                                       t2.name as team_b_name, t2.logo as team_b_logo,
                                       t3.name as toss_winner_name,
                                       tr.name as tournament_name
                                       FROM matches m 
                                       JOIN teams t1 ON m.team_a_id = t1.id 
                                       JOIN teams t2 ON m.team_b_id = t2.id 
                                       LEFT JOIN teams t3 ON m.toss_winner_id = t3.id 
                                       LEFT JOIN tournaments tr ON m.tournament_id = tr.id 
                                       ORDER BY m.date DESC");
                
                $matches = [];
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $matches[] = $row;
                    }
                }
                
                echo json_encode(['success' => true, 'matches' => $matches]);
            }
            break;
        case 'POST':
            // Create match
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['match_type']) || !isset($data['ball_type']) || !isset($data['pitch_type']) || !isset($data['overs']) || !isset($data['powerplay_overs']) || !isset($data['overs_per_bowler']) || !isset($data['city']) || !isset($data['ground']) || !isset($data['date']) || !isset($data['team_a_id']) || !isset($data['team_b_id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                return;
            }
            
            // Generate unique match ID
            $match_id = 'MTH' . sprintf('%04d', rand(1000, 9999));
            
            // Check if match ID already exists
            $stmt = $conn->prepare("SELECT id FROM matches WHERE match_id = ?");
            $stmt->bind_param("s", $match_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($result->num_rows > 0) {
                $match_id = 'MTH' . sprintf('%04d', rand(1000, 9999));
                $stmt->bind_param("s", $match_id);
                $stmt->execute();
                $result = $stmt->get_result();
            }
            
            $stmt->close();
            
            // Get team names for match name
            $team_a_name = '';
            $team_b_name = '';
            
            $stmt = $conn->prepare("SELECT name FROM teams WHERE id = ?");
            $stmt->bind_param("i", $data['team_a_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $team_a_name = $row['name'];
            }
            
            $stmt->bind_param("i", $data['team_b_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $team_b_name = $row['name'];
            }
            
            $stmt->close();
            
            // Generate match name if not provided
            $name = isset($data['name']) ? $data['name'] : $team_a_name . ' vs ' . $team_b_name;
            
            // Insert match
            $stmt = $conn->prepare("INSERT INTO matches (match_id, name, match_type, ball_type, pitch_type, overs, powerplay_overs, overs_per_bowler, city, ground, date, team_a_id, team_b_id, tournament_id, round_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $tournament_id = isset($data['tournament_id']) ? $data['tournament_id'] : null;
            $round_id = isset($data['round_id']) ? $data['round_id'] : null;
            $status = 'Scheduled';
            
            $stmt->bind_param("sssssiisssiiiiss", $match_id, $name, $data['match_type'], $data['ball_type'], $data['pitch_type'], $data['overs'], $data['powerplay_overs'], $data['overs_per_bowler'], $data['city'], $data['ground'], $data['date'], $data['team_a_id'], $data['team_b_id'], $tournament_id, $round_id, $status);
            
            if ($stmt->execute()) {
                $match_id_db = $conn->insert_id;
                
                // Get the created match
                $stmt = $conn->prepare("SELECT m.*, 
                                       t1.name as team_a_name, t1.logo as team_a_logo,
                                       t2.name as team_b_name, t2.logo as team_b_logo,
                                       tr.name as tournament_name
                                       FROM matches m 
                                       JOIN teams t1 ON m.team_a_id = t1.id 
                                       JOIN teams t2 ON m.team_b_id = t2.id 
                                       LEFT JOIN tournaments tr ON m.tournament_id = tr.id 
                                       WHERE m.id = ?");
                $stmt->bind_param("i", $match_id_db);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $match = $result->fetch_assoc();
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Match created successfully',
                        'match' => $match,
                    ]);
                } else {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Match created successfully',
                    ]);
                }
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error creating match: ' . $conn->error]);
            }
            
            $stmt->close();
            break;
        case 'PUT':
            if (count($segments) > 1 && is_numeric($segments[1])) {
                // Update match
                $id = (int)$segments[1];
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($data['name']) || !isset($data['date'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                    return;
                }
                
                // Update match
                $stmt = $conn->prepare("UPDATE matches SET name = ?, match_type = ?, ball_type = ?, pitch_type = ?, overs = ?, powerplay_overs = ?, overs_per_bowler = ?, city = ?, ground = ?, date = ?, status = ? WHERE id = ?");
                
                $match_type = isset($data['match_type']) ? $data['match_type'] : null;
                $ball_type = isset($data['ball_type']) ? $data['ball_type'] : null;
                $pitch_type = isset($data['pitch_type']) ? $data['pitch_type'] : null;
                $overs = isset($data['overs']) ? $data['overs'] : null;
                $powerplay_overs = isset($data['powerplay_overs']) ? $data['powerplay_overs'] : null;
                $overs_per_bowler = isset($data['overs_per_bowler']) ? $data['overs_per_bowler'] : null;
                $city = isset($data['city']) ? $data['city'] : null;
                $ground = isset($data['ground']) ? $data['ground'] : null;
                $status = isset($data['status']) ? $data['status'] : 'Scheduled';
                
                $stmt->bind_param("ssssiissssi", $data['name'], $match_type, $ball_type, $pitch_type, $overs, $powerplay_overs, $overs_per_bowler, $city, $ground, $data['date'], $status, $id);
                
                if ($stmt->execute()) {
                    // Get the updated match
                    $stmt = $conn->prepare("SELECT m.*, 
                                           t1.name as team_a_name, t1.logo as team_a_logo,
                                           t2.name as team_b_name, t2.logo as team_b_logo,
                                           t3.name as toss_winner_name,
                                           tr.name as tournament_name
                                           FROM matches m 
                                           JOIN teams t1 ON m.team_a_id = t1.id 
                                           JOIN teams t2 ON m.team_b_id = t2.id 
                                           LEFT JOIN teams t3 ON m.toss_winner_id = t3.id 
                                           LEFT JOIN tournaments tr ON m.tournament_id = tr.id 
                                           WHERE m.id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $match = $result->fetch_assoc();
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Match updated successfully',
                            'match' => $match,
                        ]);
                    } else {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Match updated successfully',
                        ]);
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Error updating match: ' . $conn->error]);
                }
                
                $stmt->close();
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Match ID is required']);
            }
            break;
        case 'DELETE':
            if (count($segments) > 1 && is_numeric($segments[1])) {
                // Delete match
                $id = (int)$segments[1];
                
                $stmt = $conn->prepare("DELETE FROM matches WHERE id = ?");
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Match deleted successfully',
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Error deleting match: ' . $conn->error]);
                }
                
                $stmt->close();
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Match ID is required']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
}

// Player profile routes
function handlePlayerProfileRoutes($segments, $method, $conn) {
    switch ($method) {
        case 'GET':
            if (count($segments) > 1) {
                // Get specific player profile
                $user_id = $segments[1];
                
                $stmt = $conn->prepare("SELECT u.id, u.user_id, u.name, u.email, u.mobile, u.city, u.location, pp.* 
                                       FROM users u 
                                       LEFT JOIN player_profiles pp ON u.id = pp.user_id 
                                       WHERE u.id = ? OR u.user_id = ?");
                $stmt->bind_param("is", $user_id, $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $player = $result->fetch_assoc();
                    
                    // Get player stats
                    $stats_result = $conn->query("SELECT * FROM player_stats WHERE player_id = {$player['id']}");
                    if ($stats_result && $stats_result->num_rows > 0) {
                        $player['stats'] = $stats_result->fetch_assoc();
                    } else {
                        $player['stats'] = [
                            'matches' => 0,
                            'runs' => 0,
                            'wickets' => 0,
                            'overs' => 0,
                            'balls_faced' => 0,
                            'average' => 0,
                            'economy' => 0
                        ];
                    }
                    
                    // Get player teams
                    $teams = [];
                    $teams_result = $conn->query("SELECT t.id, t.team_id, t.name, t.logo, tp.role 
                                                FROM team_players tp 
                                                JOIN teams t ON tp.team_id = t.id 
                                                WHERE tp.player_id = {$player['id']}");
                    if ($teams_result) {
                        while ($team_row = $teams_result->fetch_assoc()) {
                            $teams[] = $team_row;
                        }
                    }
                    
                    $player['teams'] = $teams;
                    
                    // Get player matches
                    $matches = [];
                    $matches_result = $conn->query("SELECT m.id, m.match_id, m.name, m.date, m.status, 
                                                  t1.name as team_a_name, t2.name as team_b_name 
                                                  FROM match_players mp 
                                                  JOIN matches m ON mp.match_id = m.id 
                                                  JOIN teams t1 ON m.team_a_id = t1.id 
                                                  JOIN teams t2 ON m.team_b_id = t2.id 
                                                  WHERE mp.player_id = {$player['id']}");
                    if ($matches_result) {
                        while ($match_row = $matches_result->fetch_assoc()) {
                              {
                        while ($match_row = $matches_result->fetch_assoc()) {
                            $matches[] = $match_row;
                        }
                    }
                    
                    $player['matches'] = $matches;
                    
                    // Get player awards
                    $awards = [];
                    $awards_result = $conn->query("SELECT * FROM awards WHERE player_id = {$player['id']}");
                    if ($awards_result) {
                        while ($award_row = $awards_result->fetch_assoc()) {
                            $awards[] = $award_row;
                        }
                    }
                    
                    $player['awards'] = $awards;
                    
                    // Get player gallery
                    $gallery = [];
                    $gallery_result = $conn->query("SELECT * FROM gallery WHERE user_id = {$player['id']} AND type = 'player'");
                    if ($gallery_result) {
                        while ($gallery_row = $gallery_result->fetch_assoc()) {
                            $gallery[] = $gallery_row;
                        }
                    }
                    
                    $player['gallery'] = $gallery;
                    
                    echo json_encode(['success' => true, 'playerProfile' => $player]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Player not found']);
                }
                
                $stmt->close();
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'User ID is required']);
            }
            break;
        case 'POST':
            // Create player profile
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['user_id']) || !isset($data['player_type'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                return;
            }
            
            // Check if user exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE id = ? OR user_id = ?");
            $stmt->bind_param("is", $data['user_id'], $data['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'User not found']);
                return;
            }
            
            $user = $result->fetch_assoc();
            $user_id = $user['id'];
            $stmt->close();
            
            // Check if player profile already exists
            $stmt = $conn->prepare("SELECT id FROM player_profiles WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Player profile already exists']);
                return;
            }
            
            $stmt->close();
            
            // Insert player profile
            $stmt = $conn->prepare("INSERT INTO player_profiles (user_id, player_type, batting_style, bowling_style, highest_score, best_bowling) VALUES (?, ?, ?, ?, ?, ?)");
            
            $player_type = $data['player_type'];
            $batting_style = isset($data['batting_style']) ? $data['batting_style'] : null;
            $bowling_style = isset($data['bowling_style']) ? $data['bowling_style'] : null;
            $highest_score = isset($data['highest_score']) ? $data['highest_score'] : 0;
            $best_bowling = isset($data['best_bowling']) ? $data['best_bowling'] : '0/0';
            
            $stmt->bind_param("isssss", $user_id, $player_type, $batting_style, $bowling_style, $highest_score, $best_bowling);
            
            if ($stmt->execute()) {
                // Create player stats
                $conn->query("INSERT INTO player_stats (player_id) VALUES ($user_id)");
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Player profile created successfully',
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error creating player profile: ' . $conn->error]);
            }
            
            $stmt->close();
            break;
        case 'PUT':
            if (count($segments) > 1) {
                // Update player profile
                $user_id = $segments[1];
                $data = json_decode(file_get_contents('php://input'), true);
                
                // Check if user exists
                $stmt = $conn->prepare("SELECT id FROM users WHERE id = ? OR user_id = ?");
                $stmt->bind_param("is", $user_id, $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'User not found']);
                    return;
                }
                
                $user = $result->fetch_assoc();
                $user_id = $user['id'];
                $stmt->close();
                
                // Check if player profile exists
                $stmt = $conn->prepare("SELECT id FROM player_profiles WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Player profile not found']);
                    return;
                }
                
                $stmt->close();
                
                // Update player profile
                $stmt = $conn->prepare("UPDATE player_profiles SET player_type = ?, batting_style = ?, bowling_style = ?, highest_score = ?, best_bowling = ? WHERE user_id = ?");
                
                $player_type = isset($data['player_type']) ? $data['player_type'] : null;
                $batting_style = isset($data['batting_style']) ? $data['batting_style'] : null;
                $bowling_style = isset($data['bowling_style']) ? $data['bowling_style'] : null;
                $highest_score = isset($data['highest_score']) ? $data['highest_score'] : null;
                $best_bowling = isset($data['best_bowling']) ? $data['best_bowling'] : null;
                
                $stmt->bind_param("sssssi", $player_type, $batting_style, $bowling_style, $highest_score, $best_bowling, $user_id);
                
                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Player profile updated successfully',
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Error updating player profile: ' . $conn->error]);
                }
                
                $stmt->close();
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'User ID is required']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
}

// Match scoring routes
function handleMatchScoringRoutes($segments, $method, $conn) {
    switch ($method) {
        case 'GET':
            if (count($segments) > 1) {
                // Get match scorecard
                $match_id = $segments[1];
                $innings = isset($_GET['innings']) ? (int)$_GET['innings'] : 1;
                
                // Check if match exists
                $stmt = $conn->prepare("SELECT m.*, 
                                       t1.id as team_a_id, t1.name as team_a_name, 
                                       t2.id as team_b_id, t2.name as team_b_name 
                                       FROM matches m 
                                       JOIN teams t1 ON m.team_a_id = t1.id 
                                       JOIN teams t2 ON m.team_b_id = t2.id 
                                       WHERE m.id = ? OR m.match_id = ?");
                $stmt->bind_param("is", $match_id, $match_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Match not found']);
                    return;
                }
                
                $match = $result->fetch_assoc();
                $match_id = $match['id'];
                $stmt->close();
                
                // Get innings
                $stmt = $conn->prepare("SELECT i.*, t.name as batting_team_name 
                                       FROM innings i 
                                       JOIN teams t ON i.team_id = t.id 
                                       WHERE i.match_id = ? AND i.innings_number = ?");
                $stmt->bind_param("ii", $match_id, $innings);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Innings not found']);
                    return;
                }
                
                $innings_data = $result->fetch_assoc();
                $innings_id = $innings_data['id'];
                $stmt->close();
                
                // Get batting scorecard
                $batsmen = [];
                $batting_result = $conn->query("SELECT bs.*, u.name as batsman_name, 
                                              u2.name as bowler_name, u3.name as fielder_name 
                                              FROM batting_scorecard bs 
                                              JOIN users u ON bs.player_id = u.id 
                                              LEFT JOIN users u2 ON bs.bowler_id = u2.id 
                                              LEFT JOIN users u3 ON bs.fielder_id = u3.id 
                                              WHERE bs.match_id = $match_id AND bs.innings_id = $innings_id");
                if ($batting_result) {
                    while ($batting_row = $batting_result->fetch_assoc()) {
                        $batsmen[] = $batting_row;
                    }
                }
                
                // Get bowling scorecard
                $bowlers = [];
                $bowling_result = $conn->query("SELECT bs.*, u.name as bowler_name 
                                              FROM bowling_scorecard bs 
                                              JOIN users u ON bs.player_id = u.id 
                                              WHERE bs.match_id = $match_id AND bs.innings_id = $innings_id");
                if ($bowling_result) {
                    while ($bowling_row = $bowling_result->fetch_assoc()) {
                        $bowlers[] = $bowling_row;
                    }
                }
                
                // Get last events
                $events = [];
                $events_result = $conn->query("SELECT me.*, u1.name as batsman_name, u2.name as bowler_name 
                                             FROM match_events me 
                                             JOIN users u1 ON me.batsman_id = u1.id 
                                             JOIN users u2 ON me.bowler_id = u2.id 
                                             WHERE me.match_id = $match_id AND me.innings_id = $innings_id 
                                             ORDER BY me.timestamp DESC LIMIT 10");
                if ($events_result) {
                    while ($event_row = $events_result->fetch_assoc()) {
                        $events[] = $event_row;
                    }
                }
                
                // Get current batsmen and bowler
                $current_batsmen = [];
                $current_batsmen_result = $conn->query("SELECT player_id FROM batting_scorecard 
                                                      WHERE match_id = $match_id AND innings_id = $innings_id 
                                                      AND dismissal_type IS NULL 
                                                      ORDER BY id ASC LIMIT 2");
                if ($current_batsmen_result) {
                    while ($batsman_row = $current_batsmen_result->fetch_assoc()) {
                        $current_batsmen[] = $batsman_row['player_id'];
                    }
                }
                
                $current_bowler = null;
                $current_bowler_result = $conn->query("SELECT bowler_id FROM match_events 
                                                     WHERE match_id = $match_id AND innings_id = $innings_id 
                                                     ORDER BY timestamp DESC LIMIT 1");
                if ($current_bowler_result && $current_bowler_result->num_rows > 0) {
                    $bowler_row = $current_bowler_result->fetch_assoc();
                    $current_bowler = $bowler_row['bowler_id'];
                }
                
                // Prepare response
                $scorecard = [
                    'match_id' => $match['id'],
                    'match_name' => $match['name'],
                    'innings' => $innings,
                    'batting_team' => $innings_data['team_id'],
                    'batting_team_name' => $innings_data['batting_team_name'],
                    'bowling_team' => $innings_data['team_id'] == $match['team_a_id'] ? $match['team_b_id'] : $match['team_a_id'],
                    'bowling_team_name' => $innings_data['team_id'] == $match['team_a_id'] ? $match['team_b_name'] : $match['team_a_name'],
                    'score' => [
                        'runs' => $innings_data['runs'],
                        'wickets' => $innings_data['wickets'],
                        'overs' => $innings_data['overs'],
                        'extras' => [
                            'wides' => $innings_data['extras_wides'],
                            'no_balls' => $innings_data['extras_no_balls'],
                            'byes' => $innings_data['extras_byes'],
                            'leg_byes' => $innings_data['extras_leg_byes'],
                            'penalty' => $innings_data['extras_penalty'],
                            'total' => $innings_data['extras_total'],
                        ],
                    ],
                    'batsmen' => $batsmen,
                    'bowlers' => $bowlers,
                    'current_batsmen' => $current_batsmen,
                    'current_bowler' => $current_bowler,
                    'last_events' => $events,
                ];
                
                echo json_encode(['success' => true, 'scorecard' => $scorecard]);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Match ID is required']);
            }
            break;
        case 'POST':
            // Record scoring event
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['match_id']) || !isset($data['innings']) || !isset($data['event'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                return;
            }
            
            // Check if match exists
            $stmt = $conn->prepare("SELECT id FROM matches WHERE id = ? OR match_id = ?");
            $stmt->bind_param("is", $data['match_id'], $data['match_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Match not found']);
                return;
            }
            
            $match = $result->fetch_assoc();
            $match_id = $match['id'];
            $stmt->close();
            
            // Check if innings exists
            $stmt = $conn->prepare("SELECT id FROM innings WHERE match_id = ? AND innings_number = ?");
            $stmt->bind_param("ii", $match_id, $data['innings']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Innings not found']);
                return;
            }
            
            $innings = $result->fetch_assoc();
            $innings_id = $innings['id'];
            $stmt->close();
            
            // Process the event
            $event = $data['event'];
            
            // Insert event
            $stmt = $conn->prepare("INSERT INTO match_events (match_id, innings_id, over_number, ball_number, event_type, event_value, batsman_id, bowler_id, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->bind_param("iiddsiiis", $match_id, $innings_id, $event['over'], $event['ball'], $event['type'], $event['value'], $event['batsman_id'], $event['bowler_id'], $event['description']);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Scoring event recorded successfully',
                    'event_id' => $conn->insert_id,
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error recording scoring event: ' . $conn->error]);
            }
            
            $stmt->close();
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
}

// YouTube routes
function handleYouTubeRoutes($segments, $method, $conn) {
    switch ($method) {
        case 'GET':
            if (count($segments) > 1 && $segments[1] === 'match-videos') {
                // Get match videos
                $match_id = isset($_GET['matchId']) ? $_GET['matchId'] : null;
                
                if (!$match_id) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Match ID is required']);
                    return;
                }
                
                // In a real app, this would fetch videos from YouTube API
                // For now, return mock data
                
                echo json_encode([
                    'success' => true,
                    'videos' => [
                        [
                            'id' => 'mock-video-id-1',
                            'title' => 'Match Highlights',
                            'thumbnail' => 'https://i.ytimg.com/vi/mock-video-id-1/hqdefault.jpg',
                            'url' => 'https://www.youtube.com/watch?v=mock-video-id-1',
                            'publishedAt' => '2025-03-20T18:00:00Z',
                        ],
                        [
                            'id' => 'mock-video-id-2',
                            'title' => 'Full Match Replay',
                            'thumbnail' => 'https://i.ytimg.com/vi/mock-video-id-2/hqdefault.jpg',
                            'url' => 'https://www.youtube.com/watch?v=mock-video-id-2',
                            'publishedAt' => '2025-03-20T19:30:00Z',
                        ],
                    ],
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'API endpoint not found']);
            }
            break;
        case 'POST':
            if (count($segments) > 1 && $segments[1] === 'stream') {
                // Create live stream
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($data['title'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Title is required']);
                    return;
                }
                
                // In a real app, this would create a live stream via YouTube API
                // For now, return mock data
                
                $stream_id = 'mock-stream-id-' . bin2hex(random_bytes(4));
                $stream_key = 'mock-stream-key-' . bin2hex(random_bytes(8));
                $video_id = 'mock-video-id-' . bin2hex(random_bytes(4));
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Live stream created successfully',
                    'streamId' => $stream_id,
                    'streamUrl' => 'rtmp://a.rtmp.youtube.com/live2/' . $stream_key,
                    'streamKey' => $stream_key,
                    'watchUrl' => 'https://www.youtube.com/watch?v=' . $video_id,
                    'matchId' => $data['matchId'] ?? null,
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'API endpoint not found']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
}

