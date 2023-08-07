<?php

declare(strict_types=1);

spl_autoload_register(function ($classNamespace) {
    $classNamespace = str_replace(['\\', 'App/'], ['/', ''], $classNamespace);
    $path = 'src/' . $classNamespace . '.php';
    require_once($path);
});

require_once("src/Utils/debug.php");
$configuration = require_once("config/config.php");

use App\Controller\NoteController;
use App\Model\AbstractDatabaseModel;
use App\Request;
use App\Exception\AppException;
use App\Exception\ConfigurationException;


$request = new Request($_GET, $_POST, $_SERVER);

try {
    AbstractDatabaseModel::initConfiguration($configuration);
    $controller = new NoteController($request);
    $controller->run();
} catch (ConfigurationException $e) {
    echo '<h1> Wystąpił błąd w aplikacji </h1>';
    echo 'Wystąpił błąd z aplikacją. Proszę skontaktuj się z administratorem: XXX@XXX.com';
} catch (AppException $e) {
    echo '<h1> Wystąpił błąd w aplikacji </h1>';
    echo '<h3>' . $e->getMessage() . '</h3>';
} catch (\Throwable $e) {
    echo '<h1> Wystąpił błąd w aplikacji </h1>';
    echo $e;
}
