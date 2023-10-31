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
    
    $sql = "select count(id) from customer where name='". addslashes($name)."'";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        if($row[0] != 0) {
            $error_message = "Предприятие с таким названием уже существует";
            $form_valid = false;
        }
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
        <div class="container-fluid"></div>
    </body>
</html>