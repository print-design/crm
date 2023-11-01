<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_ADMIN], ROLE_NAMES[ROLE_MANAGER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

$user = filter_input(INPUT_GET, 'user');
?>
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
            <div class="d-flex justify-content-between">
                <div>
                    <h1>Первичные действенные контакты</h1>
                </div>
                <div>
                    <form method="get" class="form-inline">
                        <div class="form-group">
                            <label for="user">Менеджер</label>
                            <select name="user" class="form-control ml-3" onchange="javascript: this.form.submit();">
                                <option value="">...</option>
                                <?php
                                $sql = "select id, last_name, first_name from user order by last_name, first_name";
                                $fetcher = new Fetcher($sql);
                                while($row = $fetcher->Fetch()):
                                $selected = '';
                                if($row['id'] == $user) {
                                    $selected = " selected='selected'";
                                }
                                ?>
                                <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['last_name'].' '. mb_substr($row['first_name'], 0, 1) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <?php
            include '../include/pager_top.php';
            
            $effective_results = array();
            
            foreach (RESULTS as $item) {
                if(RESULT_EFFECTIVE[$item] == 1) {
                    array_push($effective_results, $item);
                }
            }
            
            $str_effective_results = implode(', ', $effective_results);
            
            $sql = "select count(ct.id) "
                    . "from contact ct "
                    . "inner join person p on ct.person_id = p.id "
                    . "inner join customer c on p.customer_id = c.id "
                    . "inner join user u on c.manager_id = u.id "
                    . "where ct.result_id in ($str_effective_results) "
                    . "and (select count(id) from contact where date > ct.date and person_id = ct.person_id and result_id in ($str_effective_results)) = 0";
            if(!empty($user)) {
                $sql .= " and c.manager_id = $user";
            }
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $pager_total_count = $row[0];
            }
            ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Менеджер</th>
                        <th>Предприятие</th>
                        <th>Контактное лицо</th>
                        <th>Результат</th>
                        <th>След. контакт</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select date_format(ct.date, '%d.%m.%Y') date, u.last_name, u.first_name, c.name customer, p.name person, "
                            . "ct.result_id, date_format(ct.next_date, '%d.%m.%Y') next_date "
                            . "from contact ct "
                            . "inner join person p on ct.person_id = p.id "
                            . "inner join customer c on p.customer_id = c.id "
                            . "inner join user u on c.manager_id = u.id "
                            . "where ct.result_id in ($str_effective_results) "
                            . "and (select count(id) from contact where date > ct.date and person_id = ct.person_id and result_id in ($str_effective_results)) = 0";
                    if(!empty($user)) {
                        $sql .= " and c.manager_id = $user";
                    }
                    $fetcher = new Fetcher($sql);
                    while($row = $fetcher->Fetch()):
                    ?>
                    <tr>
                        <td><?=$row['date'] ?></td>
                        <td><?=$row['last_name'].' '. mb_substr($row['first_name'], 0, 1).'.' ?></td>
                        <td><?=$row['customer'] ?></td>
                        <td><?=$row['person'] ?></td>
                        <td><?=RESULT_NAMES[$row['result_id']] ?></td>
                        <td><?=$row['next_date'] == '00.00.0000' ? '' : $row['next_date'] ?></td>
                    </tr>
                    <?php endwhile; ?>
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