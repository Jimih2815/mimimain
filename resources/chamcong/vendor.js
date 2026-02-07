import './vendor/daterangepicker.min.css';
import legacyVendorSrc from './vendor/legacy-vendor.js?raw';

function injectScript(code) {
  const script = document.createElement('script');
  script.text = code;
  document.head.appendChild(script);
}

// Ensure globals for inline scripts that expect window.$ and window.moment
if (typeof window !== 'undefined' && typeof document !== 'undefined') {
  if (!window.jQuery || !window.moment || !window.jQuery.fn || !window.jQuery.fn.daterangepicker) {
    injectScript(legacyVendorSrc);
  }
}
