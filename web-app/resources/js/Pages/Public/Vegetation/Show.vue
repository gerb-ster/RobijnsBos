<template>
  <Head><title>{{ $t('appName', {page: $t('public.vegetation.show.title')}) }}</title></Head>
  <v-container fluid>
    <v-row>
      <v-col cols="12" md="4">
        <h2>Profiel {{ vegetation.label }}</h2>
        <v-list lines="two">
          <v-list-item>
            <v-list-item-title><h3>{{ vegetation.location['x'] }}, {{ vegetation.location['y'] }}</h3></v-list-item-title>
            <v-list-item-subtitle>{{ $t('vegetation.fields.location.name') }}</v-list-item-subtitle>
          </v-list-item>
          <v-list-item>
            <v-list-item-title><h3>{{ vegetation.group.name }} / {{ vegetation.group.area.name }} </h3></v-list-item-title>
            <v-list-item-subtitle>{{ $t('vegetation.fields.area') }}</v-list-item-subtitle>
          </v-list-item>
          <v-list-item>
            <v-list-item-title><h3>{{ $t('vegetationStatus.' + vegetation.status.name) }}</h3></v-list-item-title>
            <v-list-item-subtitle>{{ $t('vegetation.fields.status') }}</v-list-item-subtitle>
          </v-list-item>
          <v-list-item>
            <v-list-item-title><h3>{{ vegetation.species.dutch_name }}</h3></v-list-item-title>
            <v-list-item-subtitle>{{ $t('species.fields.dutchName') }}</v-list-item-subtitle>
          </v-list-item>
          <v-list-item>
            <v-list-item-title><h3>{{ vegetation.species.latin_name }}</h3></v-list-item-title>
            <v-list-item-subtitle>{{ $t('species.fields.latinName') }}</v-list-item-subtitle>
          </v-list-item>
          <v-list-item>
            <v-list-item-title><h3>{{ vegetation.species.type.name }}</h3></v-list-item-title>
            <v-list-item-subtitle>{{ $t('species.fields.type') }}</v-list-item-subtitle>
          </v-list-item>
          <v-list-item>
            <v-list-item-title><h3>{{ vegetation.placed }}</h3></v-list-item-title>
            <v-list-item-subtitle>{{ $t('vegetation.fields.placed') }}</v-list-item-subtitle>
          </v-list-item>
          <v-list-item>
            <v-list-item-title>
              <h3>
                <span v-for="(month, index) in vegetation.species.blossom_month">
                  {{ $t('months.'+month) }}<span v-if="index+1 < vegetation.species.blossom_month.length">, </span>
                </span>
              </h3>
            </v-list-item-title>
            <v-list-item-subtitle>{{ $t('species.fields.blossomMonth') }}</v-list-item-subtitle>
          </v-list-item>
        </v-list>
      </v-col>
      <v-col cols="12" md="4">
        <h2 class="mb-5">Opmerkingen</h2>
        <v-card
          color="primary"
          variant="tonal"
          class="mx-auto"
        >
          <v-card-title>Titel</v-card-title>
          <v-card-text>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation
            <div class="text-caption mt-4">23 mei 2025</div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <h2 class="mb-5">Onderhoud</h2>
        <v-card
          color="secondary"
          variant="tonal"
          class="mx-auto"
        >
          <v-card-title>Titel</v-card-title>
          <v-card-text>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation

            <div class="text-caption mt-4">23 mei 2025</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>

import {Head} from '@inertiajs/vue3';

const props = defineProps({vegetation: Object});

console.log(props.vegetation);

</script>
