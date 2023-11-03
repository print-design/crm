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
                    <h1>Все предприятия</h1>
                </div>
                <div class="d-flex justify-content-end">
                    <div class="mr-2">
                        <form class="form-inline" method="get">
                            <input type="hidden" name="user" value="<?= filter_input(INPUT_GET, 'user') ?>" />
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Поиск" id="find" name="find" value="<?= filter_input(INPUT_GET, 'find') ?>" />
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-outline-dark"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div>
                        <form class="form-inline" method="get">
                            <input type="hidden" name="find" value="<?= filter_input(INPUT_GET, 'find') ?>" />
                            <select id="user" name="user" class="form-control" onchange="this.form.submit();">
                                <option value="">...</option>
                                <?php
                                $sql = "select id, last_name, first_name from user order by last_name, first_name";
                                $fetcher = new Fetcher($sql);
                                
                                while($row = $fetcher->Fetch()):
                                $selected = '';
                                if(filter_input(INPUT_GET, 'user') == $row['id']) $selected = " selected='selected'";
                                ?>
                                <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['last_name'].' '.$row['first_name'] ?></option>
                                <?php
                                endwhile;
                                ?>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            $find = addslashes(filter_input(INPUT_GET, 'find'));
            $user = filter_input(INPUT_GET, 'user');
            
            include '../include/pager_top.php';
            
            $sql = "select count(c.id) from customer c inner join user u on c.manager_id = u.id ";
            if(!empty($find) || !empty($user)) {
                $sql .= "where ";
            }
            if(!empty($find)) {
                $sql .= "c.name like '%$find%' ";
            }
            if(!empty($find) && !empty($user)) {
                $sql .= "and ";
            }
            if(!empty($user)) {
                $sql .= "c.manager_id = $user";
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
                        <th>Телефон</th>
                        <th>E-Mail</th>
                        <th>Менеджер</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select c.id, c.name, c.phone, c.extension, c.email, u.last_name, u.first_name "
                            . "from customer c inner join user u on c.manager_id = u.id ";
                    
                    if(!empty($find) || !empty($user)) {
                        $sql .= "where ";
                    }
                    if(!empty($find)) {
                        $sql .= "c.name like '%$find%' ";
                    }
                    if(!empty($find) && !empty($user)) {
                        $sql .= "and ";
                    }
                    if(!empty($user)) {
                        $sql .= "c.manager_id = $user ";
                    }
                    $sql .= "order by c.name limit $pager_skip, $pager_take";
                    $fetcher = new Fetcher($sql);
                    
                    while($row = $fetcher->Fetch()):
                    ?>
                    <tr style="border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6;">
                        <td><?=$row['name'] ?></td>
                        <td><?=$row['phone'].(empty($row['extension']) ? '' : ' (доб. '.$row['extension'].')') ?></td>
                        <td><?=$row['email'] ?></td>
                        <td><?=$row['last_name'].' '.$row['first_name'] ?></td>
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