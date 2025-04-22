<template>
  <Head><title>{{ $t('appName', {page: $t('public.homepage.title')}) }}</title></Head>
  <v-container fluid>
    <v-row>
      <v-col cols="12" md="12">
        <h3 class="mb-8">Welkom op de RobijnsBos Digital Twin</h3>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>

import {Head} from '@inertiajs/vue3';

</script>
