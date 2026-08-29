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

// Load Hexagram Associations
$associations_query = "
    SELECT 
        ha.related_hexagram_id,
        ha.notes,
        at.type_name,
        at.description AS type_description,
        h.english_translation,
        h.chinese_character,
        h.chinese_pinyin
    FROM hexagram_associations ha
    JOIN association_types at ON ha.association_type_id = at.id
    JOIN iching_hexagrams h ON ha.related_hexagram_id = h.hexagram_number
    WHERE ha.source_hexagram_id = ?
    ORDER BY at.id ASC, ha.related_hexagram_id ASC
";
$stmt = $mysqli->prepare($associations_query);
$stmt->bind_param("i", $hex_id);
$stmt->execute();
$assoc_result = $stmt->get_result();

$associations_by_type = [];
while ($row = $assoc_result->fetch_assoc()) {
    $associations_by_type[$row['type_name']][] = $row;
}
$stmt->close();

$imagePath = "images/hexagram-" . $hex_id . ".jpg";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hexagram <?php echo $hex_id; ?>: <?php echo htmlspecialchars($hexagram['english_translation']); ?></title>
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif+SC:wght@400;700&display=swap" rel="stylesheet">
<style>
body {
    background: #1d3557;
    color: white;
    font-family: Arial, Helvetica, sans-serif;
    margin: 15px;
    padding: 0;
}
h1, h2, h3 { text-align: center; color: #ffd966; }
.panel {
    background: white;
    color: #222;
    border-radius: 10px;
    padding: 20px;
    margin: 20px auto;
    max-width: 700px;
    width: 100%;
    box-sizing: border-box;
}
.card-image {
    display: block;
    margin: auto;
    max-width: 100%;
    height: auto;
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
    font-size: 15px;
    font-weight: bold;
    color: #1d3557;
    word-wrap: break-word;
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

/* Responsive Navigation Layout */
.nav-flex {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
    width: 100%;
}
.nav-flex-item {
    flex: 1;
    min-width: 0;
}
.nav-flex-item.left { text-align: left; }
.nav-flex-item.center { text-align: center; }
.nav-flex-item.right { text-align: right; }

.assoc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin-top: 15px;
}
.assoc-card {
    background: #f7f7f7;
    border-left: 5px solid #162447;
    border-radius: 6px;
    padding: 12px 15px;
    color: #222;
    text-decoration: none;
    display: block;
}
.assoc-card:hover {
    background: #eef2f7;
    border-left-color: #e43f5a;
}
.assoc-title {
    font-size: 1.1rem;
    font-weight: bold;
    color: #1d3557;
}
.assoc-cjk {
    font-family: 'Noto Serif SC', serif;
    font-weight: bold;
    color: #162447;
    margin-right: 5px;
}
.assoc-notes {
    font-size: 0.9rem;
    color: #555;
    margin-top: 6px;
}

@media (max-width: 600px) {
    body {
        margin: 10px;
    }
    .panel {
        padding: 12px;
    }
    .nav-card-name {
        font-size: 13px;
    }
    .back-link {
        font-size: 14px;
    }
    .chinese-char {
        font-size: 3rem;
    }
}
</style>
</head>
<body>

<p style="text-align:center;">
    <a class="back-link" href="study_guide.php" style="color:#ffd966;">&larr; Back to All Hexagrams</a>
</p>

<h1>Hexagram <?php echo $hex_id; ?>: <?php echo htmlspecialchars($hexagram['english_translation']); ?></h1>

<?php if (file_exists($imagePath)): ?>
    <img class="card-image" src="<?php echo $imagePath; ?>" alt="Hexagram <?php echo $hex_id; ?>">
<?php endif; ?>

<div class="panel">
    <div class="nav-flex">
        <div class="nav-flex-item left">
            <?php if ($hex_id > 1): ?>
                <a class="back-link" href="hexagram.php?id=<?php echo $hex_id - 1; ?>">
                    &larr; Previous
                    <span class="nav-card-name"><?php echo htmlspecialchars($previous_name); ?></span>
                </a>
            <?php endif; ?>
        </div>
        <div class="nav-flex-item center">
            <a class="back-link" href="study_guide.php">All Hexagrams</a>
        </div>
        <div class="nav-flex-item right">
            <?php if ($hex_id < 64): ?>
                <a class="back-link" href="hexagram.php?id=<?php echo $hex_id + 1; ?>">
                    Next &rarr;
                    <span class="nav-card-name"><?php echo htmlspecialchars($next_name); ?></span>
                </a>
            <?php endif; ?>
        </div>
    </div>
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

<!-- Associated Hexagrams Knowledge Graph -->
<?php if (!empty($associations_by_type)): ?>
    <div class="panel">
        <h2>Associated Hexagrams</h2>
        
        <?php foreach ($associations_by_type as $typeName => $items): ?>
            <h3 style="text-align:left; border-bottom: 2px solid #162447; padding-bottom: 5px; color: #162447;">
                <?php echo htmlspecialchars($typeName); ?>
            </h3>
            
            <div class="assoc-grid">
                <?php foreach ($items as $item): ?>
                    <a class="assoc-card" href="hexagram.php?id=<?php echo $item['related_hexagram_id']; ?>">
                        <div class="assoc-title">
                            #<?php echo $item['related_hexagram_id']; ?> - <?php echo htmlspecialchars($item['english_translation']); ?>
                        </div>
                        <div>
                            <span class="assoc-cjk"><?php echo htmlspecialchars($item['chinese_character']); ?></span>
                            (<?php echo htmlspecialchars($item['chinese_pinyin']); ?>)
                        </div>
                        <?php if (!empty($item['notes'])): ?>
                            <div class="assoc-notes"><?php echo htmlspecialchars($item['notes']); ?></div>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <br>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div style="text-align:center; margin:20px;">
    <a class="button" href="https://en.wikipedia.org/wiki/List_of_hexagrams_of_the_I_Ching#Hexagram_<?php echo $hex_id; ?>" target="_blank">Read Wikipedia Text</a>
    <a class="button" href="https://divination.com/iching/lookup/<?php echo $hex_id; ?>-2" target="_blank">Read Divination.com Text</a>
</div>

<div class="panel">
    <div class="nav-flex">
        <div class="nav-flex-item left">
            <?php if ($hex_id > 1): ?>
                <a class="back-link" href="hexagram.php?id=<?php echo $hex_id - 1; ?>">
                    &larr; Previous
                    <span class="nav-card-name"><?php echo htmlspecialchars($previous_name); ?></span>
                </a>
            <?php endif; ?>
        </div>
        <div class="nav-flex-item center">
            <a class="back-link" href="index.php">Return to Oracle</a>
        </div>
        <div class="nav-flex-item right">
            <?php if ($hex_id < 64): ?>
                <a class="back-link" href="hexagram.php?id=<?php echo $hex_id + 1; ?>">
                    Next &rarr;
                    <span class="nav-card-name"><?php echo htmlspecialchars($next_name); ?></span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div style="margin-top:40px; text-align:center;">
    <p><a class="back-link" href="index.php" style="color:#ffd966;">Return to Oracle</a></p>
    <p><a class="back-link" href="/" style="color:#ffd966;">Main Page</a></p>
</div>

</body>
</html>
