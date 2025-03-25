<template>
  <Head><title>Homepage</title></Head>
  <v-container fluid>
    <v-row>
      <v-col cols="12" md="12">
        <h3 class="mb-8">RobijnsBos Beplanting</h3>
        <p>{{ vegetation.label }}</p>
        <p>{{ vegetation.species.dutch_name }}</p>
        <p>{{ vegetation.species.latin_name }}</p>
        <p>{{ vegetation.placed }}</p>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>

import {Head} from '@inertiajs/vue3';

defineProps({vegetation: Object});

</script>
