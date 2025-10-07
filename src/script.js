window.onload = function() {
    const input = document.querySelector('input[name="letter"]');
    if (input) { 
        input.focus();
    } else {
        const input = document.querySelector('a[href="reset.php"]');
        if (input) input.focus();
    }
};