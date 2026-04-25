<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section id="posts-filter" class="card-panel mb-4"
    x-data="{
        tempFilters: { 
            title: new URLSearchParams(window.location.search).get('title') || '', 
            status: new URLSearchParams(window.location.search).get('status') || 'all', 
            startDate: new URLSearchParams(window.location.search).get('startDate') || '', 
            endDate: new URLSearchParams(window.location.search).get('endDate') || '' 
        }
    }">
    <?= $this->include('components/admin/blog-posts/filter') ?>
</section>

<section id="posts-list" class="card-panel">
    <?= $this->include('components/admin/blog-posts/tabel_blog') ?>
</section>
<?= $this->endSection() ?>