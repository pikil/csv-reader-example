<script setup>
import { onMounted } from 'vue'
import HeaderLayout from './layouts/HeaderLayout.vue'
import FooterLayout from './layouts/FooterLayout.vue'
import MainLayout from './layouts/MainLayout.vue'

const resetVh = () => {
  document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`)
}

onMounted(() => {
  window.addEventListener('resize', resetVh) // No need to remove this listener, as it should be working throughout the whole app's lifecycle
})
</script>

<template>
  <HeaderLayout />
  <MainLayout />
  <FooterLayout />
</template>
