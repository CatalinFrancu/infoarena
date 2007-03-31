<?php
header("Content-Type: application/xml\n\n");
$optional = array();
// textInput, image, category and cloud don't work properly.. do not use them in your feed
$optional['channel'] = array('language', 'copyright', 'managingEditor',
                             'webMaster', 'pubDate', 'lastBuildDate',
                             'category', 'generator', 'docs', 'cloud', 'ttl',
                             'image', 'rating', 'textInput', 'skipHours',
                             'skipDays');

// category, enclosure, source don't work properly.. do not use them in your feed
$optional['item'] = array('author', 'pubDate', 'category', 'comments',
                          'enclosure', 'guid', 'source');

echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
echo '<rss version="2.0">'."\n";
echo '<channel>'."\n";
echo '<title>'.xmlesc(getattr($view['channel'], 'title')).'</title>'."\n";
echo '<link>'.xmlesc(getattr($view['channel'], 'link')).'</link>'."\n";
echo '<description>'.xmlesc(getattr($view['channel'], 'description')).'</description>'."\n";

foreach ($optional['channel'] as $hash_key => $hash_value) {
    if (getattr($view['channel'], $hash_value)) {
        echo '<'.$hash_value.'>';
        echo xmlesc($view['channel'][$hash_value]);
        echo '</'.$hash_value.">\n";
    }
}

foreach ($view['item'] as $v) {
    echo '<item>'."\n";
    echo '<title>'.xmlesc(getattr($v, 'title')).'</title>'."\n";
    echo '<link>'.xmlesc(getattr($v, 'link')).'</link>'."\n";
    foreach ($optional['item'] as $hash_key => $hash_value) {
        if (getattr($v, $hash_value)) {
            echo '<'.$hash_value.'>';
            echo xmlesc($v[$hash_value]);
            echo '</'.$hash_value.">\n";
        }
    }
    echo '<description>'.xmlesc(getattr($v, 'description')).'</description>'."\n";
    echo '</item>'."\n";
}

echo '</channel>'."\n";
echo '</rss>'."\n";
?>