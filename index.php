<?php
session_start();

// Get the requested page
$page = isset($_GET['page']) ? $_GET['page'] : '1';

// Basic security validation
if (!preg_match('/^[a-zA-Z0-9_]+$/', $page)) {
    die('Invalid page requested.');
}

// Handle history for the "Back" button
if ($page !== '1' && (!isset($_SESSION['history']) || end($_SESSION['history']) !== $page)) {
    if (!isset($_SESSION['history'])) {
        $_SESSION['history'] = [];
    }
    // Don't push addendums to history if they are already the last element
    if (!is_numeric($page)) {
        // If the last page was numeric, save it for the "back" button
        if (isset($_SESSION['history'][count($_SESSION['history']) - 1]) && is_numeric($_SESSION['history'][count($_SESSION['history']) - 1])) {
            //
        }
    }
    $_SESSION['history'][] = $page;
}


// Handle the "Back" action
if (isset($_GET['action']) && $_GET['action'] === 'back') {
    // Go back to the last numeric page
    while (count($_SESSION['history']) > 1) {
        $last_page = array_pop($_SESSION['history']);
        if (is_numeric(end($_SESSION['history']))) {
            $page = end($_SESSION['history']);
            // array_pop($_SESSION['history']); // Pop the current page
            break;
        }
    }
    if (count($_SESSION['history']) <= 1) {
        $page = '1';
        $_SESSION['history'] = [];
    }

    header('Location: ?page=' . $page);
    exit;
}


// Get the list of basic pages
$files = glob('content/*.md');
$numeric_pages = [];
foreach ($files as $file) {
    $basename = basename($file, '.md');
    if (is_numeric($basename)) {
        $numeric_pages[] = $basename;
    }
}
sort($numeric_pages, SORT_NUMERIC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Reader</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <div class="top-bar">
        <?php foreach ($numeric_pages as $p) : ?>
            <a href="?page=<?php echo $p; ?>" class="<?php echo ($p == $page) ? 'active' : ''; ?>"><?php echo $p; ?></a>
        <?php endforeach; ?>
    </div>

    <div class="main-content">
        <?php
        // Include the parser to render the page
        include 'parser.php';

        $page_content = load_page($page);
        echo parse_content($page_content, $page);
        ?>
    </div>

    <script src="assets/script.js"></script>
</body>
</html>
