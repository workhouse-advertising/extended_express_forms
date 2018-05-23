<?php
defined('C5_EXECUTE') or die("Access Denied.");
print $form->select($this->field('expressFormSelect'), $forms, $selected_form);
?>

<br />
<label>Available attributes in form:</label>

<?php foreach($form_fields as $formId => $formFieldGroup) : ?>
<textarea disabled style="width: 100%; resize: none" class="variableFormFields" id="<?= $this->field('expressFormSelect') ?>['fields']['<?= $formId ?>']" rows="<?= count($formFieldGroup) ?>">
<?php foreach($formFieldGroup as $field): ?>
{{<?= $field ?>}}<?= $field !== end($formFieldGroup) ? PHP_EOL : "" ?>
<?php endforeach; ?>
</textarea>
<?php endforeach; ?>

<script type="text/javascript">

function showFormFieldsMatchingSelectedForm(formId) {
    $('.variableFormFields').hide();
    $(document.getElementById("<?= $this->field('expressFormSelect') ?>['fields']['"+formId+"']")).show();
}

document.getElementById("<?= $this->field('expressFormSelect') ?>").addEventListener('change', function(event) {
    showFormFieldsMatchingSelectedForm(event.target.value);
});

showFormFieldsMatchingSelectedForm(document.getElementById("<?= $this->field('expressFormSelect') ?>").value);

</script>
