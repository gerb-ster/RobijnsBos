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
        <h2 class="mb-5">{{ $t('public.vegetation.show.comments') }}</h2>
        <v-card
          color="primary"
          variant="tonal"
          class="mx-auto mb-5"
          v-for="(comment, index) in vegetation.comments"
        >
          <v-card-title>{{ comment.name }}</v-card-title>
          <v-card-text>
            {{ comment.remarks }}
            <div class="text-caption mt-4">{{ renderDateTime(comment.created_at) }}</div>
          </v-card-text>
        </v-card>
        <v-btn
          prepend-icon="mdi-plus"
          color="primary"
          :href="$route('public.vegetation.comment.create', {vegetation: vegetation.uuid})"
          elevation="0"
        > {{ $t('public.comments.addBtn') }}
        </v-btn>
      </v-col>
      <v-col cols="12" md="4">
        <h2 class="mb-5">{{ $t('public.vegetation.show.mutations') }}</h2>
        <v-card
          color="secondary"
          variant="tonal"
          class="mx-auto mb-5"
          v-for="(mutation, index) in vegetation.mutations"
        >
          <v-card-title>{{ mutation.title }}</v-card-title>
          <v-card-text>
            {{ mutation.remarks }}
            <div class="text-caption mt-4">Op {{ renderDateTime(mutation.created_at) }} door {{ mutation.user.name }}</div>
          </v-card-text>
        </v-card>
        <v-btn
          v-if="auth.user !== null"
          prepend-icon="mdi-plus"
          color="primary"
          :href="$route('public.vegetation.mutation.create', {vegetation: vegetation.uuid})"
          elevation="0"
        > {{ $t('public.mutations.addBtn') }}
        </v-btn>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>

import {Head, usePage} from '@inertiajs/vue3';
import {renderDateTime} from "../../../Logic/Helpers.ts";
import {computed} from "vue";

const page = usePage()
const auth = computed(() => page.props.auth)

const props = defineProps({vegetation: Object});

</script>
