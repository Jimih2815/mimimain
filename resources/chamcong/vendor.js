import '../../chamcong/assets/daterangepicker.min.css';
import jquerySrc from '../../chamcong/assets/jquery.min.js?raw';
import momentSrc from '../../chamcong/assets/moment.min.js?raw';
import daterangepickerSrc from '../../chamcong/assets/daterangepicker.min.js?raw';

function injectScript(code) {
  const script = document.createElement('script');
  script.text = code;
  document.head.appendChild(script);
}

// Ensure globals for inline scripts that expect window.$ and window.moment
if (typeof window !== 'undefined' && typeof document !== 'undefined') {
  if (!window.jQuery) {
    injectScript(jquerySrc);
  }
  if (!window.moment) {
    injectScript(momentSrc);
  }
  if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.daterangepicker) {
    injectScript(daterangepickerSrc);
  }
}
