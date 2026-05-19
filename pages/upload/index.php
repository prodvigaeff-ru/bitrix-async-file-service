<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';
$APPLICATION->SetTitle('Загрузка файла');
?>

<?php $APPLICATION->IncludeComponent('fileprocessor:file.upload', '', []); ?>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'; ?>
