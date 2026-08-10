---
paths:
  - 'resources/views/**'
---

# Views

## Chart.js dashboard charts via Alpine components
Chart.js 4.5.1 is installed (allowed explicitly). Alpine chart components live in resources/js/charts.js (barChart + doughnutChart), imported in app.js BEFORE Alpine.start(). Views use <div x-data="barChart(@js($verificationsChart))"><canvas></canvas></div>. Chart data always comes pre-shaped from the service as ['labels' => [...], 'data' => [...]] — never build chart payloads inside Blade. Keep the canvas wrapped in a fixed-height div (h-64) so charts don't collapse.
