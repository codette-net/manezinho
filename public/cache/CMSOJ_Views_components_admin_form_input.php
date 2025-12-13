<?php class_exists('CMSOJ\Template') or exit; ?>
<div class="form-group">
    <label for="<?php echo $id; ?>"><?php echo $label; ?></label>

    <input 
        type="<?php echo $type ?? 'text'; ?>"
        id="<?php echo $id; ?>"
        name="<?php echo $name; ?>"
        value="<?php echo $value ?? ''; ?>"
        placeholder="<?php echo $placeholder ?? ''; ?>"
        class="form-control <?php echo $error ? 'is-invalid' : ''; ?>"
    >

    <?php if ($error): ?>
        <div class="invalid-feedback"><?php echo $error; ?></div>
    <?php endif ?>
</div>
