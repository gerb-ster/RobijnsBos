<template>
  <v-app id="layout">
    <v-app-bar elevation="0" flat style="border-bottom: 1px solid #d2d2d2 !important;">
      <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
      <v-app-bar-title>
        <v-img
          src='/images/logo_full.png'
          max-height="40"
          max-width="200"
        ></v-img>
      </v-app-bar-title>
    </v-app-bar>
    <v-navigation-drawer
      v-model="drawer"
      location="left"
      temporary
    >
      <v-list>
        <Link as="div" :href="$route('homePage.index')">
          <v-list-item
            prepend-icon="mdi-home"
            title="Home"
            value="home"
            :href="$route('homePage.index')"
            @click="drawer=!drawer"
          ></v-list-item>
        </Link>
        <v-divider></v-divider>
        <Link as="div" :href="$route('public.vegetation.overview')">
          <v-list-item
            prepend-icon="mdi-forest-outline"
            title="Overzicht"
            value="overview"
            :href="$route('public.vegetation.overview')"
            @click="drawer=!drawer"
          ></v-list-item>
        </Link>
        <Link as="div" :href="$route('public.vegetation.map')">
          <v-list-item
            prepend-icon="mdi-map-legend"
            title="Kaart"
            value="map"
            :href="$route('public.vegetation.map')"
            @click="drawer=!drawer"
          ></v-list-item>
        </Link>
      </v-list>
    </v-navigation-drawer>
    <v-main>
      <slot/>
    </v-main>
  </v-app>
</template>

<script setup>

import {Link} from "@inertiajs/vue3";
import {ref} from "vue";

const props = defineProps();

const drawer = ref(false);
const group = ref(null);

</script>
