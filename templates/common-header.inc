<?php
if (isset($language)) {
    header('Content-type: text/html; charset=' . NLS::getCharset());
    header('Vary: Accept-Language');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<!-- Horde Project: http://horde.org/ | Trean: http://horde.org/trean/ -->
<!--          Trean: Copyright 2000-2004, The Horde Project.           -->
<?php echo !empty($language) ? '<html lang="' . strtr($language, '_', '-') . '">' : '<html>' ?>
<head>
<?php

$page_title = $registry->get('name');
if (!empty($title)) $page_title .= ' :: ' . $title;
if (!empty($refresh_time) && ($refresh_time > 0) && !empty($refresh_url)) {
    echo "<meta http-equiv=\"refresh\" content=\"$refresh_time;url=$refresh_url\">\n";
}

Horde::includeScriptFiles();
?>
<title><?php echo $page_title ?></title>
<?php echo Horde::stylesheetLink('horde') ?>
</head>

<body<?php if (Util::nonInputVar('bodyClass')) echo ' class="' . $bodyClass . '"' ?>>
