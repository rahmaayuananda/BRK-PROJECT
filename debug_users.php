<?php
/**
 * DEBUG FILE - Check if users are being saved and retrieved correctly
 * Access via: http://localhost/brk-project/debug_users.php
 */

// Simple database connection without CodeIgniter dependencies
$db_config = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'brk-project'
];

// Try to connect to database
$mysqli = @new mysqli(
    $db_config['hostname'],
    $db_config['username'],
    $db_config['password'],
    $db_config['database']
);

if ($mysqli->connect_error) {
    $db_error = "Database Connection Error: " . $mysqli->connect_error;
    $db_connected = false;
} else {
    $mysqli->set_charset("utf8mb4");
    $db_connected = true;
}

// Get path to JSON file
$app_path = dirname(__FILE__) . '/application/';
$users_json_file = $app_path . 'data/users.json';

echo "<h1>🔍 User Debug Panel</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
    th { background: #0d6efd; color: white; }
    tr:hover { background: #f9f9f9; }
    .success { color: #28a745; font-weight: bold; }
    .error { color: #dc3545; font-weight: bold; }
    .warning { color: #ffc107; font-weight: bold; }
    .info { color: #0d6efd; font-weight: bold; }
    code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    .code-block { background: #f4f4f4; padding: 12px; border-radius: 6px; border-left: 4px solid #0d6efd; overflow-x: auto; }
    pre { font-size: 12px; margin: 0; }
    ul { line-height: 1.8; }
</style>";

// ============= SECTION 1: DATABASE CONNECTION =============
echo "<div class='section'>";
echo "<h2>1️⃣ Database Connection Status</h2>";

if ($db_connected) {
    echo "<p class='success'>✅ Connected to: <code>" . htmlspecialchars($db_config['database']) . "@" . htmlspecialchars($db_config['hostname']) . "</code></p>";
} else {
    echo "<p class='error'>❌ " . htmlspecialchars($db_error) . "</p>";
    echo "<p class='warning'>⚠️ Will check JSON fallback only...</p>";
}

echo "</div>";

// ============= SECTION 2: DATABASE USERS TABLE =============
if ($db_connected) {
    echo "<div class='section'>";
    echo "<h2>2️⃣ Database Users Table</h2>";
    
    $result = $mysqli->query("SHOW TABLES LIKE 'users'");
    if ($result && $result->num_rows > 0) {
        echo "<p class='success'>✅ Table 'users' EXISTS</p>";
        
        // Show table structure
        echo "<h3>📋 Table Structure:</h3>";
        $structure = $mysqli->query("DESCRIBE users");
        echo "<table>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $structure->fetch_assoc()) {
            echo "<tr>";
            echo "<td><code>" . htmlspecialchars($row['Field']) . "</code></td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>❌ Table 'users' DOES NOT EXIST</p>";
    }
    echo "</div>";
    
    // ============= SECTION 3: ALL USERS IN DATABASE =============
    echo "<div class='section'>";
    echo "<h2>3️⃣ All Users in Database</h2>";
    
    $result = $mysqli->query("SELECT id_users, username, name, role, created_at FROM users ORDER BY id_users DESC");
    if ($result) {
        $count = $result->num_rows;
        echo "<p class='info'>📊 Total users: <strong>$count</strong></p>";
        
        if ($count > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Username</th><th>Name</th><th>Mention Tag</th><th>Role</th><th>Created At</th></tr>";
            while ($row = $result->fetch_assoc()) {
                $mention_tag = str_replace(' ', '_', $row['name'] ?? $row['username'] ?? '');
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id_users']) . "</td>";
                echo "<td><code>" . htmlspecialchars($row['username']) . "</code></td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td><strong>@" . htmlspecialchars($mention_tag) . "</strong></td>";
                echo "<td>" . htmlspecialchars($row['role'] ?? 'user') . "</td>";
                echo "<td>" . htmlspecialchars($row['created_at'] ?? '-') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p class='error'>Query error: " . $mysqli->error . "</p>";
    }
    echo "</div>";
    
    // ============= SECTION 4: SEARCH FOR ANANDA =============
    echo "<div class='section'>";
    echo "<h2>4️⃣ Search for 'Ananda' User</h2>";
    
    $search_result = $mysqli->query("SELECT id_users, username, name FROM users WHERE name LIKE '%Ananda%' OR username LIKE '%Ananda%'");
    if ($search_result) {
        if ($search_result->num_rows > 0) {
            echo "<p class='success'>✅ Found " . $search_result->num_rows . " user(s) with 'Ananda':</p>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Username</th><th>Name</th><th>Mention Tag</th></tr>";
            while ($row = $search_result->fetch_assoc()) {
                $mention_tag = str_replace(' ', '_', $row['name'] ?? $row['username'] ?? '');
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id_users']) . "</td>";
                echo "<td><code>" . htmlspecialchars($row['username']) . "</code></td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td><strong>@" . htmlspecialchars($mention_tag) . "</strong></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='error'>❌ No user with 'Ananda' found in database</p>";
        }
    }
    echo "</div>";
}

// ============= SECTION 5: JSON FLATFILE =============
echo "<div class='section'>";
echo "<h2>5️⃣ Flatfile Backup (users.json)</h2>";

if (file_exists($users_json_file)) {
    $json = file_get_contents($users_json_file);
    $users = json_decode($json, true);
    
    if (is_array($users)) {
        echo "<p class='success'>✅ File exists: <code>" . htmlspecialchars($users_json_file) . "</code></p>";
        echo "<p>📊 Total users in JSON: <strong>" . count($users) . "</strong></p>";
        
        // Search for Ananda
        $found_ananda = false;
        foreach ($users as $uname => $u) {
            if (stripos($uname, 'ananda') !== false || stripos($u['fullname'] ?? '', 'ananda') !== false) {
                $found_ananda = true;
                echo "<p class='success'>✅ Found 'Ananda' in JSON file</p>";
                echo "<h3>Ananda user data:</h3>";
                echo "<div class='code-block'><pre>" . htmlspecialchars(json_encode($u, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . "</pre></div>";
                break;
            }
        }
        if (!$found_ananda) {
            echo "<p class='warning'>⚠️ 'Ananda' NOT found in JSON file</p>";
        }
        
        echo "<h3>All users in JSON file:</h3>";
        echo "<div class='code-block'><pre>" . htmlspecialchars(json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . "</pre></div>";
    } else {
        echo "<p class='error'>❌ Invalid JSON format in file</p>";
    }
} else {
    echo "<p class='error'>❌ File not found: <code>" . htmlspecialchars($users_json_file) . "</code></p>";
}
echo "</div>";

// ============= SECTION 6: TEST ENDPOINT SIMULATION =============
echo "<div class='section'>";
echo "<h2>6️⃣ Simulated Endpoint Response</h2>";

$test_users = [];

// Try to fetch from database first
if ($db_connected) {
    $result = $mysqli->query("SELECT id_users, username, name FROM users LIMIT 10");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $display = !empty($row['name']) ? $row['name'] : ($row['username'] ?? '');
            $test_users[] = [
                'id_users' => $row['id_users'],
                'username' => $row['username'],
                'name' => $display,
                'mention_tag' => str_replace(' ', '_', $display)
            ];
        }
    }
}

// If no DB users, try JSON
if (empty($test_users) && file_exists($users_json_file)) {
    $json = file_get_contents($users_json_file);
    $users = json_decode($json, true);
    if (is_array($users)) {
        foreach ($users as $uname => $u) {
            $test_users[] = [
                'username' => $uname,
                'name' => $u['fullname'] ?? $uname,
                'mention_tag' => str_replace(' ', '_', $u['fullname'] ?? $uname)
            ];
        }
    }
}

echo "<p>This is what <code>/forum/get_users</code> endpoint returns:</p>";
echo "<div class='code-block'><pre>" . htmlspecialchars(json_encode($test_users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . "</pre></div>";
echo "</div>";

// ============= SECTION 7: TROUBLESHOOTING TIPS =============
echo "<div class='section'>";
echo "<h2>7️⃣ Troubleshooting Guide</h2>";

echo "<h3>✅ If 'Ananda' shows in database:</h3>";
echo "<ul>";
echo "<li>Open forum page → press <strong>F12</strong> (DevTools)</li>";
echo "<li>Go to <strong>Console</strong> tab</li>";
echo "<li>Type <strong>@</strong> in the message input</li>";
echo "<li>Check console for: <code>✅ Fetched users:</code> and <code>✅ All users for mention:</code></li>";
echo "<li>Search the output for 'Ananda' or 'Ananda'</li>";
echo "</ul>";

echo "<h3>❌ If 'Ananda' NOT in database:</h3>";
echo "<ul>";
echo "<li><strong>Check JSON file</strong> above - if it's there, check registration process</li>";
echo "<li><strong>Create 'Ananda' user again</strong> - this time watch for errors</li>";
echo "<li>After creating, check this debug page again to confirm it was saved</li>";
echo "</ul>";

echo "<h3>🔧 Common Issues:</h3>";
echo "<ul>";
echo "<li><strong>Space in name:</strong> If name is 'Ananda Pratama', use <code>@Ananda_Pratama</code> (underscores replace spaces)</li>";
echo "<li><strong>Cache issue:</strong> Press <strong>Ctrl+Shift+Delete</strong> → clear cache → refresh page</li>";
echo "<li><strong>Multiple users:</strong> If both DB and JSON have users, endpoint returns only DB users</li>";
echo "</ul>";

echo "</div>";

// ============= SECTION 8: DIRECT ENDPOINT TEST =============
echo "<div class='section'>";
echo "<h2>8️⃣ Direct Endpoint Test</h2>";

echo "<p>Click the link below to see the actual endpoint response:</p>";
echo "<p><a href='http://localhost/brk-project/index.php/forum/get_users' target='_blank' style='padding: 10px 20px; background: #0d6efd; color: white; text-decoration: none; border-radius: 6px; display: inline-block;'>🔗 Test /forum/get_users Endpoint</a></p>";
echo "</div>";

if ($db_connected) {
    $mysqli->close();
}
?>
