<template>
  <v-card variant="tonal" color="surface-variant" class="rounded-lg bg-blue-grey-lighten-5">
    <v-card-title :class="[nameSize, 'opacity-70', 'font-weight-bold', 'text-uppercase', 'mb-0']">{{ name }}:</v-card-title>
    <v-card-text :class="[valueSize, 'font-weight-bold', 'mt-0']">{{ parseValue(value) }}</v-card-text>
  </v-card>
</template>

<script setup>

import {computed} from "vue";

function parseValue(value) {
  if (value == null){
    return '-'
  }
  if(value.constructor === Array) {
    return value.join(', ');
  }
  return value;
}

const props = defineProps({
  name: String,
  value: [String, Number, Array],
  size: {
    type: String,
    default: "normal",
    validator(value, props) {
      // The value must match one of these strings
      return ['normal', 'small', 'x-small'].includes(value)
    }
  }
});

const valueSize = computed(() => {
  switch (props.size) {
    case 'normal':
      return "text-body-large";
    case 'small':
      return "text-body-medium";
    case 'x-small':
      return "text-body-small";
  }
});

const nameSize = computed(() => {
  switch (props.size) {
    case 'normal':
      return "text-label-large";
    case 'small':
      return "text-label-medium";
    case 'x-small':
      return "text-label-small";
  }

  return props.size === 'normal' ? '' : 'text-subtitle-2'
});

</script>
