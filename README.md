# WP Consent Banner

Un plugin de WordPress para mostrar un banner de consentimiento de cookies con panel de configuración administrativo y compatibilidad con Google Tag Manager.

## 📋 Descripción

WP Consent Banner es un plugin completo que permite a los sitios web cumplir con las regulaciones de cookies (GDPR, CCPA, etc.) mediante un banner interactivo y personalizable. El plugin incluye un panel administrativo intuitivo para configurar todos los aspectos visuales y funcionales del banner.

## ✨ Características

### 🎨 Personalización Completa
- **Colores personalizables**: Banner, botones, switches, hover effects
- **Tamaños de fuente**: Texto del banner, opciones y botones
- **Texto personalizable**: Mensaje del banner completamente editable
- **Icono personalizable**: URL del icono del banner minimizado
- **Posición del icono**: Izquierda o derecha

### 🎛️ Panel Administrativo Mejorado
- **Layout de 2 columnas**: Reduce el scroll necesario para configurar
- **Títulos destacados**: Con estilo bold, underline y dos puntos
- **Campos de ancho completo**: Banner Text e Icon URL ocupan toda la fila
- **Organización lógica**: Campos agrupados por categorías

### 🔧 Funcionalidades Técnicas
- **Compatibilidad con GTM**: Integración automática con Google Tag Manager
- **Almacenamiento local**: Preferencias guardadas en localStorage
- **Responsive**: Diseño adaptativo para móviles y tablets
- **Accesibilidad**: Etiquetas ARIA y navegación por teclado
- **Sanitización**: Validación y limpieza de datos de entrada

## 📁 Estructura del Proyecto

```
wp-consent-banner/
├── wp-consent-banner.php    # Archivo principal del plugin
├── src/
│   └── config.js           # Configuración por defecto
├── dist/
│   ├── consent-banner.css  # Estilos del banner
│   └── consent-banner.js   # Lógica del banner
└── README.md              # Este archivo
```

## 🚀 Instalación

1. **Crear un .zip de este proyecto**
2. **Subirlo a Wordpress como un plugin nuevo y activarlo**
3. **Ir a configuración > Consent Banner para personalizar**

## ⚙️ Configuración

### Acceso al Panel Administrativo
- **Ubicación**: WordPress Admin > Configuración > Consent Banner
- **Permisos**: Requiere permisos de administrador (`manage_options`)

### Opciones Disponibles

#### 📝 Configuración Básica
- **Banner Text**: Texto principal del banner (ancho completo)
- **Customizable Text Labels**: Personaliza los textos de categorías (Necessary, Preferences, Statistics, Marketing) y botones (Deny All, Allow Selection, Allow All)
- **Icon URL**: URL del icono del banner minimizado (ancho completo)
- **Minimized Icon Position**: Posición del icono (izquierda/derecha)
- **Show Minimized Icon When Closed**: Mostrar icono cuando el banner está cerrado

#### 🎨 Colores del Banner
- **Banner Background Color**: Color de fondo del banner
- **Banner Text Color**: Color del texto del banner

#### 🔘 Colores de Botones Primarios (Allow All)
- **Primary Button Background**: Color de fondo del botón "Allow All"
- **Primary Button Text**: Color del texto del botón
- **Primary Button Hover Background**: Color de fondo al pasar el mouse
- **Primary Button Hover Text**: Color del texto al pasar el mouse

#### 🔘 Colores de Botones Secundarios (Deny/Selection)
- **Secondary Buttons Background**: Color de fondo de botones secundarios
- **Secondary Buttons Text**: Color del texto de botones secundarios
- **Secondary Buttons Hover Background**: Color de fondo hover
- **Secondary Buttons Hover Text**: Color del texto hover

#### 🔘 Colores del Botón de Cierre
- **Close Button Background**: Color de fondo del botón X
- **Close Button Text Color**: Color del texto del botón X
- **Close Button Hover Background**: Color de fondo hover
- **Close Button Hover Text Color**: Color del texto hover

#### 🔄 Colores de Switches de Selección
- **Selection Switch Active Color**: Color del switch activo
- **Selection Switch Inactive Color**: Color del switch inactivo

#### 🎯 Icono Minimizado
- **Minimized Icon Background**: Color de fondo del icono minimizado

#### 📏 Tamaños de Fuente
- **Banner Text Font Size**: Tamaño del texto del banner (ej: 16px)
- **Options Font Size**: Tamaño del texto de opciones (ej: 15px)
- **Buttons Font Size**: Tamaño del texto de botones (ej: 14px)

## 🎯 Funcionalidades del Banner

### Tipos de Cookies
El banner maneja 4 categorías de cookies:
- **Necessary**: Siempre activa (no se puede desactivar)
- **Preferences**: Preferencias del usuario
- **Statistics**: Estadísticas y análisis
- **Marketing**: Marketing y publicidad

### Botones de Acción
- **Deny All**: Rechaza todas las cookies no necesarias
- **Allow Selection**: Permite la selección manual del usuario
- **Allow All**: Acepta todas las cookies

### Estados del Banner
- **Inicial**: Se muestra al cargar la página por primera vez
- **Minimizado**: Icono flotante cuando está cerrado
- **Reabrir**: Click en el icono minimizado para reabrir

## 🔗 Integración con Google Tag Manager

### Google Consent Mode Integration
El plugin utiliza Google Consent Mode v2 para gestionar los consentimientos:

```javascript
gtag('consent', 'default', {
  'ad_storage': 'denied',
  'analytics_storage': 'denied',
  'ad_user_data': 'denied',
  'ad_personalization': 'denied',
  'functionality_storage': 'granted',
  'personalization_storage': 'denied',
  'security_storage': 'granted',
  'wait_for_update': 500
});

gtag('consent', 'update', {
  'ad_storage': 'granted/denied',
  'analytics_storage': 'granted/denied',
  'ad_user_data': 'granted/denied',
  'ad_personalization': 'granted/denied',
  'functionality_storage': 'granted',
  'personalization_storage': 'granted/denied',
  'security_storage': 'granted'
});
```

### Configuración en GTM
1. El plugin genera automáticamente el evento "Consent Update" cuando el usuario cambia sus preferencias
2. Configura tus tags para que respeten los consentimientos
3. El plugin manejará automáticamente la actualización de consentimientos
4. Necessary cookies (security_storage y functionality_storage) siempre están en "granted"

## 💾 Almacenamiento de Datos

### LocalStorage
- **Clave**: `userPreferences`
- **Formato**: JSON con las preferencias del usuario
- **Duración**: Persiste hasta que el usuario borre los datos del navegador

### Estructura de Datos
```json
{
  "preferences": true,
  "statistics": false,
  "marketing": true
}
```

## 🎨 Personalización Avanzada

### Variables CSS Disponibles
El plugin utiliza variables CSS que se pueden sobrescribir:

```css
#consentBanner {
  --banner-bg: #f8f9fa;
  --banner-text: #000000;
  --button-primary-bg: #28a745;
  --button-primary-text: #ffffff;
  --button-secondary-bg: #f2f2f2;
  --button-secondary-text: #000000;
  --switch-on-bg: #28a745;
  --switch-off-bg: #cccccc;
  --close-btn-bg: #f2f2f2;
  --close-btn-text: #000000;
  --icon-bg: #28a745;
  --banner-font-size: 16px;
  --option-font-size: 15px;
  --button-font-size: 14px;
}
```

### Configuración Programática
Puedes modificar la configuración desde JavaScript:

```javascript
window.ConsentBannerConfig = {
  text: "Tu mensaje personalizado",
  image: "ruta/a/tu/icono.png",
  colors: {
    bannerBg: "#ffffff",
    buttonPrimaryBg: "#007cba"
  },
  fontSizes: {
    bannerText: "18px"
  }
};
```

## 📱 Responsive Design

### Breakpoints
- **Desktop**: > 600px - Layout completo
- **Tablet**: 400px - 600px - Botones en columna
- **Mobile**: < 400px - Padding reducido, texto más pequeño

### Adaptaciones Móviles
- Botones apilados verticalmente
- Padding reducido para pantallas pequeñas
- Tamaños de fuente escalados
- Icono minimizado siempre visible

## ⚠️ Limitaciones y Consideraciones

### Lo que NO puede hacer
- **No es un plugin de cookies completo**: Solo maneja el banner de consentimiento
- **No genera políticas de privacidad**: Debes crear tu propia política
- **No es compatible con otros plugins de cookies**: Puede causar conflictos
- **No maneja cookies de terceros**: Solo proporciona el consentimiento

### Consideraciones Técnicas
- **Requiere JavaScript**: No funcionará si JS está deshabilitado
- **Depende de localStorage**: No guarda información en modo privado sin almacenamiento
- **Z-index alto**: Puede interferir con otros elementos con z-index alto

### Limitaciones de Personalización
- **No se pueden agregar nuevas categorías de cookies**: Solo las 4 predefinidas
- **Los textos son personalizables**: Puedes cambiar los textos de categorías y botones desde el panel de configuración
- **No se pueden agregar nuevos botones**: Solo los 3 predefinidos
- **No se puede cambiar la estructura HTML**: Solo estilos CSS

## 🔧 Desarrollo y Mantenimiento

### Versión Actual
- **Versión**: 1.4
- **Autor**: Lyca
- **Última actualización**: Integración con Google Consent Mode v2, textos personalizables, botón de restaurar valores por defecto

### Archivos Principales
- `wp-consent-banner.php`: Lógica del plugin y panel administrativo
- `dist/consent-banner.js`: Funcionalidad del banner frontend
- `dist/consent-banner.css`: Estilos del banner
- `src/config.js`: Configuración por defecto

### Hooks y Filtros
El plugin no incluye hooks personalizados, pero puedes:
- Modificar la configuración via `window.ConsentBannerConfig`
- Sobrescribir estilos CSS
- Usar JavaScript para interactuar con el banner

## 🆘 Soporte y Troubleshooting

### Problemas Comunes

#### El banner no aparece
1. Verifica que el plugin esté activado
2. Revisa la consola del navegador por errores JavaScript
3. Asegúrate de que no hay conflictos con otros plugins

#### Los colores no se aplican
1. Verifica que los valores de color sean válidos (formato hex)
2. Limpia la caché del navegador
3. Revisa si hay CSS personalizado que sobrescriba los estilos

#### GTM no recibe los eventos
1. Verifica que GTM esté correctamente instalado
2. Revisa la consola del navegador para el evento "Consent Update"
3. Asegúrate de que GTM esté configurado para responder a Google Consent Mode

### Debug
Para debug, puedes usar:
```javascript
// Verificar configuración actual
console.log(window.ConsentBannerConfig);

// Verificar preferencias guardadas
console.log(JSON.parse(localStorage.getItem('userPreferences')));

// Verificar si el plugin está cargado
console.log(window.WPConsentBannerLoaded);
```

## 📄 Licencia y Uso

Este plugin está diseñado para uso en sitios web que necesitan cumplir con regulaciones de cookies. Es responsabilidad del usuario asegurar que el plugin cumple con todas las regulaciones aplicables en su jurisdicción.
