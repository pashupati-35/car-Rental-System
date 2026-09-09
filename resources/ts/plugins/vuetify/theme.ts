import type { ThemeDefinition } from 'vuetify'

// Logo-inspired color palette - Alternative Combination
export const staticPrimaryColor = '#1B4F5F'
export const staticPrimaryDarkenColor = '#1b7692ff'
export const staticSecondaryColor = '#1B4F5F'
export const staticSecondaryDarkenColor = '#02a533'

export const themes: Record<string, ThemeDefinition> = {
  light: {
    dark: false,
    colors: {
      // Primary Colors (Logo Orange #1B4F5F)
      'primary': staticPrimaryColor,
      'on-primary': '#fff',
      'primary-darken-1': staticPrimaryDarkenColor,

      // Secondary Colors (Logo Green #1B4F5F)
      'secondary': staticSecondaryColor,
      'secondary-darken-1': staticSecondaryDarkenColor,
      'on-secondary': '#fff',

      // Accent Color (Deep Purple for sophistication)
      'accent': '#8b5cf6',
      'accent-darken-1': '#7c3aed',
      'on-accent': '#fff',

      // Status Colors
      "success": "#4CAF50",        // Green
      "success-darken-1": "#388E3C",
      "info": "#2196F3",           // rgb(104, 104, 234)
      "info-darken-1": "#1976D2",
      "warning": "#FFC107",        // Amber/Yellow
      "warning-darken-1": "#FFA000",
      "error": "#F44336",          // Red
      "error-darken-1": "#D32F2F",
      "on-success": "#ffffff",
      "on-info": "#ffffff",
      "on-warning": "#000000",
      "on-error": "#ffffff",

      // Background & Surface (Warm tinted)
      'background': '#fffbf5',
      'on-background': '#1c1917',
      'surface': '#ffffff',
      'on-surface': '#1c1917',

      // Grey Scale (Warm Stone palette)
      'grey-50': '#fafaf9',
      'grey-100': '#f5f5f4',
      'grey-200': '#e7e5e4',
      'grey-300': '#d6d3d1',
      'grey-400': '#a8a29e',
      'grey-500': '#78716c',
      'grey-600': '#57534e',
      'grey-700': '#44403c',
      'grey-800': '#292524',
      'grey-900': '#1c1917',

      // Legacy/Custom Colors
      'perfect-scrollbar-thumb': '#d6d3d1',
      'skin-bordered-background': '#fff',
      'skin-bordered-surface': '#fff',
      'expansion-panel-text-custom-bg': '#fffbf5',
      'track-bg': '#f5f5f4',
      'chat-bg': '#fffbf5',
    },

    variables: {
      'code-color': '#8b5cf6',
      'overlay-scrim-background': '#1c1917',
      'tooltip-background': '#292524',
      'overlay-scrim-opacity': 0.5,
      'hover-opacity': 0.04,
      'focus-opacity': 0.1,
      'selected-opacity': 0.08,
      'activated-opacity': 0.16,
      'pressed-opacity': 0.14,
      'dragged-opacity': 0.1,
      'disabled-opacity': 0.4,
      'border-color': '#1c1917',
      'border-opacity': 0.12,
      'table-header-color': '#fffbf5',
      'high-emphasis-opacity': 0.9,
      'medium-emphasis-opacity': 0.7,

      // Shadows
      'shadow-key-umbra-color': '#1c1917',
      'shadow-xs-opacity': '0.16',
      'shadow-sm-opacity': '0.18',
      'shadow-md-opacity': '0.20',
      'shadow-lg-opacity': '0.22',
      'shadow-xl-opacity': '0.24',
    },
  },

  dark: {
    dark: true,
    colors: {
      // Primary Colors (Brighter Orange for dark mode)
      'primary': '#ff7515',
      'on-primary': '#fff',
      'primary-darken-1': '#1B4F5F',

      // Secondary Colors (Brighter Green for dark mode)
      'secondary': '#03e347',
      'secondary-darken-1': '#1B4F5F',
      'on-secondary': '#fff',

      // Accent Color (Brighter Purple for dark mode)
      'accent': '#a78bfa',
      'accent-darken-1': '#8b5cf6',
      'on-accent': '#fff',

      // Status Colors (Brighter for dark mode)
      'success': '#03e347',
      'success-darken-1': '#1B4F5F',
      'on-success': '#fff',
      'info': '#1c4e55ff',
      'info-darken-1': '#06b6d4',
      'on-info': '#fff',
      'warning': '#fbbf24',
      'warning-darken-1': '#f59e0b',
      'on-warning': '#fff',
      'error': '#f87171',
      'error-darken-1': '#ef4444',
      'on-error': '#fff',

      // Background & Surface (Warm dark tones)
      'background': '#1c1917',
      'on-background': '#fafaf9',
      'surface': '#292524',
      'on-surface': '#fafaf9',

      // Grey Scale (Inverted Warm Stone)
      'grey-50': '#292524',
      'grey-100': '#44403c',
      'grey-200': '#57534e',
      'grey-300': '#78716c',
      'grey-400': '#a8a29e',
      'grey-500': '#d6d3d1',
      'grey-600': '#e7e5e4',
      'grey-700': '#f5f5f4',
      'grey-800': '#fafaf9',
      'grey-900': '#ffffff',

      // Legacy/Custom Colors
      'perfect-scrollbar-thumb': '#57534e',
      'skin-bordered-background': '#292524',
      'skin-bordered-surface': '#292524',
      'expansion-panel-text-custom-bg': '#44403c',
      'track-bg': '#44403c',
      'chat-bg': '#292524',
    },

    variables: {
      'code-color': '#a78bfa',
      'overlay-scrim-background': '#0c0a09',
      'tooltip-background': '#fafaf9',
      'overlay-scrim-opacity': 0.5,
      'hover-opacity': 0.04,
      'focus-opacity': 0.1,
      'selected-opacity': 0.08,
      'activated-opacity': 0.16,
      'pressed-opacity': 0.14,
      'disabled-opacity': 0.4,
      'dragged-opacity': 0.1,
      'border-color': '#fafaf9',
      'border-opacity': 0.12,
      'table-header-color': '#44403c',
      'high-emphasis-opacity': 0.9,
      'medium-emphasis-opacity': 0.7,

      // Shadows
      'shadow-key-umbra-color': '#0c0a09',
      'shadow-xs-opacity': '0.20',
      'shadow-sm-opacity': '0.22',
      'shadow-md-opacity': '0.24',
      'shadow-lg-opacity': '0.26',
      'shadow-xl-opacity': '0.28',
    },
  },
}

export default themes