<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_ADMIN], ROLE_NAMES[ROLE_MANAGER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если нет параметра id, переходим к списку
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
if(null !== filter_input(INPUT_POST, 'customer_edit_submit')) {
    $id = filter_input(INPUT_POST, 'id');
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
    
    if($form_valid) {
        $name = addslashes($name);
        $person = addslashes($person);
        $phone = addslashes($phone);
        $extension = addslashes($extension);
        $email = addslashes($email);
        
        $sql = "select count(id) from customer where name='$name' and id <> $id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            if($row[0] != 0) {
                $error_message = "Предприятие с таким названием уже существует";
            }
        }
        
        if(empty($error_message)) {
            $sql = "update customer set name = '$name', person = '$person', phone = '$phone', extension = '$extension', email = '$email' where id = $id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        if(empty($error_message)) {
            header('Location: details.php?id='.$id);
        }
    }
}

// Получение объекта
$sql = "select name, person, phone, extension, email from customer where id = $id";
$fetcher = new Fetcher($sql);
$row = $fetcher->Fetch();

$name = htmlentities(filter_input(INPUT_POST, 'name'));
if(empty($name)) {
    $name = htmlentities($row['name']);
}

$person = htmlentities(filter_input(INPUT_POST, 'person'));
if(empty($person)) {
    $person = htmlentities($row['person']);
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
            <a class="btn btn-outline-dark backlink" href="details.php?id=<?=$id ?>">Назад</a>
            <div class="row">
                <div class="col-5">
                    <h1>Редактирование предприятия</h1>
                    <form method="post">
                        <input type="hidden" name="id" value="<?= $id ?>" />
                        <div class="form-group">
                            <label for="name">Наименование</label>
                            <input type="text" name="name" class="form-control<?=$name_valid ?>" value="<?= $name ?>" required="required" autocomplete="off" />
                            <div class="invalid-feedback">Наименование обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="person">Основное контактное лицо</label>
                            <input type="text" name="person" class="form-control" value="<?= $person ?>" autocomplete="off" />
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="form-group w-75 pr-3">
                                <label for="phone">Телефон</label>
                                <input type="text" id="phone" name="phone" class="form-control" value="<?= $phone ?>" autocomplete="off" />
                            </div>
                            <div class="form-group w-25">
                                <label for="extension">Расширение</label>
                                <input type="text" name="extension" class="form-control" value="<?= $extension ?>" autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">E-Mail</label>
                            <input type="email" name="email" class="form-control" value="<?= $email ?>" autocomplete="off" />
                            <div class="invalid-feedback">Неправильный формат E-Mail</div>
                        </div>
                        <div class="pt-4">
                            <button type="submit" class="btn btn-dark w-25" id="customer_edit_submit" name="customer_edit_submit">Сохранить</button>
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