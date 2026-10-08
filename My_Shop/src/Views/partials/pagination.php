<?php
/** @var int $page @var int $pages @var string $basePath */
if ($pages <= 1) { return; }
$link = function (int $n) use ($basePath) {
    $query = $_GET;
    $query['page'] = $n;
    return url($basePath) . '?' . http_build_query($query);
};
?>
<nav class="pagination" aria-label="Pagination">
    <?php if ($page > 1): ?><a href="<?= e($link($page - 1)) ?>" aria-label="Page précédente">&lt;</a><?php endif; ?>
    <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a href="<?= e($link($i)) ?>" class="<?= $i === $page ? 'active' : '' ?>" <?= $i === $page ? 'aria-current="page"' : '' ?>><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($page < $pages): ?><a href="<?= e($link($page + 1)) ?>" aria-label="Page suivante">&gt;</a><?php endif; ?>
</nav>
