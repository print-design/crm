<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_ADMIN], ROLE_NAMES[ROLE_MANAGER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
$form_valid = true;
$error_message = '';

$name_valid = '';
$email_valid = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'organization_create_submit')) {
    $name = filter_input(INPUT_POST, 'name');
    
    if(empty($name)) {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
    
    $person = filter_input(INPUT_POST, 'person');
    $phone = filter_input(INPUT_POST, 'phone');
    $extension = filter_input(INPUT_POST, 'extension');
    $email = filter_input(INPUT_POST, 'email');
    
    if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_valid = ISINVALID;
        $form_valid = false;
    }
    
    $manager_id = filter_input(INPUT_POST, 'manager_id');
    
    if($form_valid) {
        $name = addslashes($name);
        $person = addslashes($person);
        $phone = addslashes($phone);
        $extension = addslashes($extension);
        $email = addslashes($email);
        
        $sql = "select count(id) from customer where name='$name'";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            if($row[0] != 0) {
                $error_message = "Предприятие с таким названием уже существует";
            }
        }
        
        if(empty($error_message)) {
            $sql = "insert into customer (name, person, phone, extension, email, manager_id) values ('$name', '$person', '$phone', '$extension', '$email', $manager_id)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
            $id = $executer->insert_id;
        }
        
        if(empty($error_message)) {
            header('Location: details.php?id='.$id);
        }
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
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/organization/">Назад</a>
            <div class="row">
                <div class="col-5">
                    <h1>Новое предприятие</h1>
                    <form method="post">
                        <input type="hidden" name="manager_id" value="<?= GetUserId() ?>" />
                        <div class="form-group">
                            <label for="name">Наименование</label>
                            <input type="text" name="name" class="form-control<?=$name_valid ?>" value="<?= filter_input(INPUT_POST, 'name') ?>" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Наименование обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="person">Основное контактное лицо</label>
                            <input type="text" name="person" class="form-control" value="<?= filter_input(INPUT_POST, 'person') ?>" autocomplete="off" />
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="form-group w-75 pr-3">
                                <label for="phone">Телефон</label>
                                <input type="tel" name="phone" class="form-control" value="<?= filter_input(INPUT_POST, 'phone') ?>" autocomplete="off" />
                            </div>
                            <div class="form-group w-25">
                                <label for="extension">Расширение</label>
                                <input type="text" name="extension" class="form-control" value="<?= filter_input(INPUT_POST, 'extension') ?>" autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">E-Mail</label>
                            <input type="email" name="email" class="form-control" value="<?= filter_input(INPUT_POST, 'email') ?>" autocomplete="off" />
                            <div class="invalid-feedback">Неправильный формат E-Mail</div>
                        </div>
                        <div class="pt-4">
                            <button type="submit" class="btn btn-dark w-25" id="organization_create_submit" name="organization_create_submit">Создать</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>