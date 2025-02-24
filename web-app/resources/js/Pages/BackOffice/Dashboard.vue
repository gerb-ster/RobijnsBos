<template>
  <Head><title>{{ $t('dashboard.title') }}</title></Head>
  <v-container fluid>
    <flash-messages/>
    <p>DashBoard</p>
  </v-container>
</template>

<script setup>

import FlashMessages from "../../Shared/FlashMessages.vue";

</script>
