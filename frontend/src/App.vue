<template>
  <div id="app">
    <Header />
    <main>
      <router-view />
    </main>
  </div>
</template>

<script setup>
import Header from './components/Header.vue'
</script>

<style>
#app {
  min-height: 100vh;
}

main {
  min-height: calc(100vh - 64px);
}
</style>
