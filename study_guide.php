<?php
$mysqli = new mysqli("localhost", "iching_db", "iching_db", "iching_db");

if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $stmt = $mysqli->prepare("
        SELECT hexagram_number, english_translation, chinese_character, chinese_pinyin
        FROM iching_hexagrams
        WHERE english_translation LIKE ? OR chinese_pinyin LIKE ?
        ORDER BY hexagram_number
    ");
    $term = '%' . $search . '%';
    $stmt->bind_param("ss", $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $mysqli->query("
        SELECT hexagram_number, english_translation, chinese_character, chinese_pinyin
        FROM iching_hexagrams
        ORDER BY hexagram_number
    ");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I Ching Study Guide</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+SC:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #555555;
            color: white;
            font-family: "Times New Roman", Times, serif;
            margin: 20px;
        }
        h1 { text-align: center; color: #ffd966; }
        .card-list { max-width: 700px; margin: auto; }
        .card-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            margin: 4px 0;
            background: #162447;
            border: 1px solid #e43f5a;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            font-size: 18px;
        }
        .card-link:hover { background: #e43f5a; }
        .cjk-span {
            font-family: 'Noto Serif SC', serif;
            font-weight: bold;
            color: #ffd966;
            margin-right: 8px;
        }
        .footer { margin-top: 30px; text-align: center; }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            background: #162447;
            border: 1px solid #e43f5a;
            border-radius: 6px;
            color: white;
            text-decoration: none;
        }
        .button:hover { background: #e43f5a; }
    </style>
</head>
<body>

<h1>I Ching Study Guide</h1>

<div style="text-align:center; margin-bottom:20px;">
    <a class="button" href="study_guide.php">All Hexagrams</a>
</div>

<form method="get" style="text-align:center; margin-bottom:20px;">
    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search hexagrams..." style="padding:8px; width:300px;">
    <button type="submit" style="padding:8px 15px;">Search</button>
</form>

<p style="text-align:center;">
    <a class="button" href="hexagram.php?id=<?php echo rand(1,64); ?>">Random Hexagram Study</a>
</p>

<div class="card-list">
<?php
while ($row = $result->fetch_assoc()) {
    echo '<a class="card-link" href="hexagram.php?id=' . $row['hexagram_number'] . '">';
    echo '<span>#' . $row['hexagram_number'] . ' - ' . htmlspecialchars($row['english_translation']) . '</span>';
    echo '<span><span class="cjk-span">' . htmlspecialchars($row['chinese_character']) . '</span>(' . htmlspecialchars($row['chinese_pinyin']) . ')</span>';
    echo '</a>';
}
?>
</div>

<div class="footer">
    <p><a href="index.php" style="color:white;">Return to I Ching Oracle</a></p>
    <p><a href="/" style="color:white;">Return to Main Page</a></p>
</div>

</body>
</html>
