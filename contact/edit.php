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
if(filter_input(INPUT_POST, 'contact_edit_submit') !== null) {
    $id = filter_input(INPUT_POST, 'id');
    $result_id = filter_input(INPUT_POST, 'result_id');
    $next_date = filter_input(INPUT_POST, 'next_date');
    $comment = filter_input(INPUT_POST, 'comment');
    
    if(empty($result_id)) {
        $result_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $comment = addslashes($comment);
        
        $sql = "update contact set result_id = $result_id, next_date = '$next_date', comment = '$comment' where id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            if($result_id == RESULT_ORDER) {
                $sql = "select count(id) from zakaz where contact_id = $id";
                $fetcher = new Fetcher($sql);
                if($row = $fetcher->Fetch()) { echo $row[0];
                    if($row[0] == 0) {
                        $sql = "insert into zakaz (contact_id) values ($id)";
                        $executer = new Executer($sql);
                        $error_message = $executer->error;
                        $order_id = $executer->insert_id;
                        if(!empty($order_id)) {
                            header('Location: '.APPLICATION.'/order/edit.php?id='.$order_id);
                            exit();
                        }
                    }
                }
            }
            
            $sql = "select p.customer_id from person p inner join contact c on c.person_id = p.id where c.id = $id";
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

$sql = "select c.id customer_id, c.name customer, p.id person_id, p.name person, p.position, p.phone, p.extension, p.email, "
        . "ct.result_id, ct.next_date, ct.comment "
        . "from contact ct "
        . "inner join person p on ct.person_id = p.id "
        . "inner join customer c on p.customer_id = c.id "
        . "where ct.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $customer_id = $row['customer_id'];
    $customer = $row['customer'];
    $person_id = $row['person_id'];
    $person = $row['person'];
    $position = $row['position'];
    $phone = $row['phone'];
    $email = $row['email'];
    
    $result_id = filter_input(INPUT_POST, 'result_id');
    if(empty($result_id)) {
        $result_id = $row['result_id'];
    }
    
    $next_date = filter_input(INPUT_POST, 'next_date');
    if(empty($next_date)) {
        $next_date = $row['next_date'];
    }
    
    $comment = filter_input(INPUT_POST, 'comment');
    if(empty($comment)) {
        $comment = $row['comment'];
    }
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
                    <h1>Редактирование контакта</h1>
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
                        <input type="hidden" name="id" value="<?= $id ?>" />
                        <div class="d-flex justify-content-between">
                            <div class="form-group">
                                <label for="result_id">Результат контакта</label>
                                <select name="result_id" class="form-control<?=$result_id_valid ?>" required="required">
                                    <option value="">...</option>
                                    <?php
                                    foreach (RESULTS as $item):
                                        $selected = '';
                                        if($result_id == $item) {
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
                                <input type="date" name="next_date" class="form-control" value="<?= $next_date ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comment">Комментарий</label>
                            <textarea name="comment" class="form-control" rows="5"><?= $comment ?></textarea>
                        </div>
                        <div class="pt-4">
                            <button type="submit" class="btn btn-dark w-25" id="contact_edit_submit" name="contact_edit_submit">Сохранить</button>
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