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

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'perspective_create_submit')) {
    $customer_id = filter_input(INPUT_POST, 'customer_id');
    $date = filter_input(INPUT_POST, 'date');
    $date_minus = filter_input(INPUT_POST, 'date_minus');
    $date_plus = filter_input(INPUT_POST, 'date_plus');
    $film_id = filter_input(INPUT_POST, 'film_id'); if(empty($film_id)) $film_id = 0;
    $film_variation_id = filter_input(INPUT_POST, 'film_variation_id'); if(empty($film_variation_id)) $film_variation_id = 0;
    $expenses = filter_input(INPUT_POST, 'expenses'); if(empty($expenses)) $expenses = 0;
    $film_width = filter_input(INPUT_POST, 'film_width'); if(empty($film_width)) $film_width = 0;
    $film_length = filter_input(INPUT_POST, 'film_length'); if(empty($film_length)) $film_length = 0;
    $film_weight = filter_input(INPUT_POST, 'film_weight'); if(empty($film_weight)) $film_weight = 0;
    $film_price = filter_input(INPUT_POST, 'film_price'); if(empty($film_price)) $film_price = 0;
    $film_currency = filter_input(INPUT_POST, 'film_currency');
    $probability = filter_input(INPUT_POST, 'probability'); if(empty($probability)) $probability = 0;
    
    if($form_valid) {
        $sql = "insert into perspective (customer_id, date, date_minus, date_plus, film_id, film_variation_id, expenses, film_width, film_length, "
                . "film_weight, film_price, film_currency, probability) "
                . "values ($customer_id, '$date', '$date_minus', '$date_plus', $film_variation_id, $expenses, $film_width, $film_length, "
                . "$film_weight, $film_price, '$film_currency', $probability)";
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
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/customer/details.php?id=<?=$id ?>">Назад</a>
            <div class="row">
                <div class="col-12 col-md-6">
                    <h1>Новое планируемое действие</h1>
                    <form method="post">
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="date_minus">Дата (-)</label>
                                    <input type="date" name="date_minus" value="<?= filter_input(INPUT_POST, 'date_minus') ?>" class="form-control" />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="date">Дата</label>
                                    <input type="date" name="date" value="<?= filter_input(INPUT_POST, 'date') ?>" class="form-control" />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="date_plus">Дата (+)</label>
                                    <input type="date" name="date_plus" value="<?= filter_input(INPUT_POST, 'date_plus') ?>" class="form-control" />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="expenses">Затраты</label>
                                    <input type="text" name="expenses" value="<?= filter_input(INPUT_POST, 'expenses') ?>" class="form-control int-only" />
                                </div>
                            </div>
                            <div class="col-4"></div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="probability">Вероятность (%)</label>
                                    <input type="text" name="probability" value="<?= filter_input(INPUT_POST, 'probability') ?>" class="form-control int-only" />
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="film_id">Тип плёнки</label>
                                    <select name="film_id" class="form-control">
                                        <option value="">...</option>
                                        <?php
                                        $sql = "select id, name from film order by name";
                                        $fetcher = new Fetcher($sql);
                                        while ($row = $fetcher->Fetch()):
                                            $selected = "";
                                        if(filter_input(INPUT_POST, 'film_id') == $row['id']) {
                                            $selected = " selected='selected'";
                                        }
                                        ?>
                                        <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['name'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="film_variation_id">Толщина плёнки</label>
                                    <select name="film_variation_id" class="form-control">
                                        <option value="">...</option>
                                    </select>
                                </div>
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