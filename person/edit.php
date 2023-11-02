<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_ADMIN], ROLE_NAMES[ROLE_MANAGER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если нет параметра id, переходим к списку предприятий
$id = filter_input(INPUT_GET, 'id');

if(empty($id)) {
    header('Location: '.APPLICATION.'/customer/');
}

// Валидация формы
$form_valid = true;
$error_message = '';

$name_valid = '';
$email_valid = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'person_edit_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $customer_id = filter_input(INPUT_POST, 'customer_id');
    $name = filter_input(INPUT_POST, 'name');
    
    if(empty($name)) {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
    
    $position = filter_input(INPUT_POST, 'position');
    $phone = filter_input(INPUT_POST, 'phone');
    $extension = filter_input(INPUT_POST, 'extension');
    $email = filter_input(INPUT_POST, 'email');
    
    if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $name = addslashes($name);
        $position = addslashes($position);
        $phone = addslashes($phone);
        $extension = addslashes($extension);
        $email = addslashes($email);
        
        $sql = "update person set name = '$name', position = '$position', phone = '$phone', extension = '$extension', email = '$email' where id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            header('Location: '.APPLICATION.'/customer/details.php?id='.$customer_id);
        }
    }
}

// Получение объекта
$customer_id = '';
$name = '';
$position = '';
$phone = '';
$extension = '';
$email = '';

$sql = "select customer_id, name, position, phone, extension, email from person where id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $customer_id = $row['customer_id'];
    
    $name = htmlentities(filter_input(INPUT_POST, 'name'));
    if(empty($name)) {
        $name = htmlentities($row['name']);
    }
    
    $position = htmlentities(filter_input(INPUT_POST, 'position'));
    if(empty($position)) {
        $position = htmlentities($row['position']);
    }
    
    $phone = htmlentities(filter_input(INPUT_POST, 'phone'));
    if(empty($phone)) {
        $phone = htmlentities($row['phone']);
    }
    
    $extension = htmlentities(filter_input(INPUT_POST, 'extension'));
    if(empty($extension)) {
        $extension = htmlentities($row['extension']);
    }
    
    $email = htmlentities(filter_input(INPUT_POST, 'email'));
    if(empty($email)) {
        $email = htmlentities($row['email']);
    }
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
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/customer/details.php?id=<?=$customer_id ?>">Назад</a>
            <div class="row">
                <div class="col-12 col-md-6">
                    <h1>Редактирование контактного лица</h1>
                    <form method="post">
                        <input type="hidden" name="id" value="<?=$id ?>" />
                        <input type="hidden" name="customer_id" value="<?=$customer_id ?>" />
                        <div class="form-group">
                            <label for="name">ФИО</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlentities($name) ?>" autocomplete="off" required="required" />
                            <div class="invalid-feedback">ФИО обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="position">Должность (роль)</label>
                            <input type="text" name="position" class="form-control" value="<?= htmlentities($position) ?>" autocomplete="off" />
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="phone">Телефон</label>
                                    <input type="text" id="phone" name="phone" value="<?= htmlentities($phone) ?>" class="form-control" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="extension">Добавочный</label>
                                    <input type="text" name="extension" value="<?= htmlentities($extension) ?>" class="form-control" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="email">E-Mail</label>
                                    <input type="text" name="email" value="<?= htmlentities($email) ?>" class="form-control" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-dark w-25" id="person_edit_submit" name="person_edit_submit">Сохранить</button>
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