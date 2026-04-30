<template>
  <Head>
    <title>{{ $t('appName', {page: $t('public.vegetation.show.title')}) }}</title>
  </Head>
  <v-container max-width="1400" class="mx-auto">
    <v-row>
      <v-col cols="12" md="8">
        <div class="text-headline-large font-weight-bold">{{ vegetation.species.dutch_name }} &bull; {{ vegetation.species.latin_name }}</div>
        <p class="text-body-medium">{{ vegetation.label }}</p>
      </v-col>
      <v-col cols="12" md="4">
        <v-chip
          variant="outlined"
          size="large"
          border="sm surface-variant"
          class="float-end border-surface-light"
          elevation="1"
        >
          {{ $t('vegetation.fields.location.name') }}:
          <span class="font-weight-bold ml-2">{{ vegetation.location['x'] }}, {{ vegetation.location['y'] }}</span>
        </v-chip>
        <v-chip
          variant="outlined"
          size="large"
          border="sm surface-variant"
          class="float-end mr-2"
          elevation="1"
        >
          {{ $t('vegetation.fields.area') }}:
          <span class="font-weight-bold ml-2">{{ vegetation.area ? vegetation.area.name : '-' }}</span>
        </v-chip>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" md="5">
        <v-card
          variant="text"
          rounded="lg"
          class="elevation-2"
          border="sm"
        >
          <v-toolbar color="transparent" density="comfortable">
            <v-toolbar-title
              class="font-weight-bold"
              text="Overzicht"
            ></v-toolbar-title>
            <template v-slot:append>
              <div class="font-weight-bold text-body-medium mr-3 blue-grey-darken-1">{{ vegetation.number }}</div>
            </template>
          </v-toolbar>
          <v-divider></v-divider>
          <v-card-text class="pb-8 pt-4">
            <div class="text-headline-small">{{ vegetation.species.dutch_name }}</div>
            <div class="text-body-medium">{{ vegetation.species.latin_name }}</div>
            <v-row no-gutters class="mt-4">
              <v-col cols="12" md="6">
                <property-card
                  :name="$t('vegetation.fields.placed')"
                  :value="vegetation.placed"
                  class="mr-1"
                ></property-card>
              </v-col>
              <v-col cols="12" md="6">
                <property-card
                  :name="$t('vegetation.fields.status')"
                  :value="$t('vegetationStatus.' + vegetation.status.name)"
                  class="ml-1"
                ></property-card>
              </v-col>
              <v-col cols="12" md="6" class="mt-2">
                <property-card
                  :name="$t('species.fields.blossomMonth')"
                  :value="vegetation.species.blossom_month"
                  class="mr-1"
                ></property-card>
              </v-col>
              <v-col cols="12" md="6" class="mt-2">
                <property-card
                  :name="$t('species.fields.height')"
                  :value="vegetation.species.height"
                  class="ml-1"
                ></property-card>
              </v-col>
            </v-row>
            <p class="text-body-medium">{{ vegetation.remarks }}</p>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="7">
        <v-card
          variant="text"
          rounded="lg"
          class="elevation-2"
          border="sm"
        >
          <v-toolbar color="transparent" density="comfortable">
            <v-toolbar-title
              class="font-weight-bold"
              text="Kaart / Label"
            ></v-toolbar-title>
            <template v-slot:append>
              <div class="font-weight-bold text-body-medium mr-3 blue-grey-darken-1">Preview</div>
            </template>
          </v-toolbar>
          <v-divider></v-divider>
          <v-card-text class="pb-4 pt-4">
            <v-img
              class="border border-error mb-3"
              :src="route('public.vegetation.showBoard', {vegetation: vegetation.uuid})"
            ></v-img>
          </v-card-text>
        </v-card>
        <v-card
          variant="text"
          rounded="lg"
          class="elevation-2 mt-4"
          border="sm"
        >
          <v-toolbar color="transparent" density="comfortable">
            <v-toolbar-title
              class="font-weight-bold"
              text="Notities"
            ></v-toolbar-title>
            <template v-slot:append>
              <div class="font-weight-bold text-body-medium mr-3 blue-grey-darken-1">Intern</div>
            </template>
          </v-toolbar>
          <v-divider></v-divider>
          <v-card-text class="pb-8 pt-4">

          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>

import {Head, usePage} from '@inertiajs/vue3';
import {renderDateTime} from "../../../Logic/Helpers.ts";
import {computed, inject} from "vue";
import PropertyCard from "../../../Components/PropertyCard.vue";
const route = inject('route');

const page = usePage()
const auth = computed(() => page.props.auth)

const props = defineProps({
  vegetation: Object,
  canAdministrate: Boolean
});

</script>
