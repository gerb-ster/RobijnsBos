<template>
  <Head :title="$t('species.createTitle')"/>
  <v-container fluid>
    <flash-messages/>
    <v-form @submit.prevent="submit">
      <v-row>
        <v-col cols="12" md="4">
          <div :class="['text-h5', 'pa-2']">{{ $t('species.createTitle') }}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" md="4">
          <v-text-field
            v-model="form.dutch_name"
            :label="$t('species.fields.dutchName')"
            :rules="rules.required"
            required
          ></v-text-field>
          <v-text-field
            v-model="form.latin_name"
            :label="$t('species.fields.latinName')"
            :rules="rules.required"
            required
          ></v-text-field>
          <v-select
            v-model="form.blossom_month"
            :items="months"
            :label="$t('species.fields.blossomMonth')"
            :item-value="item => item"
            :item-title="item => $t('months.'+item)"
            multiple
            persistent-hint
          ></v-select>
          <v-text-field
            v-model="form.height"
            :label="$t('species.fields.height')"
            :rules="rules.required"
            required
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" md="4">
          <v-btn
            prepend-icon="mdi-content-save"
            size="large"
            color="primary"
            type="submit"
            elevation="0"
          > {{ $t('form.saveBtn') }}
          </v-btn>
          <Link :href="$route('species.index')">
            <v-btn
              prepend-icon="mdi-keyboard-return"
              size="large"
              class="ml-5"
              :href="$route('species.index')"
              elevation="0"
            > {{ $t('form.backBtn') }}
            </v-btn>
          </Link>
        </v-col>
      </v-row>
    </v-form>
  </v-container>
</template>

<script setup>

import {Head, Link, useForm} from '@inertiajs/vue3';
import {email, required} from "@vee-validate/rules";
import {useI18n} from "vue-i18n";
import FlashMessages from "../../../../Shared/FlashMessages.vue";

const props = defineProps();

const {t} = useI18n({});

const months = [
  "january","february","march",
  "april","may","june",
  "july","august","september",
  "october","november","december"
];

const form = useForm({
  dutch_name: null,
  latin_name: null,
  blossom_month: null,
  height: null,
});

const rules = {
  required: [
    value => required(value) || t('form.validation.required')
  ]
}

async function submit(event) {
  const results = await event;

  if (results.valid) {
    form.post(route('species.store'));
  }
}

</script>
