<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_ADMIN], ROLE_NAMES[ROLE_MANAGER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

if(IsInRole(ROLE_NAMES[ROLE_ADMIN])) {
    // Смена менеджера
    if(filter_input(INPUT_POST, 'change_manager_submit') !== null) {
        $id = filter_input(INPUT_POST, 'id');
        $manager_id = filter_input(INPUT_POST, 'manager_id');
        
        $sql = "update customer set manager_id = $manager_id where id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            $info_message = "Менеджер сменился успешно";
        }
    }
}

// Удаление перспективного планирования
if(filter_input(INPUT_POST, 'delete_perspective_planning_submit') !== null) {
    $id = filter_input(INPUT_POST, 'id');
    
    $sql = "delete from perspective_planning where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $info_message = "Перспективное планирование удалено успешно";
    }
}

// Если нет параметра id, переходим к списку
$id = filter_input(INPUT_GET, 'id');

if(empty($id)) {
    header('Location: '.APPLICATION.'/organization/');
}

// Получение объекта
$name = '';
$person = '';
$phone = '';
$extension = '';
$email = '';
$manager_id = 0;
$last_name = '';
$first_name = '';

$sql = "select c.name, c.person, c.phone, c.extension, c.email, c.manager_id, u.last_name, u.first_name "
        . "from customer c inner join user u on c.manager_id = u.id where c.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $name = $row['name'];
    $person = $row['person'];
    $phone = $row['phone'];
    $extension = $row['extension'];
    $email = $row['email'];
    $manager_id = $row['manager_id'];
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
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
            
            if(!empty($info_message)) {
                echo "<div class='alert alert-success'>$info_message</div>";
            }
            ?>
            <div class="row">
                <div class="col-4">
                    <div class=" d-flex justify-content-between">
                        <div><h1><?=$name ?></h1></div>
                        <div><a class="btn btn-outline-dark" href="edit.php?id=<?=$id ?>"><i class="fas fa-edit"></i>&nbsp;&nbsp;Редактировать</a></div>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th>Менеджер</th>
                            <td><?=$last_name.' '.$first_name ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-8">
                    <h2>Контакты</h2>
                </div>
            </div>            
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>