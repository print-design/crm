<?php
include 'left_bar.php';

$php_self = $_SERVER['PHP_SELF'];
$substrings = mb_split("/", $php_self);
$count = count($substrings);
$folder = '';
$file = '';

if($count > 1) {
    $folder = $substrings[$count - 2];
    $file = $substrings[$count - 1];
}

$user_class = '';

if($folder == 'user') {
    $user_class = ' disabled';
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <?php if(IsInRole(array(ROLE_NAMES[ROLE_ADMIN]))): ?>
            <li class="nav-item">
                <a class="nav-link<?=$user_class ?>" href="<?=APPLICATION ?>/user/">Сотрудники</a>
            </li>
            <?php endif; ?>
        </ul>
        <div class="ml-auto"></div>
        <?php
        include 'header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>