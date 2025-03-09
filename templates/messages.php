<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> fade-in" role="alert">
        <?php echo $_SESSION['message']['text']; unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>