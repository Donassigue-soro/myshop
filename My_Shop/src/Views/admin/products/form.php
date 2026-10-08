<?php \WecodeGuy\ProjetMyShop\Core\View::partial('partials/admin_nav', ['section' => $section]); ?>
<main class="page narrow">
    <h1><?= $isEdit ? 'Modifier : ' . e($product['name']) : 'Nouveau produit' ?></h1>

    <form method="post" enctype="multipart/form-data" class="form"
          action="<?= $isEdit ? url('/admin/products/' . (int)$product['id']) : url('/admin/products') ?>" novalidate>
        <?= csrf_field() ?>

        <label for="name">Nom</label>
        <input type="text" id="name" name="name" value="<?= e($product['name']) ?>" required maxlength="150">
        <span class="errors"><?= e($errors['name'] ?? '') ?></span>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="5" maxlength="5000"><?= e($product['description']) ?></textarea>
        <span class="errors"><?= e($errors['description'] ?? '') ?></span>

        <label for="price">Prix</label>
        <input type="number" id="price" name="price" value="<?= e($product['price']) ?>" min="0" step="0.01" required>
        <span class="errors"><?= e($errors['price'] ?? '') ?></span>

        <label for="category_id">Catégorie</label>
        <select id="category_id" name="category_id">
            <option value="0">— Aucune —</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= (int)($product['category_id'] ?? 0) === (int)$c['id'] ? 'selected' : '' ?>>
                    <?= $c['parent_id'] ? '— ' : '' ?><?= e($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="errors"><?= e($errors['category_id'] ?? '') ?></span>

        <?php if (!empty($product['image_path'])): ?>
            <label>Image actuelle</label>
            <img src="<?= e(product_image($product['image_path'])) ?>" alt="" class="thumb thumb-lg">
        <?php endif; ?>
        <label for="image"><?= $isEdit ? 'Remplacer l\'image' : 'Image' ?> <small>(JPEG, PNG, GIF, WebP — 2 Mo max)</small></label>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
        <span class="errors"><?= e($errors['image'] ?? '') ?></span>

        <div class="actions">
            <a href="<?= url('/admin/products') ?>" class="btn btn-outline">Annuler</a>
            <button type="submit" class="btn">Enregistrer</button>
        </div>
    </form>
</main>
