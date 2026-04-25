<div class="card-panel admin-topbar">
    <div class="admin-topbar-leading">
        <?php if (!empty($breadcrumbs) && is_array($breadcrumbs)) : ?>
            <nav class="breadcrumb has-succeeds-separator is-small mb-2" aria-label="breadcrumbs">
                <ul>
                    <?php foreach ($breadcrumbs as $index => $breadcrumb) : ?>
                        <li class="<?= ($index === count($breadcrumbs) - 1) ? 'is-active' : '' ?>">
                            <a href="<?= $breadcrumb['url'] ?? '#' ?>" class="<?= ($index === count($breadcrumbs) - 1) ? 'has-text-grey' : 'has-text-primary' ?>">
                                <?= esc($breadcrumb['title']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>

        <h1><?= esc($pageTitle ?? 'Dashboard') ?></h1>
        <?php if (!empty($pageDescription)) : ?>
            <p class="is-size-7-mobile"><?= esc($pageDescription) ?></p>
        <?php endif; ?>
    </div>

    <?php if (isset($hasAction) && $hasAction === true) : ?>
        <div class="admin-topbar-actions">
            <?php if (isset($isActionModal) && $isActionModal === true) : ?>
                <button
                    type="button"
                    class="button is-link"
                    @click="$dispatch('open-modal')">
                    <span class="icon">
                        <i class="fa-solid fa-circle-plus"></i>
                    </span>
                    <span>Tambah Data</span>
                </button>
            <?php else : ?>
                <a href="<?= esc($actionUrl ?? '#') ?>" class="button is-link">
                    <span class="icon">
                        <i class="fa-solid fa-circle-plus"></i>
                    </span>
                    <span>Tambah Data</span>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>