<?php

use AmlaCameroun\FPMatchSimple\Core\Identity;
use AmlaCameroun\FPMatchSimple\Core\FPServerAPI;

require '../vendor/autoload.php';
$env = require 'env.php';


if (empty($_POST)) $responseStr = "";
else {
    // FP Server API settings
    $configs = [
        'base_url' => $env['fp_server_base_url'],
        'auth_key' => $env['auth_token'],
        'time_out' => 10,
        'cert_path' => '',
    ];
    FPServerAPI::setConfigs($configs);

    $data = $_POST['data'];

    switch ($_POST['operation']) {

        case 'ADD_USER':
            $data = json_decode($data, true);
            $identity = new Identity($data['id'], $data['fps']);
            $identity->synchronize();
            $responseStr = sprintf('status=SUCCESS time=%ss', $identity->getSyncTime());
            break;

        case 'ADD_USER_MANY':
            $data = json_decode($data, true);
            $identities = [];
            foreach ($data as $item) {
                $identity = new Identity($item['id'], $item['fps']);
                array_push($identities, $identity);
            }
            $time = Identity::synchronizeMultiples($identities);
            $responseStr = sprintf('status=SUCCESS time=%ss', $time);
            break;

        case 'FIND_USER':
            $result = Identity::find($data);
            if ($result->getId() === null) {
                $responseStr = sprintf('status=NOT FOUND time=%ss', $result->getTime());
            } else {
                $responseStr = sprintf('status=SUCCESS time=%ss userID=%s matching_percentage=%s%%', $result->getTime(), $result->getId(), $result->getPercentage());
            }

            break;

        case 'FORGET_USER':
            $data = json_decode($data, true);
            $time = Identity::forget($data['id']);
            $responseStr = sprintf('status=SUCCESS time=%ss', $time);
            break;

        case 'CLEAR_DB':
            $data = json_decode($data, true);
            $time = Identity::clearDB();
            $responseStr = sprintf('status=SUCCESS time=%ss', $time);
            break;
    }
}


?>
<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>FP MATCH SIMPLE WEB TEST</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body>
    <div class="row p-5">
        <div class="col-12">
            <div class="row m-5">
                <div class="col-12 text-center">
                    <div class="d-inline">
                        <button class="btn btn-success action-btn" data-operation="ADD_USER">Add a user</button>
                    </div>
                    <div class="d-inline">
                        <button class="btn btn-success action-btn" data-operation="ADD_USER_MANY">Add multiple users</button>
                    </div>
                    <div class="d-inline">
                        <button class="btn btn-primary action-btn" data-operation="FIND_USER">Search user</button>
                    </div>
                    <div class="d-inline">
                        <button class="btn btn-danger action-btn" data-operation="FORGET_USER">Forget user</button>
                    </div>
                    <div class="d-inline">
                        <button class="btn btn-danger action-btn" data-operation="CLEAR_DB">Clear database</button>
                    </div>
                </div>
            </div>
            <form id="form" method="POST">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6 mx-auto">
                                <div class="d-inline">
                                    <input type="hidden" id="input-operation" name="operation">
                                    <textarea name="data" class="form-control" id="" cols="30" rows="10"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-8 mx-auto">
                                <input type="text" class="form-control" class="disabled" id="output" value="<?= $responseStr ?>" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>


    </div>

    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        $('.action-btn').click(function() {
            $('#input-operation').val($(this).attr('data-operation'));
            $('#form').submit();
        });
    </script>
</body>

</html>