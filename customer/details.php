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
if(filter_input(INPUT_POST, 'delete_perspective_submit') !== null) {
    $id = filter_input(INPUT_POST, 'id');
    
    $sql = "delete from perspective where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $info_message = "Перспективное планирование удалено успешно";
    }
}

// Если нет параметра id, переходим к списку
$id = filter_input(INPUT_GET, 'id');

if(empty($id)) {
    header('Location: '.APPLICATION.'/customer/');
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
            
            if(!empty($info_message)) {
                echo "<div class='alert alert-success'>$info_message</div>";
            }
            ?>
            <div class="row">
                <div class="col-5">
                    <div class="d-flex justify-content-between">
                        <div><h1><?=$name ?></h1></div>
                        <div><a class="btn btn-outline-dark" href="edit.php?id=<?=$id ?>" style="width: 160px;"><i class="fas fa-edit"></i>&nbsp;&nbsp;Редактировать</a></div>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th>Менеджер</th>
                            <td><?=$last_name.' '.$first_name ?></td>
                        </tr>
                        <tr>
                            <th>Основное контактное лицо</th>
                            <td><?=$person ?></td>
                        </tr>
                        <tr>
                            <th>Телефон</th>
                            <td><?=$phone.(empty($extension) ? '' : ' (доп. '.$extension.')') ?></td>
                        </tr>
                        <tr>
                            <th>E-Mail</th>
                            <td><a href="mailto:<?=$email ?>"><?=$email ?></a></td>
                        </tr>
                    </table>
                    <div class="d-flex justify-content-between">
                        <div><h2>Контактные лица</h2></div>
                        <div><a class="btn btn-outline-dark" href="<?=APPLICATION ?>/person/edit.php?id=<?=$id ?>" style="width: 160px;"><i class="fas fa-plus"></i>&nbsp;&nbsp;Добавить</a></div>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ФИО</th>
                                <th>Должность (роль)</th>
                                <th>Телефон</th>
                                <th>E-Mail</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "select id, name, position, phone, extension, email from person where customer_id = $id";
                            $fetcher = new Fetcher($sql);
                            while($row = $fetcher->Fetch()):
                            ?>
                            <tr>
                                <td><a href="<?=APPLICATION.'/contact/create.php?person='.$row['id'] ?>" title="Новый контакт <?=$row['name'] ?>" class="btn btn-outline-dark"><i class="fas fa-phone"></i></a></td>
                                <td><?=$row['name'] ?></td>
                                <td><?=$row['position'] ?></td>
                                <td><?=$row['phone'].(empty($row['extension']) ? '' : " <span class='text-nowrap'>(доп. ".$row['extension'].")</span>") ?></td>
                                <td><?=$row['email'] ?></td>
                                <td><a href="<?=APPLICATION ?>/person/edit.php?id=<?=$row['id'] ?>" title="Редактировать" class="btn btn-outline-dark"><i class="fas fa-edit"></i></a></td>
                            </tr>
                            <?php
                            endwhile;
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-7">
                    <h2>Контакты</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Менеджер</th>
                                <th>Конт. лицо</th>
                                <th>Должность</th>
                                <th>Результат</th>
                                <th>Действ.</th>
                                <th>След.</th>
                                <th>Комментарий</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "select c.id id, date_format(c.date, '%d.%m.%Y') date, u.first_name, u.last_name, c.result_id, date_format(c.next_date, '%d.%m.%Y') next_date, "
                                    . "p.name, p.position, p.phone, p.extension, p.email, c.comment "
                                    . "from person p "
                                    . "inner join contact c on c.person_id = p.id "
                                    . "inner join user u on c.manager_id = u.id "
                                    . "where p.customer_id=".$_GET['id']." "
                                    . "order by c.id desc";
                            $fetcher = new Fetcher($sql);
                            while($row = $fetcher->Fetch()):
                            ?>
                            <tr>
                                <td><?=$row['date'] ?></td>
                                <td class="text-nowrap"><?=$row['last_name'].' '.mb_substr($row['first_name'], 0, 1).'.' ?></td>
                                <td><?=$row['name'] ?></td>
                                <td><?=$row['position'] ?></td>
                                <td><?=RESULT_NAMES[$row['result_id']] ?></td>
                                <td><?=(RESULT_EFFECTIVE[$row['result_id']] == 1 ? '&#x2713;' : '') ?></td>
                                <td><?=$row['next_date'] ?></td>
                                <td><?=$row['comment'] ?></td>
                                <td><a href="<?=APPLICATION ?>/contact/edit.php?id=<?=$row['id'] ?>" class="btn btn-outline-dark"><i class="fas fa-edit"></i></a></td>
                            </tr>
                            <?php
                            endwhile;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <div><h2>Перспективное планирование</h2></div>
                <div><a class="btn btn-outline-dark" href="<?=APPLICATION ?>/perspective/create.php?id=<?=$id ?>" style="width: 160px;"><i class="fas fa-plus"></i>&nbsp;&nbsp;Добавить</a></div>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Дата&nbsp;&ndash;</th>
                        <th>Дата</th>
                        <th>Дата&ndash;+</th>
                        <th>Затраты</th>
                        <th>Тип плёнки</th>
                        <th>Толщина плёнки</th>
                        <th>Ширина плёнки</th>
                        <th>Длина плёнки</th>
                        <th>Вес плёнки</th>
                        <th>Цена плёнки</th>
                        <th>Вероятность</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select p.id, p.date, date_format(p.date, '%d.%m.%Y') fdate, p.date_minus, "
                            . "date_format(p.date_minus, '%d.%m.%Y') fdate_minus, p.date_plus, date_format(p.date_plus, '%d.%m.%Y') fdate_plus, "
                            . "p.expenses, f.name film, fv.thickness, p.film_width, p.film_length, p.film_weight, p.film_price, p.film_currency, p.probability "
                            . "from perspective p "
                            . "inner join film_variation fv on p.film_variation_id = fv.id "
                            . "inner join film f on fv.film_id = f.id "
                            . "where p.customer_id = $id "
                            . "order by p.date desc";
                    $fetcher = new Fetcher($sql);
                    while($row = $fetcher->Fetch()):
                    ?>
                    <tr>
                        <td><?=$row['fdate_minus'] ?></td>
                        <td><?=$row['fdate'] ?></td>
                        <td><?=$row['fdate_plus'] ?></td>
                        <td><?=$row['expenses'] ?></td>
                        <td><?=$row['film'] ?></td>
                        <td><?=$row['thickness'] ?></td>
                        <td><?=$row['film_width'] ?></td>
                        <td><?=$row['film_length'] ?></td>
                        <td><?=$row['film_weight'] ?></td>
                        <td><?=$row['film_price'].' '.CURRENCY_SHORTNAMES[$row['film_currency']] ?></td>
                        <td><?=$row['probability'] ?></td>
                        <td><a href="<?=APPLICATION ?>/perspective/edit.php?id=<?=$row['id'] ?>" class="btn btn-outline-dark"><i class="fas fa-edit"></i></a></td>
                        <td>
                            <form>
                                <input type="hidden" name="id" value="<?=$row['id'] ?>" />
                                <button type="submit" name="delete_perspective_submit" class="btn btn-outline-dark" onclick="javascript: return confirm('Действительно удалить?');"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php
                    endwhile;
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>