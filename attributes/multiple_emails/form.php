<?php
    defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="multiple_emails">

    <script type="text/javascript">
        var addAdditionalEmail = function(button) {
            var container = $(button).closest('.multiple_emails');
            var template = _($("script[role='add_email_address']", container).html()).template();
            var elem = $(template());
            $(button).before(elem);
        }
    </script>

    <?php 
        foreach($value as $email) {
            print $form->email( $this->field('value') . '[]', "$email");
        }
    ?>
    <button type="btn btn-primary" onclick="addAdditionalEmail(this); return false;">+</button>

    <script type="text/template" role="add_email_address">
        <?php print $form->email( $this->field('value') . '[]', ''); ?>
    </script>
    <style>
        .multiple_emails > input {
            margin-bottom: 5px;
        }
    </style>

</div>
