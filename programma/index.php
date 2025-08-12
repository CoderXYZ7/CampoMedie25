<?php
$dir = '.';
$files = glob($dir . '/*-programma.md');
$selected_file = null;
$file_content = '';
$sections = [
    'Orari' => '',
    'Guida per la riflessione' => '',
    'Attività' => '',
    'Links' => ''
];
$title = '';

if (isset($_GET['file'])) {
    $selected_file = $_GET['file'];
    if (in_array($selected_file, $files)) {
        $lines = file($selected_file);
        $current_section = '';
        foreach ($lines as $line) {
            if (strpos($line, '# ') === 0) {
                $title = $line;
            } elseif (strpos($line, '## Orari') === 0) {
                $current_section = 'Orari';
            } elseif (strpos($line, '### Guida per la riflessione') === 0) {
                $current_section = 'Guida per la riflessione';
            } elseif (strpos($line, '### Attività') === 0) {
                $current_section = 'Attività';
            } elseif (strpos($line, '## Links') === 0) {
                $current_section = 'Links';
            } elseif ($current_section) {
                $sections[$current_section] .= $line;
            }
        }
    }
}

$saved = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file'])) {
    $file_to_save = $_POST['file'];
    if (in_array($file_to_save, $files)) {
        $new_content = $_POST['title'] . "\n";
        $new_content .= "## Orari\n" . $_POST['orari'];
        $new_content .= "### Guida per la riflessione\n" . $_POST['guida'];
        $new_content .= "### Attività\n" . $_POST['attivita'];
        $new_content .= "## Links\n" . $_POST['links'];

        file_put_contents($file_to_save, $new_content);
        $saved = true;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Programma Editor</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/1.9.1/showdown.min.js"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; margin: 0; background-color: #f8f9fa; }
        .main-container { display: flex; height: 100vh; }
        .sidebar {
            width: 250px;
            background-color: #fff;
            padding: 20px;
            border-right: 1px solid #dee2e6;
        }
        .editor-container { display: flex; flex: 1; }
        .editor-pane { flex: 1; padding: 20px; }
        .preview-pane { flex: 1; padding: 20px; border-left: 1px solid #dee2e6; background-color: #fff; }
        .file-list { list-style: none; padding: 0; }
        .file-list li { margin-bottom: 10px; }
        .file-list a { text-decoration: none; color: #007bff; }
        .file-list a:hover { color: #0056b3; }
        textarea {
            width: 100%;
            height: 150px;
            font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, Courier, monospace;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            padding: .375rem .75rem;
        }
        .section { margin-bottom: 20px; }
        .save-btn {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .save-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #28a745;
            color: #fff;
            padding: 15px;
            border-radius: 5px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="sidebar">
            <h2>Files</h2>
            <ul class="file-list">
                <?php foreach ($files as $file): ?>
                    <li><a href="?file=<?php echo urlencode($file); ?>"><?php echo htmlspecialchars(basename($file)); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="editor-container">
            <?php if ($selected_file): ?>
                <div class="editor-pane">
                    <h2>Editing: <?php echo htmlspecialchars(basename($selected_file)); ?></h2>
                    <form method="POST" id="editor-form">
                        <input type="hidden" name="file" value="<?php echo htmlspecialchars($selected_file); ?>">
                        <input type="hidden" name="title" value="<?php echo htmlspecialchars($title); ?>">

                        <div class="section">
                            <h3>## Orari</h3>
                            <textarea id="orari" name="orari" oninput="updatePreview()"><?php echo htmlspecialchars($sections['Orari']); ?></textarea>
                        </div>

                        <div class="section">
                            <h3>### Guida per la riflessione</h3>
                            <textarea id="guida" name="guida" oninput="updatePreview()"><?php echo htmlspecialchars($sections['Guida per la riflessione']); ?></textarea>
                        </div>

                        <div class="section">
                            <h3>### Attività</h3>
                            <textarea id="attivita" name="attivita" oninput="updatePreview()"><?php echo htmlspecialchars($sections['Attività']); ?></textarea>
                        </div>

                        <div class="section">
                            <h3>## Links</h3>
                            <textarea id="links" name="links" oninput="updatePreview()"><?php echo htmlspecialchars($sections['Links']); ?></textarea>
                        </div>

                        <br>
                        <button type="submit" class="save-btn">Save</button>
                    </form>
                </div>
                <div class="preview-pane">
                    <h2>Preview</h2>
                    <div id="preview"></div>
                </div>
            <?php else: ?>
                <div class="editor-pane">
                    <p>Select a file to start editing.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="save-notification" id="save-notification">
        File saved successfully!
    </div>

    <script>
        var converter = new showdown.Converter();
        var unsavedChanges = false;

        function updatePreview() {
            unsavedChanges = true;
            var title = document.querySelector('input[name="title"]').value;
            var orari = document.getElementById('orari').value;
            var guida = document.getElementById('guida').value;
            var attivita = document.getElementById('attivita').value;
            var links = document.getElementById('links').value;

            var markdown = title + "\n" +
                           "## Orari\n" + orari + 
                           "### Guida per la riflessione\n" + guida +
                           "### Attività\n" + attivita +
                           "## Links\n" + links;

            var html = converter.makeHtml(markdown);
            document.getElementById('preview').innerHTML = html;
        }

        // Initial preview update
        if (document.getElementById('orari')) {
            updatePreview();
            unsavedChanges = false; // Reset after initial load
        }

        document.getElementById('editor-form').addEventListener('submit', function() {
            unsavedChanges = false;
        });

        window.addEventListener('beforeunload', function (e) {
            if (unsavedChanges) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        <?php if ($saved): ?>
        const notification = document.getElementById('save-notification');
        notification.style.display = 'block';
        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
        <?php endif; ?>

    </script>
</body>
</html>