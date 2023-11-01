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

$my_class = '';
$all_class = '';
$contact_class = '';
$planned_class = '';
$order_class = '';

if($folder == 'customer' && $file == 'all.php') {
    $all_class = ' disabled';
}
elseif($folder == 'customer') {
    $my_class = ' disabled';
}
elseif($folder == 'contact') {
    $contact_class = ' disabled';
}
elseif($folder == 'planned') {
    $planned_class = ' disabled';
}
elseif($folder == 'order') {
    $order_class = ' disabled';
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link<?=$my_class ?>" href="<?=APPLICATION ?>/customer/">Мои предприятия</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$all_class ?>" href="<?=APPLICATION ?>/customer/all.php">Все предприятия</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$contact_class ?>" href="<?=APPLICATION ?>/contact/">Первич. действ. контакты</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$planned_class ?>" href="<?=APPLICATION ?>/planned/">Запланировано</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$order_class ?>" href="<?=APPLICATION ?>/order/">Заказы</a>
            </li>
        </ul>
        <div class="ml-auto"></div>
        <?php
        include 'header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>