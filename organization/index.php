<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_ADMIN], ROLE_NAMES[ROLE_MANAGER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
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
            <div class="d-flex justify-content-between mb-auto">
                <div>
                    <h1>Мои предприятия</h1>
                </div>
                <div>
                    <form class="form-inline" method="get">
                        <a href="create.php" title="Добавить предприятие" class="btn btn-outline-dark mr-sm-2">
                            <i class="fas fa-plus"></i>&nbsp;Добавить
                        </a>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Поиск" id="find" name="find" value="<?= filter_input(INPUT_GET, 'find') ?>" />
                            <div class="input-group-append">
                                <button type="submit" type="button" class="btn btn-outline-dark"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php
            $find = addslashes(filter_input(INPUT_GET, 'find'));
            
            include '../include/pager_top.php';
            
            $sql = "select count(id) from customer where manager_id = ". GetUserId();
            if(!empty($find)) {
                $sql .= " and name like '%$find%'";
            }
            
            $fetcher = new Fetcher($sql);
            
            if($row = $fetcher->Fetch()) {
                $pager_total_count = $row[0];
            }
            ?>
            <table class="table table-hover">
                <thead>
                    <tr style="border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6;">
                        <th>Наименование</th>
                        <th>Основное контактное лицо</th>
                        <th>Телефон</th>
                        <th>E-Mail</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select id, name, person, phone, extension, email from customer where manager_id = ". GetUserId();
                    if(!empty($find)) {
                        $sql .= " and name like '%$find%'";
                    }
                    $sql .= " order by name limit $pager_skip, $pager_take";
                    $fetcher = new Fetcher($sql);
                    
                    while($row = $fetcher->Fetch()):
                    ?>
                    <tr style="border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6;">
                        <td><?=$row['name'] ?></td>
                        <td><?=$row['person'] ?></td>
                        <td><?=$row['phone'].(empty($row['extension']) ? '' : ' (доб. '.$row['extension'].')') ?></td>
                        <td><?=$row['email'] ?></td>
                        <td><a href="details.php?id=<?=$row['id'] ?>"><img src="../images/icons/vertical-dots.svg" /></a></td>
                    </tr>
                    <?php
                    endwhile;
                    ?>
                </tbody>
            </table>
            <?php
            include '../include/pager_bottom.php';
            ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>