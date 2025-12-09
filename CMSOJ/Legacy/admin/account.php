<?php
include 'main.php';
// Default account values
$account = [
    'email' => '',
    'password' => '',
    'role' => 'Member',
    'display_name' => '',
];
// Roles list
$roles_list = ['Admin', 'Member'];
// Check if the ID param exists
if (isset($_GET['id'])) {
    // Retrieve the account from the database
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing account
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Check to see if email already exists
        $stmt = $pdo->prepare('SELECT id FROM accounts WHERE email = ? AND email != ?');
        $stmt->execute([ $_POST['email'], $account['email'] ]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $error_msg = 'Email already exists!';
        }
        // Update the account
        if (!isset($error_msg)) {
            // Update the account
            $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $account['password'];
            $stmt = $pdo->prepare('UPDATE accounts SET email = ?, password = ?, display_name = ?, role = ? WHERE id = ?');
            $stmt->execute([ $_POST['email'], $password, $_POST['display_name'], $_POST['role'], $_GET['id'] ]);
            header('Location: accounts.php?success_msg=2');
            exit;
        } else {
            // Save the submitted values
            $account = [
                'email' => $_POST['email'],
                'role' => $_POST['role'],
                'display_name' => $_POST['display_name']
            ];
        }
    }
    if (isset($_POST['delete'])) {
        // Redirect and delete account
        header('Location: accounts.php?delete=' . $_GET['id']);
        exit;
    }
} else {
    // Create a new account
    $page = 'Create';
    if (isset($_POST['submit'])) {
        // Check to see if email already exists
        $stmt = $pdo->prepare('SELECT id FROM accounts WHERE email = ?');
        $stmt->execute([ $_POST['email'] ]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $error_msg = 'Email already exists!';
        }
        // Insert the account
        if (!isset($error_msg)) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO accounts (email,password,display_name,role) VALUES (?,?,?,?)');
            $stmt->execute([ $_POST['email'], $password, $_POST['display_name'], $_POST['role'] ]);
            header('Location: accounts.php?success_msg=1');
            exit;
        } else {
            // Save the submitted values
            $account = [
                'email' => $_POST['email'],
                'role' => $_POST['role'],
                'display_name' => $_POST['display_name']
            ];
        }
    }
}
?>
<?=template_admin_header($page . ' Account', 'accounts', 'manage')?>

<form action="" method="post">

    <div class="content-title responsive-flex-wrap responsive-pad-bot-3">
        <h2 class="responsive-width-100"><?=$page?> Account</h2>
        <a href="accounts.php" class="btn alt mar-right-2">Cancel</a>
        <?php if ($page == 'Edit'): ?>
        <input type="submit" name="delete" value="Delete" class="btn red mar-right-2" onclick="return confirm('Are you sure you want to delete this account?')">
        <?php endif; ?>
        <input type="submit" name="submit" value="Save" class="btn">
    </div>

    <?php if (isset($error_msg)): ?>
    <div class="mar-top-4">
        <div class="msg error">
            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24V264c0 13.3-10.7 24-24 24s-24-10.7-24-24V152c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>
            <p><?=$error_msg?></p>
            <svg class="close" width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
        </div>
    </div>
    <?php endif; ?>

    <div class="content-block">
        
        <div class="form responsive-width-100">

            <label for="email"><span class="required">*</span> Email</label>
            <input id="email" type="email" name="email" placeholder="Email" value="<?=htmlspecialchars($account['email'], ENT_QUOTES)?>" required>

            <label for="password"><?=$page == 'Edit' ? 'New ' : '<span class="required">*</span> '?>Password</label>
            <input type="password" id="password" name="password" placeholder="<?=$page == 'Edit' ? 'New ' : ''?>Password" autocomplete="new-password" value=""<?=$page == 'Edit' ? '' : ' required'?>>

            <label for="display_name">Display Name</label>
            <input id="display_name" type="text" name="display_name" placeholder="Joe Bloggs" value="<?=htmlspecialchars($account['display_name'], ENT_QUOTES)?>">

            <label for="role">Role</label>
            <select id="role" name="role" style="margin-bottom: 30px;">
                <?php foreach ($roles_list as $role): ?>
                <option value="<?=$role?>"<?=$role==$account['role']?' selected':''?>><?=$role?></option>
                <?php endforeach; ?>
            </select>

        </div>
    
    </div>

</form>

<?=template_admin_footer()?>