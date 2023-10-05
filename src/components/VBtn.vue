<script setup>
import VIcon from './VIcon.vue'

defineProps({
  label: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'button'
  },
  icon: {
    type: String,
    default: ''
  },
  iconClass: {
    type: String,
    default: 'w-6 h-6'
  }
})
</script>

<template>
  <button
    class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors py-2 px-4 text-xl flex flex-row gap-4 items-center select-none"
    :type="type"
  >
    <VIcon v-if="icon" :name="icon" :class="iconClass" />
    <span v-if="label">{{label}}</span>
    <slot />
  </button>
</template>
