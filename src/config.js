window.ConsentBannerConfig = {
  text: "We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.",
  image: "./images/placeholder.png",

  colors: {
    // Banner
    bannerBg: "#f8f9fa",
    bannerText: "#000000",

    // Botones principales (los 3 del banner)
    buttonPrimaryBg: "#28a745",        // Allow All
    buttonPrimaryText: "#ffffff",
    buttonSecondaryBg: "#f2f2f2",      // Deny All / Allow Selection
    buttonSecondaryText: "#000000",

    // Hover (nuevo)
    buttonHoverBg: "#218838",          // hover en Allow All
    buttonHoverText: "#ffffff",
    buttonSecondaryHoverBg: "#e0e0e0", // hover en secundarios
    buttonSecondaryHoverText: "#000000",

    // Botones de selección individuales (4 switches)
    switchOnBg: "#28a745",
    switchOffBg: "#ccc",

    // Icono minimizado
    iconBg: "#28a745",

    // Botón de cierre (X)
    closeBtnBg: "#f2f2f2",
    closeBtnColor: "#000000",
    closeBtnHoverBg: "#e0e0e0",
    closeBtnHoverColor: "#000000"
  },

  // Tamaños de fuente personalizables
  fontSizes: {
    bannerText: "16px",
    optionText: "15px",
    buttonText: "14px"
  },

  // Posición e ícono
  iconPosition: "left",
  showMinimizedIcon: true
};