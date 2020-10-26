<?php
require_once('autoload.php');

if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

try {
    $productModel = new ProductModel();
    $productList = $productModel->fetchAllData();
    $productJson = json_encode($productList);
} catch (PDOException $e) {
    echo $e->getMessage();
}

if (isset($_GET['delete_id'])) {
    $productModel->delete($_GET['delete_id']);
    header('Location: product_list.php');
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品リスト</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/css/theme.default.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/product_list.css">
    <link rel="stylesheet" href="../css/button.css">
    <link rel="stylesheet" href="../css/util.css">
</head>

<body>
    <div class="container">
            <?php include('header.html') ?>
            <?php include('secondHeader.html') ?>
            <main> <?php getPage('商品リスト') ?>
            <div class="search"><input type="text" id="search"> <input type="button" value="絞り込む" id="button"> <input type="button" value="すべて表示" id="button2"></div>
                <table class="table-bordered" id="result" style="margin: 0 auto;">
                    <thead class="thead-right">
                        <tr>
                            <th scope="col"><span style="font-size: 17px;">ID</span></th>
                            <th scope="col"><span style="font-size: 17px;">商品名</span></th>
                            <th scope="col"><span style="font-size: 17px;">画像</span></th>
                            <th scope="col"><span style="font-size: 17px;">登録日時</span></th>
                            <th scope="col"><span style="font-size: 17px;">更新日時</span></th>
                            <th scope="col"><a href="product_edit.php?new=true" role="button" class="btn btn-sm">新規登録</a></th>
                        </tr>
                    </thead>
                    <tbody> <?php foreach ($productList as $product) : ?> <tr>
                                <td><?= h($product['id']) ?></td>
                                <td><?= h($product['name']) ?></td>
                                <td><img src="../<?= IMG_PATH . h($product['img']) ?>" alt="<?= h($product['img']) ?>"></td>
                                <td><?= h($product['created_at']) ?></td>
                                <td><?= h($product['updated_at']) ?></td>
                                <td><a href="product_edit.php?id=<?= h($product['id']) ?>" role="button" class="btn btn-sm" style="margin-right: 10px">編集</a><a href="#modal<?= $product['id'] ?>" rel="modal:open" role="button" class="btn btn-sm">削除</a></td>
                            </tr>
                            <div id="modal<?= $product['id'] ?>" class="modal" style="height: 100px">
                                <p>本当に削除しますか？</p> <a href="product_list.php?delete_id=<?= $product['id'] ?>" role="button" class="btn btn-sm">OK</a> <a href="" rel="modal:close" role="button" class="btn btn-sm">キャンセル</a>
                            </div> <?php endforeach; ?>
                    </tbody>
                </table>
            </main>
            <?php include('footer.html') ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
    <script type="text/javascript">
        //検索機能
        $(function() {
            $('#button').bind("click", function() {
                let re = new RegExp($('#search').val());
                $('#result tbody tr').each(function() {
                    let txt = $(this).find("td:eq(1)").html();
                    if (txt.match(re) != null) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
            $('#button2').bind("click", function() {
                $('#search').val('');
                $('#result tr').show();
            });
        });

        //ソート機能
        $(document).ready(function() {
            $('#result').tablesorter({
                headers: {
                    2: {
                        sorter: false
                    },
                    3: {
                        sorter: false
                    },
                    4: {
                        sorter: 'usLongDate'
                    },
                    5: {
                        sorter: false
                    }
                },
            });
        });
    </script>
</body>

</html>