<p>
    <span class="text-danger"><i class="bi bi-heart-fill"></i> Intentos: <?php echo "$leftAttempts/$maxAttempts"?></span>
    <?php if ($usedLetters): ?>
        <span class="text-secondary px-2">
            <i class="bi bi-alphabet-uppercase"></i> Letras usadas: <?php echo $usedLetters ?>
        </span>
    <?php endif ?>
</p>