# Modern Web Guidance Evals: Measurably better web code with Modern Web Guidance
- URL pedida: https://goo.gle/48BbhzL
- URL final: https://googlechrome.github.io/modern-web-guidance-src/  (HTTP 200)
- Canonical: —   · lang: en   · robots meta: —
- Meta description (200 car.): Modern Web Guidance Evaluation Dashboard: Compare guided vs. unguided AI coding agent pass rates, automated accessibility check pass rates, completion time, and token efficiency across benchmark jobs.
- og:title: Modern Web Guidance Evals: Measurably better web code with Modern Web Guidance   · og:image: https://googlechrome.github.io/modern-web-guidance-src/og-image.png
- Palabras: 623 · imágenes: 0 · enlaces internos: 5 · externos: 6
- Secciones más enlazadas: /modern-web-guidance-src (5)
- hreflang: ninguno
- Datos estructurados: ninguno
## Encabezados
H1: Modern Web Guidance Evals
H2: Measurably better web code with Modern Web Guidance
H3: Best-practice pass rate
H3: Automated accessibility checks
H2: Inspect paired trials
H2: By agent family
H2: Browse all jobs
## Texto (primeras 12.000 caracteres)
Measurably better web code with Modern Web Guidance
Nightly and baseline A/B jobs give ✱Claude Code, ❂Codex CLI, and ✦Antigravity CLI identical tasks with and without Modern Web Guidance, grading output in a real browser.
Sign in with Google
Best-practice pass rate
…
pass-rate lift
Loading…
Automated accessibility checks
…
Not a full WCAG audit
Loading…
Latest jobs per model · loading…
Inspect paired trials
Side-by-side browser checks from the latest job per agent (not an aggregate).
Select coding agent
Select evaluation task
Task prompt
Loading task prompt…
By agent family
Latest job per model (models run in the last 7 days; nightly or 1.0 baseline). Model and per-guide accessibility breakdowns
below.
Agent summary window
Latest job per model
Last 5 jobs per model
Latest job per model by agent family. Unguided → guided.
Agent
Pass rate
Accessibility
Sample
Loading…———
Breakdowns by model and guide
Last
days
All jobs
Comparison check scope
All checks
Accessibility checks
How we test & measure
Controlled A/B tasks
Each task pairs a starter web app with a feature prompt (for example, "add search-term highlighting"). Every agent, model, and prompt runs twice: once without MWG and once with MWG installed as a skill.
Grading & pass rate
Playwright opens the finished app in a real browser and checks the DOM, styles, and source against each requirement. Pass rate is checks passed divided by checks run (trials that encounter an infrastructure error or time out before grading are left out, along with their paired trial). Lift is shown in percentage points (pp): 25% → 90% is +65 pp.
Do graders penalize valid alternative code in "Without MWG"?
Graders assert rendered browser behavior and spec-compliant accessibility semantics (such as computed roles, focus movement, and keyboard interaction) rather than guide-specific variable names or markup structure. When an audit finds a grader that rejects a valid alternative implementation, we broaden the check in the open-source repository
and re-score the job.
Why isn't "With MWG" 100%?
Task prompts never mention MWG, so the agent has to recognize when a guide applies and follow every detail. Misses come from skipped steps (such as an older-browser fallback), missed guide lookups (see Search Quality
), or strict grader edge cases.
Automated accessibility checks
Pass rate across automated browser checks verifying foundational platform mechanics across five dimensions: keyboard support, focus and inert, prefers-reduced-motion and contrast, ARIA roles and states, and form labels. Automated checks catch common AI omissions in code but only cover a subset of accessibility requirements—they do not measure full WCAG compliance or replace manual and assistive-technology testing.
Task duration
Median coding time per task, excluding browser grading. Reported per agent because CLIs run differently.
Token usage (uncached)
Multi-step agents re-send cached conversation history on every turn, inflating raw totals. We report uncached tokens (total − cached) to measure new input and output per task.
Canonical benchmark jobs (nightly & 1.0 baseline)
Automated nightly jobs (nightly-<date>-<agent>) and curated 1.0 baseline evaluations run the full task set. Headline numbers pool the latest job per model across agent families for models run in the last 7 days.
Open-source benchmark
Guides, base apps, prompts, and Playwright graders live in GoogleChrome/modern-web-guidance-src
.
Browse all jobs
Select a job for per-task diffs and logs.
Unguided
→
Guided
Scheduled only
Sort jobs
Sort: Newest
Sort: Oldest
Sort: Pass-rate lift
Sort: A11y lift
Sort: Shortest duration
Sort: Fewest tokens
Filter jobs by agent
All agents
Evaluation jobs comparing unguided and guided results. Pass-rate change in percentage points (pp); duration and token change in %, lower is better.
Job & date
Filter by agent
Agent
Filter by model
Model
Pass rate
A11y checks
Median duration
Median uncached tokens
Filter by source
SourceLocalRemote
No evaluation jobs found yet. Run an evaluation to get started.