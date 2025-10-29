(function(window, document){

  const consentConfig = {
    text: "We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.",
    image: window.location.origin + "/wp-content/plugins/wp-consent-banner/dist/cookie_100.png",
    colors: {
      bannerBg: "#f8f9fa",
      bannerText: "#000000",
      buttonPrimaryBg: "#28a745",
      buttonPrimaryText: "#ffffff",
      buttonSecondaryBg: "#f2f2f2",
      buttonSecondaryText: "#000000",
      iconBg: "#28a745",
      closeBtnBg: "#f2f2f2",
      closeBtnText: "#333333",
      // nuevas propiedades (hover + switches)
      buttonHoverBg: "#218838",
      buttonHoverText: "#ffffff",
      buttonSecondaryHoverBg: "#e0e0e0",
      buttonSecondaryHoverText: "#000000",
      switchOnBg: "#28a745",
      switchOffBg: "#cccccc"
    },
    iconPosition: "left",
    showMinimizedIcon: true,
    ui: {
      closeBtnHoverOpacity: 0.85,
      buttonHoverOpacity: 0.9
    },
    fontSizes: {
      bannerText: "16px",
      optionText: "14px",
      buttonText: "15px"
    },
    labels: {
      necessary: "Necessary",
      preferences: "Preferences",
      statistics: "Statistics",
      marketing: "Marketing"
    },
    buttonTexts: {
      denyAll: "Deny All",
      allowSelection: "Allow Selection",
      allowAll: "Allow All"
    }
  };

  // merge shallow: window.ConsentBannerConfig may override keys
  const defaultConfig = window.ConsentBannerConfig || {};
  const config = Object.assign({}, consentConfig, defaultConfig);
  // merge nested objects (colors, ui, fontSizes, labels, buttonTexts)
  config.colors = Object.assign({}, consentConfig.colors, (defaultConfig.colors || {}));
  config.ui = Object.assign({}, consentConfig.ui, (defaultConfig.ui || {}));
  config.fontSizes = Object.assign({}, consentConfig.fontSizes, (defaultConfig.fontSizes || {}));
  config.labels = Object.assign({}, consentConfig.labels, (defaultConfig.labels || {}));
  config.buttonTexts = Object.assign({}, consentConfig.buttonTexts, (defaultConfig.buttonTexts || {}));

  // Global references to elements
  let consentBanner, consentOverlay, minimizedIcon;
  let closeBtn, denyAllBtn, allowSelectionBtn, allowAllBtn;
  let preferencesCheckbox, statisticsCheckbox, marketingCheckbox;

  // --- GTM integration ---
  function updateConsentToGTM() {
    // Initialize gtag if not already available
    if (typeof gtag === 'undefined') {
      window.dataLayer = window.dataLayer || [];
      window.gtag = function() { dataLayer.push(arguments); };
    }
    
    // Get current checkbox states
    const prefsChecked = preferencesCheckbox ? preferencesCheckbox.checked : false;
    const statsChecked = statisticsCheckbox ? statisticsCheckbox.checked : false;
    const marketingChecked = marketingCheckbox ? marketingCheckbox.checked : false;
    
    // Build consent object based on checkbox states
    // Necessary cookies (security_storage and functionality_storage) are always granted
    const consents = {
      functionality_storage: "granted",  // Always granted (Necessary)
      security_storage: "granted",        // Always granted (Necessary)
      personalization_storage: prefsChecked ? "granted" : "denied",  // Only Preferences controls this
      analytics_storage: statsChecked ? "granted" : "denied",
      ad_storage: marketingChecked ? "granted" : "denied",
      ad_personalization: marketingChecked ? "granted" : "denied",
      ad_user_data: marketingChecked ? "granted" : "denied",
    };
    
    // Send consent update command caller gtag
    // This automatically triggers the "Consent Update" event in GTM
    gtag('consent', 'update', consents);
  }
  
  // Initialize banner elements
  function initBanner() {
    // Check if elements already exist
    if (document.getElementById("consentBanner")) return;
    
    // Generate banner HTML and inject
    const bannerHtml = `
      <div id="consentOverlay" class="hidden"></div>
      <div id="consentBanner" class="hidden">
        <button id="closeBtn" aria-label="Close consent dialog">×</button>
        <p id="consentText">${config.text}</p>

        <div class="consentOption">
          <span>${config.labels.necessary}</span>
          <label class="switch">
            <input type="checkbox" id="necessaryCheckbox" checked disabled>
            <span class="slider round"></span>
          </label>
        </div>

        <div class="consentOption">
          <span>${config.labels.preferences}</span>
          <label class="switch">
            <input type="checkbox" id="preferencesCheckbox">
            <span class="slider round"></span>
          </label>
        </div>

        <div class="consentOption">
          <span>${config.labels.statistics}</span>
          <label class="switch">
            <input type="checkbox" id="statisticsCheckbox">
            <span class="slider round"></span>
          </label>
        </div>

        <div class="consentOption">
          <span>${config.labels.marketing}</span>
          <label class="switch">
            <input type="checkbox" id="marketingCheckbox">
            <span class="slider round"></span>
          </label>
        </div>

        <div id="bannerActions">
          <button id="denyAllBtn">${config.buttonTexts.denyAll}</button>
          <button id="allowSelectionBtn">${config.buttonTexts.allowSelection}</button>
          <button id="allowAllBtn">${config.buttonTexts.allowAll}</button>
        </div>
      </div>

      <div id="minimizedIcon" role="button" aria-label="Open consent dialog">
        <img src="${config.image}" alt="Consent Icon">
      </div>
    `;
    
    if (document.body) {
      document.body.insertAdjacentHTML('beforeend', bannerHtml);
    } else {
      document.addEventListener('DOMContentLoaded', () => {
        document.body.insertAdjacentHTML('beforeend', bannerHtml);
        grabElements();
        registerEventListeners();
        applyConfig();
        loadPreferences();
      });
      return;
    }

    grabElements();
    registerEventListeners();
    applyConfig();
    loadPreferences();
  }
  
  function grabElements() {
    // Grab elements
    consentBanner = document.getElementById("consentBanner");
    consentOverlay = document.getElementById("consentOverlay");
    minimizedIcon = document.getElementById("minimizedIcon");

    closeBtn = document.getElementById("closeBtn");
    denyAllBtn = document.getElementById("denyAllBtn");
    allowSelectionBtn = document.getElementById("allowSelectionBtn");
    allowAllBtn = document.getElementById("allowAllBtn");

    preferencesCheckbox = document.getElementById("preferencesCheckbox");
    statisticsCheckbox = document.getElementById("statisticsCheckbox");
    marketingCheckbox = document.getElementById("marketingCheckbox");
  }
  
  function registerEventListeners() {
    // Register event listeners for checkboxes
    if (preferencesCheckbox) preferencesCheckbox.addEventListener("change", updateConsentToGTM);
    if (statisticsCheckbox) statisticsCheckbox.addEventListener("change", updateConsentToGTM);
    if (marketingCheckbox) marketingCheckbox.addEventListener("change", updateConsentToGTM);
    
    if (closeBtn) closeBtn.addEventListener("click", closeBanner);
    if (minimizedIcon) minimizedIcon.addEventListener("click", showBanner);
    if (denyAllBtn) denyAllBtn.addEventListener("click", denyAll);
    if (allowSelectionBtn) allowSelectionBtn.addEventListener("click", allowSelection);
    if (allowAllBtn) allowAllBtn.addEventListener("click", allowAll);
  }
  
  // Start initialization
  if (document.body) {
    initBanner();
  } else {
    document.addEventListener('DOMContentLoaded', initBanner);
  }

  // --- apply config to CSS variables and inline values ---
  function applyConfig() {
    const consentTextEl = document.getElementById("consentText");
    if (consentTextEl) consentTextEl.innerText = config.text;
    
    if (minimizedIcon) {
      const imgEl = minimizedIcon.querySelector("img");
      if (imgEl) imgEl.src = config.image;
    }

    // base colors
    consentBanner.style.setProperty('--banner-bg', config.colors.bannerBg || '#f8f9fa');
    consentBanner.style.setProperty('--banner-text', config.colors.bannerText || '#000000');

    consentBanner.style.setProperty('--button-primary-bg', config.colors.buttonPrimaryBg || '#28a745');
    consentBanner.style.setProperty('--button-primary-text', config.colors.buttonPrimaryText || '#ffffff');

    consentBanner.style.setProperty('--button-secondary-bg', config.colors.buttonSecondaryBg || '#f2f2f2');
    consentBanner.style.setProperty('--button-secondary-text', config.colors.buttonSecondaryText || '#000000');

    // new hover + switch vars
    consentBanner.style.setProperty('--button-primary-hover-bg', config.colors.buttonHoverBg || '#218838');
    consentBanner.style.setProperty('--button-primary-hover-text', config.colors.buttonHoverText || '#ffffff');
    consentBanner.style.setProperty('--button-secondary-hover-bg', config.colors.buttonSecondaryHoverBg || '#e0e0e0');
    consentBanner.style.setProperty('--button-secondary-hover-text', config.colors.buttonSecondaryHoverText || '#000000');
    consentBanner.style.setProperty('--switch-on-bg', config.colors.switchOnBg || '#28a745');
    consentBanner.style.setProperty('--switch-off-bg', config.colors.switchOffBg || '#cccccc');

    // close button
    consentBanner.style.setProperty('--close-btn-bg', config.colors.closeBtnBg || '#f2f2f2');
    consentBanner.style.setProperty('--close-btn-text', config.colors.closeBtnText || '#333333');

    consentBanner.style.setProperty('--icon-bg', config.colors.iconBg || '#28a745');

    // Note: hover opacities are defined in CSS, not needed as CSS variables

    // font sizes
    consentBanner.style.setProperty('--banner-font-size', config.fontSizes.bannerText || '16px');
    consentBanner.style.setProperty('--option-font-size', config.fontSizes.optionText || '14px');
    consentBanner.style.setProperty('--button-font-size', config.fontSizes.buttonText || '15px');

    // icon position and visibility
    if (minimizedIcon) {
      if (config.iconPosition === "right") {
        minimizedIcon.style.left = "auto";
        minimizedIcon.style.right = "20px";
      } else {
        minimizedIcon.style.left = "20px";
        minimizedIcon.style.right = "auto";
      }

      if (!config.showMinimizedIcon) {
        minimizedIcon.style.display = "none";
      }
    }
  }

  // --- UI functions ---
  function showBanner() {
    consentBanner.classList.remove("hidden");
    consentOverlay.style.display = "block";
    minimizedIcon.style.display = "none";
  }

  function closeBanner() {
    savePreferences();
    updateConsentToGTM();
    consentBanner.classList.add("hidden");
    consentOverlay.style.display = "none";
    minimizedIcon.style.display = config.showMinimizedIcon ? "block" : "none";
  }

  function denyAll() {
    preferencesCheckbox.checked = false;
    statisticsCheckbox.checked = false;
    marketingCheckbox.checked = false;
    closeBanner();
  }

  function allowSelection() {
    preferencesCheckbox.disabled = false;
    statisticsCheckbox.disabled = false;
    marketingCheckbox.disabled = false;
    closeBanner();
  }

  function allowAll() {
    preferencesCheckbox.checked = true;
    statisticsCheckbox.checked = true;
    marketingCheckbox.checked = true;
    preferencesCheckbox.disabled = false;
    statisticsCheckbox.disabled = false;
    marketingCheckbox.disabled = false;
    closeBanner();
  }

  function savePreferences() {
    const prefs = {
      preferences: preferencesCheckbox.checked,
      statistics: statisticsCheckbox.checked,
      marketing: marketingCheckbox.checked,
    };
    localStorage.setItem("userPreferences", JSON.stringify(prefs));
  }

  function loadPreferences() {
    const saved = localStorage.getItem("userPreferences");
    if (saved) {
      try {
        const prefs = JSON.parse(saved);
        preferencesCheckbox.checked = !!prefs.preferences;
        statisticsCheckbox.checked = !!prefs.statistics;
        marketingCheckbox.checked = !!prefs.marketing;
      } catch(e) {}
      consentBanner.classList.add("hidden");
      consentOverlay.style.display = "none";
      minimizedIcon.style.display = config.showMinimizedIcon ? "block" : "none";
      updateConsentToGTM();
    } else {
      showBanner();
    }
  }

  window.WPConsentBannerLoaded = true;

})(window, document);