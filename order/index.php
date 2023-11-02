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
            <h1>Заказы</h1>
            <?php
            include '../include/pager_top.php';
            
            $sql = "select count(z.id) "
                    . "from zakaz z "
                    . "inner join contact ct on z.contact_id = ct.id "
                    . "inner join person p on ct.person_id = p.id "
                    . "inner join customer c on p.customer_id = c.id "
                    . "inner join user u on c.manager_id = u.id";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $pager_total_count = $row[0];
            }
            ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Дата контакта</th>
                        <th>Менеджер</th>
                        <th>Предприятие</th>
                        <th>Товар</th>
                        <th>Количество</th>
                        <th>Цена (1 шт.)</th>
                        <th>Цена (всего)</th>
                        <th>Оплачено</th>
                        <th>Дата отгрузки</th>
                        <th>Дата закл. дог.</th>
                        <th>Дата выст. сч.</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select z.id, date_format(ct.date, '%d.%m.%Y') date, u.last_name, u.first_name, c.name customer, "
                            . "z.product, z.number, z.price price_one, z.number * z.price price_total, "
                            . "(select sum(money) from payment where zakaz_id = z.id) payed, "
                            . "date_format(z.shipment_date, '%d.%m.%Y') shipment_date, "
                            . "date_format(z.contract_date, '%d.%m.%Y') contract_date, "
                            . "date_format(z.bill_date, '%d.%m.%Y') bill_date "
                            . "from zakaz z "
                            . "inner join contact ct on z.contact_id = ct.id "
                            . "inner join person p on ct.person_id = p.id "
                            . "inner join customer c on p.customer_id = c.id "
                            . "inner join user u on c.manager_id = u.id "
                            . "order by ct.date desc limit $pager_skip, $pager_take";
                    $fetcher = new Fetcher($sql);
                    while($row = $fetcher->Fetch()):
                    ?>
                    <tr>
                        <td><?=$row['date'] ?></td>
                        <td><?=$row['last_name'].' '.$row['first_name'] ?></td>
                        <td><?=$row['customer'] ?></td>
                        <td><?=$row['product'] ?></td>
                        <td><?=$row['number'] ?></td>
                        <td><?=$row['price_one'] ?> руб.</td>
                        <td><?=$row['price_total'] ?> руб.</td>
                        <td><?= intval($row['price_total']) > intval($row['payed']) ? '' : '&#x2713;' ?></td>
                        <td><?=$row['shipment_date'] ?></td>
                        <td><?=$row['contract_date'] ?></td>
                        <td><?=$row['bill_date'] ?></td>
                        <td><a href="details.php?id=<?=$row['id'] ?>"><img src="../images/icons/vertical-dots.svg" /></a></td>
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