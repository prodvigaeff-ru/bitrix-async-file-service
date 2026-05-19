<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arResult */

$statusLabels = [
    'pending'    => ['label' => 'Ожидает',        'class' => 'fp-badge--pending'],
    'processing' => ['label' => 'Обрабатывается', 'class' => 'fp-badge--processing'],
    'done'       => ['label' => 'Готово',          'class' => 'fp-badge--done'],
    'error'      => ['label' => 'Ошибка',          'class' => 'fp-badge--error'],
];
?>
<div class="fp-list">
    <div class="fp-list__header">
        <h1>Список файлов</h1>
        <a href="/pages/upload/" class="fp-btn fp-btn--primary">+ Загрузить файл</a>
    </div>

    <?php if (empty($arResult['FILES'])): ?>
        <p class="fp-empty">Файлы ещё не загружались.</p>
    <?php else: ?>
        <table class="fp-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя файла</th>
                    <th>Статус</th>
                    <th>Дата загрузки</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($arResult['FILES'] as $file):
                    $status = $statusLabels[$file['STATUS']] ?? ['label' => $file['STATUS'], 'class' => ''];
                ?>
                    <tr>
                        <td><?= (int)$file['ID'] ?></td>
                        <td><?= htmlspecialchars((string)$file['ORIGINAL_NAME']) ?></td>
                        <td>
                            <span class="fp-badge <?= $status['class'] ?>"><?= $status['label'] ?></span>
                            <?php if ($file['STATUS'] === 'error' && $file['ERROR_MESSAGE']): ?>
                                <small class="fp-error-msg"><?= htmlspecialchars((string)$file['ERROR_MESSAGE']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars((string)$file['CREATED_AT']) ?></td>
                        <td>
                            <?php if ($file['DOWNLOAD_URL']): ?>
                                <a href="<?= htmlspecialchars($file['DOWNLOAD_URL']) ?>" download class="fp-link">Скачать</a>
                            <?php else: ?>
                                <span class="fp-na">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<style>
.fp-list { max-width: 960px; margin: 40px auto; font-family: sans-serif; }
.fp-list__header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.fp-table { width: 100%; border-collapse: collapse; }
.fp-table th, .fp-table td { padding: 10px 14px; border: 1px solid #e2e8f0; text-align: left; }
.fp-table th { background: #f7fafc; font-weight: 600; }
.fp-table tr:hover { background: #f7fafc; }
.fp-badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
.fp-badge--pending    { background: #fef3c7; color: #92400e; }
.fp-badge--processing { background: #dbeafe; color: #1e40af; }
.fp-badge--done       { background: #d1fae5; color: #065f46; }
.fp-badge--error      { background: #fee2e2; color: #991b1b; }
.fp-error-msg { display: block; font-size: 11px; color: #ef4444; margin-top: 2px; }
.fp-btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; }
.fp-btn--primary { background: #4299e1; color: #fff; }
.fp-btn--primary:hover { background: #3182ce; }
.fp-empty { color: #718096; }
.fp-na { color: #a0aec0; }
.fp-link { color: #4299e1; text-decoration: none; }
.fp-link:hover { text-decoration: underline; }
</style>
