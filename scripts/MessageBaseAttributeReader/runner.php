<?php

declare(strict_types=1);

use Common\Initializer;
use Libs\Utils;

require_once '../vendor/autoload.php';

const ROOT_DIR = __DIR__;

$logsFolder = __DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
Initializer::initSettings($logsFolder);

$uuidPackageName = $argv[1] ?? null;
$messagePath = $argv[2] ?? null;

$xpath = Utils::loadXmlAndRemoveNamespaces2(file_get_contents($messagePath . DIRECTORY_SEPARATOR . 'message.xml'));

$result = [];
$result['header_uuid'] = $xpath->query("//communication/header/@uid")->item(0)->nodeValue ?? null;
$result['header_created'] = $xpath->query("//communication/header/@created")->item(0)->nodeValue ?? null;
$result['source_uuid'] = $xpath->query("//communication/header/source/@uid")->item(0)->nodeValue ?? null;
$result['package_uuid'] = $uuidPackageName;

$stdout = fopen('php://stdout', 'w');
fwrite($stdout, json_encode($result, JSON_UNESCAPED_SLASHES));
