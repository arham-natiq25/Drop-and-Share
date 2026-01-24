<template>
  <router-view v-slot="{ Component, route }">
    <component :is="route.meta.layout === 'auth' ? AuthLayout : AppLayout">
      <component :is="Component" />
    </component>
  </router-view>
</template>

<script setup>
import AppLayout from './layouts/AppLayout.vue'
import AuthLayout from './layouts/AuthLayout.vue'
</script>

