(text/plain; charset=utf-8, no es HTML)
<p align="center">
  <img src="https://github.com/GoogleChrome/modern-web-guidance/raw/main/.github/img/modern-web-guidance.svg" alt="Modern Web Guidance Logo" width="150" />
</p>

# Modern Web Guidance

[Modern Web Guidance](https://developer.chrome.com/docs/modern-web-guidance) is a set of skills that embed web platform expertise, best practices, and browser compatibility data directly into your coding agents. It helps to steer your coding agents away from legacy patterns, and instead toward solutions that harness the power and capabilities of the modern web platform.

*Stewarded by the Google Chrome, Microsoft Edge, Mozilla Firefox, and MDN teams, alongside the web development community.*

[Documentation](https://developer.chrome.com/docs/modern-web-guidance) · [Eval Dashboard](https://googlechrome.github.io/modern-web-guidance-src/) · [Source Code](https://github.com/GoogleChrome/modern-web-guidance-src) · [Report an Issue](https://github.com/GoogleChrome/modern-web-guidance-src/issues)

## <img src="https://github.com/GoogleChrome/modern-web-guidance/raw/main/.github/img/terminal.svg" width="24" height="24" style="vertical-align: middle; margin-right: 4px;"> Quickstart

```shell
npx modern-web-guidance@latest install
```

This command runs an interactive wizard to install Modern Web Guidance. See [Alternative Installation Methods](#-alternative-installation-methods) below.

#### Try it out (without installing)

```shell
# Search for relevant guides
npx modern-web-guidance@latest search "animate a dialog modal backdrop"

# Retrieve a guide by ID
npx modern-web-guidance@latest retrieve "animate-to-from-top-layer"

# Scan a codebase for legacy patterns to modernize
npx modern-web-guidance@latest legacy-scan .
```

## <img src="https://github.com/GoogleChrome/modern-web-guidance/raw/main/.github/img/lightbulb.svg" width="24" height="24" style="vertical-align: middle; margin-right: 4px;"> Why?

Coding agents often default to older patterns because LLM training data contains vast amounts of legacy code. This often leads them to generate bloated JavaScript for tasks that now have native, high-performance web platform solutions.

Even if a model knows an API exists, it often lacks the density of real-world, modern implementation patterns required for production-ready code.

**Modern Web Guidance bridges this gap.** Our skill's CLI returns targeted, expert-curated guidelines directly into your agent's context window, focusing on:
* **Modern Browser APIs**: Helping models correctly structure APIs they frequently misuse.
* **Performance & Accessibility**: Preferring platform-level APIs that can be optimized by the browser and include built-in accessibility affordances.
* **Responsible Fallbacks**: Guiding models to use sensible, lightweight fallbacks instead of heavy polyfills or legacy libraries.

## <img src="https://github.com/GoogleChrome/modern-web-guidance/raw/main/.github/img/package.svg" width="24" height="24" style="vertical-align: middle; margin-right: 4px;"> What's Included?

We cover the past several years of the web platform's new features, all the way up to the cutting edge. The guides are **designed to be token-efficient**; we run evals enabling us to prune lowest-common-denominator content that models already know.

### Core Disciplines

<table width="100%" style="border-collapse: collapse; border: none;">
  <tr style="border: none;">
    <td width="33%" valign="top" style="border: none; padding: 6px;">
      <h4>🎨 User Experience</h4>
      <p style="font-size: 0.9em; line-height: 1.4;">Smooth visual states (View Transitions, entry/exit animations, parallax scroll, CSS <code>scrollbar-color</code>).</p>
    </td>
    <td width="33%" valign="top" style="border: none; padding: 6px;">
      <h4>📐 CSS Layout</h4>
      <p style="font-size: 0.9em; line-height: 1.4;">Modern layout systems (container queries, <code>subgrid</code>, modern color spaces like <code>oklch</code>, text-wrap tuning, and line-height trimming).</p>
    </td>
    <td width="33%" valign="top" style="border: none; padding: 6px;">
      <h4>⚡ Performance</h4>
      <p style="font-size: 0.9em; line-height: 1.4;">Speed optimizations (instant preloading, Interaction to Next Paint (INP) diagnostics, and scheduling tasks via <code>scheduler.yield</code>).</p>
    </td>
  </tr>
  <tr style="border: none;">
    <td width="33%" valign="top" style="border: none; padding: 6px;">
      <h4>📝 Forms & UI</h4>
      <p style="font-size: 0.9em; line-height: 1.4;">Native components (Anchor Positioning for tooltips, Popover API, dialogs, <code>:user-invalid</code> validation, and auto-sizing fields).</p>
    </td>
    <td width="33%" valign="top" style="border: none; padding: 6px;">
      <h4>♿ Accessibility</h4>
      <p style="font-size: 0.9em; line-height: 1.4;">Important considerations (screen reader and keyboard operability, content navigation and discoverability).</p>
    </td>
    <td width="33%" valign="top" style="border: none; padding: 6px;">
      <h4>🤖 Built-in AI</h4>
      <p style="font-size: 0.9em; line-height: 1.4;">Local client models (native translation, summarization, and language detection APIs).</p>
    </td>
  </tr>
</table>

_View an example:_ [the `navigation-drawer` guide](https://github.com/GoogleChrome/modern-web-guidance/blob/main/skills/modern-web-guidance/guides/ui-components/navigation-drawer.md).

<!-- INJECT_SKILL_COVERAGE_START -->
#### The full list

<details>
<summary><strong>250 modern web features</strong></summary>

### CSS & Layout (140 features)

| | | |
| :--- | :--- | :--- |
| [::backdrop](https://web-platform-dx.github.io/web-features-explorer/features/backdrop/) | [content-visibility](https://web-platform-dx.github.io/web-features-explorer/features/content-visibility/) | [progress()](https://web-platform-dx.github.io/web-features-explorer/features/progress-function/) |
| [::details-content](https://web-platform-dx.github.io/web-features-explorer/features/details-content/) | [contrast-color()](https://web-platform-dx.github.io/web-features-explorer/features/contrast-color/) | [reading-flow](https://web-platform-dx.github.io/web-features-explorer/features/reading-flow/) |
| [::file-selector-button](https://web-platform-dx.github.io/web-features-explorer/features/file-selector-button/) | [Cross-document view transitions](https://web-platform-dx.github.io/web-features-explorer/features/cross-document-view-transitions/) | [Relative colors](https://web-platform-dx.github.io/web-features-explorer/features/relative-color/) |
| [::placeholder](https://web-platform-dx.github.io/web-features-explorer/features/placeholder/) | [currentColor](https://web-platform-dx.github.io/web-features-explorer/features/currentcolor/) | [rem](https://web-platform-dx.github.io/web-features-explorer/features/rem/) |
| [::selection](https://web-platform-dx.github.io/web-features-explorer/features/selection/) | [Custom highlights](https://web-platform-dx.github.io/web-features-explorer/features/highlight/) | [resize (CSS property)](https://web-platform-dx.github.io/web-features-explorer/features/resize/) |
| [:dir()](https://web-platform-dx.github.io/web-features-explorer/features/dir-pseudo/) | [Custom properties](https://web-platform-dx.github.io/web-features-explorer/features/custom-properties/) | [revert](https://web-platform-dx.github.io/web-features-explorer/features/revert-value/) |
| [:focus-visible](https://web-platform-dx.github.io/web-features-explorer/features/focus-visible/) | [display animation](https://web-platform-dx.github.io/web-features-explorer/features/display-animation/) | [revert-rule](https://web-platform-dx.github.io/web-features-explorer/features/revert-rule/) |
| [:focus-within](https://web-platform-dx.github.io/web-features-explorer/features/focus-within/) | [Element-scoped view transitions](https://web-platform-dx.github.io/web-features-explorer/features/view-transitions-element-scoped/) | [Safe area inset environment variables](https://web-platform-dx.github.io/web-features-explorer/features/safe-area-inset/) |
| [:has()](https://web-platform-dx.github.io/web-features-explorer/features/has/) | [em unit](https://web-platform-dx.github.io/web-features-explorer/features/em-unit/) | [Scroll marker target pseudo-classes](https://web-platform-dx.github.io/web-features-explorer/features/scroll-marker-targets/) |
| [:host-context()](https://web-platform-dx.github.io/web-features-explorer/features/host-context/) | [field-sizing](https://web-platform-dx.github.io/web-features-explorer/features/field-sizing/) | [Scroll method promises](https://web-platform-dx.github.io/web-features-explorer/features/scroll-promises/) |
| [:is()](https://web-platform-dx.github.io/web-features-explorer/features/is/) | [filter](https://web-platform-dx.github.io/web-features-explorer/features/filter/) | [Scroll snap](https://web-platform-dx.github.io/web-features-explorer/features/scroll-snap/) |
| [:modal](https://web-platform-dx.github.io/web-features-explorer/features/modal/) | [flex-wrap: balance](https://web-platform-dx.github.io/web-features-explorer/features/flexbox-flex-wrap-balance/) | [Scroll snap events](https://web-platform-dx.github.io/web-features-explorer/features/scroll-snap-events/) |
| [:not()](https://web-platform-dx.github.io/web-features-explorer/features/not/) | [Flexbox](https://web-platform-dx.github.io/web-features-explorer/features/flexbox/) | [scroll-axis-lock](https://web-platform-dx.github.io/web-features-explorer/features/scroll-axis-lock/) |
| [:nth-child() of &lt;selector>](https://web-platform-dx.github.io/web-features-explorer/features/nth-child-of/) | [font-size](https://web-platform-dx.github.io/web-features-explorer/features/font-size/) | [scroll-behavior](https://web-platform-dx.github.io/web-features-explorer/features/scroll-behavior/) |
| [:placeholder-shown](https://web-platform-dx.github.io/web-features-explorer/features/placeholder-shown/) | [font-size-adjust](https://web-platform-dx.github.io/web-features-explorer/features/font-size-adjust/) | [Scroll-driven animations](https://web-platform-dx.github.io/web-features-explorer/features/scroll-driven-animations/) |
| [:user-valid and :user-invalid](https://web-platform-dx.github.io/web-features-explorer/features/user-pseudos/) | [Forced colors](https://web-platform-dx.github.io/web-features-explorer/features/forced-colors/) | [scroll-initial-target](https://web-platform-dx.github.io/web-features-explorer/features/scroll-initial-target/) |
| [:where()](https://web-platform-dx.github.io/web-features-explorer/features/where/) | [Gradient interpolation](https://web-platform-dx.github.io/web-features-explorer/features/gradient-interpolation/) | [scroll-target-group](https://web-platform-dx.github.io/web-features-explorer/features/scroll-target-group/) |
| [@function](https://web-platform-dx.github.io/web-features-explorer/features/function/) | [Gradients](https://web-platform-dx.github.io/web-features-explorer/features/gradients/) | [scrollbar-color](https://web-platform-dx.github.io/web-features-explorer/features/scrollbar-color/) |
| [@scope](https://web-platform-dx.github.io/web-features-explorer/features/scope/) | [Grid](https://web-platform-dx.github.io/web-features-explorer/features/grid/) | [scrollbar-gutter](https://web-platform-dx.github.io/web-features-explorer/features/scrollbar-gutter/) |
| [@starting-style](https://web-platform-dx.github.io/web-features-explorer/features/starting-style/) | [Grid lanes](https://web-platform-dx.github.io/web-features-explorer/features/grid-lanes/) | [scrollbar-width](https://web-platform-dx.github.io/web-features-explorer/features/scrollbar-width/) |
| [@supports](https://web-platform-dx.github.io/web-features-explorer/features/supports/) | [Hyphenation](https://web-platform-dx.github.io/web-features-explorer/features/hyphens/) | [scrollend](https://web-platform-dx.github.io/web-features-explorer/features/scrollend/) |
| [abs() and sign()](https://web-platform-dx.github.io/web-features-explorer/features/abs-sign/) | [image-set()](https://web-platform-dx.github.io/web-features-explorer/features/image-set/) | [scrollIntoView()](https://web-platform-dx.github.io/web-features-explorer/features/scroll-into-view/) |
| [accent-color](https://web-platform-dx.github.io/web-features-explorer/features/accent-color/) | [Individual transform properties](https://web-platform-dx.github.io/web-features-explorer/features/individual-transforms/) | [Shadow parts](https://web-platform-dx.github.io/web-features-explorer/features/shadow-parts/) |
| [Active view transition](https://web-platform-dx.github.io/web-features-explorer/features/active-view-transition/) | [inherit](https://web-platform-dx.github.io/web-features-explorer/features/inherit-value/) | [sibling-count() and sibling-index()](https://web-platform-dx.github.io/web-features-explorer/features/sibling-count/) |
| [Alt text for generated content](https://web-platform-dx.github.io/web-features-explorer/features/alt-text-generated-content/) | [initial](https://web-platform-dx.github.io/web-features-explorer/features/initial-value/) | [Sticky positioning](https://web-platform-dx.github.io/web-features-explorer/features/sticky-positioning/) |
| [Anchor position container queries](https://web-platform-dx.github.io/web-features-explorer/features/container-anchor-position-queries/) | [Interaction media queries](https://web-platform-dx.github.io/web-features-explorer/features/interaction/) | [Subgrid](https://web-platform-dx.github.io/web-features-explorer/features/subgrid/) |
| [Anchor positioning](https://web-platform-dx.github.io/web-features-explorer/features/anchor-positioning/) | [interpolate-size](https://web-platform-dx.github.io/web-features-explorer/features/interpolate-size/) | [System colors](https://web-platform-dx.github.io/web-features-explorer/features/system-color/) |
| [Animations (CSS)](https://web-platform-dx.github.io/web-features-explorer/features/animations-css/) | [isolation](https://web-platform-dx.github.io/web-features-explorer/features/isolation/) | [Text overflow](https://web-platform-dx.github.io/web-features-explorer/features/text-overflow/) |
| [appearance](https://web-platform-dx.github.io/web-features-explorer/features/appearance/) | [Lab and LCH](https://web-platform-dx.github.io/web-features-explorer/features/lab/) | [Text stroke and fill  (compatibility prefixes)](https://web-platform-dx.github.io/web-features-explorer/features/text-stroke-fill/) |
| [aspect-ratio](https://web-platform-dx.github.io/web-features-explorer/features/aspect-ratio/) | [light-dark()](https://web-platform-dx.github.io/web-features-explorer/features/light-dark/) | [text-box](https://web-platform-dx.github.io/web-features-explorer/features/text-box/) |
| [at-rule()](https://web-platform-dx.github.io/web-features-explorer/features/supports-at-rule/) | [line-clamp](https://web-platform-dx.github.io/web-features-explorer/features/line-clamp/) | [text-decoration](https://web-platform-dx.github.io/web-features-explorer/features/text-decoration/) |
| [attr()](https://web-platform-dx.github.io/web-features-explorer/features/attr/) | [line-height](https://web-platform-dx.github.io/web-features-explorer/features/line-height/) | [text-decoration-skip-ink](https://web-platform-dx.github.io/web-features-explorer/features/text-decoration-skip-ink/) |
| [background-blend-mode](https://web-platform-dx.github.io/web-features-explorer/features/background-blend-mode/) | [linear() easing](https://web-platform-dx.github.io/web-features-explorer/features/linear-easing/) | [text-decoration-skip-ink: all](https://web-platform-dx.github.io/web-features-explorer/features/text-decoration-skip-ink-all/) |
| [background-clip: border-area](https://web-platform-dx.github.io/web-features-explorer/features/background-clip-border-area/) | [Logical properties](https://web-platform-dx.github.io/web-features-explorer/features/logical-properties/) | [text-decoration-skip-spaces](https://web-platform-dx.github.io/web-features-explorer/features/text-decoration-skip-spaces/) |
| [border-radius](https://web-platform-dx.github.io/web-features-explorer/features/border-radius/) | [Masks](https://web-platform-dx.github.io/web-features-explorer/features/masks/) | [text-wrap](https://web-platform-dx.github.io/web-features-explorer/features/text-wrap/) |
| [box-shadow](https://web-platform-dx.github.io/web-features-explorer/features/box-shadow/) | [min(), max(), and clamp()](https://web-platform-dx.github.io/web-features-explorer/features/min-max-clamp/) | [text-wrap: balance](https://web-platform-dx.github.io/web-features-explorer/features/text-wrap-balance/) |
| [calc-size()](https://web-platform-dx.github.io/web-features-explorer/features/calc-size/) | [mix-blend-mode](https://web-platform-dx.github.io/web-features-explorer/features/mix-blend-mode/) | [text-wrap: pretty](https://web-platform-dx.github.io/web-features-explorer/features/text-wrap-pretty/) |
| [Cascade layers](https://web-platform-dx.github.io/web-features-explorer/features/cascade-layers/) | [Nesting](https://web-platform-dx.github.io/web-features-explorer/features/nesting/) | [transition-behavior](https://web-platform-dx.github.io/web-features-explorer/features/transition-behavior/) |
| [ch unit](https://web-platform-dx.github.io/web-features-explorer/features/ch/) | [Oklab and OkLCh](https://web-platform-dx.github.io/web-features-explorer/features/oklab/) | [Transitions (CSS)](https://web-platform-dx.github.io/web-features-explorer/features/transitions/) |
| [clip-path](https://web-platform-dx.github.io/web-features-explorer/features/clip-path/) | [overflow-clip-margin](https://web-platform-dx.github.io/web-features-explorer/features/overflow-clip-margin/) | [Trigonometric functions (CSS)](https://web-platform-dx.github.io/web-features-explorer/features/trig-functions/) |
| [color-mix()](https://web-platform-dx.github.io/web-features-explorer/features/color-mix/) | [overflow-wrap](https://web-platform-dx.github.io/web-features-explorer/features/overflow-wrap/) | [unset](https://web-platform-dx.github.io/web-features-explorer/features/unset-value/) |
| [color-scheme](https://web-platform-dx.github.io/web-features-explorer/features/color-scheme/) | [overflow: clip](https://web-platform-dx.github.io/web-features-explorer/features/overflow-clip/) | [user-select](https://web-platform-dx.github.io/web-features-explorer/features/user-select/) |
| [Conic gradients](https://web-platform-dx.github.io/web-features-explorer/features/conic-gradients/) | [overlay](https://web-platform-dx.github.io/web-features-explorer/features/overlay/) | [View transitions](https://web-platform-dx.github.io/web-features-explorer/features/view-transitions/) |
| [Container queries (size)](https://web-platform-dx.github.io/web-features-explorer/features/container-queries/) | [overscroll-behavior](https://web-platform-dx.github.io/web-features-explorer/features/overscroll-behavior/) | [view-transition-class](https://web-platform-dx.github.io/web-features-explorer/features/view-transition-class/) |
| [Container scroll-state queries](https://web-platform-dx.github.io/web-features-explorer/features/container-scroll-state-queries/) | [prefers-color-scheme media query](https://web-platform-dx.github.io/web-features-explorer/features/prefers-color-scheme/) | [Viewport units](https://web-platform-dx.github.io/web-features-explorer/features/viewport-units/) |
| [Container style queries](https://web-platform-dx.github.io/web-features-explorer/features/container-style-queries/) | [prefers-contrast media query](https://web-platform-dx.github.io/web-features-explorer/features/prefers-contrast/) | [Web animations](https://web-platform-dx.github.io/web-features-explorer/features/web-animations/) |
| [Content](https://web-platform-dx.github.io/web-features-explorer/features/content/) | [prefers-reduced-motion media query](https://web-platform-dx.github.io/web-features-explorer/features/prefers-…