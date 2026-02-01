<div class="col text-center">
    <?php if ($result): ?>
        <?php echo $result ?>
        <form method="post" class="text-center mt-3">
            <input type="hidden" name="restart_game">
            <input type="submit" class="col btn btn-outline-dark mt-2" value="Nuevo juego">
        </form>
    <?php else: ?>
        <h2 class="display-5 my-3">Introduce una letra</h2>
        <?php if ($alert): ?>
            <div class="alert alert-warning mt-3 text-center fw-bold" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $alert ?>
            </div>
        <?php endif ?>
        <?php include "$viewsDirectory/inGame/gameMenu.html" ?>
    <?php endif ?>  
</div>