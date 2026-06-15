document.addEventListener("DOMContentLoaded", () => { //Only run this code after the HTML page is fully loaded.
    const themeToggleBtn = document.getElementById('themeToggle'); //Find the button with id = themeToggle
    
    if (themeToggleBtn) { //Only continue if the button is actually found.
        themeToggleBtn.addEventListener('click', () => { //When user clicks the button, run this function.
            const currentTheme = document.documentElement.getAttribute('data-theme'); //check this code at html (<script> const savedTheme = localStorage.getItem('theme') || 'light';document.documentElement.setAttribute('data-theme', savedTheme);</script>)
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark'; //currect what color then change to another color
            
            document.documentElement.setAttribute('data-theme', newTheme); //Update the HTML tag with new theme (<html data-theme="light">)
            localStorage.setItem('theme', newTheme); //Stored in memory weather what mode on now 
        });
    }
});