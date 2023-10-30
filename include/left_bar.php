<?php
$php_self = $_SERVER['PHP_SELF'];
$substrings = mb_split("/", $php_self);
$count = count($substrings);
$folder = '';
$file = '';

if($count > 1) {
    $folder = $substrings[$count - 2];
    $file = $substrings[$count - 1];
}

$marketing_class = '';
$admin_class = '';

if($folder == "marketing") {
    $marketing_class = " active";
}
elseif($folder == "user" || $folder == "supplier" || $folder == 'admin') {
    $admin_class = " active";
}

?>
<div id="left_bar">
    <a href="<?=APPLICATION ?>/" class="left_bar_item logo" title="На главную" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/logo.svg" /></a>
    <?php
    // Маркетинг
    if(IsInRole(array(ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_MANAGER_SENIOR]))):
    ?>
    <a href="<?=APPLICATION ?>/marketing/" class="left_bar_item<?=$marketing_class ?>" title="Маркетинг" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/nav_grafik.svg" /></a>
    <?php
    endif;
    // Админка
    if(IsInRole(ROLE_NAMES[ROLE_ADMIN])):
    ?>
    <a href="<?=APPLICATION ?>/user/" class="left_bar_item<?=$admin_class ?>" title="Админка" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/nav_admin.svg" /></a>
    <?php
    endif;
    ?>
</div>