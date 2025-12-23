<?php class_exists('CMSOJ\Template') or exit; ?>
<label class="switch">
  <input type="checkbox" name="<?php echo $name; ?>" id="<?php echo $id; ?>" <?php echo $value ? 'checked' : ''; ?>>
  <span class="slider"></span> <?php echo $label; ?>
</label>
