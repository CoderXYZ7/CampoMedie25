<?php
session_start();

// Get the requested page
$page = isset($_GET['page']) ? $_GET['page'] : 'main';

// Basic security validation
if (!preg_match('/^[a-zA-Z0-9_]+$/', $page)) {
    die('Invalid page requested.');
}

// Handle history for the "Back" button
if ($page !== 'main' && (!isset($_SESSION['history']) || end($_SESSION['history']) !== $page)) {
    if (!isset($_SESSION['history'])) {
        $_SESSION['history'] = ['main'];
    }
    if (is_numeric($page) || $page === 'main') {
        $_SESSION['history'][] = $page;
    } else { // Addendum
        if (!in_array($page, $_SESSION['history'])) {
            $_SESSION['history'][] = $page;
        }
    }
}

// Handle the "Back" action
if (isset($_GET['action']) && $_GET['action'] === 'back') {
    // Pop the current page
    if (count($_SESSION['history']) > 1) {
        array_pop($_SESSION['history']);
    }
    $previous_page = end($_SESSION['history']);
    header('Location: ?page=' . $previous_page);
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

$is_addendum = !is_numeric($page) && $page !== 'main';

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
        <?php if ($is_addendum) : ?>
            <a href="?action=back" class="home-back-btn">Back</a>
        <?php else : ?>
            <a href="?page=main" class="home-back-btn">Home</a>
        <?php endif; ?>

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
