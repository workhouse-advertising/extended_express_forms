<?php defined('C5_EXECUTE') or die("Access Denied.");
    // Get field errors
   $fieldErrors = $context->getFieldErrors($key);
?>
<div class="form-group <?= $key->getAttributeKeyHandle(); ?>">
    <?php if ($view->supportsLabel()): ?>
        <label class="control-label"><?=$view->getLabel()?></label>
    <?php endif; ?>

    <?php if ($view->isRequired()): ?>
        <span class="text-muted small"><?=t('Required')?></span>
    <?php endif; ?>

    <?php $view->renderControl()?>

    <?php if ($fieldErrors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($fieldErrors as $fieldError): ?>
                    <li><?= $fieldError; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
