<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<a name="form-<?= $form->getEntity()->getHandle() ?>" class="form-anchor" id="form-recruitment-anchor"></a>

<input type="hidden" name="express_form_id" value="<?=$form->getID()?>">
<?=$token->output('express_form')?>

<div class="ccm-dashboard-express-form">
    <?php foreach ($form->getFieldSets() as $fieldSet): ?>

        <fieldset>
            <?php if ($fieldSet->getTitle()): ?>
                <legend><?= h($fieldSet->getTitle()) ?></legend>
            <?php endif; ?>

            <?php foreach($fieldSet->getControls() as $setControl): ?>
                <?php
                    $controlView = $setControl->getControlView($context);
                ?>
                <?php if (is_object($controlView)): ?>
                    <?php
                        $renderer = $controlView->getControlRenderer();
                        // $fieldErrors = $renderer->view->getContext()->getFieldErrors($setControl->getAttributeKey());
                        // Add form errors to the scope
                        //// TODO: Add other fields as required
                        // $renderer->view->addScopeItem('required', $setControl->isRequired());
                    ?>
                    <?= $renderer->render(); ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </fieldset>
    <?php endforeach; ?>
</div>
