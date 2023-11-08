<?php

require_once(__DIR__ . '/../../conf/conf.php');
$embedding = $FEATURES["MEMORY_EMBEDDING"]["TEXT2VEC_PROVIDER"];

//Run the Compact Command
$commandcompact = 'php /var/www/html/HerikaServer/debug/util_memory_subsystem.php compact';
$commandcompact = shell_exec($commandcompact);



// Run sync command
$commandsync = 'php /var/www/html/HerikaServer/debug/util_memory_subsystem.php sync';
$outputsync = shell_exec($commandsync);

"<h1>Compact Memories</h1>";
echo"<pre>$commandcompact</pre>";

// Output sync command
if ($embedding == 'local') {
    echo "<h1>Memory Sync for Local Text2Vec</h1>";
} else {
    echo "<h1>Memory Sync for OpenAI's ADA2</h1>";
}

echo "<ul>";
$lines = explode("\n", $outputsync);
foreach ($lines as $line) {
    $line = trim($line);
    if (!empty($line)) {
        echo "<li>$line</li>";
    }
}
echo "</ul>";
?>
