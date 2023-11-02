<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_ADMIN], ROLE_NAMES[ROLE_MANAGER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если нет параметра id
// переходим к списку предприятий
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/customer/');
}

// Получение объекта
$date = '';
$last_name = '';
$first_name = '';
$customer = '';
$product = '';
$number = '';
$price_one = '';
$price_total = '';
$shipment_date = '';
$contract_date = '';
$bill_date = '';

$sql = "select z.product, z.number, z.price price_one, z.number * z.price price_total, "
        . "date_format(z.shipment_date, '%d.%m.%Y') shipment_date, date_format(z.contract_date, '%d.%m.%Y') contract_date, "
        . "date_format(z.bill_date, '%d.%m.%Y') bill_date, date_format(ct.date, '%d.%m.%Y') date, c.name customer, u.last_name, u.first_name "
        . "from zakaz z "
        . "inner join contact ct on z.contact_id = ct.id "
        . "inner join person p on ct.person_id = p.id "
        . "inner join customer c on p.customer_id = c.id "
        . "inner join user u on c.manager_id = u.id "
        . "where z.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $date = $row['date'];
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    $customer = $row['customer'];
    $product = $row['product'];
    $number = $row['number'];
    $price_one = $row['price_one'];
    $price_total = $row['price_total'];
    $shipment_date = $row['shipment_date'];
    $contract_date = $row['contract_date'];
    $bill_date = $row['bill_date'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            table.table tr th {
                font-weight: bold;
            }
        </style>
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
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/order/">К списку</a>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="d-flex justify-content-between">
                        <div><h1>Заказ</h1></div>
                        <div><a href="edit.php?id=<?=$id ?>" class="btn btn-outline-dark"><i class="fas fa-edit"></i>&nbsp;Редактировать</a></div>
                    </div>
                    <table class="table table-bordered">
                        <tbody>
                            <tr><th>Дата контакта</th><td><?=$date ?></td></tr>
                            <tr><th>Менеджер</th><td><?=$last_name.' '.$first_name ?></td></tr>
                            <tr><th>Предприятие</th><td><?=$customer ?></td></tr>
                            <tr><th>Товар</th><td><?=$product ?></td></tr>
                            <tr><th>Количество</th><td><?=$number ?></td></tr>
                            <tr><th>Цена (1 шт.)</th><td><?=$price_one ?> руб.</td></tr>
                            <tr><th>Цена (всего)</th><td><?=$price_total ?> руб.</td></tr>
                            <tr><th>Дата отгрузки</th><td><?=$shipment_date ?></td></tr>
                            <tr><th>Дата заключения договора</th><td><?=$contract_date ?></td></tr>
                            <tr><th>Дата выставления счёта</th><td><?=$bill_date ?></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-12 col-md-6">
                    <div class="d-flex justify-content-between">
                        <div><h2>Оплата заказа</h2></div>
                        <?php if(IsInRole(ROLE_NAMES[ROLE_ACCOUNTANT])): ?>
                        <div><a href="<?=APPLICATION ?>/payment/create.php?id=<?=$id ?>"><i class="fas fa-plus"></i>&nbsp;</a>Добавить оплату</div>
                        <?php endif; ?>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Сумма оплаты</th>
                                <?php if(IsInRole(ROLE_NAMES[ROLE_ACCOUNTANT])): ?>
                                <th></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "select id, date_format(date, '%d.%m.%Y') date, money from payment where zakaz_id = $id";
                            $fetcher = new Fetcher($sql);
                            while ($row = $fetcher->Fetch()):
                            ?>
                            <tr>
                                <td><?=$row['date'] ?></td>
                                <td><?=$row['money'] ?></td>
                                <?php if(IsInRole(ROLE_NAMES[ROLE_ACCOUNTANT])): ?>
                                <td><a href="<?=APPLICATION ?>/payment/edit.php?id=<?=$row['id'] ?>"><i class="fas fa-edit"></i></a></td>
                                <?php endif; ?>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>