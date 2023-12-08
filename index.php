<!DOCTYPE html>
<html>
<head>
    <title>Snippets</title>
    <script src="prism.js"></script>
    <link href="prism.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.4.slim.min.js" integrity="sha256-a2yjHM4jnF9f54xUQakjZGaqYs/V1CYvWpoqZzC2/Bw=" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: Arial;
            font-size: 16px;
        }
        h2,h3 {
            display:inline-flex;
            gap:5px;
            margin-bottom:0;
        }
        h3 {
            margin-left: 20px;
        }
        h2::after,
        h3::after {
           content: '+';
        }
        h2.show::after,
        h3.show::after {
           content: '-';
        }
        h2 + *,
        h3 + * {
            display:none;
        }
        h2.show + *,
        h3.show + * {
            display: block;
        }
    </style>
</head>
<body>
    <h1>Snippets</h1>
    <?= by_type() ?>
    <hr />
    <script>
    $('h2,h3').click(toggleNext);
    function toggleNext() { $(this).toggleClass('show'); }
    </script>
</body>
</html>
<?php
function by_type(){
    $files = glob('snippets/*');
    $types = [];
    foreach ($files as $file) $types[file_extension($file)][] = $file;
    ob_start();
    foreach ($types as $type => $files) {
        echo div( 
            h2($type) .
            snippets($files));
    }
    return ob_get_clean();
}
function snippets($files) {
    ob_start();
    foreach ($files as $file) {
        echo div(
            h3(basename($file)) .
            snippet($file));
    }
    return div(ob_get_clean());
}
function snippet($file) {
    ob_start();
    ?><pre><code class="lang-<?= file_extension($file) ?>"><?=
        htmlspecialchars(file_get_contents($file))
    ?></code></pre>
    <?php
    return ob_get_clean();
}
function file_extension($path) {
    return pathinfo($path)['extension'];
}
// function heading($html) {
//     return '<div class="heading">$html</div>';
// }
function div($html) { return "<div>$html</div>"; }
function b($html) { return "<b>$html</b>"; }
function h2($html) { return "<h2>$html</h2>"; }
function h3($html) { return "<h3>$html</h3>"; }
