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
if(null !== filter_input(INPUT_POST, 'person_create_submit')) {
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
        
        $sql = "insert into person (customer_id, name, position, phone, extension, email) values ($customer_id, '$name', '$position', '$phone', '$extension', '$email')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            header('Location: '.APPLICATION.'/customer/details.php?id='.$customer_id);
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
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/customer/details.php?id=<?=$id ?>">Назад</a>
            <div class="row">
                <div class="col-12 col-md-6">
                    <h1>Новое контактное лицо</h1>
                    <form method="post">
                        <input type="hidden" name="customer_id" value="<?=$id ?>" />
                        <div class="form-group">
                            <label for="name">ФИО</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlentities(filter_input(INPUT_POST, 'name')) ?>" autocomplete="off" required="required" />
                            <div class="invalid-feedback">ФИО обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="position">Должность (роль)</label>
                            <input type="text" name="position" class="form-control" value="<?= htmlentities(filter_input(INPUT_POST, 'position')) ?>" autocomplete="off" />
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="phone">Телефон</label>
                                    <input type="text" id="phone" name="phone" value="<?= htmlentities(filter_input(INPUT_POST, 'phone')) ?>" class="form-control" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="extension">Добавочный</label>
                                    <input type="text" name="extension" value="<?= htmlentities(filter_input(INPUT_POST, 'extension')) ?>" class="form-control" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="email">E-Mail</label>
                                    <input type="text" name="email" value="<?= htmlentities(filter_input(INPUT_POST, 'email')) ?>" class="form-control" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-dark w-25" id="person_create_submit" name="person_create_submit">Создать</button>
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