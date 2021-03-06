<?php
session_start();
include '../db.php';
$alert = null;
$danger = 'btn-danger';
if (isset($_POST['login'])) {
    $username = trim(strtolower($_POST['username']));
    $password = trim(strtolower($_POST['password']));
    if (empty($username) || empty($password)) {
        $alert = 'Empty username or password!';
    } elseif ($username != 'user' && $password != 'pass') {
        $alert = 'Грешно име или парола!';
    } else {
        $_SESSION['admin'] = 'logged';
        header('Location: index.php');
    }
}
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ../index.php');
}
if (isset($_GET['delete_product'])) {
    $id = addslashes((int) $_GET['delete_product']);
    $con->query("DELETE FROM products WHERE id = $id LIMIT 1");
    header('Location: index.php');
}
if (isset($_GET['delete_product_new'])) {
	$id = addslashes((int) $_GET['delete_product_new']);
	$con->query("DELETE FROM products_new_proposals WHERE id = $id LIMIT 1");
	header('Location: index.php?page=new');
}
if (isset($_GET['delete_categ'])) {
    $id = addslashes((int) $_GET['delete_categ']);
    $con->query("DELETE FROM categories WHERE id = $id LIMIT 1");
    $con->query("DELETE FROM products WHERE categorie = $id LIMIT 1");
    header('Location: index.php');
}
if (isset($_GET['delete_categ_new'])) {
	$id = addslashes((int) $_GET['delete_categ_new']);
	$con->query("DELETE FROM categories_new_proposals WHERE id = $id LIMIT 1");
	$con->query("DELETE FROM products_new_proposals WHERE categorie = $id LIMIT 1");
	header('Location: index.php?page=new');
}
if (isset($_POST['categorie'])) {
	$name = addslashes(trim($_POST['name']));
	if (mb_strlen($name) > 0) {
		$quantity = addslashes(trim($_POST['quantity']));
		$_POST['price'] = str_replace(",", ".", $_POST['price']);
		$price = addslashes($_POST['price']);
		$categorie = addslashes((int) $_POST['categorie']);
		$max = $con->query("SELECT MAX(pid) FROM (SELECT pid FROM products UNION ALL SELECT pid FROM products_new_proposals) a");
		$maxid = $max->fetch_row();
		$pid = (int) $maxid[0] + 1;
		$con->query("INSERT INTO products (name, pid, quantity, price, categorie) VALUES ('$name', $pid, '$quantity', '$price', $categorie)");
		header('Location: index.php');
	}
}
if (isset($_POST['categorie_new'])) {
    $name = addslashes(trim($_POST['name']));
    if (mb_strlen($name) > 0) {
        $quantity = addslashes(trim($_POST['quantity']));
        $_POST['price'] = str_replace(",", ".", $_POST['price']);
        $price = addslashes($_POST['price']);
        $categorie = addslashes((int) $_POST['categorie_new']);
        $max = $con->query("SELECT MAX(pid) FROM (SELECT pid FROM products UNION ALL SELECT pid FROM products_new_proposals) a");
		$maxid = $max->fetch_row();
		$pid = (int) $maxid[0] + 1;
        $con->query("INSERT INTO products_new_proposals (name, pid, quantity, price, categorie) VALUES ('$name', $pid, '$quantity', '$price', $categorie)");
        header('Location: index.php?page=new');
    }
}
if (isset($_POST['categ_name'])) {
    $name = addslashes(trim($_POST['categ_name']));
    if (mb_strlen($name) > 0) {
        $con->query("INSERT INTO categories (name) VALUES ('$name')");
        header('Location: index.php');
    }
}
if (isset($_POST['categ_name_new'])) {
	$name = addslashes(trim($_POST['categ_name_new']));
	if (mb_strlen($name) > 0) {
		$con->query("INSERT INTO categories_new_proposals (name) VALUES ('$name')");
		header('Location: index.php?page=new');
	}
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Balkaniada - Admin Panel</title>
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/mine.css">
        <script src="../js/jquery.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class="container" style="margin-bottom:30px;">
            <?php if (!isset($_SESSION['admin'])) { ?>
                <div class="col-sm-6 col-sm-offset-3">
                    <?php if ($alert !== null) { ?>
                        <div class="alert <?= $danger ?>"><?= $alert ?></div>
                    <?php } ?>
                    <h1>Вход:</h1>
                    <form role="form" method="POST">
                        <div class="form-group">
                            <label for="text">Потребител:</label>
                            <input type="text" name="username" value="<?= @$_POST['username'] ?>" class="form-control" id="text">
                        </div>
                        <div class="form-group">
                            <label for="pwd">Парола:</label>
                            <input type="password" name="password" value="<?= @$_POST['password'] ?>" class="form-control" id="pwd">
                        </div>
                        <button type="submit" name="login" class="btn btn-default">Влез</button>
                    </form>
                </div>
            <?php } else { ?>
                <nav class="navbar navbar-default">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <a class="navbar-brand" href="#">Администрация</a>
                        </div>
                        <div>
                            <ul class="nav navbar-nav">
                                <li class="<?= !isset($_GET['page']) ? 'active' : '' ?>"><a href="index.php">Продукти</a></li>
                                <!-- <li class="<?= isset($_GET['page']) && $_GET['page']=='new' ? 'active' : '' ?>"><a href="index.php?page=new">Новите ни предложения</a></li>  -->
                            </ul>
                            <ul class="nav navbar-nav navbar-right">
                                <li><a href="?logout"><span class="glyphicon glyphicon-user"></span> Изход</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
                <?php if (!isset($_GET['page'])) { ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <h1>Хранителни продукти</h1>
                        </div>
                        <div class="col-sm-6" style="padding-top: 25px;">
                            <button type="button" class="btn btn-default pull-right" style="margin-left:10px;" data-toggle="modal" data-target="#categoryModal">
                                Добави категория
                            </button>
                            <button type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#productModal">
                                Добави продукт
                            </button>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed  ">
                            <?php
                            $categ = '';
                            $table = $con->query("SELECT products.*, categories.name as categ, categories.id as categ_id FROM products INNER JOIN categories ON categories.id = products.categorie ORDER BY products.categorie, products.id");
                            while ($row = $table->fetch_assoc()) {
                                if ($row['categ'] != $categ) {
                                    ?>
                                    <tr class="success">
                                        <th colspan="5" class="text-center"><h3 class="categ-<?= $row['categ_id'] ?>"><?= $row['categ'] ?></h3><a href="javascript:void(0)" onclick="editCategory(<?= $row['categ_id'] ?>)" class="pull-left edit-categ-id-<?= $row['categ_id'] ?>">Редактирай</a><a href="javascript:void(0)" onclick="saveEditCategory(<?= $row['categ_id'] ?>)" style="display: none;" class="pull-left save-categ-id-<?= $row['categ_id'] ?>">Запиши</a><a href="?delete_categ=<?= $row['categ_id'] ?>" class="pull-right" onclick="return confirm(\'Сигурен ли си че ще триеш цялата категория с всички продукти?\')">Изтрий</a><input type="text" class="form-control categ-input-<?= $row['categ_id'] ?>" value="<?= $row['categ'] ?>" style="display:none;"></th>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center"><b>бр./грамаж</b></td><td class="text-center"><b>ед. цена</b></td>
                                        <td class="text-center"><b>Изтрий</b></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr>
                                    <td># <?= substr($row['pid'], 7, 12); ?></td>
                                    <td><b><span class="name-<?= $row['id'] ?>"><?= $row['name'] ?></span></b><input type="text" class="name-input-<?= $row['id'] ?>" value="<?= $row['name'] ?>" style="display:none; width:80%;"><a href="javascript:void(0)" onclick="editName(<?= $row['id'] ?>)" class="pull-right edit-name-id-<?= $row['id'] ?>"><span class="glyphicon glyphicon-pencil"></span></a><a href="javascript:void(0)" onclick="saveEditName(<?= $row['id'] ?>)" style="display: none;" class="pull-right save-name-id-<?= $row['id'] ?>"><span class="glyphicon glyphicon-floppy-disk"></span></a></td>
                                    <td class="text-center"><span class="quantity-<?= $row['id'] ?>"><?= $row['quantity'] ?></span><input type="text" class="quantity-input-<?= $row['id'] ?>" value="<?= $row['quantity'] ?>" style="display:none; width:80%;"><a href="javascript:void(0)" onclick="editQuantity(<?= $row['id'] ?>)" class="pull-right edit-quantity-id-<?= $row['id'] ?>"><span class="glyphicon glyphicon-pencil"></span></a><a href="javascript:void(0)" onclick="saveEditQuantity(<?= $row['id'] ?>)" style="display: none;" class="pull-right save-quantity-id-<?= $row['id'] ?>"><span class="glyphicon glyphicon-floppy-disk"></span></a></td>
                                    <td class="text-center"><span class="glyphicon glyphicon-euro"></span> <span class="price-<?= $row['id'] ?>"><?= $row['price'] ?></span><input type="text" class="price-input-<?= $row['id'] ?>" value="<?= $row['price'] ?>" style="display:none; width:80%;"><a href="javascript:void(0)" onclick="editPrice(<?= $row['id'] ?>)" class="pull-right edit-price-id-<?= $row['id'] ?>"><span class="glyphicon glyphicon-pencil"></span></a><a href="javascript:void(0)" onclick="saveEditPrice(<?= $row['id'] ?>)" style="display: none;" class="pull-right save-price-id-<?= $row['id'] ?>"><span class="glyphicon glyphicon-floppy-disk"></span></a></td>
                                    <td class="text-center danger"><a href="?delete_product=<?= $row['id'] ?>" onclick="return confirm('Сигурен ли си че ще го триеш?')"><span class="glyphicon glyphicon-remove"></span></a></td>
                                </tr>
                                <?php
                                $categ = $row['categ'];
                            }
                            ?>
                        </table>
                    </div>
                        <script>
                                        function editCategory(id) {
                                            $('.edit-categ-id-' + id).hide();
                                            $('.save-categ-id-' + id).show();
                                            $('.categ-' + id).hide();
                                            $('.categ-input-' + id).show();
                                        }
                                        function saveEditCategory(id) {
                                            $("#loading").show();
                                            var newVal = $('.categ-input-' + id).val();
                                            $('.categ-' + id).text(newVal);
                                            $('.edit-categ-id-' + id).show();
                                            $('.save-categ-id-' + id).hide();
                                            $('.categ-' + id).show();
                                            $('.categ-input-' + id).hide();
                                            $.ajax({
                                                type: "POST",
                                                url: "ajax.php",
                                                data: {categ: newVal, id: id}
                                            }).done(function (data) {
                                                $("#loading").hide();
                                            });
                                        }

                                        function editName(id) {
                                            $('.edit-name-id-' + id).hide();
                                            $('.save-name-id-' + id).show();
                                            $('.name-' + id).hide();
                                            $('.name-input-' + id).show();
                                        }
                                        function saveEditName(id) {
                                            $("#loading").show();
                                            var newVal = $('.name-input-' + id).val();
                                            $('.name-' + id).text(newVal);
                                            $('.edit-name-id-' + id).show();
                                            $('.save-name-id-' + id).hide();
                                            $('.name-' + id).show();
                                            $('.name-input-' + id).hide();
                                            $.ajax({
                                                type: "POST",
                                                url: "ajax.php",
                                                data: {name: newVal, id: id}
                                            }).done(function (data) {
                                                $("#loading").hide();
                                            });
                                        }

                                        function editQuantity(id) {
                                            $('.edit-quantity-id-' + id).hide();
                                            $('.save-quantity-id-' + id).show();
                                            $('.quantity-' + id).hide();
                                            $('.quantity-input-' + id).show();
                                        }
                                        function saveEditQuantity(id) {
                                            $("#loading").show();
                                            var newVal = $('.quantity-input-' + id).val();
                                            $('.quantity-' + id).text(newVal);
                                            $('.edit-quantity-id-' + id).show();
                                            $('.save-quantity-id-' + id).hide();
                                            $('.quantity-' + id).show();
                                            $('.quantity-input-' + id).hide();
                                            $.ajax({
                                                type: "POST",
                                                url: "ajax.php",
                                                data: {quantity: newVal, id: id}
                                            }).done(function (data) {
                                                $("#loading").hide();
                                            });
                                        }

                                        function editPrice(id) {
                                            $('.edit-price-id-' + id).hide();
                                            $('.save-price-id-' + id).show();
                                            $('.price-' + id).hide();
                                            $('.price-input-' + id).show();
                                        }
                                        function saveEditPrice(id) {
                                            $("#loading").show();
                                            var newVal = $('.price-input-' + id).val();
                                            $('.price-' + id).text(newVal);
                                            $('.edit-price-id-' + id).show();
                                            $('.save-price-id-' + id).hide();
                                            $('.price-' + id).show();
                                            $('.price-input-' + id).hide();
                                            $.ajax({
                                                type: "POST",
                                                url: "ajax.php",
                                                data: {price: newVal, id: id}
                                            }).done(function (data) {
                                                $("#loading").hide();
                                            });
                                        }

                                        function addProduct() {
                                            document.getElementById("addprod").submit();
                                        }
                                        function addCateg() {
                                            document.getElementById("addcat").submit();
                                        }
    </script>
    <!-- Modal Product -->
    <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Добавяне на продукт</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form" id="addprod" method="POST">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="name">Име:</label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" id="name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="qua">Грамаж:</label>
                            <div class="col-sm-10">
                                <input type="text" name="quantity" class="form-control" id="qua">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="price">Цена:</label>
                            <div class="col-sm-10">
                                <input type="text" name="price" class="form-control" id="price">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="pwd">Категория:</label>
                            <div class="col-sm-10">
                                <?php $cats = $con->query("SELECT * FROM categories"); ?>
                                <select class="form-control" name="categorie" id="sel1">
                                    <?php while ($row = $cats->fetch_assoc()) { ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отказ</button>
                    <button type="button" class="btn btn-primary" onclick="addProduct()">Добави</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Categorie -->
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Добавяне на категория</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form" id="addcat" method="POST">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="name">Име:</label>
                            <div class="col-sm-10">
                                <input type="text" name="categ_name" class="form-control" id="name">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отказ</button>
                    <button type="button" class="btn btn-primary" onclick="addCateg()">Добави</button>
                </div>
            </div>
        </div>
    </div>
                <?php } elseif(isset($_GET['page']) && $_GET['page'] == 'new') { ?>
                
                
                
                
                
                
                                    <div class="row">
                        <div class="col-sm-6">
                            <h1>Новите ни предложения</h1>
                        </div>
                        <div class="col-sm-6" style="padding-top: 25px;">
                            <button type="button" class="btn btn-default pull-right" style="margin-left:10px;" data-toggle="modal" data-target="#categoryModal">
                                Добави категория
                            </button>
                            <button type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#productModal">
                                Добави продукт
                            </button>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed  ">
                            <?php
                            $categ = '';
                            $table = $con->query("SELECT products_new_proposals.*, categories_new_proposals.name as categ, categories_new_proposals.id as categ_id FROM products_new_proposals INNER JOIN categories_new_proposals ON categories_new_proposals.id = products_new_proposals.categorie ORDER BY products_new_proposals.categorie, products_new_proposals.id");
                            while ($row = $table->fetch_assoc()) {
                                if ($row['categ'] != $categ) {
                                    ?>
                                    <tr class="success">
                                        <th colspan="5" class="text-center"><h3 class="categ-<?= $row['categ_id'] ?>"><?= $row['categ'] ?></h3><a href="javascript:void(0)" onclick="editCategory(<?= $row['categ_id'] ?>)" class="pull-left edit-categ-id-<?= $row['categ_id'] ?>">Редактирай</a><a href="javascript:void(0)" onclick="saveEditCategory(<?= $row['categ_id'] ?>)" style="display: none;" class="pull-left save-categ-id-<?= $row['categ_id'] ?>">Запиши</a><a href="?delete_categ_new=<?= $row['categ_id'] ?>" class="pull-right" onclick="return confirm(\'Сигурен ли си че ще триеш цялата категория с всички продукти?\')">Изтрий</a><input type="text" class="form-control categ-input-<?= $row['categ_id'] ?>" value="<?= $row['categ'] ?>" style="display:none;"></th>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center"><b>бр./грамаж</b></td><td class="text-center"><b>ед. цена</b></td>
                                        <td class="text-center"><b>Изтрий</b></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr>
                                    <td># <?= substr($row['pid'], 7, 12); ?></td>
                                    <td><b><span class="name-<?= $row['id'] ?>"><?= $row['name'] ?></span></b><input type="text" class="name-input-<?= $row['id'] ?>" value="<?= $row['name'] ?>" style="display:none; width:80%;"><a href="javascript:void(0)" onclick="editName(<?= $row['id'] ?>)" class="pull-right edit-name-id-<?= $row['id'] ?>"><span class="glyphicon glyphicon-pencil"></span></a><a href="javascript:void(0)" onclick="saveEditName(<?= $row['id'] ?>)" style="display: none;" class="pull-right save-name-id-<?= $row['id'] ?>"><span class="glyphicon glyphicon-floppy-disk"></span></a></td>
                                    <td class="text-center"><span class="quantity-<?= $row['id'] ?>"><?= $row['quantity'] ?></span><input type="text" class="quantity-input-<?= $row['id'] ?>" value="<?= $row['quantity'] ?>" style="display:none; width:80%;"><a href="javascript:void(0)" onclick="editQuantity(<?= $row['id'] ?>)" class="pull-right edit-quantity-id-<?= $row['id'] ?>"><span class="glyphicon glyphicon-pencil"></span></a><a href="javascript:void(0)" onclick="saveEditQuantity(<?= $row['id'] ?>)" style="display: none;" class="pull-right save-quantity-id-<?= $row['id'] ?>"><span class="glyphicon glyphicon-floppy-disk"></span></a></td>
                                    <td class="text-center"><span class="glyphicon glyphicon-euro"></span> <span class="price-<?= $row['id'] ?>"><?= $row['price'] ?></span><input type="text" class="price-input-<?= $row['id'] ?>" value="<?= $row['price'] ?>" style="display:none; width:80%;"><a href="javascript:void(0)" onclick="editPrice(<?= $row['id'] ?>)" class="pull-right edit-price-id-<?= $row['id'] ?>"><span class="glyphicon glyphicon-pencil"></span></a><a href="javascript:void(0)" onclick="saveEditPrice(<?= $row['id'] ?>)" style="display: none;" class="pull-right save-price-id-<?= $row['id'] ?>"><span class="glyphicon glyphicon-floppy-disk"></span></a></td>
                                    <td class="text-center danger"><a href="?delete_product_new=<?= $row['id'] ?>" onclick="return confirm('Сигурен ли си че ще го триеш?')"><span class="glyphicon glyphicon-remove"></span></a></td>
                                </tr>
                                <?php
                                $categ = $row['categ'];
                            }
                            ?>
                        </table>
                    </div>
                        <script>
                                        function editCategory(id) {
                                            $('.edit-categ-id-' + id).hide();
                                            $('.save-categ-id-' + id).show();
                                            $('.categ-' + id).hide();
                                            $('.categ-input-' + id).show();
                                        }
                                        function saveEditCategory(id) {
                                            $("#loading").show();
                                            var newVal = $('.categ-input-' + id).val();
                                            $('.categ-' + id).text(newVal);
                                            $('.edit-categ-id-' + id).show();
                                            $('.save-categ-id-' + id).hide();
                                            $('.categ-' + id).show();
                                            $('.categ-input-' + id).hide();
                                            $.ajax({
                                                type: "POST",
                                                url: "ajax_new_proposals.php",
                                                data: {categ: newVal, id: id}
                                            }).done(function (data) {
                                                $("#loading").hide();
                                            });
                                        }

                                        function editName(id) {
                                            $('.edit-name-id-' + id).hide();
                                            $('.save-name-id-' + id).show();
                                            $('.name-' + id).hide();
                                            $('.name-input-' + id).show();
                                        }
                                        function saveEditName(id) {
                                            $("#loading").show();
                                            var newVal = $('.name-input-' + id).val();
                                            $('.name-' + id).text(newVal);
                                            $('.edit-name-id-' + id).show();
                                            $('.save-name-id-' + id).hide();
                                            $('.name-' + id).show();
                                            $('.name-input-' + id).hide();
                                            $.ajax({
                                                type: "POST",
                                                url: "ajax_new_proposals.php",
                                                data: {name: newVal, id: id}
                                            }).done(function (data) {
                                                $("#loading").hide();
                                            });
                                        }

                                        function editQuantity(id) {
                                            $('.edit-quantity-id-' + id).hide();
                                            $('.save-quantity-id-' + id).show();
                                            $('.quantity-' + id).hide();
                                            $('.quantity-input-' + id).show();
                                        }
                                        function saveEditQuantity(id) {
                                            $("#loading").show();
                                            var newVal = $('.quantity-input-' + id).val();
                                            $('.quantity-' + id).text(newVal);
                                            $('.edit-quantity-id-' + id).show();
                                            $('.save-quantity-id-' + id).hide();
                                            $('.quantity-' + id).show();
                                            $('.quantity-input-' + id).hide();
                                            $.ajax({
                                                type: "POST",
                                                url: "ajax_new_proposals.php",
                                                data: {quantity: newVal, id: id}
                                            }).done(function (data) {
                                                $("#loading").hide();
                                            });
                                        }

                                        function editPrice(id) {
                                            $('.edit-price-id-' + id).hide();
                                            $('.save-price-id-' + id).show();
                                            $('.price-' + id).hide();
                                            $('.price-input-' + id).show();
                                        }
                                        function saveEditPrice(id) {
                                            $("#loading").show();
                                            var newVal = $('.price-input-' + id).val();
                                            $('.price-' + id).text(newVal);
                                            $('.edit-price-id-' + id).show();
                                            $('.save-price-id-' + id).hide();
                                            $('.price-' + id).show();
                                            $('.price-input-' + id).hide();
                                            $.ajax({
                                                type: "POST",
                                                url: "ajax_new_proposals.php",
                                                data: {price: newVal, id: id}
                                            }).done(function (data) {
                                                $("#loading").hide();
                                            });
                                        }

                                        function addProduct() {
                                            document.getElementById("addprod_new").submit();
                                        }
                                        function addCateg() {
                                            document.getElementById("addcateg_new").submit();
                                        }
    </script>
    <!-- Modal Product -->
    <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Добавяне на продукт</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form" id="addprod_new" method="POST">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="name">Име:</label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" id="name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="qua">Грамаж:</label>
                            <div class="col-sm-10">
                                <input type="text" name="quantity" class="form-control" id="qua">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="price">Цена:</label>
                            <div class="col-sm-10">
                                <input type="text" name="price" class="form-control" id="price">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="pwd">Категория:</label>
                            <div class="col-sm-10">
                                <?php $cats = $con->query("SELECT * FROM categories_new_proposals"); ?>
                                <select class="form-control" name="categorie_new" id="sel1">
                                    <?php while ($row = $cats->fetch_assoc()) { ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отказ</button>
                    <button type="button" class="btn btn-primary" onclick="addProduct()">Добави</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Categorie -->
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Добавяне на категория</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form" id="addcateg_new" method="POST">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="name">Име:</label>
                            <div class="col-sm-10">
                                <input type="text" name="categ_name_new" class="form-control" id="name">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отказ</button>
                    <button type="button" class="btn btn-primary" onclick="addCateg()">Добави</button>
                </div>
            </div>
        </div>
    </div>
                
            <?php } 
            } ?>
        </div>
    <div style="position: fixed; top:50%; left:50%; margin-left:-80px; margin-top:-80px; display:none;" id="loading"><img src="../imgs/Loading.gif"><p class="text-center"><b>Записване..</b></p></div>
</body>
</html>
