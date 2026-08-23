<?php
// 1. Database Configuration
$host    = 'localhost';
$db      = 'iching_db'; // Replace with your actual database name
$user    = 'iching_db'; // Replace with your MySQL username
$pass    = 'iching_db'; // Replace with your MySQL password
$charset = 'utf8mb4';

// 2. Establish PDO Connection
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$question = trim($_POST['question'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $question === '') {
    die('Please enter a question before consulting the I Ching.');
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// 3. SQL Query to Fetch One Random Hexagram
$hexagram = null;
$imagePath = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $question !== '') {

    $query = "SELECT
                hexagram_number,
                chinese_pinyin,
                english_translation,
                core_meaning,
                description
              FROM iching_hexagrams
              ORDER BY RAND()
              LIMIT 1";

    $stmt = $pdo->query($query);
    $hexagram = $stmt->fetch();
}

// 4. Generate External Links & Image Path
$wikiUrl = "";
$divinationUrl = "";

if ($hexagram) {
    $hexNum = intval($hexagram['hexagram_number']);
    
    // Wikipedia and Divination.com Deep-Links
    $wikiUrl = "https://en.wikipedia.org/wiki/List_of_hexagrams_of_the_I_Ching#Hexagram_" . $hexNum;
    $divinationUrl = "https://divination.com/iching/lookup/" .  $hexNum . "-2";
    
    // Construct Path to Local Hexagram JPEG
    $imagePath = "images/hexagram-" . $hexNum . ".jpg";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I Ching Consultation in PHP/MySql</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@exampleuser/water.css@2/out/water.css">
    <link href="https://fonts.googleapis.com/css?family=Times" rel="stylesheet" />
    <style type="text/css">
        body,p,table,h1,h2,h3,h4,h5,h6,p,li,th,td,tr {
            font-family: "Times New Roman", serif; 
            line-height: 1.2;
        }
        .button {
            font-size: 48px;
            cursor: pointer;
        }
        .button-group {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
            font-size: 48px;
        }
        button, .btn-link {
            cursor: pointer;
        }
        .btn-link {
            display: inline-block;
            text-decoration: none;
            background-color: var(--button-bg);
            color: var(--button-text);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 48px;
        }
        .btn-link:hover {
            background-color: var(--button-hover);
        }
        /* Custom image style for hexagram rendering */
        .hexagram-img {
            max-width: 300px;
            height: auto;
            margin: 15px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<center>
<?php
  $currentDateTime = new DateTime('now');
  $currentDate = $currentDateTime->format('l, F j, Y H:i:s');
  echo "The time is " . $currentDate . " GMT";
?>
<p>I Ching Consultation: Reflect on your question, then cast for a hexagram.</p>
<form method="post" action="">
    <p>
        <label for="question">
            Enter your question or situation:
        </label>
    </p>

    <textarea
        id="question"
        name="question"
        rows="4"
        cols="60"
        required
        placeholder="What would you like guidance about?"
    ><?php echo htmlspecialchars($_POST['question'] ?? ''); ?></textarea>

    <br><br>

    <button type="submit">
        Cast Hexagram
    </button>
</form>

<hr>
<?php if (!empty($question)): ?>
<p>
    <b>Your Question:</b><br>
    <?php echo nl2br(htmlspecialchars($question)); ?>
</p>
<?php endif; ?>

<?php if ($hexagram): ?>
    <p><b>Hexagram Number: <?php echo htmlspecialchars($hexagram['hexagram_number']); ?></b></p>

    <!-- Render Hexagram JPEG Image -->
    <?php if (file_exists($imagePath)): ?>
        <p>
            <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                 alt="Hexagram <?php echo htmlspecialchars($hexagram['hexagram_number']); ?>" 
                 class="hexagram-img">
        </p>
    <?php else: ?>
        <p><i>[Hexagram Image placeholder: Place hexagram-<?php echo htmlspecialchars($hexNum); ?>.jpg in the images/ directory]</i></p>
    <?php endif; ?>

    <p><b><?php echo htmlspecialchars($hexagram['english_translation']); ?></b></p>
    <p><b><?php echo htmlspecialchars($hexagram['core_meaning']); ?></b></p>

    <button onclick="window.open('<?php echo htmlspecialchars($wikiUrl); ?>','_blank');">
        Click/tap here to read Full wikipedia Text
    </button><br><br>

    <button onclick="window.open('<?php echo htmlspecialchars($divinationUrl); ?>','_blank');">
        Click/tap here to read the divination.com text
    </button><br><br>

    <button onclick="window.location.reload();">Cast a New Hexagram</button><br>
<?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <p>No hexagram data found. Please ask me to check my database table.</p>
<?php endif; ?>

<p>Created with my limited knowledge of <b><i>mysql</i></b> and PHP, with the help of Google Gemini AI</p>
<p><a href="/tarot-study-guide" target=_blank>Do a Tarot Card Reading</a></p>
<p><a href="/" target=_blank>Go To the Main Page of this website</a></p>
</center>

    <p>Additional notes:</p>
    <ul>
      <li>The time is shown in the GMT timezone; when converted to your local time, it should be within a few seconds of yours.</li>
      <li>If it isn't, then your browser is displaying your last visit to this page.</li>
      <li>In that case, do indeed FIRST reflect on your question, <em>then</em> cast your hexagram; that's personally when I get the most value out of it.</li>
    </ul>
    <br>

    <h1>The Art of Inquiry: How to Consult the I Ching</h1>
    <p>Welcome to this sacred space of reflection. The I Ching, or Book of Changes, is not a tool for mere fortune-telling or a parlor trick to predict a rigid future. Instead, it acts as a cosmic mirror, reflecting the hidden dynamics, psychological currents, and shifting energies of your present situation.</p>
    <p>To receive a clear answer from the Oracle, you must approach it with a clear mind. The quality of your insight depends entirely on the quality of your inquiry.</p>

    <h2>The Power of the Preliminary Pause</h2>
    <p>Before you click a button or cast a single line, I ask you to pause.</p>
    <p>In our fast-paced digital world, our instinct is to react instantly, to demand immediate answers to chaotic thoughts. But the I Ching responds to stillness. Take three deep breaths. Ground yourself in the present moment. Clear away the immediate static of anxiety, frustration, or impatience.</p>
    <p>Approach the hexagram not as a passive spectator waiting to be told what to do, but as an active participant seeking wisdom. Treat this moment as a serious conversation with a wise, objective mentor.</p>

    <h2>Formulating Your Question</h2>
    <p>Properly considering and phrasing your question is the most crucial part of the entire process. A vague, chaotic, or double-minded question will result in a confusing, fragmented hexagram.</p>
    <p>When formulating your question, follow these essential guidelines:</p>

    <h3>1. Avoid "Yes" or "No" Questions</h3>
    <p>The Oracle speaks in nuances, cycles, and transformations. Questions like <i>"Should I quit my job?"</i> or <i>"Will I get back with my ex?"</i> force a binary choice onto a universe that operates in fluid waves.</p>
    <ul>
      <li><p><b>Instead of:</b> <i>"Will my new business succeed?"</i></p></li>
      <li><p><b>Try:</b> <i>"What energies or obstacles should I expect if I pursue this new business venture?"</i></p></li>
    </ul>

    <h3>2. Focus on Your Agency (Own Your Position)</h3>
    <p>You cannot control the actions of others, but you can control your own responses, attitude, and character. Frame your inquiry around your own path of right action.</p>
    <ul>
      <li><p><b>Instead of:</b> <i>"Why is my partner being so distant?"</i></p></li>
      <li><p><b>Try:</b> <i>"How can I best navigate the current distance in my relationship, and what is required of My position right now?"</i></p></li>
    </ul>

    <h3>3. Seek Insight into the Present</h3>
    <p>The future is not set in stone; it is born from the seeds of the present. Ask for clarity on <i>what is happening right now</i> so you can make the wisest choices moving forward.</p>
    <ul>
      <li>
        <p><b>Great starting phrases include:</b></p>
        <ul>
          <li><p><i>"What is the true nature of the situation regarding..."</i></p></li>
          <li><p><i>"What do I need to understand about my current relationship with..."</i></p></li>
          <li><p><i>"What is the wisest approach to take regarding..."</i></p></li>
        </ul>
      </li>
    </ul>

    <h2>How to Proceed with Your Reading</h2>
    <ol>
      <li><p><b>Write It Down:</b> Physically type or write your question out on a piece of paper. The act of writing forces your brain to crystallize your thoughts. If you cannot summarize your inquiry into one or two clear sentences, your mind is still too crowded. Simplify until it is pure.</p></li>
      <li><p><b>Hold the Intent:</b> As you prepare to cast the hexagram, hold the written question in your mind's eye. Visualize the people, choices, or feelings involved. Let your intent fill the space.</p></li>
      <li><p><b>Cast the Hexagram:</b> Click the button to generate your lines.</p></li>
      <li><p><b>Contemplate the Lines:</b> Look at the visual image that appears. Do not rush straight to the text description. Sit with the visual shape of your hexagram for a moment. Let it settle into your intuition.</p></li>
    </ol>

    <p>Remember, the I Ching does not strip away your free will; it illuminates it. Use the wisdom generated here to cultivate patience when the oracle advises waiting, and to find courage when it signals that the time has come to cross the great water.</p>

<p>Disclaimer: The views and experiences shared in this tool reflect personal perspectives on spirituality, self-discovery, and personal growth. They are offered for informational and inspirational purposes only and should not be considered professional, medical, psychological, legal, or financial advice. Each individual's path is unique, and readers are encouraged to exercise their own discernment and seek appropriate professional guidance when necessary.</p>

<?php 
  echo "Last Modified (on the server side): " . date('D, d M Y H:i:s', filemtime($_SERVER['SCRIPT_FILENAME'])) . " GMT";
?>

</body>
</html>
