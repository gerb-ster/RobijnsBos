<template>
  <Head><title>{{ $t('appName', {page: $t('public.vegetation.map.title')}) }}</title></Head>
  <v-container fluid class="pa-0">
    <v-layout>
      <v-navigation-drawer
        v-model="drawer"
        :rail="rail"
        permanent
        @click="rail = false"
        theme="dark"
        :width="340"
      >
        <v-list>
          <v-list-item
            prepend-icon="mdi-map-legend"
            title="Kaart Opties"
          >
            <template v-slot:append>
              <v-btn
                icon="mdi-chevron-left"
                variant="text"
                @click.stop="rail = !rail"
              ></v-btn>
            </template>
          </v-list-item>
        </v-list>
        <v-divider></v-divider>
        <v-card
          class="mt-4 ms-3 me-3"
          variant="tonal"
          color="surface-variant"
          title="Lagen"
          v-if="!rail"
        >
          <v-card-text>
            <v-checkbox
              hide-details
              v-model="raster"
              label="Raster"
              density="compact"
            ></v-checkbox>
            <v-checkbox
              hide-details
              v-model="coords"
              label="Coördinaten"
              density="compact"
            ></v-checkbox>
            <v-checkbox
              hide-details
              v-model="names"
              label="Namen"
              density="compact"
            ></v-checkbox>
          </v-card-text>
        </v-card>
        <v-card
          class="mt-4 ms-3 me-3"
          variant="tonal"
          color="surface-variant"
          title="Soort Beplanting"
          v-if="!rail"
        >
          <v-card-text>
            <v-checkbox
              v-for="type in speciesTypes"
              hide-details
              v-model="selectedSpecies"
              :label="$t('specieTypes.'+type.name)"
              density="compact"
              :value="type.name"
            ></v-checkbox>
          </v-card-text>
        </v-card>
      </v-navigation-drawer>
      <v-main class="bg-grey-darken-1 pa-0">
        <svg-pan-zoom
          v-if="mapData"
          :style="{'width': '100%', 'height': pageHeight}"
          :zoomEnabled="true"
          :controlIconsEnabled="true"
          :fit="false"
          :center="true"
          @created="registerSvgPanZoom"
        >
          <svg width="100%" :height="pageHeight" v-html="mapData" ref="mapRef"></svg>
        </svg-pan-zoom>
      </v-main>
    </v-layout>
  </v-container>
</template>

<script setup>

import {Head} from '@inertiajs/vue3';
import {SvgPanZoom} from "vue-svg-pan-zoom";
import {computed, onBeforeMount, ref, watch} from "vue";
import axios from "axios";
import {useDisplay} from "vuetify";

const props = defineProps(['speciesTypes']);

const { height } = useDisplay();

const searchField = ref('');

const raster = ref(true);
const coords = ref(true);
const names = ref(true);
const selectedSpecies = ref(props.speciesTypes.map(type => type.name));

const mapData = ref(null);
const svgPanZoomRef = ref(null);
const mapRef = ref(null);

const drawer = ref(true)
const rail = ref(true)

const pageHeight = computed(() => {
  return height.value - 65;
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

function zoomInHandler(e) {
  console.log(e);
}

function onTapItem(e) {
  console.log(e);
}

watch(raster, () => {
  mapRef.value.getElementById('Raster').classList.toggle('hide');
});

watch(coords, () => {
  mapRef.value.querySelectorAll('.coordinates').forEach((item) => {
    item.classList.toggle('hide');
  });
});

watch(names, () => {
  mapRef.value.querySelectorAll('.speciesName').forEach((item) => {
    item.classList.toggle('hide');
  });
});

watch(selectedSpecies, () => {
  const allSpecies = props.speciesTypes.map(type => type.name);
  let deselected = allSpecies.filter(x => !selectedSpecies.value.includes(x));

  deselected.forEach((type) => {
    mapRef.value.querySelectorAll('.'+type).forEach((item) => {
      item.classList.add('hide');
    });
  });

  selectedSpecies.value.forEach((type) => {
    mapRef.value.querySelectorAll('.'+type).forEach((item) => {
      item.classList.remove('hide');
    });
  });
});

</script>

<style>
  .hide {
    visibility: hidden;
  }
</style>
