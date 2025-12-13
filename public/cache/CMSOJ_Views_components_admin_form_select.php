<?php class_exists('CMSOJ\Template') or exit; ?>
<div class="form-group">
    <label for="<?php echo $id; ?>"><?php echo $label; ?></label>

    <select name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="form-control">
        <?php foreach ($options as $key): ?>
            <option value="<?php echo $key; ?>" <?= $value == $key ? 'selected' : '' ?>>
                <?php echo $options[$key]; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php if ($error) : ?>
        <div class="invalid-feedback"><?php echo $error; ?></div>
    <?php endif ?>
</div>
