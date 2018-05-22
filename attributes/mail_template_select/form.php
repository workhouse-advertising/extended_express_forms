<?php
defined('C5_EXECUTE') or die("Access Denied.");
print $form->select($this->field('mailTemplateSelect'), $templates, $selected_template);

