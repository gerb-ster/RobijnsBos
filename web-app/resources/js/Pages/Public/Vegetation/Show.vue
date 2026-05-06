<template>
  <Head>
    <title>{{ $t('appName', {page: $t('public.vegetation.show.title')}) }}</title>
  </Head>
  <v-container max-width="960" class="mx-auto">
    <v-row>
      <v-col cols="12" md="6">
        <div class="text-headline-large font-weight-bold">{{ vegetation.species.dutch_name }} &bull; {{ vegetation.species.latin_name }}</div>
        <p class="text-body-medium">{{ vegetation.label }}</p>
      </v-col>
      <v-col cols="12" md="6">
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
              text="Notities & Onderhoud"
            ></v-toolbar-title>
            <template v-slot:append>
              <div class="font-weight-bold text-body-medium mr-3 blue-grey-darken-1">Intern</div>
            </template>
          </v-toolbar>
          <v-divider></v-divider>
          <v-card-text class="pb-8 pt-4">
            <v-tabs v-model="tab" color="primary" :items="tabs">
              <template v-slot:tab="{ item }">
                <v-tab
                  :text="item.text"
                  :value="item.value"
                  class="rounded-pill pa-2 mr-2 font-weight-bold"
                  variant="outlined"
                  border="sm surface-variant"
                  selected-class="bg-light-green-lighten-5 white"
                ></v-tab>
              </template>
            </v-tabs>
            <v-tabs-window v-model="tab">
              <v-tabs-window-item value="comments">
                <v-sheet class="pt-5">
                  <v-btn
                    prepend-icon="mdi-plus"
                    color="success"
                    :href="route('public.vegetation.comment.create', {vegetation: vegetation.uuid})"
                    elevation="0"
                    :text="$t('public.comments.addBtn')"
                    block
                    class="rounded-pill font-weight-bold"
                  ></v-btn>
                  <v-card
                    variant="outlined"
                    border="sm surface-variant"
                    class="mx-auto mt-5 rounded-lg"
                    v-for="(comment, index) in vegetation.comments"
                  >
                    <v-toolbar color="transparent" density="comfortable">
                      <v-toolbar-title
                        class="font-weight-bold"
                        :text="comment.name"
                      ></v-toolbar-title>
                      <template v-slot:append>
                        <div class="font-weight-bold text-body-medium mr-3 blue-grey-darken-1">{{ renderNiceDate(comment.created_at) }}</div>
                      </template>
                    </v-toolbar>
                    <v-card-text class="ml-2 text-body-large">
                      {{ comment.remarks }}
                    </v-card-text>
                  </v-card>
                </v-sheet>
              </v-tabs-window-item>
              <v-tabs-window-item value="mutations">
                <v-sheet class="pt-5">
                  <v-btn
                    v-if="auth.user !== null"
                    prepend-icon="mdi-plus"
                    color="success"
                    :href="route('public.vegetation.mutation.create', {vegetation: vegetation.uuid})"
                    elevation="0"
                    :text="$t('public.mutations.addBtn')"
                    block
                    class="rounded-pill font-weight-bold"
                  ></v-btn>
                  <v-card
                    color="secondary"
                    variant="tonal"
                    class="mx-auto mb-5"
                    v-for="(mutation, index) in vegetation.mutations"
                  >
                    <v-toolbar color="transparent" density="comfortable">
                      <v-toolbar-title
                        class="font-weight-bold"
                        :text="mutation.title"
                      ></v-toolbar-title>
                      <template v-slot:append>
                        <div class="font-weight-bold text-body-medium mr-3 blue-grey-darken-1">Op {{ renderNiceDate(mutation.created_at) }} door {{ mutation.user.name }}</div>
                      </template>
                    </v-toolbar>
                    <v-card-text>
                      {{ mutation.remarks }}
                    </v-card-text>
                  </v-card>
                </v-sheet>
              </v-tabs-window-item>
            </v-tabs-window>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>

import {Head, usePage} from '@inertiajs/vue3';
import {renderDateTime, renderNiceDate} from "../../../Logic/Helpers.ts";
import {computed, inject, shallowRef} from "vue";
import PropertyCard from "../../../Components/PropertyCard.vue";
import {useI18n} from "vue-i18n";

const route = inject('route');
const {t} = useI18n({});
const page = usePage();

const auth = computed(() => page.props.auth)

const tab = shallowRef('comments');

const tabs = [
  {
    text: t('public.vegetation.show.comments') ,
    value: 'comments',
  },
  {
    text: t('public.vegetation.show.mutations') ,
    value: 'mutations',
  }
]

const props = defineProps({
  vegetation: Object,
  canAdministrate: Boolean
});

</script>
