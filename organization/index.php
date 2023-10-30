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
                            <input type="text" class="form-control" placeholder="Поиск" id="find" name="find" value="<?= isset($_GET['find']) ? $_GET['find'] : '' ?>" />
                            <div class="input-group-append">
                                <button type="submit" type="button" class="btn btn-outline-dark"><i class="fas fa-search"></i></button>
                            </div>
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