<template>
  <Head><title>{{ $t('appName', {page: $t('public.vegetation.map.title')}) }}</title></Head>
  <v-container fluid>
    <v-row>
      <v-col md="2">
        <h4>Legenda</h4>
        <v-divider class="mt-4 mb-4"></v-divider>
        <v-text-field
          v-model="searchField"
          prepend-inner-icon="mdi-magnify"
          :label="$t('form.search')"
          single-line
          hide-details
          density="compact"
        ></v-text-field>
        <v-card
          class="mt-4"
          variant="tonal"
          color="surface-variant"
          subtitle="Selecteer lagen"
          title="Lagen"
        >
          <v-card-text>
            <v-checkbox
              hide-details
              v-model="raster"
              label="Raster"
            ></v-checkbox>
            <v-checkbox
              hide-details
              v-model="coords"
              label="Coördinaten"
            ></v-checkbox>
            <v-checkbox
              hide-details
              v-model="names"
              label="Namen"
            ></v-checkbox>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col md="10" class="bg-grey-darken-1 pa-0">
        <svg-pan-zoom
          v-if="mapData"
          :style="{'width': '100%', 'height': pageHeight}"
          :zoomEnabled="true"
          :controlIconsEnabled="false"
          :fit="false"
          :center="true"
        >
          <svg width="100%" :height="pageHeight" v-html="mapData" ref="mapRef"></svg>
        </svg-pan-zoom>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>

import {Head} from '@inertiajs/vue3';
import {SvgPanZoom} from "vue-svg-pan-zoom";
import {computed, onBeforeMount, ref, watch} from "vue";
import axios from "axios";
import {useDisplay} from "vuetify";

const { height } = useDisplay();

const searchField = ref('');

const raster = ref(true);
const coords = ref(true);
const names = ref(true);

const mapData = ref(null);
const svgPanZoomRef = ref(null);
const mapRef = ref(null);

const pageHeight = computed(() => {
  return height.value - 80;
});

onBeforeMount(() => {
  axios.get(route('public.vegetation.map.image')).then(response => {
    mapData.value = response.data;
  }, (error) => {
    console.log(error);
  });
});

function registerSvgPanZoom(objectRef) {
  svgPanZoomRef.value = objectRef;
}

watch(raster, () => {
  mapRef.value.getElementById('Raster').classList.toggle('hide');
});

watch(coords, () => {
  mapRef.value.getElementById('Coordinaten').classList.toggle('hide');
});

watch(names, () => {
  mapRef.value.getElementById('Nederlandse-namen').classList.toggle('hide');
});

</script>

<style>
  .hide {
    visibility: hidden;
  }
</style>
