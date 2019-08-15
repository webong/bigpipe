<?php

$time = microtime();

$time = explode(' ', $time);

$time = $time[1] + $time[0];

$start = $time;
?>
<?php
// header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
// header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
// header("Pragma: no-cache");
// header("Content-Type: text/html");

require_once('Browser.php');
require_once('h_bigpipe.php');
require_once('h_pagelet.php');

$use_padding = true;

if (isset($_REQUEST['disable_padding'])) {
    $use_padding = false;
}

function test_delayed_rendering($msg)
{
    global $use_padding;
    // Simulate some long operation
    usleep(100000); // 100 ms
    $padding = '';
    if ($use_padding) {
        for ($i = 0; $i < 8192; $i++) {
            $padding .= ' ';
        }
    }
    return "$msg <!-- $padding -->\n";
}

function test_simple_replace($msg)
{
    global $use_padding;
    $padding = '';
    if ($use_padding) {
        for ($i = 0; $i < 8192; $i++) {
            $padding .= ' ';
        }
    }
    return "$msg <!-- $padding --><br>\n";
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>BigPipe example</title>
    <script type="text/javascript" src="prototype.js"></script>
    <script type="text/javascript" src="prototypepatch.js"></script>
</head>

<body>
    <h1 id="header">BigPipe test.</h1>
    <?php
    if ($use_padding) {
        ?>
    <p>This version uses padding to fill out browser caches so that the bigpipe delayed rendering effect can be seen easily. This causes problems with firebug, because the page content is big. Use <a href="?disable_padding=1">this</a> link to disable the padding so you can use firebug more easily.
        <?php
        } else {
            ?>
        <p>The padding has been disabled. The page load takes much longer because there's still some sleep() delays inside.</p>
        <?php
        }
        ?>
        <!-- simulate that the page is much bigger than it is. Browsers have internal buffering which hides how bigpipe actual works.
					 This allows us to simulate real world effect with a big page. -->
        <!-- <?php if ($use_padding) {
                    for ($i = 0; $i < 128000; $i++) {
                        echo ' ';
                    }
                } ?> -->

        <h2>Simple content replace</h2>
        <?php
        echo test_simple_replace('Ok');
        ?>

        <h2>Test delayed rendering (50 times)</h2>
        <?php
        for ($i = 0; $i < 50; $i++) {
            $pagelet = test_delayed_rendering($i);
            echo $pagelet;
        }
        $pagelet = test_simple_replace('Ok');
        echo $pagelet;
        ?>
        <h2>Content replace with inline javascript</h2>
        <div id="javascript_inline_test">Be patient, this will be completed in the end after delayed rendering</div>
        <script>
            $('javascript_inline_test').innerHTML = 'Ok';
        </script>

        <h2>Content replace with external javascript file</h2>
        <div id="external_js">Be patient, this will be completed in the end after delayed rendering</div>
        <script src="test.js"></script>

        <h2>Content replace with external javascript and inline javascript</h2>
        <div id="external_js2">be patient, this will be completed in the end after delayed rendering test</div>
        <script src="test2.js"></script>
        <script>
            test2('external_js2', 'Ok');
        </script>

        <script>
            $('header').innerHTML = 'All done';
        </script>
    </p>
</body>
<?php
$time = microtime();

$time = explode(' ', $time);

$time = $time[1] + $time[0];

$finish = $time;

$total_time = round(($finish - $start), 4);

echo 'Page loaded time : ' . $total_time . ' seconds.';

?>