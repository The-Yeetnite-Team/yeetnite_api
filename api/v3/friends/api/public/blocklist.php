<?php
require_once 'database.php';

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo getBlockList($_GET['accountId'], $database);
        break;
    case 'POST':
        // block user
        $blockList = json_decode(getBlockList($_GET['accountId'], $database), true);
        // we don't want duplicates
        if (!in_array($_GET['blocking'], $blockList)) {
            $blockList[] = $_GET['blocking'];
        }
        updateBlocklist($_GET['accountId'], json_encode($blockList), $database);
        // TODO unfriend from database and XMPP server
        break;
    case 'DELETE':
        // unblock user
        $blockList = json_decode(getBlockList($_GET['accountId'], $database), true);
        unset($blockList[array_search($_GET['blocking'], $blockList)]);
        updateBlocklist($_GET['accountId'], json_encode($blockList), $database);
        http_response_code(204);
        break;
}

function getBlockList(string $username, Database $database): string
{
    return $database->select(array('blockList'), 'users', "WHERE username = '$username'")[0]['blockList'];
}

function updateBlocklist(string $username, string $blockList, Database $database): void
{
    $database->update('users', array('blockList'), array($blockList), "WHERE username = '$username'");
}