import type { IconAliases, IconProps } from 'vuetify'
import { h } from 'vue'

import checkboxChecked from '../../assets/svg/checkbox-checked.svg'
import checkboxIndeterminate from '../../assets/svg/checkbox-indeterminate.svg'
import checkboxUnchecked from '../../assets/svg/checkbox-unchecked.svg'
import radioChecked from '../../assets/svg/radio-checked.svg'
import radioUnchecked from '../../assets/svg/radio-unchecked.svg'

const customIcons: Record<string, unknown> = {
  'mdi-checkbox-blank-outline': checkboxUnchecked,
  'mdi-checkbox-marked': checkboxChecked,
  'mdi-minus-box': checkboxIndeterminate,
  'mdi-radiobox-marked': radioChecked,
  'mdi-radiobox-blank': radioUnchecked,
}

const aliases: Partial<IconAliases> = {
  info: 'ri-error-warning-line',
  success: 'ri-checkbox-circle-line',
  warning: 'ri-alert-line',
  error: 'ri-error-warning-line',
  calendar: 'ri-calendar-2-line',
  collapse: 'ri-arrow-up-s-line',
  complete: 'ri-check-line',
  cancel: 'ri-close-line',
  close: 'ri-close-line',
  delete: 'ri-close-circle-fill',
  clear: 'ri-close-line',
  prev: 'ri-arrow-left-s-line',
  next: 'ri-arrow-right-s-line',
  delimiter: 'ri-circle-line',
  sort: 'ri-arrow-up-line',
  expand: 'ri-arrow-down-s-line',
  menu: 'ri-menu-line',
  subgroup: 'ri-arrow-down-s-fill',
  dropdown: 'ri-arrow-down-s-line',
  edit: 'ri-pencil-line',
  ratingEmpty: 'ri-star-line',
  ratingFull: 'ri-star-fill',
  ratingHalf: 'ri-star-half-line',
  loading: 'ri-refresh-line',
  first: 'ri-skip-back-mini-line',
  last: 'ri-skip-forward-mini-line',
  unfold: 'ri-split-cells-vertical',
  file: 'ri-attachment-2',
  plus: 'ri-add-line',
  minus: 'ri-subtract-line',
  sortAsc: 'ri-arrow-up-line',
  sortDesc: 'ri-arrow-down-line',
  contacts: 'ri-contacts-book-2-fill',
  institution: 'ri-government-line',
  education: 'ri-school-fill',
  country: 'ri-earth-line',
  level: 'ri-stairs-fill',
}


export const iconify = {
  component: (props: IconProps) => {
    const iconClasses = typeof props.icon === 'string' && props.icon.startsWith('mdi-')
      ? ['mdi', props.icon]
      : [props.icon]

    if (typeof props.icon === 'string') {
      const iconComponent = customIcons[props.icon]

      if (iconComponent)
        return h(iconComponent)
    }

    return h(
      props.tag,
      {
        ...props,
        class: iconClasses,
        tag: undefined,
        icon: undefined,
      },
    )
  },
}

export const icons = {
  defaultSet: 'iconify',
  aliases,
  sets: {
    iconify,
  },
}
