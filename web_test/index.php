<?php
    require '../vendor/autoload.php';
    $env = require 'env.php';



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
                <div class="col-3">
                    <button class="btn btn-success">Ajouter un utilisateur</button>
                </div>
                <div class="col-3">
                    <button class="btn btn-success">Ajouter plusieurs utilisateurs</button>
                </div>
                <div class="col-3">
                    <button class="btn btn-danger">Suprimer un utilisateur</button>
                </div>
                <div class="col-3">
                    <button class="btn btn-danger">Suprimer plusieurs utilisateurs</button>
                </div>
            </div>
            <form id="form">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6 mx-auto">
                                <input type="hidden" id="input-operation" name="operation">
                                <textarea name="data" class="form-control" id="" cols="30" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="row m-t-5">
                            <div class="col-6 mx-auto">
                                <input type="text" class="form-control" class="disabled" id="output" value="" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        

    </div>
    <?= $env['fp_server_base_url'] ?>
    <?= $env['auth_token'] ?>

    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>