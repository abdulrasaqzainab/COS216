<footer>
    <p>&copy; <?php echo date("Y"); ?> McAlister's Listing. All rights reserved.</p>
</footer>
<button onclick="toggleTheme()">Change Theme</button>

<script>
    // Function to toggle between light and dark themes
    function toggleTheme() {
        var stylesheet = document.getElementById('themeStylesheet');
        var currentTheme = stylesheet.getAttribute('href');
        var newTheme = currentTheme === 'css/mycss.css' ? 'css/dark.css' : 'css/mycss.css';
        stylesheet.setAttribute('href', newTheme);
        // Store theme preference in localStorage
        localStorage.setItem('theme', newTheme);
    }

    // Check if theme preference is stored and apply it
    var theme = localStorage.getItem('theme');
    if (theme) {
        var stylesheet = document.getElementById('themeStylesheet');
        stylesheet.setAttribute('href', theme);
    }
</script>
</body>
</html>


