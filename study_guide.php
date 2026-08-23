<?php
$host    = 'localhost';
$db      = 'iching_db';
$user    = 'iching_db';
$pass    = 'iching_db';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $stmt = $pdo->query("
        SELECT 
            hexagram_number, 
            chinese_character, 
            chinese_pinyin, 
            english_translation, 
            core_meaning, 
            description 
        FROM iching_hexagrams 
        ORDER BY hexagram_number ASC
    ");
    $hexagrams = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Database Connection Error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I Ching Study Guide - All 64 Hexagrams</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@exampleuser/water.css@2/out/water.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+SC:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: Times, serif; 
            background: #1a1a2e; 
            color: #fff; 
            margin: 0 auto; 
            padding: 20px;  
            text-align: center;
            line-height: 1.4;
            max-width: 900px;
        }
        h1 { color: #ffd966; }
        .nav-links { margin-bottom: 30px; }
        .nav-links a {
            color: #ffd966;
            font-weight: bold;
            text-decoration: underline;
            margin: 0 15px;
        }
        .hexagram-card {
            background: #162447;
            border: 2px solid #e43f5a;
            border-radius: 8px;
            padding: 20px;
            margin: 25px auto;
            text-align: center;
            max-width: 750px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        }
        .hexagram-img { 
            width: 220px; 
            border-radius: 4px; 
            margin: 15px 0;
            border: 1px solid #1a1a2e;
        }
        .chinese-char {
            font-family: 'Noto Serif SC', 'Times New Roman', serif;
            font-size: 3.5rem;
            font-weight: bold;
            color: #ffd966;
            display: block;
            line-height: 1;
            margin: 10px 0 5px 0;
        }
        .pinyin-text {
            font-size: 1.3rem;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .desc-text {
            text-align: left;
            background: #1a1a2e;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #ffd966;
            margin: 15px 0;
        }
        button { 
            background: #1a1a2e; 
            color: teal; 
            border: none; 
            padding: 8px 16px; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 16px; 
            margin: 5px;
        }
        button:hover { 
            background: #ff4a68; 
            color: #fff;
        }
    </style>
</head>
<body>

    <h1>I Ching Study Guide</h1>
    <p>Complete Reference for all 64 Hexagrams of the King Wen Sequence</p>

    <div class="nav-links">
        <a href=".">Back to I Ching Oracle</a>
        <a href="/tarot-study-guide" target="_blank">Do a Tarot Card Reading</a>
        <a href="/" target="_blank">Main Page</a>
    </div>

    <hr>

    <?php foreach ($hexagrams as $hex): ?>
        <?php $imagePath = "images/hexagram-" . $hex['hexagram_number'] . ".jpg"; ?>
        
        <div class="hexagram-card" id="hex-<?php echo $hex['hexagram_number']; ?>">
            <h2>Hexagram <?php echo $hex['hexagram_number']; ?>: <?php echo htmlspecialchars($hex['english_translation']); ?></h2>
            
            <?php if (file_exists($imagePath)): ?>
                <p>
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                         alt="Hexagram <?php echo $hex['hexagram_number']; ?>" 
                         class="hexagram-img">
                </p>
            <?php endif; ?>

            <span class="chinese-char"><?php echo htmlspecialchars($hex['chinese_character'] ?? ''); ?></span>
            <p class="pinyin-text"><b>(<?php echo htmlspecialchars($hex['chinese_pinyin']); ?>)</b></p>

            <div class="desc-text">
                <p><b>Description & Commentary:</b></p>
                <p><?php echo htmlspecialchars($hex['description']); ?></p>
            </div>

            <button onclick="window.open('https://en.wikipedia.org/wiki/List_of_hexagrams_of_the_I_Ching#Hexagram_<?php echo $hex['hexagram_number']; ?>','_blank');">
                Wikipedia Text
            </button>
            <button onclick="window.open('https://divination.com/iching/lookup/<?php echo $hex['hexagram_number']; ?>-2','_blank');">
                Divination.com Text
            </button>
        </div>
    <?php endforeach; ?>

</body>
</html>
