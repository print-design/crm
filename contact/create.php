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

$result_id_valid = '';

// Обработка отправки формы
if(filter_input(INPUT_POST, 'contact_create_submit') !== null) {
    $manager_id = filter_input(INPUT_POST, 'manager_id');
    $person_id = filter_input(INPUT_POST, 'person_id');
    $result_id = filter_input(INPUT_POST, 'result_id');
    $next_date = filter_input(INPUT_POST, 'next_date');
    $comment = filter_input(INPUT_POST, 'comment');
    
    if(empty($result_id)) {
        $result_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $comment = addslashes($comment);
        
        $sql = "insert into contact (manager_id, person_id, result_id, next_date, comment) values ($manager_id, $person_id, $result_id, '$next_date', '$comment')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $contact_id = $executer->insert_id;
        
        if(empty($error_message)) {
            if($result_id == RESULT_ORDER) {
                $sql = "insert into zakaz (contact_id) values ($contact_id)";
                $executer = new Executer($sql);
                $error_message = $executer->error;
                $order_id = $executer->insert_id;
                if(!empty($order_id)) {
                    header('Location: '.APPLICATION.'/order/edit.php?id='.$order_id);
                    exit();
                }
            }
            
            $sql = "select p.customer_id from person p inner join contact c on c.person_id = p.id where c.id = $contact_id";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $customer_id = $row['customer_id'];
                if(!empty($customer_id)) {
                    header('Location: '.APPLICATION.'/customer/details.php?id='.$customer_id);
                }
            }
        }
    }
}

// Получение объекта
$customer_id = 0;
$customer = '';
$person_id = 0;
$person = '';
$position = '';
$phone = '';
$email = '';

$sql = "select c.id customer_id, c.name customer, p.id person_id, p.name person, p.position, p.phone, p.extension, p.email "
        . "from person p "
        . "inner join customer c on p.customer_id = c.id "
        . "where p.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $customer_id = $row['customer_id'];
    $customer = $row['customer'];
    $person_id = $row['person_id'];
    $person = $row['person'];
    $position = $row['position'];
    $phone = $row['phone'];
    $email = $row['email'];
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
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/customer/details.php?id=<?=$customer_id ?>">Назад</a>
            <div class="row">
                <div class="col-12 col-md-6">
                    <h1>Новый контакт</h1>
                    <table class="table table-bordered">
                        <tbody>
                            <tr><th>Предприятие</th><td><?=$customer ?></td></tr>
                            <tr><th>Контактное лицо</th><td><?=$person ?></td></tr>
                            <tr><th>Должность (роль)</th><td><?=$position ?></td></tr>
                            <tr><th>Телефон</th><td><?=$phone.(empty($extension) ? '' : ' (доп. '.$extension.')') ?></td></tr>
                            <tr><th>E-Mail</th><td><?=$email ?></td></tr>
                        </tbody>
                    </table>
                    <form method="post">
                        <input type="hidden" name="manager_id" value="<?= GetUserId() ?>" />
                        <input type="hidden" name="person_id" value="<?=$person_id ?>" />
                        <div class="d-flex justify-content-between">
                            <div class="form-group">
                                <label for="result_id">Результат контакта</label>
                                <select name="result_id" class="form-control<?=$result_id_valid ?>" required="required">
                                    <option value="">...</option>
                                    <?php
                                    foreach (RESULTS as $item):
                                        $selected = '';
                                        if(filter_input(INPUT_POST, 'result_id') == $item) {
                                            $selected = " selected='selected'";
                                        }
                                    ?>
                                    <option value="<?=$item ?>"<?=$selected ?>><?=RESULT_NAMES[$item] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Результат контакта обязательно</div>
                            </div>
                            <div class="form-group">
                                <label for="next_date">Дата следующего контакта</label>
                                <input type="date" name="next_date" class="form-control" value="<?= filter_input(INPUT_POST, 'next_date') ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comment">Комментарий</label>
                            <textarea name="comment" class="form-control" rows="5"><?= htmlentities(filter_input(INPUT_POST, 'comment')) ?></textarea>
                        </div>
                        <div class="pt-4">
                            <button type="submit" class="btn btn-dark w-25" id="customer_create_submit" name="contact_create_submit">Создать</button>
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