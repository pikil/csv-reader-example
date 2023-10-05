<script setup>
import { computed } from 'vue'

const defViewbox = '0 0 24 24'

const props = defineProps({
  name: {
    type: String,
    default: ''
  }
})

const data = computed(() => props.name.split('|'))
const paths = computed(() => data.value[0].split('&&').map(path => path.split('@@')))
const viewBox = computed(() => data.value[1] || defViewbox)
const classes = computed(() => 'fill-current inline' + (props.class ? ' ' + props.class : ''))

</script>

<template>
  <svg :viewBox="viewBox" :class="classes">
    <path
      v-for="([d, style, transform], index) in paths"
      :key="index"
      :d="d"
      :style="style"
      :transform="transform"
    />
  </svg>
</template>
