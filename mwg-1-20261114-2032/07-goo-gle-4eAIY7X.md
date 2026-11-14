# Beyond code generation: Using Modern Web Guidance for continuous developer learning  |  Blog  |  Chrome for Developers
- URL pedida: https://goo.gle/4eAIY7X
- URL final: https://developer.chrome.com/blog/beyond-code-generation?hl=en  (HTTP 200)
- Canonical: https://developer.chrome.com/blog/beyond-code-generation   · lang: en   · robots meta: —
- Meta description (171 car.): Learn how to use Modern Web Guidance as an interactive learning and discovery tool to stay current with modern HTML, CSS, and Web API features while coding with AI agents.
- og:title: Beyond code generation: Using Modern Web Guidance for continuous developer learning  |  Blog  |  Chrome for Developers   · og:image: https://developer.chrome.com/static/blog/beyond-code-generation/image/hero.png
- Palabras: 1726 · imágenes: 3 · enlaces internos: 81 · externos: 36
- Secciones más enlazadas: /docs (59), /blog (8), / (3), /case-studies (3), /origintrials (2), /release-notes (2), /new (2), /deprecated (1), /static (1)
- hreflang:
  - en → https://developer.chrome.com/blog/beyond-code-generation
  - x-default → https://developer.chrome.com/blog/beyond-code-generation
  - en-cn → https://developer.chrome.google.cn/blog/beyond-code-generation
  - x-default → https://developer.chrome.google.cn/blog/beyond-code-generation
- Datos estructurados:
  - BreadcrumbList
    {"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Blog","item":"https://developer.chrome.com/blog"},{"@type":"ListItem","position":2,"name":"Beyond code generation: Using Modern Web Guidance for continuous developer learning","item":"https://developer.chrome.com/blog/beyond-code-generation"}]}
## Encabezados
H1: Beyond code generation: Using Modern Web Guidance for continuous developer learning Stay organized with collections Save and categorize content based on your preferences.
H2: Why AI agents default to yesterday's web
H2: Use Modern Web Guidance as a learning tool
H3: Browse the docs on Chrome for Developers
H3: Read the "Modern Web Guidance" section at the end of every response
H3: Learn progressive enhancement and browser support with Baseline
H3: Audit existing code to find gaps
H3: Explore the catalog from your terminal
H3: Follow the release notes
H2: Start learning with Modern Web Guidance
H3: Contribute
H3: Related content
H3: Follow
## Texto (primeras 12.000 caracteres)
Modern Web Guidance is now stable! Upgrade your coding agents with expert-vetted web development best practices. Read the announcement
.
Chrome for Developers
Blog
Beyond code generation: Using Modern Web Guidance for continuous developer learning
Stay organized with collections
Save and categorize content based on your preferences.
Tara Agyemang
Published: November 11, 2026
Discussions about AI coding tools almost always focus on speed: How fast an agent can scaffold an app, build a new feature, or migrate a whole codebase.
But when an agent writes the code, two questions come up. Does it use the best of the modern web platform? And how do you keep growing your own technical understanding when you aren't typing every line yourself?
This week, we released Modern Web Guidance 1.0
, a set of skills that give your coding agents web platform expertise, best practices, and browser compatibility data. It covers over 200 real-world use cases and steers agents toward clean, native HTML and CSS instead of legacy JavaScript workarounds.
Since our early preview at Google I/O, developers have told us they use Modern Web Guidance for more than better code. They also use it to discover and learn modern web features as they work.
Why AI agents default to yesterday's web
The web platform has evolved quickly. Features that once needed heavy JavaScript or fragile workarounds (like modal focus traps, custom-styled selects, entry and exit animations, or skipping offscreen rendering) work natively in browsers.
But models learn from huge archives of existing code, and ten years of legacy code far outweighs the last twelve months. So unguided agents reach for old habits. Across nearly 1,000 eval runs on our public eval dashboard
, frontier models passed about 60% of our modern web checks on their own, and jumped to around 80–90% when guided by Modern Web Guidance.
When you install Modern Web Guidance
, your agent loads concise, expert-written guides right before it writes code. Instead of pulling your codebase back toward 2019-era patterns, it introduces you to modern standards you may not have seen yet.
For example, using Modern Web Guidance with your agent can help you discover connected platform features. Fixing a tooltip or menu bug can introduce you to the Popover API
. That can lead you to CSS Anchor Positioning
to position elements without JavaScript libraries.
When an agent writes a modern CSS rule or HTML attribute you haven't seen before, you can pause, check how it works, and develop your understanding. Modern Web Guidance 1.0 was designed so that your agent doesn't use those features silently. It explains what it chose and why.
Use Modern Web Guidance as a learning tool
You can learn from Modern Web Guidance inside your agent, in your terminal, and on the web.
Browse the docs on Chrome for Developers
You can browse what Modern Web Guidance covers without reading raw Markdown. Explore all 200+ guides
, covering accessibility, performance, security, privacy, html, css, css-layout, forms, passkeys, webmcp, and built-in-ai.
Every discipline is written and reviewed by web platform experts, with support from Google Chrome, Microsoft Edge, Firefox, and the broader web community.
Read the "Modern Web Guidance" section at the end of every response
In 1.0, the skill tells your agent to add a Modern Web Guidance section at the end of every response where it uses a guide.
The agent lists:
Which guide IDs it applied, or which topics it checked if no guides were applied.
Which APIs, CSS rules, or fallbacks it used and what issues they prevent.
A reference to the specific rule from the guide that drove the decision.
Take a common task that AI models tend to get wrong: Animating a modal <dialog> or popover in and out of the top layer
. In our evaluation runs
on the animate-to-from-top-layer
and dialog
guides, five out of six unguided runs failed to use modern CSS entry and exit animation properties, and six out of six unguided runs ignored prefers-reduced-motion
.
With Modern Web Guidance installed, your agent uses the dialog
and animate-to-from-top-layer
guides and explains its choices at the bottom of its reply:
### Modern Web Guidance
- **`dialog`**: Opened the native `<dialog>` declaratively using Invoker Commands (`command="show-modal"` and `commandfor`), and prevented background page scrolling using `overscroll-behavior: contain` on `dialog:modal` and `dialog::backdrop` instead of locking `body` scroll.
> "DON'T lock scrolling with `overflow: hidden` or `position: fixed` on `html` or `body`."
- **`animate-to-from-top-layer`**: Used `@starting-style` for the entry animation and `transition-behavior: allow-discrete` with `display` and `overlay` in the transition list so the browser waits for the exit transition before removing the dialog from the top layer, gated behind `prefers-reduced-motion: no-preference`.
> "MANDATORY: Respect user preference for reduced motion by selectively applying transitions."
From this output, you learn three techniques you can reuse:
Invoker Commands
let a <button> open a modal declaratively without a custom JavaScript click listener.
@starting-style
and transition-behavior: allow-discrete
let you smoothly animate elements entering and leaving display: none and the top layer in pure CSS, without setTimeout before calling dialog.close().
Locking scroll with overflow: hidden on <body> removes the root scrollbar and causes layout shift. Pairing overscroll-behavior: contain on dialog:modal and overflow: hidden; overscroll-behavior: contain on dialog::backdrop stops scroll chaining cleanly.
To go further, add instructions to your AGENTS.md file that ask your agent to quiz you on the features it used or explain its choices as it works. For example, it can tell you what it planned to write, what it missed, and what it changed because of the guide.
Learn progressive enhancement and browser support with Baseline
Baseline
tells you at a glance whether a web feature is Limited availability, Newly available (supported across major browsers), or Widely available (interoperable for at least 30 months).
Every guide in Modern Web Guidance embeds Baseline support data alongside a specific fallback strategy. That makes your agent a practical teacher for progressive enhancement.
A good example is custom-styled <select> elements. For years, styling a drop-down menu to match a design system meant rebuilding the entire control out of <div> elements and JavaScript. In our evaluation suite
, unguided models scored 44% on custom select tasks—versus 92% with Modern Web Guidance—because they built custom <div> drop-downs that dropped <label> elements, omitted the name attribute, broke keyboard navigation, and failed to submit with the form.
With Modern Web Guidance, your agent teaches you how to opt into customizable <select>
using appearance: base-select, ::picker(select), and <selectedcontent>:
<label for="shipping">Shipping preference</label>
<select class="brand-select" id="shipping" name="shipping">
<button>
<selectedcontent></selectedcontent>
</button>
<option value="standard">
<span aria-hidden="true">🚲</span> Standard shipping
</option>
<option value="express" selected>
<span aria-hidden="true">🏍️</span> Express shipping
</option>
</select>
/* Opt the select button and its top-layer drop-down picker into custom styling */
.brand-select,
.brand-select::picker(select) {
appearance: base-select;
}
The guide also explains why this pattern is safe to use as a progressive enhancement: In browsers that don't support appearance: base-select yet, the parser ignores the inner <button> and <selectedcontent> tags, and renders a standard, accessible native <select> drop-down without JavaScript.
You can also set your browser support target in AGENTS.md or CLAUDE.md so the fallbacks your agent applies and explains match your team's exact browser support policy.
Audit existing code to find gaps
A good way to learn is to look at real code and ask, "What's missing here, and why does it matter?" You can use the guides to help answer these questions by auditing existing code. Ask your agent to review a component or a pull request before it changes anything:
Prompt to try:
Use Modern Web Guidance to audit our collapsible FAQ component (ProductFAQ.tsx). Don't edit yet. What native HTML and CSS could replace our accordion JavaScript and keep collapsed answers searchable?
Audits like this highlight platform features that models often overlook:
Making collapsed content searchable with find-in-page (search-hidden-content
): Agents often hide collapsed accordion panels with display: none, so find-in-page (Ctrl+F or Cmd+F) can't find the text. Auditing ProductFAQ.tsx suggests two native fixes: Using <details> elements that share a name attribute to build an exclusive accordion with searchable text, or using hidden="until-found" with a beforematch event listener so the browser opens a section when a search matches it.
Animating smoothly to height: auto in pure CSS (animate-to-intrinsic-sizes
): Instead of measuring scrollHeight in JavaScript or using fragile max-height: 999px to animate a panel, set interpolate-size: allow-keywords (or use calc-size()) so panels can animate from 0 to height: auto.
Explore the catalog from your terminal
You don't need an agent to use Modern Web Guidance. The modern-web-guidance CLI is a standalone tool for checking the modern way to build a feature.
You can try it without installing anything globally:
# Search for guides matching a task you are about to build
npx modern-web-guidance@latest search "animate a dialog modal backdrop"
# List all available guides across disciplines
npx modern-web-guidance@latest list
# Read a specific guide directly in your terminal
npx modern-web-guidance@latest retrieve "animate-to-from-top-layer"
Guides skip detailed background, so reading one with the retrieve command gives you a focused list of modern APIs, edge cases, and fallbacks for that task.
Follow the release notes
Watching releases on the Modern Web Guidance GitHub repository
shows when a feature reaches Baseline, or when experts update a pattern. Each release lists new and updated guides, so you can skim what changed and then run the update.
Start learning with Modern Web Guidance
AI coding tools don't mean you're trading away your technical depth for speed. When your agent is grounded in up-to-date web standards and shows its work, every prompt, code review, and refactor becomes a chance to sharpen your own understanding of the web platform.
Ask your agent to install Modern Web Guidance, or run:
npx modern-web-guidance@latest install
If you already installed an earlier preview version, update to 1.0 to get the full catalog of 200+ guides and new features:
npx modern-web-guidance@latest update
Read the Getting started documentation
, explore the public evaluation dashboard
, and tell us what you discover by filing feedback on GitHub
.
Except as otherwise noted, the content of this page is licensed under the Creative Commons Attribution 4.0 License
, and code samples are licensed under the Apache 2.0 License
. For details, see the Google Developers Site Policies
. Java is a registered trademark of Oracle and/or its affiliates.
Last updated 2026-11-11 UTC.
[[["Easy to understand","easyToUnderstand","thumb-up"],["Solved my problem","solvedMyProblem","thumb-up"],["Other","otherUp","thumb-up"]],[["Missing the information I need","missingTheInformationINeed","thumb-down"],["Too complicated / too many steps","tooComplicatedTooManySteps","thumb-down"],["Out of date","outOfDate","thumb-down"],["Samples / code issue","samplesCodeIssue","thumb-down"],["Other","otherDown","thumb-down"]],["Last updated 2026-11-11 UTC."],[],[]]