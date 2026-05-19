<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arResult */
?>
<div class="fp-upload">
    <h1>Загрузка файла</h1>

    <?php if ($arResult['ERROR']): ?>
        <div class="fp-alert fp-alert--error">
            <?= htmlspecialchars($arResult['ERROR']) ?>
        </div>
    <?php endif; ?>

    <?php if ($arResult['SUCCESS']): ?>
        <div class="fp-alert fp-alert--success">
            Файл успешно загружен и поставлен в очередь на обработку.
            <a href="/pages/files/">Перейти к списку файлов &rarr;</a>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="fp-form">
        <?= bitrix_sessid_post() ?>
        <div class="fp-form__group">
            <label for="upload_file" class="fp-form__label">Выберите файл:</label>
            <input type="file" id="upload_file" name="upload_file" class="fp-form__input" required>
        </div>
        <button type="submit" class="fp-btn fp-btn--primary">Загрузить</button>
    </form>

    <p><a href="/pages/files/">&larr; Список файлов</a></p>
</div>

<style>
.fp-upload { max-width: 600px; margin: 40px auto; font-family: sans-serif; }
.fp-alert { padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; }
.fp-alert--error   { background: #fde8e8; border: 1px solid #f56565; color: #c53030; }
.fp-alert--success { background: #e6ffed; border: 1px solid #48bb78; color: #276749; }
.fp-form__group { margin-bottom: 16px; }
.fp-form__label { display: block; margin-bottom: 6px; font-weight: 600; }
.fp-form__input { display: block; width: 100%; padding: 8px; border: 1px solid #cbd5e0; border-radius: 4px; box-sizing: border-box; }
.fp-btn { padding: 10px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
.fp-btn--primary { background: #4299e1; color: #fff; }
.fp-btn--primary:hover { background: #3182ce; }
</style>
