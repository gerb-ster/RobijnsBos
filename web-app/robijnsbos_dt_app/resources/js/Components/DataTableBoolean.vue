<template>
  <td>
    <v-icon>
      {{ value ? "mdi-check" : "mdi-minus" }}
    </v-icon>
    <slot></slot>
  </td>
</template>

<script setup>
const props = defineProps(['value']);
</script>
