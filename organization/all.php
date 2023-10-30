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
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Поиск" id="find" name="find" value="<?= isset($_GET['find']) ? $_GET['find'] : '' ?>" />
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-outline-dark"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div>
                        <form class="form-inline" method="get">
                            <select id="user" name="user" class="form-control" onchange="this.form.submit();">
                                <option value="">...</option>
                                <?php
                                $sql = "select id, last_name, first_name from user order by last_name, first_name";
                                $fetcher = new Fetcher($sql);
                                
                                while($row = $fetcher->Fetch()):
                                $selected = '';
                                if(filter_input(INPUT_GET, 'user') == $row['id']) $selected = " selected='selected'";
                                /*$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                                $sql = "select id, last_name, first_name, middle_name from manager order by last_name";
                                    
                                if($conn->connect_error) {
                                    die('Ошибка соединения: ' . $conn->connect_error);
                                }
                                    
                                $conn->query('set names utf8');
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $selected = isset($_GET['manager']) && $_GET['manager'] == $row['id'] ? " selected='selected'" : '';
                                        echo '<option value='.$row['id'].$selected.'>'.$row['last_name'].' '.(mb_strlen($row['first_name']) > 1 ? mb_substr($row['first_name'], 0, 1).'.' : $row['first_name']).' '.(mb_strlen($row['first_name']) > 1 ? mb_substr($row['middle_name'], 0, 1).'.' : $row['middle_name']).'</option>';
                                    }
                                }
                                $conn->close();*/
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
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>