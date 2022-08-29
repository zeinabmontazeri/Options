<?php
require_once realpath(__DIR__ . '/vendor/autoload.php');

use Symfony\Component\Dotenv\Dotenv;

function loadEnv()
{
    $dotenv = new Dotenv();
    $dotenv->load(__DIR__ . '/.env');
    $dotenv->overload(__DIR__ . '/.env.local');
}


function extractUriData($uri)
{
    $matches = [];
    preg_match('/(?<db_type>.+):\/\/(?<credential>.+:?.+)@(?<server>.+)\/(?<db>.+)\?/', $uri, $matches);

    $credential = $matches['credential'];
    if(strpos($credential, ':')){
        $credentialDetail = explode(':', $credential);
        $uname = $credentialDetail[0];
        $pass = $credentialDetail[1];
    } else {
        $uname = $credential;
    }

    $cnnDetail = [
        'db' => $matches['db'],
        'uname' => $uname,
        'server' => $matches['server'],
        'db_type' => $matches['db_type']
    ];

    if (isset($pass)){
        $cnnDetail['pass'] = $pass;
    }

    return $cnnDetail;
}


function checkDbExistance($cnn)
{
    $connection = new PDO(
        sprintf("%s:host=%s", $cnn['db_type'], $cnn['server']),
        $cnn['uname'],
        isset($cnn['pass']) ? $cnn['pass'] : null
    );
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $connection->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =:dbname");
    $stmt->execute(array(":dbname" => $cnn['db']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $stmt->rowCount() == 1;
}

loadEnv();
$cnn = extractUriData($_ENV['DATABASE_URL']);
exit(checkDbExistance($cnn) ? 'exists' : 'notexists');
