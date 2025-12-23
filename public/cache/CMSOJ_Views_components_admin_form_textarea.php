<?php class_exists('CMSOJ\Template') or exit; ?>
<div class="form-group">
    <label for="<?php echo $id; ?>"><?php echo $label; ?></label>

    <textarea
        id="<?php echo $id; ?>"
        name="<?php echo $name; ?>"
        rows="<?php echo $rows ?? 5; ?>"
        class="form-control <?php echo $error ? 'is-invalid' : ''; ?>"
    ><?php echo $value ?? ''; ?></textarea>
    <?php if (!empty($error)): ?>
        <div class="invalid-feedback"><?php echo $error; ?></div>
    <?php endif; ?>
</div>