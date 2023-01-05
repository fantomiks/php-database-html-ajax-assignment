<?php

require '../vendor/autoload.php';

$config = \App\Core\Config::getInstance();
$config->setConfig(
    '{"host": "db", "dbname": "test", "username":"test", "password":"test"}'
);

$database = \App\Core\Database::getInstance();
$userRepo = new \App\Repositories\UserRepository($database);
$userMapper = new \App\Mappers\UserMapper();
$userService = new \App\Services\UserService($userRepo, $userMapper);

if (!empty($_GET['user_id']) && !empty($_GET['action']) && $_GET['action'] === 'bump') {
    $user = $userService->bumpAccessCount($_GET['user_id']);
    echo $userMapper->toJson($user);
    exit;
}

?>

<html lang="en"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/docs/4.1/assets/img/favicons/favicon.ico">

    <title>Users</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" />
</head>
<body>
<div class="container">
    <table class="table">
        <thead>
        <tr>
            <th scope="col">User ID</th>
            <th scope="col">Name</th>
            <th scope="col">Access count</th>
            <th scope="col">Modify Datetime</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($userService->getUsers() as $user) : ?>
        <tr>
            <th scope="row"><?= $user->getUserId() ?></th>
            <td><?= $user->getName() ?></td>
            <td><?= $user->getAccessCount() ?></td>
            <td><?= $user->getModifyDt() ?></td>
            <td><button type="button" class="btn btn-primary" data-id="<?= $user->getUserId() ?>">bump</button></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>

<script>
    function getAjax(url, success) {
        let xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        xhr.open('GET', url);
        xhr.onreadystatechange = function() {
            if (xhr.readyState > 3 && xhr.status === 200) success(xhr.responseText);
        };
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send();
        return xhr;
    }

    let user = {"user_id": 0, "name": "", "access_count": 0, "modify_dt": ""};
    let buttons = document.getElementsByClassName('btn');
    for (let i = 0; i < buttons.length; i++) {

        let btn = buttons[i];
        let userID = btn.getAttribute('data-id');

        btn.addEventListener("click", function() {
            getAjax('?action=bump&user_id=' + userID, function (data) {
                user = JSON.parse(data);
                btn.parentNode.parentNode.children[2].innerHTML = user.access_count;
                btn.parentNode.parentNode.children[3].innerHTML = user.modify_dt;
            })
        });
    }
</script>

</body>
</html>
