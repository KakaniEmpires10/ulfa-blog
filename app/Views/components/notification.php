<?php
$variant = $variant ?? 'info';
$icon    = $icon ?? null;
$title   = $title ?? null;
$message = $message ?? null;
$messages = $messages ?? [];

if ($message !== null && $message !== '') {
    $messages = array_merge([$message], (array) $messages);
}

$messages = array_values(array_filter(array_map(static fn ($item) => trim((string) $item), (array) $messages)));

if ($messages === []) {
    return;
}

$variantClass = match ($variant) {
    'success' => 'is-success',
    'danger', 'error' => 'is-danger',
    'warning' => 'is-warning',
    default => 'is-info',
};
?>

<div class="notification material-notification <?= esc($variantClass) ?>" role="alert">
    <?php if ($icon !== null && $icon !== '') : ?>
        <div class="notification-icon" aria-hidden="true">
            <i class="<?= esc($icon) ?>"></i>
        </div>
    <?php endif; ?>

    <div class="notification-content">
        <?php if ($title !== null && $title !== '') : ?>
            <p class="notification-title"><?= esc($title) ?></p>
        <?php endif; ?>

        <?php if (count($messages) === 1) : ?>
            <p class="notification-message"><?= esc($messages[0]) ?></p>
        <?php else : ?>
            <ul class="notification-list">
                <?php foreach ($messages as $item) : ?>
                    <li><?= esc($item) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
