// Set theme on website load

// default theme
const defaultTheme = "dark";  // "light" | "dark"

// Check if a theme is set in local storage
const storedTheme = localStorage.getItem("theme");

// Set default theme if no theme is set
if (!storedTheme) {
  localStorage.setItem("theme", defaultTheme);
}

// Apply theme from local storage
document.documentElement.classList.toggle(
  "dark",
  localStorage.getItem("theme") === "dark"
);
// ----
