<?php

    require_once('vendor/autoload.php');

    $request = $_SERVER['REQUEST_URI'];
    $request = explode('?', $request);

    switch ($request[0])
    {
        case '/':
        case '/qrcode':
        case '/sessao':
            require_once('./index.php');
            break;

        case '/chat';
        case '/messages';

            $sessionkey = $sessionkey = $_GET['sessionkey'];
            $session = $sessionkey = $_GET['session'];

            if(!isset($sessionkey) and !isset($sessionkey))
            {
                echo '<h1>Falta dados para acessar o chat</h1>';
                exit();
            }

            require_once('./chat.php');
            break;

        default: echo '<h1>Página não encontrada</h1>';
    }
