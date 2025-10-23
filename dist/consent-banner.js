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
      /* tertiary/quaternary fallback to secondary if not provided */
      buttonTertiaryBg: null,
      buttonTertiaryText: null,
      buttonQuaternaryBg: null,
      buttonQuaternaryText: null,
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
    }
  };

  // merge shallow: window.ConsentBannerConfig may override keys
  const defaultConfig = window.ConsentBannerConfig || {};
  const config = Object.assign({}, consentConfig, defaultConfig);
  // merge nested objects (colors, ui, fontSizes)
  config.colors = Object.assign({}, consentConfig.colors, (defaultConfig.colors || {}));
  config.ui = Object.assign({}, consentConfig.ui, (defaultConfig.ui || {}));
  config.fontSizes = Object.assign({}, consentConfig.fontSizes, (defaultConfig.fontSizes || {}));

  // generate banner HTML and inject
  const bannerHtml = `
    <div id="consentOverlay" class="hidden"></div>
    <div id="consentBanner" class="hidden">
      <button id="closeBtn" aria-label="Close consent dialog">×</button>
      <p id="consentText">${config.text}</p>

      <div class="consentOption">
        <span>Necessary</span>
        <label class="switch">
          <input type="checkbox" id="necessaryCheckbox" checked disabled>
          <span class="slider round"></span>
        </label>
      </div>

      <div class="consentOption">
        <span>Preferences</span>
        <label class="switch">
          <input type="checkbox" id="preferencesCheckbox">
          <span class="slider round"></span>
        </label>
      </div>

      <div class="consentOption">
        <span>Statistics</span>
        <label class="switch">
          <input type="checkbox" id="statisticsCheckbox">
          <span class="slider round"></span>
        </label>
      </div>

      <div class="consentOption">
        <span>Marketing</span>
        <label class="switch">
          <input type="checkbox" id="marketingCheckbox">
          <span class="slider round"></span>
        </label>
      </div>

      <div id="bannerActions">
        <button id="denyAllBtn">Deny All</button>
        <button id="allowSelectionBtn">Allow Selection</button>
        <button id="allowAllBtn">Allow All</button>
      </div>
    </div>

    <div id="minimizedIcon" role="button" aria-label="Open consent dialog">
      <img src="${config.image}" alt="Consent Icon">
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', bannerHtml);

  // grab elements
  const consentBanner = document.getElementById("consentBanner");
  const consentOverlay = document.getElementById("consentOverlay");
  const minimizedIcon = document.getElementById("minimizedIcon");

  const closeBtn = document.getElementById("closeBtn");
  const denyAllBtn = document.getElementById("denyAllBtn");
  const allowSelectionBtn = document.getElementById("allowSelectionBtn");
  const allowAllBtn = document.getElementById("allowAllBtn");

  const preferencesCheckbox = document.getElementById("preferencesCheckbox");
  const statisticsCheckbox = document.getElementById("statisticsCheckbox");
  const marketingCheckbox = document.getElementById("marketingCheckbox");

  // --- GTM integration ---
  function updateConsentToGTM() {
    const consents = {
      functionality_storage: preferencesCheckbox.checked ? "granted" : "denied",
      personalization_storage: (preferencesCheckbox.checked || marketingCheckbox.checked) ? "granted" : "denied",
      analytics_storage: statisticsCheckbox.checked ? "granted" : "denied",
      ad_storage: marketingCheckbox.checked ? "granted" : "denied",
      ad_personalization: marketingCheckbox.checked ? "granted" : "denied",
      ad_user_data: marketingCheckbox.checked ? "granted" : "denied",
      security_storage: marketingCheckbox.checked ? "granted" : "denied",
    };
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
      event: "gtm_consent_update",
      consents: consents
    });
  }

  // --- apply config to CSS variables and inline values ---
  function applyConfig() {
    const consentTextEl = document.getElementById("consentText");
    if (consentTextEl) consentTextEl.innerText = config.text;
    const imgEl = minimizedIcon.querySelector("img");
    if (imgEl) imgEl.src = config.image;

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

    // tertiary/quaternary
    consentBanner.style.setProperty('--button-tertiary-bg', config.colors.buttonTertiaryBg || config.colors.buttonSecondaryBg || '#f2f2f2');
    consentBanner.style.setProperty('--button-tertiary-text', config.colors.buttonTertiaryText || config.colors.buttonSecondaryText || '#000000');
    consentBanner.style.setProperty('--button-quaternary-bg', config.colors.buttonQuaternaryBg || config.colors.buttonSecondaryBg || '#f2f2f2');
    consentBanner.style.setProperty('--button-quaternary-text', config.colors.buttonQuaternaryText || config.colors.buttonSecondaryText || '#000000');

    // close button
    consentBanner.style.setProperty('--close-btn-bg', config.colors.closeBtnBg || '#f2f2f2');
    consentBanner.style.setProperty('--close-btn-text', config.colors.closeBtnText || '#333333');

    consentBanner.style.setProperty('--icon-bg', config.colors.iconBg || '#28a745');

    // hover opacities
    consentBanner.style.setProperty('--button-hover-opacity', (config.ui && config.ui.buttonHoverOpacity) ? String(config.ui.buttonHoverOpacity) : '0.9');
    consentBanner.style.setProperty('--close-hover-opacity', (config.ui && config.ui.closeBtnHoverOpacity) ? String(config.ui.closeBtnHoverOpacity) : '0.85');

    // font sizes
    consentBanner.style.setProperty('--banner-font-size', config.fontSizes.bannerText || '16px');
    consentBanner.style.setProperty('--option-font-size', config.fontSizes.optionText || '14px');
    consentBanner.style.setProperty('--button-font-size', config.fontSizes.buttonText || '15px');

    // icon position
    if (config.iconPosition === "right") {
      minimizedIcon.style.left = "auto";
      minimizedIcon.style.right = "20px";
    } else {
      minimizedIcon.style.left = "20px";
      minimizedIcon.style.right = "auto";
    }

    // show/hide minimized icon
    if (!config.showMinimizedIcon) {
      minimizedIcon.style.display = "none";
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

  // --- events ---
  closeBtn.addEventListener("click", closeBanner);
  minimizedIcon.addEventListener("click", showBanner);
  denyAllBtn.addEventListener("click", denyAll);
  allowSelectionBtn.addEventListener("click", allowSelection);
  allowAllBtn.addEventListener("click", allowAll);
  preferencesCheckbox.addEventListener("change", updateConsentToGTM);
  statisticsCheckbox.addEventListener("change", updateConsentToGTM);
  marketingCheckbox.addEventListener("change", updateConsentToGTM);

  document.addEventListener("DOMContentLoaded", () => {
    applyConfig();
    loadPreferences();
  });

  window.WPConsentBannerInit = function(){
    try {
      applyConfig();
      loadPreferences();
    } catch(e) {}
  };

  window.WPConsentBannerLoaded = true;

})(window, document);