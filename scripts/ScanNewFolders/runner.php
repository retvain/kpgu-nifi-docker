<?php

declare(strict_types=1);

use Common\Initializer;
use Libs\FolderChange;
use Libs\Semaphore;
use Voskhod\TechExtensions\Common\Folder;

require_once '../vendor/autoload.php';

const ROOT_DIR = __DIR__;

$logsFolder = __DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
Initializer::initSettings($logsFolder);

$uuidPackageName = $argv[1];
$inFolder = $argv[2];
$archiveInFolderPath = $argv[3];
$processingFolderPath = $argv[4];


$folderChange = new FolderChange(
    $inFolder,
    5,
    1
);

$folderChange->setWaitFileExists(['*.ltr']);
$folderChange->refreshData();

$readyFolders = $folderChange->getReadyFolders();
$md5Hash = array_key_first($readyFolders);
$readyFolderForProcess = $readyFolders[$md5Hash];

foreach ($readyFolders as $md5 => $folder) {
    $source = $inFolder . DIRECTORY_SEPARATOR . $folder;
    $mutex = new Semaphore($md5, $source);
    if ($mutex->lock()) {
        $result = [];
        try {
            $destinationArchiveIn = $archiveInFolderPath . DIRECTORY_SEPARATOR . $uuidPackageName;
            $destinationProcessing = $processingFolderPath . DIRECTORY_SEPARATOR . $uuidPackageName;
            Folder::copy($source, $destinationArchiveIn);
            Folder::move($source, $destinationProcessing);

            $result['success'] = true;
            $result['message_name'] = $uuidPackageName;
            $result['md5_hash'] = $md5;
            $result['package_uuid'] = $uuidPackageName;
        } catch (Throwable $e) {
            $result['success'] = false;
            $result['error'] = $e->getMessage();
        }

        $stdout = fopen('php://stdout', 'w');
        fwrite($stdout, json_encode($result, JSON_UNESCAPED_SLASHES));

        return;
    }
}
