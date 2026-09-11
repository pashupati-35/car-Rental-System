<script setup lang="ts">
import { computed } from "vue"

// ── Props ─────────────────────────────────────────────────────────────────
const props = withDefaults(
  defineProps<{
        password: string;
        confirmPassword?: string;

        /** Hide the confirm-match pill (e.g. when confirm field is on a different step) */
        hideConfirm?: boolean;
    }>(),
  { confirmPassword: "", hideConfirm: false },
)

// ── Expose so parent can gate form submission ─────────────────────────────
defineExpose({ isValid: computed(() => passedCount.value === RULES.length) })

interface RuleItem {
  key: string
  label: string
  test: (v: string) => boolean
}

// ── Rules ─────────────────────────────────────────────────────────────────
const RULES: RuleItem[] = [
  {
    key: "len",
    label: "At least 8 characters",
    test: (v: string) => v.length >= 8,
  },
  {
    key: "upper",
    label: "One uppercase letter (A–Z)",
    test: (v: string) => /[A-Z]/.test(v),
  },
  {
    key: "num",
    label: "One number (0–9)",
    test: (v: string) => /\d/.test(v),
  },
  {
    key: "special",
    label: "One special character (!@#$%…)",
    test: (v: string) => /[!@#$%^&*()\-_=+[\]{};':"\\|,.<>/?]/.test(v),
  },
]

// ── Computed ──────────────────────────────────────────────────────────────
const results = computed(() =>
  RULES.map((r: RuleItem) => ({
    ...r,
    state:
            props.password.length === 0
              ? "idle"
              : r.test(props.password)
                ? "pass"
                : "fail",
  })),
)

const passedCount = computed(
  () => results.value.filter((r: { state: string }) => r.state === "pass").length,
)

const strength = computed(() => {
  if (!props.password.length) return null

  const levels = [
    { label: "Weak", color: "#E24B4A", bars: 1 },
    { label: "Fair", color: "#EF9F27", bars: 2 },
    { label: "Good", color: "#1D9E75", bars: 3 },
    { label: "Strong", color: "#0F6E56", bars: 4 },
  ]

  return levels[Math.min(passedCount.value, 4) - 1] ?? levels[0]
})

const barClass = (i: number) => {
  if (!strength.value || i > strength.value.bars) return "bar"

  const map: Record<string, string> = {
    "#E24B4A": "bar weak",
    "#EF9F27": "bar fair",
    "#1D9E75": "bar good",
    "#0F6E56": "bar strong",
  }

  return map[strength.value.color] ?? "bar"
}

const passwordsMatch = computed(() => {
  if (props.hideConfirm || !props.confirmPassword?.length) return null
  
  return props.password === props.confirmPassword
})
</script>

<template>
  <div class="pw-checklist">
    <!-- ── Strength bar ── -->
    <Transition name="fade">
      <div
        v-if="strength"
        class="strength-row"
      >
        <div class="bars">
          <div
            v-for="i in 4"
            :key="i"
            :class="barClass(i)"
          />
        </div>
        <span
          class="str-label"
          :style="{ color: strength.color }"
        >{{
          strength.label
        }}</span>
      </div>
    </Transition>


    <!-- ── Confirm match pill ── -->
    <Transition name="pop">
      <span
        v-if="passwordsMatch !== null"
        class="match-pill"
        :class="[passwordsMatch ? 'ok' : 'no']"
      >
        <VIcon
          :icon="passwordsMatch ? 'ri-check-line' : 'ri-close-line'"
          size="13"
        />
        {{
          passwordsMatch
            ? "Passwords match"
            : "Passwords do not match"
        }}
      </span>
    </Transition>
  </div>
</template>

<style scoped lang="scss">
.pw-checklist {
    margin-top: 8px;
}

/* ── Strength bar ── */
.strength-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.bars {
    display: flex;
    gap: 4px;
    flex: 1;
}

.bar {
    flex: 1;
    height: 3px;
    border-radius: 99px;
    background: rgba(var(--v-border-color), 0.15);
    transition: background 0.35s ease;

    &.weak {
        background: #e24b4a;
    }
    &.fair {
        background: #ef9f27;
    }
    &.good {
        background: #1d9e75;
    }
    &.strong {
        background: #0f6e56;
    }
}

.str-label {
    font-size: 12px;
    font-weight: 600;
    min-width: 42px;
    text-align: right;
    transition: color 0.3s ease;
}

/* ── Rules card ── */
.rules-card {
    border: 1px solid rgba(var(--v-border-color), 0.16);
    border-radius: 12px;
    overflow: hidden;
    background: rgba(var(--v-theme-surface-variant), 0.4);
}

.rules-header {
    padding: 8px 14px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: rgba(var(--v-theme-on-surface), 0.45);
    border-bottom: 1px solid rgba(var(--v-border-color), 0.12);
    background: rgba(var(--v-theme-surface), 0.8);
}

.rule {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 14px;
    border-bottom: 1px solid rgba(var(--v-border-color), 0.1);
    transition: background 0.2s ease;

    &:last-child {
        border-bottom: none;
    }

    &.pass {
        background: rgba(29, 158, 117, 0.07);
    }
    &.fail {
        background: rgba(226, 75, 74, 0.06);
    }
}

.dot {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.25s ease;

    .idle & {
        border: 1.5px dashed rgba(var(--v-border-color), 0.4);
        background: transparent;
    }

    .pass & {
        background: #1d9e75;
        border: 1.5px solid #1d9e75;
        color: #fff;
    }

    .fail & {
        background: #e24b4a;
        border: 1.5px solid #e24b4a;
        color: #fff;
    }
}

.rule-text {
    font-size: 13px;
    transition: color 0.2s ease;

    .idle & {
        color: rgba(var(--v-theme-on-surface), 0.5);
    }
    .pass & {
        color: #085041;
        font-weight: 500;
    }
    .fail & {
        color: #a32d2d;
    }
}

/* ── Confirm match pill ── */
.match-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: 8px;
    padding: 5px 12px;
    border-radius: 99px;
    font-size: 12.5px;
    font-weight: 500;

    &.ok {
        background: rgba(29, 158, 117, 0.1);
        color: #085041;
        border: 1px solid rgba(29, 158, 117, 0.25);
    }

    &.no {
        background: rgba(226, 75, 74, 0.09);
        color: #a32d2d;
        border: 1px solid rgba(226, 75, 74, 0.22);
    }
}

/* ── Transitions ── */
.fade-enter-active,
.fade-leave-active {
    transition:
        opacity 0.25s ease,
        transform 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}

.pop-enter-active {
    animation: popIn 0.2s ease;
}
.pop-leave-active {
    animation: popIn 0.15s ease reverse;
}

@keyframes popIn {
    from {
        transform: scale(0.85);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}
</style>
