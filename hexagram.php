<?php
$mysqli = new mysqli("localhost", "iching_db", "iching_db", "iching_db");

if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

$hex_id = isset($_GET['id']) ? intval($_GET['id']) : 1;
if ($hex_id < 1 || $hex_id > 64) {
    $hex_id = 1;
}

// Load Hexagram
$stmt = $mysqli->prepare("SELECT * FROM iching_hexagrams WHERE hexagram_number = ?");
$stmt->bind_param("i", $hex_id);
$stmt->execute();
$hexagram = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$hexagram) {
    die("Unknown hexagram.");
}

// Load Prev & Next Names
$previous_name = '';
$next_name = '';

if ($hex_id > 1) {
    $prev_id = $hex_id - 1;
    $stmt = $mysqli->prepare("SELECT english_translation FROM iching_hexagrams WHERE hexagram_number = ?");
    $stmt->bind_param("i", $prev_id);
    $stmt->execute();
    $stmt->bind_result($previous_name);
    $stmt->fetch();
    $stmt->close();
}

if ($hex_id < 64) {
    $next_id = $hex_id + 1;
    $stmt = $mysqli->prepare("SELECT english_translation FROM iching_hexagrams WHERE hexagram_number = ?");
    $stmt->bind_param("i", $next_id);
    $stmt->execute();
    $stmt->bind_result($next_name);
    $stmt->fetch();
    $stmt->close();
}

$imagePath = "images/hexagram-" . $hex_id . ".jpg";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Hexagram <?php echo $hex_id; ?>: <?php echo htmlspecialchars($hexagram['english_translation']); ?></title>
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif+SC:wght@400;700&display=swap" rel="stylesheet">
<style>
body {
    background: #1d3557;
    color: white;
    font-family: Arial, Helvetica, sans-serif;
    margin: 30px;
}
h1, h2 { text-align: center; color: #ffd966; }
.panel {
    background: white;
    color: #222;
    border-radius: 10px;
    padding: 20px;
    margin: 25px auto;
    max-width: 700px;
}
.card-image {
    display: block;
    margin: auto;
    max-width: 300px;
    border-radius: 6px;
}
.back-link {
    color: #1d3557;
    text-decoration: underline;
    font-weight: bold;
}
.meaning {
    line-height: 1.7;
    font-size: 18px;
}
.nav-card-name {
    display: block;
    margin-top: 6px;
    font-size: 16px;
    font-weight: bold;
    color: #1d3557;
}
.chinese-char {
    font-family: 'Noto Serif SC', serif;
    font-size: 4rem;
    font-weight: bold;
    color: #162447;
    text-align: center;
    display: block;
}
.button {
    display: inline-block;
    padding: 8px 16px;
    background: #162447;
    border-radius: 4px;
    color: #ffd966;
    text-decoration: none;
    margin: 5px;
}
.button:hover { background: #e43f5a; color: white; }
</style>
</head>
<body>

<p style="text-align:center;">
    <a class="back-link" href="study_guide.php" style="color:#ffd966;">Back to All Hexagrams</a>
</p>

<h1>Hexagram <?php echo $hex_id; ?>: <?php echo htmlspecialchars($hexagram['english_translation']); ?></h1>

<?php if (file_exists($imagePath)): ?>
    <img class="card-image" src="<?php echo $imagePath; ?>" alt="Hexagram <?php echo $hex_id; ?>">
<?php endif; ?>

<div class="panel">
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="text-align:left; vertical-align:top; width:33%;">
                <?php if ($hex_id > 1): ?>
                    <a class="back-link" href="hexagram.php?id=<?php echo $hex_id - 1; ?>">
                        &larr; Previous Hexagram
                        <span class="nav-card-name"><?php echo htmlspecialchars($previous_name); ?></span>
                    </a>
                <?php endif; ?>
            </td>
            <td style="text-align:center; vertical-align:top; width:33%;">
                <a class="back-link" href="study_guide.php">All Hexagrams</a>
            </td>
            <td style="text-align:right; vertical-align:top; width:33%;">
                <?php if ($hex_id < 64): ?>
                    <a class="back-link" href="hexagram.php?id=<?php echo $hex_id + 1; ?>">
                        Next Hexagram &rarr;
                        <span class="nav-card-name"><?php echo htmlspecialchars($next_name); ?></span>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>

<div class="panel" style="text-align:center;">
    <span class="chinese-char"><?php echo htmlspecialchars($hexagram['chinese_character']); ?></span>
    <p style="font-size:1.4rem; font-weight:bold; margin-top:5px;">(<?php echo htmlspecialchars($hexagram['chinese_pinyin']); ?>)</p>
</div>

<div class="panel">
    <h2>Core Meaning</h2>
    <div class="meaning">
        <?php echo nl2br(htmlspecialchars($hexagram['core_meaning'])); ?>
    </div>
</div>

<div class="panel">
    <h2>Description & Commentary</h2>
    <div class="meaning">
        <?php echo nl2br(htmlspecialchars($hexagram['description'])); ?>
    </div>
</div>

<div style="text-align:center; margin:20px;">
    <a class="button" href="https://en.wikipedia.org/wiki/List_of_hexagrams_of_the_I_Ching#Hexagram_<?php echo $hex_id; ?>" target="_blank">Read Wikipedia Text</a>
    <a class="button" href="https://divination.com/iching/lookup/<?php echo $hex_id; ?>-2" target="_blank">Read Divination.com Text</a>
</div>

<div class="panel">
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="text-align:left; vertical-align:top; width:33%;">
                <?php if ($hex_id > 1): ?>
                    <a class="back-link" href="hexagram.php?id=<?php echo $hex_id - 1; ?>">
                        &larr; Previous Hexagram
                        <span class="nav-card-name"><?php echo htmlspecialchars($previous_name); ?></span>
                    </a>
                <?php endif; ?>
            </td>
            <td style="text-align:center; vertical-align:top; width:33%;">
                <a class="back-link" href=".">Return to Oracle</a>
            </td>
            <td style="text-align:right; vertical-align:top; width:33%;">
                <?php if ($hex_id < 64): ?>
                    <a class="back-link" href="hexagram.php?id=<?php echo $hex_id + 1; ?>">
                        Next Hexagram &rarr;
                        <span class="nav-card-name"><?php echo htmlspecialchars($next_name); ?></span>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>

<div style="margin-top:40px; text-align:center;">
    <p><a class="back-link" href="index.php" style="color:#ffd966;">Return to Oracle</a></p>
    <p><a class="back-link" href="/" style="color:#ffd966;">Main Page</a></p>
</div>

</body>
</html>
