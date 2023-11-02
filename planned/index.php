<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_ADMIN], ROLE_NAMES[ROLE_MANAGER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header_marketing.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <h1>Запланировано</h1>
            <?php
            include '../include/pager_top.php';
            
            $sql = "select count(ct.id) "
                    . "from contact ct "
                    . "inner join person p on ct.person_id = p.id "
                    . "inner join customer c on p.customer_id = c.id "
                    . "where c.manager_id = ". GetUserId(). " and ct.next_date is not null "
                    . "and unix_timestamp(ct.next_date) < unix_timestamp(date_add(current_date(), interval 1 day)) "
                    . "and (select count(id) from contact where person_id = p.id and unix_timestamp(date) >= unix_timestamp(current_date())) = 0 "
                    . "order by ct.next_date desc";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $pager_total_count = $row[0];
            }
            ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Контактное лицо</th>
                        <th>Дата</th>
                        <th>Предприятие</th>
                        <th>Телефон</th>
                        <th>E-Mail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select ct.id contact_id, date_format(ct.next_date, '%d.%m.%Y') next_date, p.id person_id, p.name person, "
                            . "p.phone, p.extension, p.email, c.id customer_id, c.name customer "
                            . "from contact ct "
                            . "inner join person p on ct.person_id = p.id "
                            . "inner join customer c on p.customer_id = c.id "
                            . "where c.manager_id = ". GetUserId(). " and ct.next_date is not null "
                            . "and unix_timestamp(ct.next_date) < unix_timestamp(date_add(current_date(), interval 1 day)) "
                            . "and (select count(id) from contact where person_id = p.id and unix_timestamp(date) >= unix_timestamp(current_date())) = 0 "
                            . "order by ct.next_date desc limit $pager_skip, $pager_take";
                    $fetcher = new Fetcher($sql);
                    while($fetcher->Fetch()):
                    ?>
                    <tr>
                        <td><?=$person ?></td>
                        <td><?=$next_date ?></td>
                        <td><?=$customer ?></td>
                        <td><?=$phone.(empty($extension) ? '' : ' (доб. '.$extension.')') ?></td>
                        <td><?=$email ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php
            include '../include/pager_bottom.php';
            ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>