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

// Валидация формы
$form_valid = true;
$error_message = '';

$product_valid = '';
$number_valid = '';
$price_valid = '';

// Обработка отправки формы
if(filter_input(INPUT_POST, 'order_edit_submit') !== null) {
    $id = filter_input(INPUT_POST, 'id');
    $product = filter_input(INPUT_POST, 'product');
    
    if(empty($product)) {
        $product_valid = ISINVALID;
        $form_valid = false;
    }
    
    $number = filter_input(INPUT_POST, 'number');
    
    if(empty($number)) {
        $number_valid = ISINVALID;
        $form_valid = false;
    }
    
    $price = filter_input(INPUT_POST, 'price');
    
    if(empty($price)) {
        $price_valid = ISINVALID;
        $form_valid = false;
    }
    
    $shipment_date = filter_input(INPUT_POST, 'shipment_date');
    $contract_date = filter_input(INPUT_POST, 'contract_date');
    $bill_date = filter_input(INPUT_POST, 'bill_date');
    
    if($form_valid) {
        $product = addslashes($product);
        
        $sql = "update zakaz set product = '$product', number = $number, price = $price, shipment_date = '$shipment_date', contract_date = '$contract_date', bill_date = '$bill_date' where id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            header('Location: details.php?id='.$id);
        }
    }
}

// Получение объекта
$product = '';
$number = '';
$price = '';
$shipment_date = '';
$contract_date = '';
$bill_date = '';

$date = '';
$last_name = '';
$first_name = '';
$customer = '';

$sql = "select z.product, z.number, z.price, z.shipment_date, z.contract_date, z.bill_date, "
        . "date_format(ct.date, '%d.%m.%Y') date, u.last_name, u.first_name, c.name customer "
        . "from zakaz z "
        . "inner join contact ct on z.contact_id = ct.id "
        . "inner join person p on ct.person_id = p.id "
        . "inner join customer c on p.customer_id = c.id "
        . "inner join user u on c.manager_id = u.id "
        . "where z.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $product = htmlentities(filter_input(INPUT_POST, 'product'));
    if(empty($product)) {
        $product = htmlentities($row['product']);
    }
    
    $number = filter_input(INPUT_POST, 'number');
    if(empty($number)) {
        $number = $row['number'];
    }
    
    $price = filter_input(INPUT_POST, 'price');
    if(empty($price)) {
        $price = $row['price'];
    }
    
    $shipment_date = filter_input(INPUT_POST, 'shipment_date');
    if(empty($shipment_date)) {
        $shipment_date = $row['shipment_date'];
    }
    
    $contract_date = filter_input(INPUT_POST, 'contract_date');
    if(empty($contract_date)) {
        $contract_date = $row['contract_date'];
    }
    
    $bill_date = filter_input(INPUT_POST, 'bill_date');
    if(empty($bill_date)) {
        $bill_date = $row['bill_date'];
    }
    
    $date = $row['date'];
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    $customer = $row['customer'];
}
?>
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
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/order/details.php?id=<?=$id ?>">Назад</a>
            <h1>Редактирование заказа</h1>
            <div class="row">
                <div class="col-12 col-md-6">
                    <table class="table table-bordered">
                        <thead>
                            <tr><th>Дата контакта</th><td><?=$date ?></td></tr>
                            <tr><th>Менеджер</th><td><?=$last_name.' '.$first_name ?></td></tr>
                            <tr><th>Предприятие</th><td><?=$customer ?></td></tr>
                        </thead>
                    </table>
                    <form method="post">
                        <input type="hidden" name="id" value="<?=$id ?>" />
                        <div class="form-group">
                            <label for="product">Товар</label>
                            <textarea rows="5" name="product" class="form-control<?=$product_valid ?>"><?=$product ?></textarea>
                            <div class="invalid-feedback">Товар обязательно</div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="number">Количество</label>
                                    <input type="text" name="number" class="form-control int-only<?=$number_valid ?>" value="<?= empty($number) ? '' : $number ?>" autocomplete="off" required="required" />
                                    <div class="invalid-feedback">Количество обязательно</div>
                                </div>
                            </div>
                            <div class="col-4"></div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="price">Цена (1 шт, руб.)</label>
                                    <input type="text" name="price" class="form-control int-only<?=$price_valid ?>" value="<?= empty($price) ? '' : $price ?>" autocomplete="off" required="required" />
                                    <div class="invalid-feedback">Цена обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="shipment_date">Дата отгрузки</label>
                                    <input type="date" name="shipment_date" class="form-control" value="<?=$shipment_date ?>" />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="contract_date">Дата заключения договора</label>
                                    <input type="date" name="contract_date" class="form-control" value="<?=$contract_date ?>" />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="bill_date">Дата выставления счёта</label>
                                    <input type="date" name="bill_date" class="form-control" value="<?=$bill_date ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="pt-4">
                            <button type="submit" class="btn btn-dark w-25" id="order_edit_submit" name="order_edit_submit">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>