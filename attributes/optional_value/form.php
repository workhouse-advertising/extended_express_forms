<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div>
    <label>
        Yes <input type="radio" name="optional_value" class="form-control" value="show" />
    </label>
    <label>
        No <input type="radio" name="optional_value" class="form-control" value="hide" />
    </label>
</div>
<div class="optional-value" style="display: block;" id="optionalValueInput">
    <?php
        $attributes = [
            // Add a placeholder
            'placeholder' => $akTextPlaceholder,
        ];
        if (isset($isRequired) && $isRequired) {
            // $attributes['required'] = 'required';
        }
        print $form->text(
            $this->field('value'),
            $value,
            $attributes
        );
    ?>
</div>
<script type="text/javascript">
    // $('input[name=optional_value]').change(function() {
    //     if ($(this).val() == 'show') {
    //         $('#optionalValueInput').show();
    //     } else {
    //         $('#optionalValueInput').hide();
    //     }
    // });
</script>