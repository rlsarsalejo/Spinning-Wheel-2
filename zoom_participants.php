<?php
// Database configuration
$host = '127.0.0.1:3306';
$dbname = 'u127020187_zoom';
$user = 'u127020187_rene';
$pass = 'G3n3sis1:28';

// Create a database connection
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Function to handle webhook data
function handleWebhook($data, $pdo) {
    $event = $data['event'] ?? '';
    $meetingId = $data['payload']['object']['id'] ?? '';
    $participantId = $data['payload']['object']['participant']['id'] ?? '';
    $participantName = $data['payload']['object']['participant']['user_name'] ?? '';

    if ($event === 'meeting.participant_joined') {
        $stmt = $pdo->prepare("INSERT INTO participants (meeting_id, participant_id, participant_name, status) VALUES (?, ?, ?, 'joined') ON DUPLICATE KEY UPDATE status = 'joined', timestamp = CURRENT_TIMESTAMP");
        $stmt->execute([$meetingId, $participantId, $participantName]);
    } elseif ($event === 'meeting.participant_left') {
        $stmt = $pdo->prepare("UPDATE participants SET status = 'left', timestamp = CURRENT_TIMESTAMP WHERE meeting_id = ? AND participant_id = ?");
        $stmt->execute([$meetingId, $participantId]);
    }
}

// Function to get the total number of participants who are currently joined
function getJoinedCount($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM participants WHERE status = 'joined'");
    return $stmt->fetchColumn();
}

// Function to display the participants table with filtering
function displayParticipants($pdo, $filter = 'all') {
    // Get the total number of currently joined participants
    $joinedCount = getJoinedCount($pdo);

    $sql = "SELECT * FROM participants";
    if ($filter !== 'all') {
        $sql .= " WHERE status = :filter";
    }
    $sql .= " ORDER BY timestamp DESC";

    $stmt = $pdo->prepare($sql);
    if ($filter !== 'all') {
        $stmt->bindParam(':filter', $filter);
    }
    $stmt->execute();
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>Zoom Meeting Participants</title>";
    echo "<style>";
    echo "body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }";
    echo "h1 { text-align: center; color: #333; padding: 20px; }";
    echo "form { text-align: center; margin: 20px; }";
    echo "select, button { padding: 10px; font-size: 16px; }";
    echo "table { width: 80%; margin: 20px auto; border-collapse: collapse; }";
    echo "th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }";
    echo "th { background-color: #4CAF50; color: white; }";
    echo "tr:nth-child(even) { background-color: #f2f2f2; }";
    echo "tr:hover { background-color: #ddd; }";
    echo "footer { text-align: center; padding: 10px; background-color: #333; color: white; position: fixed; width: 100%; bottom: 0; }";
    echo "</style>";
    echo "</head>";
    echo "<body>";

    echo "<h1>Zoom Meeting Participants</h1>";
    echo "<form method='GET' action=''>";
    echo "<label for='status'>Filter by status:</label>";
    echo "<select name='status' id='status' onchange='this.form.submit()'>";
    echo "<option value='all'" . ($filter === 'all' ? ' selected' : '') . ">All</option>";
    echo "<option value='joined'" . ($filter === 'joined' ? ' selected' : '') . ">Joined</option>";
    echo "<option value='left'" . ($filter === 'left' ? ' selected' : '') . ">Left</option>";
    echo "</select>";
    echo "</form>";

    // Display total number of joined participants
    echo "<p style='text-align: center; font-size: 18px;'><strong>Total Currently Joined Participants: " . htmlspecialchars($joinedCount) . "</strong></p>";

    echo "<table>";
    echo "<tr><th>Meeting ID</th><th>Participant ID</th><th>Participant Name</th><th>Status</th><th>Timestamp</th></tr>";

    foreach ($participants as $participant) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($participant['meeting_id']) . "</td>";
        echo "<td>" . htmlspecialchars($participant['participant_id']) . "</td>";
        echo "<td>" . htmlspecialchars($participant['participant_name']) . "</td>";
        echo "<td>" . htmlspecialchars($participant['status']) . "</td>";
        echo "<td>" . htmlspecialchars($participant['timestamp']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "<footer>© " . date('Y') . " Zoom Meeting Participants</footer>";
    echo "</body>";
    echo "</html>";
}

// Handle webhook request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);

    if ($data === null) {
        http_response_code(400);
        echo "Invalid JSON data";
        exit;
    }

    handleWebhook($data, $pdo);
    http_response_code(200);
    echo "Webhook received";
} else {
    // Get filter status from query parameter
    $filter = isset($_GET['status']) ? $_GET['status'] : 'all';
    // Display participants table with the selected filter
    displayParticipants($pdo, $filter);
}
?>
