<template>
  <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm text-gray-600 mb-1">{{ title }}</p>
        <p class="text-3xl font-bold text-gray-900">{{ value }}</p>
        <p v-if="subtitle" class="text-sm text-gray-500 mt-1">{{ subtitle }}</p>
      </div>
      <div v-if="icon" class="text-4xl" :class="iconColor">
        {{ icon }}
      </div>
    </div>
    <div v-if="trend" class="mt-4 flex items-center text-sm">
      <span :class="trendColor" class="font-medium">
        {{ trend > 0 ? '+' : '' }}{{ trend }}%
      </span>
      <span class="text-gray-500 ml-2">vs mois dernier</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: ''
  },
  iconColor: {
    type: String,
    default: 'text-green-600'
  },
  trend: {
    type: Number,
    default: null
  }
})

const trendColor = computed(() => {
  return props.trend > 0 ? 'text-green-600' : 'text-red-600'
})
</script>
